<?php
/** Private Google reporting for the Suite admin. No frontend collector or credential export. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Admin_Stats_20261009 {
    public const DATA = 'zps_admin_stats_20261009_data';
    public const QUEUE = 'zps_admin_stats_20261009_queue';
    public const LOCK = 'zps_admin_stats_20261009_lock';
    public const WORKER = 'zp_admin_stats_worker_20261009';
    public const DAILY = 'zp_admin_stats_daily_20261009';
    public const HOURLY = 'zp_admin_stats_hourly_20261009';
    private const METRICS = ['activeUsers', 'totalUsers', 'sessions', 'screenPageViews'];
    private const REPORT_IDS = ['ga4.today','ga4.7d','ga4.28d','ga4.daily','ga4.sources.today','ga4.sources.7d','ga4.sources.28d','gsc.total','gsc.daily','gsc.queries','gsc.pages','gsc.positions'];
    private static ?array $last_response_shape = null;
    private const AI = ['chatgpt.com'=>'ChatGPT','chat.openai.com'=>'ChatGPT','chatgpt'=>'ChatGPT','claude.ai'=>'Claude','perplexity.ai'=>'Perplexity','gemini.google.com'=>'Gemini','copilot.microsoft.com'=>'Copilot','copilot.cloud.microsoft'=>'Copilot','chat.mistral.ai'=>'Mistral','chat.deepseek.com'=>'DeepSeek','grok.com'=>'Grok','meta.ai'=>'Meta AI','you.com'=>'You.com'];

    public static function allowed(): bool {
        return current_user_can('manage_options') && (!class_exists('RankMath\\Helper') || !method_exists('RankMath\\Helper', 'has_cap') || \RankMath\Helper::has_cap('analytics'));
    }
    public static function connection(): array {
        $profile = get_option('rank_math_google_analytic_profile', []);
        $settings = get_option('rank_math_google_analytic_options', []);
        $profile = is_array($profile) ? $profile : []; $settings = is_array($settings) ? $settings : [];
        $property = isset($profile['profile']) && is_string($profile['profile']) ? $profile['profile'] : '';
        $home = trailingslashit(home_url('/'));
        // Only the currently configured canonical URL-prefix property; no caller-selectable account.
        $valid_gsc = $property === $home && substr($property, 0, 8) === 'https://';
        $ga_property = isset($settings['property_id']) ? (string) $settings['property_id'] : '';
        $stream = isset($settings['view_id']) ? (string) $settings['view_id'] : '';
        $valid_ga = (bool) preg_match('/^[1-9][0-9]{0,19}$/D', $ga_property) && (bool) preg_match('/^[1-9][0-9]{0,19}$/D', $stream);
        $transport = class_exists('RankMath\\Google\\Api') && method_exists('RankMath\\Google\\Api', 'get') && method_exists('RankMath\\Google\\Api', 'http_post') && method_exists('RankMath\\Google\\Api', 'set_workflow') && method_exists('RankMath\\Google\\Api', 'is_success');
        $authorized = $transport && class_exists('RankMath\\Google\\Authentication') && method_exists('RankMath\\Google\\Authentication', 'is_authorized') && \RankMath\Google\Authentication::is_authorized();
        return ['provider'=>'Rank Math', 'transport_available'=>$transport, 'authorized'=>(bool) $authorized, 'gsc_configured'=>$valid_gsc, 'ga4_configured'=>$valid_ga, 'gsc_property'=>$valid_gsc ? $property : null, 'ga4_property_id'=>$valid_ga ? $ga_property : null, 'ga4_stream_id'=>$valid_ga ? $stream : null, 'status'=>!$transport ? 'provider_unavailable' : (!$authorized ? 'needs_connection' : ((!$valid_gsc && !$valid_ga) ? 'needs_property' : 'configured'))];
    }
    private static function persist(string $key, array $value): void {
        update_option($key, $value, false);
        if (get_option($key, null) !== $value) { throw new RuntimeException('storage_failed'); }
    }
    private static function claim(): ?string {
        $token = wp_generate_uuid4(); $lock = ['token'=>$token, 'expires'=>time()+90];
        if (add_option(self::LOCK, $lock, '', false)) { return $token; }
        $old = get_option(self::LOCK, []);
        if (!is_array($old) || (int) ($old['expires'] ?? PHP_INT_MAX) >= time()) { return null; }
        // Compare-and-delete, so a stale lock cannot remove a different worker's fresh lock.
        global $wpdb;
        if (!isset($wpdb) || !$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", self::LOCK, maybe_serialize($old)))) { return null; }
        wp_cache_delete(self::LOCK, 'options');
        return add_option(self::LOCK, $lock, '', false) ? $token : null;
    }
    private static function release(string $token): void {
        $lock = get_option(self::LOCK, []);
        if (is_array($lock) && ($lock['token'] ?? '') === $token) { delete_option(self::LOCK); }
    }
    public static function range(string $period, string $timezone = 'Europe/Warsaw', ?int $now = null): array {
        if (!in_array($period, ['today', '7d', '28d', 'gsc'], true)) { throw new RuntimeException('invalid_period'); }
        $zone = new DateTimeZone($period === 'gsc' ? 'America/Los_Angeles' : $timezone);
        $end = (new DateTimeImmutable('@' . ($now ?? time())))->setTimezone($zone)->setTime(0, 0);
        if ($period === 'gsc') { $end = $end->modify('-3 days'); }
        $days = $period === 'today' ? 1 : ($period === '7d' ? 7 : 28);
        return ['start'=>$end->modify('-' . ($days-1) . ' days')->format('Y-m-d'), 'end'=>$end->format('Y-m-d'), 'timezone'=>$zone->getName(), 'partial'=>$period !== 'gsc'];
    }
    public static function plan(string $mode, array $connection, ?int $now = null): array {
        if (!in_array($mode, ['full','hourly'], true)) { throw new RuntimeException('invalid_mode'); }
        $tasks = [];
        $data = get_option(self::DATA, []); $tz = is_array($data) && !empty($data['ga4_timezone']) ? $data['ga4_timezone'] : wp_timezone()->getName();
        try { new DateTimeZone($tz); } catch (Throwable $e) { $tz = wp_timezone()->getName(); }
        $add = static function (string $id, string $kind, string $period) use (&$tasks, $tz, $now) { $tasks[] = ['id'=>$id,'kind'=>$kind,'period'=>$period,'range'=>self::range($period, $tz, $now),'attempt'=>0,'not_before'=>0]; };
        if (!empty($connection['ga4_configured'])) { $add('ga4.today','ga4_total','today'); $add('ga4.sources.today','ga4_sources','today'); }
        if ($mode === 'full') {
            if (!empty($connection['gsc_configured'])) {
                foreach (['total','daily','queries','pages','positions'] as $part) { $add('gsc.'.$part, 'gsc_'.$part, 'gsc'); }
                $tasks[count($tasks)-1]['range']['start'] = $tasks[count($tasks)-1]['range']['end'];
            }
            if (!empty($connection['ga4_configured'])) {
                foreach (['7d','28d'] as $period) { $add('ga4.'.$period, 'ga4_total', $period); }
                $add('ga4.daily','ga4_daily','28d');
                foreach (['7d','28d'] as $period) { $add('ga4.sources.'.$period, 'ga4_sources', $period); }
            }
        }
        return $tasks;
    }
    public static function request_refresh(string $mode = 'full'): string {
        if (!self::allowed()) { return 'forbidden'; }
        return self::enqueue($mode);
    }
    private static function enqueue(string $mode): string {
        $connection = self::connection();
        if (empty($connection['authorized'])) { return $connection['status']; }
        $tasks = self::plan($mode, $connection);
        if (!$tasks) { return 'needs_property'; }
        $lock = self::claim(); if ($lock === null) { return 'busy'; }
        try {
            $old = get_option(self::QUEUE, []);
            if (is_array($old) && !empty($old['tasks']) && (int) ($old['started_at'] ?? 0) > time()-1800) {
                if ($mode === 'full' && ($old['mode'] ?? '') === 'hourly') { $old['next_full'] = true; self::persist(self::QUEUE, $old); }
                self::schedule_worker(); return 'queued';
            }
            $queue = ['run'=>wp_generate_uuid4(),'mode'=>$mode,'status'=>'queued','started_at'=>time(),'updated_at'=>time(),'tasks'=>$tasks,'completed'=>0,'total'=>count($tasks),'errors'=>[],'next_full'=>false];
            self::persist(self::QUEUE, $queue); self::schedule_worker(); return 'queued';
        } catch (Throwable $e) { return 'storage_failed'; }
        finally { self::release($lock); }
    }
    private static function schedule_worker(int $at = 0): void {
        $at = max(time()+1, $at);
        if (!wp_next_scheduled(self::WORKER)) { wp_schedule_single_event($at, self::WORKER); }
    }
    public static function schedules(): void {
        if (!self::allowed()) { return; }
        if (!wp_next_scheduled(self::DAILY)) { self::next_daily(); }
        if (!wp_next_scheduled(self::HOURLY)) { wp_schedule_event(time()+HOUR_IN_SECONDS, 'hourly', self::HOURLY); }
    }
    private static function next_daily(): void {
        $now = new DateTimeImmutable('now', wp_timezone()); $next = $now->setTime(3,5);
        if ($next <= $now) { $next = $next->modify('+1 day'); }
        wp_schedule_single_event($next->getTimestamp(), self::DAILY);
    }
    public static function daily(): void { self::next_daily(); self::enqueue('full'); }
    public static function hourly(): void { self::enqueue('hourly'); }
    public static function no_debug_log(): bool { return false; }

    /** Only two fixed Google read-report routes, never a caller-controlled URL/body transport. */
    public static function request_spec(array $task, array $connection): array {
        $range = $task['range'] ?? [];
        if (!isset($range['start'],$range['end']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $range['start']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $range['end']) || $range['start'] > $range['end']) { throw new RuntimeException('invalid_dates'); }
        $start = new DateTimeImmutable($range['start']); $end = new DateTimeImmutable($range['end']);
        if ($start->format('Y-m-d') !== $range['start'] || $end->format('Y-m-d') !== $range['end'] || $start->diff($end)->days > 30) { throw new RuntimeException('invalid_dates'); }
        $kind = $task['kind'] ?? '';
        if (in_array($kind, ['ga4_total','ga4_daily','ga4_sources'], true) && !empty($connection['ga4_configured']) && preg_match('/^[1-9][0-9]{0,19}$/D', (string) ($connection['ga4_property_id'] ?? '')) && preg_match('/^[1-9][0-9]{0,19}$/D', (string) ($connection['ga4_stream_id'] ?? ''))) {
            $dimensions = $kind === 'ga4_daily' ? [['name'=>'date']] : ($kind === 'ga4_sources' ? [['name'=>'sessionSourceMedium'],['name'=>'sessionDefaultChannelGroup']] : []);
            $body = ['dateRanges'=>[['startDate'=>$range['start'],'endDate'=>$range['end']]],'metrics'=>array_map(static function ($name) { return ['name'=>$name]; }, self::METRICS),'dimensionFilter'=>['filter'=>['fieldName'=>'streamId','stringFilter'=>['matchType'=>'EXACT','value'=>$connection['ga4_stream_id']]]],'limit'=>$kind === 'ga4_sources' ? 1000 : 100];
            if ($dimensions) { $body['dimensions'] = $dimensions; }
            return ['url'=>'https://analyticsdata.googleapis.com/v1beta/properties/'.$connection['ga4_property_id'].':runReport','body'=>$body];
        }
        if (in_array($kind, ['gsc_total','gsc_daily','gsc_queries','gsc_pages','gsc_positions'], true) && !empty($connection['gsc_configured']) && ($connection['gsc_property'] ?? '') === trailingslashit(home_url('/')) && substr($connection['gsc_property'],0,8) === 'https://') {
            $dimension = ['gsc_daily'=>'date','gsc_queries'=>'query','gsc_pages'=>'page','gsc_positions'=>'query'];
            $body = ['startDate'=>$range['start'],'endDate'=>$range['end'],'type'=>'web','dataState'=>'final','rowLimit'=>$kind==='gsc_positions' ? 5000 : (in_array($kind,['gsc_queries','gsc_pages'],true) ? 1000 : 100)];
            if ($kind === 'gsc_positions' && $range['start'] !== $range['end']) { throw new RuntimeException('invalid_dates'); }
            if (isset($dimension[$kind])) { $body['dimensions'] = [$dimension[$kind]]; }
            return ['url'=>'https://www.googleapis.com/webmasters/v3/sites/'.rawurlencode($connection['gsc_property']).'/searchAnalytics/query','body'=>$body];
        }
        throw new RuntimeException('invalid_report');
    }
    private static function count_value($value): int {
        if (is_string($value) && !preg_match('/^[0-9]{1,15}$/D', $value)) { throw new RuntimeException('invalid_response'); }
        if ((!is_numeric($value) || !is_scalar($value)) || (float) $value < 0 || (float) $value > 999999999999999 || floor((float) $value) !== (float) $value) { throw new RuntimeException('invalid_response'); }
        return (int) $value;
    }
    private static function decimal($value, float $max): float {
        if (!is_numeric($value) || !is_finite((float) $value) || (float) $value < 0 || (float) $value > $max) { throw new RuntimeException('invalid_response'); }
        return (float) $value;
    }
    private static function text($value, int $max = 500): string {
        if (!is_string($value) || strlen($value) > $max || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $value)) { throw new RuntimeException('invalid_response'); }
        return $value;
    }
    private static function metadata(array $meta): array {
        foreach(['subjectToThresholding','dataLossFromOtherRow']as$key){if(array_key_exists($key,$meta)&&!is_bool($meta[$key])){throw new RuntimeException('invalid_response');}}
        foreach(['samplingMetadatas','dataTruncationReasons','schemaRestrictionResponse']as$key){if(array_key_exists($key,$meta)&&!is_array($meta[$key])){throw new RuntimeException('invalid_response');}}
        $empty=array_key_exists('emptyReason',$meta)?self::text($meta['emptyReason'],200):null;
        return ['thresholded'=>!empty($meta['subjectToThresholding']),'sampled'=>!empty($meta['samplingMetadatas']),'other_row_loss'=>!empty($meta['dataLossFromOtherRow']),'truncated'=>!empty($meta['dataTruncationReasons']),'schema_restricted'=>!empty($meta['schemaRestrictionResponse']['activeMetricRestrictions']),'empty_reason'=>$empty];
    }
    public static function parse(array $task, array $response): array {
        if (isset($response['error'])) { throw new RuntimeException('api_error'); }
        $kind = $task['kind']; $range = $task['range'];
        $result = ['status'=>'ready','start'=>$range['start'],'end'=>$range['end'],'partial'=>$range['partial'],'timezone'=>$range['timezone'],'fetched_at'=>time(),'rows'=>[],'limited'=>false];
        if (substr($kind,0,4) === 'ga4_') {
            // GA4 can return only metadata before aggregate data is available. No measured zero is implied.
            if($kind==='ga4_total'&&!array_diff(array_keys($response),['metadata','kind','propertyQuota'])&&is_array($response['metadata']??null)){
                $meta=$response['metadata'];$tz=self::text($meta['timeZone']??null,80);
                if(!in_array($tz,DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC),true)||(array_key_exists('kind',$response)&&$response['kind']!=='analyticsData#runReport')||(array_key_exists('propertyQuota',$response)&&!is_array($response['propertyQuota']))){throw new RuntimeException('invalid_response');}
                $result['metadata']=self::metadata($meta);
                $result['limited']=$result['metadata']['thresholded']||$result['metadata']['sampled']||$result['metadata']['other_row_loss']||$result['metadata']['truncated']||$result['metadata']['schema_restricted']||!empty($result['metadata']['empty_reason']);
                $result['status']=$result['limited']?'limited':'awaiting_data';$result['timezone']=$tz;$result['no_data_envelope']=true;$result['metrics']=array_fill_keys(self::METRICS,null);unset($result['rows']);return $result;
            }
            $headers = $response['metricHeaders'] ?? null; $dim_headers = $response['dimensionHeaders'] ?? [];
            if (!is_array($headers) || !is_array($dim_headers)) { throw new RuntimeException('invalid_response'); }
            $metrics = array_map(static function ($h) { return is_array($h) && isset($h['name']) ? self::text($h['name'],80) : ''; }, $headers);
            if (count($metrics) !== count(self::METRICS) || count(array_unique($metrics)) !== count($metrics) || array_diff(self::METRICS,$metrics)) { throw new RuntimeException('invalid_response'); }
            $dims = array_map(static function ($h) { return is_array($h) && isset($h['name']) ? self::text($h['name'],80) : ''; }, $dim_headers);
            $expected = $kind === 'ga4_daily' ? ['date'] : ($kind === 'ga4_sources' ? ['sessionSourceMedium','sessionDefaultChannelGroup'] : []);
            if (count($dims) !== count($expected) || array_diff($expected,$dims) || count(array_unique($dims)) !== count($dims)) { throw new RuntimeException('invalid_response'); }
            $meta = $response['metadata'] ?? []; if (!is_array($meta)) { throw new RuntimeException('invalid_response'); }
            $result['metadata'] = self::metadata($meta);
            if (isset($meta['timeZone'])) { $tz=self::text($meta['timeZone'],80); try { new DateTimeZone($tz); } catch(Throwable $e) { throw new RuntimeException('invalid_response'); } $result['timezone']=$tz; }
            $rows=$response['rows']??[];if(!is_array($rows)||count($rows)>1000||($kind==='ga4_total'&&count($rows)>1)){throw new RuntimeException('invalid_response');}
            $seen=[];
            foreach($rows as $row){
                if(!is_array($row)||!isset($row['metricValues'])||!is_array($row['metricValues'])||count($row['metricValues'])!==count($metrics)){throw new RuntimeException('invalid_response');}
                $item=[];foreach($metrics as $i=>$name){$item[$name]=self::count_value($row['metricValues'][$i]['value']??null);}
                $values=$row['dimensionValues']??[];if(!is_array($values)||count($values)!==count($dims)){throw new RuntimeException('invalid_response');}
                foreach($dims as $i=>$name){$item[$name]=self::text($values[$i]['value']??null,500);}
                if($kind==='ga4_daily'){$date=$item['date'];if(!preg_match('/^\d{8}$/D',$date)){throw new RuntimeException('invalid_response');}$item['date']=substr($date,0,4).'-'.substr($date,4,2).'-'.substr($date,6,2);$parsed=DateTimeImmutable::createFromFormat('!Y-m-d',$item['date']);if(!$parsed||$parsed->format('Y-m-d')!==$item['date']||$item['date']<$range['start']||$item['date']>$range['end']||isset($seen[$date])){throw new RuntimeException('invalid_response');}$seen[$date]=true;}
                if($kind==='ga4_sources'){$source=$item['sessionSourceMedium'];$key=$source.'|'.$item['sessionDefaultChannelGroup'];if(isset($seen[$key])){throw new RuntimeException('invalid_response');}$seen[$key]=true;$split=strrpos($source,' / ');$item['source']=$split===false?$source:substr($source,0,$split);$item['medium']=$split===false?'(not set)':substr($source,$split+3);$item['channel']=$item['sessionDefaultChannelGroup'];unset($item['sessionSourceMedium'],$item['sessionDefaultChannelGroup']);}
                $result['rows'][]=$item;
            }
            $result['limited']=$result['metadata']['thresholded']||$result['metadata']['sampled']||$result['metadata']['other_row_loss']||$result['metadata']['truncated']||$result['metadata']['schema_restricted']||!empty($result['metadata']['empty_reason'])||(int)($response['rowCount']??count($rows))>count($rows);
            if($kind==='ga4_total'){$result['metrics']=$result['rows'][0]??array_fill_keys(self::METRICS,($result['limited']||$result['metadata']['empty_reason'])?null:0);unset($result['rows']);}
            if($result['limited']){$result['status']='limited';}
            return $result;
        }
        if(!in_array($kind,['gsc_total','gsc_daily','gsc_queries','gsc_pages','gsc_positions'],true)){throw new RuntimeException('invalid_report');}
        $rows=$response['rows']??[];if(!is_array($rows)||count($rows)>($kind==='gsc_positions'?5000:1000)||($kind==='gsc_total'&&count($rows)>1)){throw new RuntimeException('invalid_response');}
        $dimension=['gsc_daily'=>'date','gsc_queries'=>'query','gsc_pages'=>'page','gsc_positions'=>'query'];$seen=[];
        foreach($rows as $row){
            if(!is_array($row)){throw new RuntimeException('invalid_response');}
            $item=['clicks'=>self::count_value($row['clicks']??null),'impressions'=>self::count_value($row['impressions']??null),'ctr'=>self::decimal($row['ctr']??null,1),'position'=>self::decimal($row['position']??null,100000)];
            if(isset($dimension[$kind])){$keys=$row['keys']??null;if(!is_array($keys)||count($keys)!==1){throw new RuntimeException('invalid_response');}$name=$dimension[$kind];$item[$name]=self::text($keys[0],$name==='page'?2048:500);if(isset($seen[$item[$name]])){throw new RuntimeException('invalid_response');}$seen[$item[$name]]=true;if($name==='date'&&(!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$item[$name])||$item[$name]<$range['start']||$item[$name]>$range['end'])){throw new RuntimeException('invalid_response');}}
            if($kind==='gsc_positions'){$item['date']=$range['end'];}
            $result['rows'][]=$item;
        }
        $result['limited']=in_array($kind,['gsc_queries','gsc_pages','gsc_positions'],true)&&count($rows)>=($kind==='gsc_positions'?5000:1000);
        if($kind==='gsc_total'){$result['metrics']=$result['rows'][0]??['clicks'=>0,'impressions'=>0,'ctr'=>0.0,'position'=>null];unset($result['rows']);}
        return $result;
    }
    /** Structural diagnostics only: no response values, credentials, URLs or property identifiers. */
    private static function response_shape(array $response, int $line): array {
        $type=static function(array $container,string $key): string {return array_key_exists($key,$container)?gettype($container[$key]):'missing';};
        $headers=static function($value,array $allowed): array {$names=[];if(is_array($value)){foreach(array_slice($value,0,8)as$header){$name=is_array($header)?($header['name']??null):null;$names[]=is_string($name)&&in_array($name,$allowed,true)?$name:'unexpected';}}return $names;};
        $values=static function($value): array {$types=[];if(is_array($value)){foreach(array_slice($value,0,8)as$entry){$types[]=is_array($entry)?(array_key_exists('value',$entry)?gettype($entry['value']):'missing'):gettype($entry);}}return $types;};
        $rows=$response['rows']??null;$first=is_array($rows)&&$rows?reset($rows):null;$first=is_array($first)?$first:[];$meta=$response['metadata']??null;
        $shape=['origin_line'=>max(1,min(999999,$line)),'metric_headers_type'=>$type($response,'metricHeaders'),'metric_headers_count'=>is_array($response['metricHeaders']??null)?count($response['metricHeaders']):null,'metric_header_names'=>$headers($response['metricHeaders']??null,self::METRICS),'dimension_headers_type'=>$type($response,'dimensionHeaders'),'dimension_headers_count'=>is_array($response['dimensionHeaders']??null)?count($response['dimensionHeaders']):null,'dimension_header_names'=>$headers($response['dimensionHeaders']??null,['date','sessionSourceMedium','sessionDefaultChannelGroup']),'rows_type'=>$type($response,'rows'),'rows_count'=>is_array($rows)?count($rows):null,'first_metric_values_type'=>$type($first,'metricValues'),'first_metric_values_count'=>is_array($first['metricValues']??null)?count($first['metricValues']):null,'first_metric_value_types'=>$values($first['metricValues']??null),'first_dimension_values_type'=>$type($first,'dimensionValues'),'first_dimension_values_count'=>is_array($first['dimensionValues']??null)?count($first['dimensionValues']):null,'first_dimension_value_types'=>$values($first['dimensionValues']??null),'metadata_type'=>$type($response,'metadata'),'metadata_field_types'=>[],'row_count_type'=>$type($response,'rowCount')];
        if(is_array($meta)){foreach(['timeZone','emptyReason','subjectToThresholding','samplingMetadatas','dataLossFromOtherRow','dataTruncationReasons','schemaRestrictionResponse']as$key){if(array_key_exists($key,$meta)){$shape['metadata_field_types'][$key]=gettype($meta[$key]);}}}
        $shape['report_kind']=array_key_exists('kind',$response)?($response['kind']==='analyticsData#runReport'?'analyticsData#runReport':'unexpected'):'missing';
        return $shape;
    }
    private static function fetch(array $task, array $connection): array {
        self::$last_response_shape=null;
        $spec=self::request_spec($task,$connection);$api=\RankMath\Google\Api::get();
        add_filter('rank_math/analytics/log_response',[self::class,'no_debug_log'],PHP_INT_MAX);
        try {
            $api->set_workflow('');$response=$api->http_post($spec['url'],$spec['body'],8);
            if(!$api->is_success()||!is_array($response)||isset($response['error'])){$code=is_array($response)&&isset($response['error']['code'])?(int)$response['error']['code']:0;throw new RuntimeException(in_array($code,[401,403,429,500,502,503,504],true)?'google_'.$code:'transport_error');}
            try{return self::parse($task,$response);}catch(Throwable $e){if($e->getMessage()==='invalid_response'){self::$last_response_shape=self::response_shape($response,$e->getLine());}throw $e;}
        } finally {$api->set_workflow('');remove_filter('rank_math/analytics/log_response',[self::class,'no_debug_log'],PHP_INT_MAX);}
    }
    public static function worker(): void {
        $lock=self::claim();if($lock===null){self::schedule_worker(time()+30);return;}$next_full=false;
        try {
            $q=get_option(self::QUEUE,[]);if(!is_array($q)||empty($q['tasks'])){return;}
            $connection=self::connection();if(empty($connection['authorized'])){$q['status']=$connection['status'];$q['tasks']=[];self::persist(self::QUEUE,$q);return;}
            $q['status']='running';self::persist(self::QUEUE,$q);$start=microtime(true);$processed=0;
            // Small background batches; admin render never blocks on Google requests.
            while(!empty($q['tasks'])&&$processed<3&&microtime(true)-$start<12){
                $eligible=null;foreach($q['tasks'] as $i=>$task){if((int)$task['not_before']<=time()){$eligible=$i;break;}}if($eligible===null){break;}
                $task=$q['tasks'][$eligible];array_splice($q['tasks'],$eligible,1);$processed++;
                try {
                    $report=self::fetch($task,$connection);$data=get_option(self::DATA,[]);$data=is_array($data)?$data:[];
                    // Learn the actual GA4 timezone before subsequent reports are requested.
                    if(substr($task['kind'],0,4)==='ga4_'&&$report['timezone']!==$task['range']['timezone']){
                        $data['ga4_timezone']=$report['timezone'];self::persist(self::DATA,$data);$task['range']=self::range($task['period'],$report['timezone']);$task['not_before']=0;$q['tasks'][]=$task;
                        foreach($q['tasks'] as &$pending){if(substr($pending['kind'],0,4)==='ga4_'){$pending['range']=self::range($pending['period'],$report['timezone']);}}unset($pending);continue;
                    }
                    $report['property']=substr($task['kind'],0,4)==='ga4_'?$connection['ga4_property_id']:$connection['gsc_property'];
                    $report['stream']=substr($task['kind'],0,4)==='ga4_'?$connection['ga4_stream_id']:null;
                    $data['reports'][$task['id']]=$report;$data['updated_at']=time();if(substr($task['kind'],0,4)==='ga4_'){$data['ga4_timezone']=$report['timezone'];}
                    if($task['kind']==='gsc_positions'){
                        if(($data['position_history_property']??null)!==$connection['gsc_property']){$data['position_history']=[];}
                        $data['position_history_property']=$connection['gsc_property'];
                        $data['position_history'][$report['end']]=$report['rows'];ksort($data['position_history']);
                        $cutoff=(new DateTimeImmutable($report['end'],new DateTimeZone('America/Los_Angeles')))->modify('-59 days')->format('Y-m-d');
                        foreach(array_keys($data['position_history'])as$day){if($day<$cutoff){unset($data['position_history'][$day]);}}
                        $data['history_limited']=false;
                        while(count($data['position_history'])>60||array_sum(array_map('count',$data['position_history']))>30000){array_shift($data['position_history']);$data['history_limited']=true;}
                    }
                    self::persist(self::DATA,$data);
                    unset($q['errors'][$task['id']]);$q['completed']++;
                } catch(Throwable $e) {
                    $safe=in_array($e->getMessage(),['google_401','google_403','google_429','google_500','google_502','google_503','google_504','transport_error','invalid_response','invalid_report','invalid_dates','storage_failed'],true)?$e->getMessage():'report_failed';
                    $q['errors'][$task['id']]=['code'=>$safe,'at'=>time()];
                    if($safe==='invalid_response'&&self::$last_response_shape!==null){$q['errors'][$task['id']]['shape']=self::$last_response_shape;}
                    if(in_array($safe,['google_429','google_500','google_502','google_503','google_504','transport_error'],true)&&$task['attempt']<2){$task['attempt']++;$task['not_before']=time()+($task['attempt']===1?60:300);$q['tasks'][]=$task;}
                }
                $q['updated_at']=time();self::persist(self::QUEUE,$q);
            }
            if($q['tasks']){$q['status']='queued';$times=array_column($q['tasks'],'not_before');self::schedule_worker(max(time()+1,min($times)));}
            else{$q['status']=$q['errors']?'partial_error':'ready';$q['finished_at']=time();$next_full=!empty($q['next_full']);}
            self::persist(self::QUEUE,$q);
        } catch(Throwable $e) { self::schedule_worker(time()+60); /* Last-good data remains; no raw exception or credential is logged. */ }
        finally{self::release($lock);}
        if($next_full){self::enqueue('full');}
    }
    private static function report(array $reports,string $id,array $connection): ?array {
        $r=$reports[$id]??null;if(!is_array($r)){return null;}
        $ga=substr($id,0,4)==='ga4.';
        if(($r['property']??null)!==($ga?$connection['ga4_property_id']:$connection['gsc_property'])||($ga&&($r['stream']??null)!==$connection['ga4_stream_id'])){return null;}
        $q=get_option(self::QUEUE,[]);$error=is_array($q)?($q['errors'][$id]??null):null;$reasons=[];
        if(is_array($error)&&((int)($error['at']??0)>=(int)($r['fetched_at']??0))){$reasons[]='refresh_error';}
        if(!$connection['authorized']){$reasons[]='connection_unavailable';}
        $period=$ga?($id==='ga4.daily'?'28d':substr($id,strrpos($id,'.')+1)):'gsc';$tz=$ga?($r['timezone']??wp_timezone()->getName()):'America/Los_Angeles';$expected=self::range($period,$tz);
        if($id==='gsc.positions'){$expected['start']=$expected['end'];}
        $r['range_matches_expected']=($r['start']??null)===$expected['start']&&($r['end']??null)===$expected['end'];$r['expected_start']=$expected['start'];$r['expected_end']=$expected['end'];
        if(!$r['range_matches_expected']){$reasons[]='range_mismatch';}
        $r['freshness_limit_seconds']=$ga&&$period==='today'?2*HOUR_IN_SECONDS:36*HOUR_IN_SECONDS;$fetched=(int)($r['fetched_at']??0);$r['age_seconds']=$fetched>0?max(0,time()-$fetched):null;
        if(!$fetched){$reasons[]='missing_fetch_time';}elseif($fetched>time()+300){$reasons[]='clock_mismatch';}elseif($r['age_seconds']>$r['freshness_limit_seconds']){$reasons[]='age_exceeded';}
        $r['stale_reasons']=$reasons;$r['stale']=(bool)$reasons;
        return $r;
    }
    private static function source_health(?array $source,?array $total): array {
        $displayable=$source!==null&&in_array($source['status']??'', ['ready','limited'],true)&&is_array($source['rows']??null);$reasons=[];
        if(!$displayable){$reasons[]='source_not_ready';}if(!empty($source['limited'])){$reasons[]='source_limited';}if(!empty($source['stale'])){$reasons[]='source_stale';}
        if(($total['status']??'')!=='ready'){$reasons[]='total_not_ready';}if(!empty($total['limited'])){$reasons[]='total_limited';}if(!empty($total['stale'])){$reasons[]='total_stale';}
        $aligned=$source!==null&&$total!==null&&($source['start']??null)===($total['start']??null)&&($source['end']??null)===($total['end']??null)&&($source['timezone']??null)===($total['timezone']??null);
        if(!$aligned){$reasons[]='period_mismatch';}
        return ['displayable'=>$displayable,'aligned_with_total'=>$aligned,'totals_usable'=>$displayable&&($source['status']??'')==='ready'&&!$reasons,'reasons'=>$reasons];
    }
    private static function cron_health(array $q,array $scheduled,array $connection): array {
        $now=time();$issues=[];$pending=!empty($q['tasks']);$lock=get_option(self::LOCK,[]);$active=is_array($lock)&&(int)($lock['expires']??0)>$now;$started=(int)($q['started_at']??0);$age=$pending&&$started>0?max(0,$now-$started):null;
        if(($q['status']??'')==='disabled'){return ['status'=>'disabled','issues'=>[],'queue_age_seconds'=>$age,'worker_active'=>$active,'checked_at'=>$now];}
        if(!empty($connection['ga4_configured'])||!empty($connection['gsc_configured'])){foreach(['daily','hourly']as$kind){if($kind==='hourly'&&empty($connection['ga4_configured'])){continue;}$at=$scheduled['next_'.$kind.'_at'];if($at===null){$issues[]=$kind.'_missing';}elseif($at<$now-300){$issues[]=$kind.'_overdue';}}}
        if($pending&&!$active){if($scheduled['next_worker_at']===null){$issues[]='worker_missing';}elseif($scheduled['next_worker_at']<$now-120){$issues[]='worker_overdue';}}
        $updated=(int)($q['updated_at']??$started);if($pending&&!$active&&$updated>0&&$updated<$now-900){$issues[]='queue_stalled';}
        return ['status'=>$issues?'warning':'healthy','issues'=>$issues,'queue_age_seconds'=>$age,'worker_active'=>$active,'checked_at'=>$now];
    }
    private static function opportunities(?array $report): array {
        $thresholds=['minimum_impressions'=>50,'maximum_ctr'=>0.02,'minimum_position'=>4.0,'maximum_position'=>20.0,'maximum_results'=>10];$out=['status'=>$report['status']??'unavailable','rows'=>[],'thresholds'=>$thresholds,'period_start'=>$report['start']??null,'as_of'=>$report['end']??null,'fetched_at'=>$report['fetched_at']??null,'limited'=>!empty($report['limited']),'stale'=>!empty($report['stale']),'candidate_count'=>0];
        if(!$report||!in_array($report['status']??'',['ready','limited'],true)){return $out;}
        $host=parse_url(home_url('/'),PHP_URL_HOST);
        foreach($report['rows']??[]as$row){$url=is_array($row)?($row['page']??''):'';$parts=is_string($url)?parse_url($url):false;if(!is_array($parts)||($parts['scheme']??'')!=='https'||($parts['host']??'')!==$host||isset($parts['user'])||isset($parts['pass'])||isset($parts['port'])){continue;}if(($row['impressions']??0)<50||!isset($row['ctr'],$row['position'])||$row['ctr']>0.02||$row['position']<4||$row['position']>20){continue;}$row['reason']='high_impressions_low_ctr';$out['rows'][]=$row;}
        usort($out['rows'],static function($a,$b){return $b['impressions']<=>$a['impressions']?:strcmp($a['page'],$b['page']);});$out['candidate_count']=count($out['rows']);$out['rows']=array_slice($out['rows'],0,10);return $out;
    }
    private static function ai_rows(array $rows): array {
        $known=[];foreach($rows as $row){$host=strtolower(trim($row['source']??''));$host=preg_replace('~^https?://~','',$host);$host=explode('/',$host)[0];foreach(self::AI as $domain=>$label){if($host===$domain||(strpos($domain,'.')!==false&&substr($host,-strlen('.'.$domain))==='.'. $domain)){$known[]=['source'=>$row['source'],'label'=>$label,'medium'=>$row['medium'],'channel'=>$row['channel'],'sessions'=>$row['sessions']];break;}}}return $known;
    }
    public static function data(): array {
        if(!self::allowed()){return ['connection'=>['status'=>'forbidden'],'ga4'=>[],'gsc'=>[],'sync'=>['status'=>'forbidden','queued'=>false,'updated_at'=>null]];}
        $connection=self::connection();$stored=get_option(self::DATA,[]);$stored=is_array($stored)?$stored:[];$reports=(array)($stored['reports']??[]);$q=get_option(self::QUEUE,[]);$q=is_array($q)?$q:[];
        $ga=['periods'=>[],'daily'=>[],'sourcesByPeriod'=>[],'aiByPeriod'=>[],'sources'=>[],'ai'=>[],'timezone'=>$stored['ga4_timezone']??null,'status'=>'awaiting_sync'];
        foreach(['today','7d','28d']as$period){$range=self::range($period,$ga['timezone']?:wp_timezone()->getName());$total=self::report($reports,'ga4.'.$period,$connection);if($period==='today'&&$total&&$total['end']!==$range['end']){$total=null;}
            $ga['periods'][$period]=$total?array_merge($total,$total['metrics']):array_merge($range,['status'=>'unavailable','fetched_at'=>null,'metrics'=>array_fill_keys(self::METRICS,null)],array_fill_keys(self::METRICS,null));
            $source=self::report($reports,'ga4.sources.'.$period,$connection);if($period==='today'&&$source&&$source['end']!==$range['end']){$source=null;}$ga['sourcesByPeriod'][$period]=$source['rows']??[];$ga['aiByPeriod'][$period]=self::ai_rows($ga['sourcesByPeriod'][$period]);$ga['sourceReports'][$period]=$source??['status'=>'unavailable','fetched_at'=>null];$ga['sourceHealth'][$period]=self::source_health($source,$total);}
        $daily=self::report($reports,'ga4.daily',$connection);$ga['daily']=$daily['rows']??[];$ga['dailyReport']=$daily??['status'=>'unavailable','fetched_at'=>null];$ga['sources']=$ga['sourcesByPeriod']['today'];$ga['ai']=$ga['aiByPeriod']['today'];$ga['status']=$ga['periods']['today']['status'];
        $gsc=['property'=>$connection['gsc_property'],'status'=>'awaiting_sync','period'=>'28d','data_through'=>null,'daily'=>[],'queries'=>[],'pages'=>[]];
        $total=self::report($reports,'gsc.total',$connection);$range=self::range('gsc');$gsc=array_merge($gsc,$range);$gsc['totals']=$total['metrics']??['clicks'=>null,'impressions'=>null,'ctr'=>null,'position'=>null];$gsc['totalReport']=$total??['status'=>'unavailable','fetched_at'=>null];
        foreach(['daily','queries','pages','positions']as$part){$r=self::report($reports,'gsc.'.$part,$connection);$gsc[$part]=$r['rows']??[];$gsc[$part.'Report']=$r??['status'=>'unavailable','fetched_at'=>null];}
        $gsc['positionHistory']=($stored['position_history_property']??null)===$connection['gsc_property']?(array)($stored['position_history']??[]):[];$gsc['positionHistoryLimited']=!empty($stored['history_limited']);
        if($total){$gsc['start']=$total['start'];$gsc['end']=$total['end'];$gsc['status']=$total['status'];}
        if($gsc['daily']){$gsc['data_through']=max(array_column($gsc['daily'],'date'));}
        $gsc['opportunities']=self::opportunities(self::report($reports,'gsc.pages',$connection));
        $errors=(array)($q['errors']??[]);$last=$errors?end($errors):null;$last_id=$errors?key($errors):null;$last_id=in_array($last_id,self::REPORT_IDS,true)?$last_id:null;
        $scheduled=[];foreach(['next_daily_at'=>self::DAILY,'next_hourly_at'=>self::HOURLY,'next_worker_at'=>self::WORKER]as$key=>$hook){$at=wp_next_scheduled($hook);$scheduled[$key]=$at!==false&&(int)$at>0?(int)$at:null;}
        $health=self::cron_health($q,$scheduled,$connection);
        return ['connection'=>$connection,'ga4'=>$ga,'gsc'=>$gsc,'sync'=>array_merge(['status'=>$q['status']??'not_synced','updated_at'=>$stored['updated_at']??null,'queued'=>!empty($q['tasks']),'completed'=>(int)($q['completed']??0),'total'=>(int)($q['total']??0),'last_safe_error'=>is_array($last)?($last['code']??null):null,'last_error_report_id'=>$last_id,'last_response_shape'=>$last_id&&is_array($last)?($last['shape']??null):null,'health'=>$health,'cron_disabled'=>defined('DISABLE_WP_CRON')&&DISABLE_WP_CRON,'daily_at'=>'03:05 '.wp_timezone()->getName(),'today_refresh'=>'hourly','cron_note'=>'WP-Cron wykonuje zadania po kolejnym uruchomieniu WordPressa; cache i brak ruchu mogą opóźnić harmonogram.'],$scheduled)];
    }
    public static function refresh_action(): void {
        if(!self::allowed()||($_SERVER['REQUEST_METHOD']??'')!=='POST'){wp_die('Brak uprawnień.',403);}check_admin_referer('zp_admin_stats_refresh_20261009');
        $result=self::request_refresh('full');if($result==='queued'&&function_exists('spawn_cron')){spawn_cron();}
        wp_safe_redirect(add_query_arg(['page'=>'zp-suite-stats','zp_stats'=>$result],admin_url('admin.php')));exit;
    }
    public static function status_ajax(): void {
        if(!self::allowed()){wp_send_json_error(['code'=>'forbidden'],403);}check_ajax_referer('zp_admin_stats_status');
        $data=self::data();$sync=$data['sync'];if(!empty($sync['queued'])&&function_exists('spawn_cron')){spawn_cron();}
        wp_send_json_success(['status'=>$sync['status'],'updated_at'=>$sync['updated_at'],'queued'=>$sync['queued'],'ready'=>!$sync['queued']&&$sync['status']==='ready','completed'=>$sync['completed'],'total'=>$sync['total'],'last_safe_error'=>$sync['last_safe_error']]);
    }
    public static function refresh_form(): void {
        if(!self::allowed()){return;}echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'" class="zpSuiteStatsRefresh"><input type="hidden" name="action" value="zp_admin_stats_refresh_20261009">';wp_nonce_field('zp_admin_stats_refresh_20261009');echo '<button type="submit" class="button button-primary">Odśwież dane Google w tle</button></form>';
    }
    public static function keyword(string $phrase): array {
        $out=['matched'=>false,'query'=>null,'position'=>null,'clicks'=>null,'impressions'=>null,'as_of'=>null,'date'=>null,'period'=>'day','period_start'=>null,'property'=>null,'status'=>'unavailable','history'=>[]];if(!self::allowed()){return $out;}
        $data=self::data();$g=$data['gsc'];$day=isset($g['positionsReport']['end']);$report=$day?$g['positionsReport']:$g['queriesReport'];$rows=$day?$g['positions']:$g['queries'];
        $out['as_of']=$report['end']??null;$out['date']=$day?$out['as_of']:null;$out['period']=$day?'day':'28_days';$out['period_start']=$report['start']??null;$out['property']=$g['property'];$out['status']=$report['status']??'unavailable';
        $normalize=static function($s){$s=preg_replace('/\s+/u',' ',trim($s));return function_exists('mb_strtolower')?mb_strtolower($s,'UTF-8'):strtolower($s);};$needle=$normalize($phrase);
        if($needle===''){return $out;}
        foreach($g['positionHistory']as$date=>$history){foreach($history as$row){if($normalize($row['query'])===$needle){$out['history'][]=['date'=>$date,'position'=>$row['position'],'clicks'=>$row['clicks'],'impressions'=>$row['impressions']];break;}}}
        foreach($rows as$row){if($normalize($row['query'])===$needle){return array_merge($out,['matched'=>true,'query'=>$row['query'],'position'=>$row['position'],'clicks'=>$row['clicks'],'impressions'=>$row['impressions']]);}}return $out;
    }
    public static function deactivate(): void {
        foreach([self::WORKER,self::DAILY,self::HOURLY]as$hook){wp_clear_scheduled_hook($hook);}
        $q=get_option(self::QUEUE,[]);if(is_array($q)&&$q){$q['tasks']=[];$q['status']='disabled';try{self::persist(self::QUEUE,$q);}catch(Throwable $e){/* Do not prevent plugin deactivation on a storage failure. */}}
    }
}
function zp_admin_stats_data(): array { return ZP_Admin_Stats_20261009::data(); }
function zp_admin_stats_request_refresh(): string { return ZP_Admin_Stats_20261009::request_refresh(); }
function zp_admin_stats_refresh_form(): void { ZP_Admin_Stats_20261009::refresh_form(); }
function zp_admin_stats_keyword_position(string $phrase): array { return ZP_Admin_Stats_20261009::keyword($phrase); }
add_action('admin_init',[ZP_Admin_Stats_20261009::class,'schedules']);
add_action(ZP_Admin_Stats_20261009::WORKER,[ZP_Admin_Stats_20261009::class,'worker']);
add_action(ZP_Admin_Stats_20261009::DAILY,[ZP_Admin_Stats_20261009::class,'daily']);
add_action(ZP_Admin_Stats_20261009::HOURLY,[ZP_Admin_Stats_20261009::class,'hourly']);
add_action('admin_post_zp_admin_stats_refresh_20261009',[ZP_Admin_Stats_20261009::class,'refresh_action']);
add_action('wp_ajax_zp_admin_stats_status',[ZP_Admin_Stats_20261009::class,'status_ajax']);
register_deactivation_hook(dirname(__DIR__).'/zaprojektowani-suite.php',[ZP_Admin_Stats_20261009::class,'deactivate']);
