<?php
/** Private bounded reuse of actual Senuto measurements; no provider calls or invented rows. */
if(!defined('ABSPATH'))exit;
final class ZP_Preparation_Research_Cache_20261010 {
    public const DATA='zps_content_prep_auto_20261010_research_cache';
    public static function catalog_hash(array $catalog): string {
        $pages=[];foreach($catalog['pages']??[] as $id=>$p)$pages[(string)$id]=[$p['id'],$p['url'],$p['group']];ksort($pages);
        $sources=[];foreach($catalog['sources']??[] as $id=>$s)$sources[$id]=[$s['url'],$s['group']];ksort($sources);
        $categories=[];foreach($catalog['categories']??[] as $id=>$c)$categories[(string)$id]=[$c['id'],$c['name']];ksort($categories);
        return hash('sha256',wp_json_encode([$catalog['seeds']??[],$pages,$sources,$categories]));
    }
    public static function identity(array $catalog,string $token,int $actor): array {
        if($token===''||$actor<1)throw new RuntimeException('cache_unavailable');
        return ['date'=>wp_date('Y-m-d',time(),new DateTimeZone('Europe/Warsaw')),'actor'=>$actor,'connection_fingerprint'=>hash('sha256',$token),'catalog_hash'=>self::catalog_hash($catalog),'country_id'=>1,'match_mode'=>'narrow','page'=>1,'limit'=>50,'selection_minimum_volume'=>ZP_Content_Preparation_20261010::MINIMUM_VOLUME,'minimum_words'=>2];
    }
    private static function row(array $row,array $seed,array $identity): array {
        $prep=ZP_Content_Preparation_20261010::class;$keyword=$row['keyword']??null;$volume=$row['volume']??null;$at=$row['retrieved_at']??null;
        if(!is_string($keyword)||strlen($keyword)>240||!is_int($volume)||$volume<10||$volume>100000000||!in_array($row['market']??null,['national','katowice'],true)||!is_int($at)||$at<1||wp_date('Y-m-d',$at,new DateTimeZone('Europe/Warsaw'))!==$identity['date']||($row['date']??null)!==$identity['date']||($row['provenance']??null)!=='senuto_api'||($row['source']??null)!=='Senuto API · seed: '.$seed['keyword']||($row['seed']??null)!==$seed['keyword']||($row['group']??null)!==$seed['group']||($row['relevance_checked']??null)!==true||($row['request_match_mode']??null)!=='narrow'||($row['request_country_id']??null)!==1||($row['volume_scope']??null)!=='Polska — baza słów kluczowych PL'||!$prep::keyword_relevant($keyword,$seed['keyword']))throw new RuntimeException('cache_invalid');
        $id=substr(hash('sha256',$prep::normalized($keyword).'|'.$identity['date'].'|Senuto API'),0,24);if(($row['id']??null)!==$id)throw new RuntimeException('cache_invalid');
        if(($row['kid']??null)!==null&&(!is_string($row['kid'])||!preg_match('/^[A-Za-z0-9_-]{1,100}$/D',$row['kid'])))throw new RuntimeException('cache_invalid');
        $keys=['id','keyword','volume','date','source','market','volume_scope','provenance','retrieved_at','kid','cpc','difficulty','seed','relevance_checked','group','request_match_mode','request_country_id','request_minimum_volume'];$out=[];foreach($keys as $key){if(array_key_exists($key,$row))$out[$key]=$row[$key];}return $out;
    }
    public static function read(array $identity,array $catalog): array {
        $data=get_option(self::DATA,[]);if(!is_array($data)||($data['identity']??null)!==$identity||!is_array($data['seeds']??null)||count($data['seeds'])>8)return [];$definitions=[];foreach($catalog['seeds'] as $s)$definitions[$s['id']]=$s;
        try{foreach($data['seeds'] as $id=>$entry){if(!isset($definitions[$id])||!is_array($entry)||!is_array($entry['rows']??null)||count($entry['rows'])>50||($entry['keyword']??null)!==$definitions[$id]['keyword']||($entry['group']??null)!==$definitions[$id]['group']||!is_bool($entry['has_next_page']??null)||!in_array($entry['kind']??'',['api_measurement','legacy_approved_subset'],true))throw new RuntimeException('cache_invalid');foreach($entry['rows'] as $rowId=>$row){if(!is_array($row))throw new RuntimeException('cache_invalid');$clean=self::row($row,$definitions[$id],$identity);if($rowId!==$clean['id'])throw new RuntimeException('cache_invalid');}}}catch(Throwable $ignored){return [];}
        return $data['seeds'];
    }
    private static function save(array $data): void {update_option(self::DATA,$data,false);if(get_option(self::DATA,[])!==$data)throw new RuntimeException('storage_failed');}
    public static function put(array $identity,array $catalog,array $seed,array $result): void {
        if(!is_array($result['rows']??null)||count($result['rows'])>50||!is_bool($result['has_next_page']??null))throw new RuntimeException('cache_invalid');$rows=[];foreach($result['rows'] as $r){if(!is_array($r))throw new RuntimeException('cache_invalid');$clean=self::row($r,$seed,$identity);$rows[$clean['id']]=$clean;}
        $entries=self::read($identity,$catalog);$entries[$seed['id']]=['keyword'=>$seed['keyword'],'group'=>$seed['group'],'rows'=>$rows,'has_next_page'=>$result['has_next_page'],'kind'=>'api_measurement','observed_minimum_volume'=>ZP_Content_Preparation_20261010::MINIMUM_VOLUME,'retrieved_at'=>time(),'saved_at'=>time()];self::save(['identity'=>$identity,'seeds'=>$entries]);
    }
    /** Explicit native approval associates a legacy same-day report with the current connection. */
    public static function approve_legacy(array $state,array $identity,array $catalog): array {
        if(($state['state']??'')!=='complete'||($state['stage']??'')!=='complete'||($state['actor']??null)!==$identity['actor']||!is_array($state['catalog']??null)||self::catalog_hash($state['catalog'])!==$identity['catalog_hash']||($state['seed_index']??null)!==count($catalog['seeds'])||!is_array($state['seed_results']??null)||count($state['seed_results'])!==count($catalog['seeds'])||!is_array($state['candidates']??null)||count($state['candidates'])>400)throw new RuntimeException('cache_legacy_invalid');
        $defs=[];$entries=[];foreach($catalog['seeds'] as $i=>$seed){$record=$state['seed_results'][$i];$at=$record['retrieved_at']??null;if(!is_array($record)||($record['seed']??null)!==$seed['keyword']||!is_int($at)||wp_date('Y-m-d',$at,new DateTimeZone('Europe/Warsaw'))!==$identity['date']||!is_int($record['returned']??null)||$record['returned']<0||$record['returned']>50||!is_int($record['accepted']??null)||$record['accepted']<0||$record['accepted']>$record['returned']||!is_bool($record['has_next_page']??null))throw new RuntimeException('cache_legacy_invalid');$defs[$seed['keyword']]=$seed;$entries[$seed['id']]=['keyword'=>$seed['keyword'],'group'=>$seed['group'],'rows'=>[],'has_next_page'=>$record['has_next_page'],'kind'=>'legacy_approved_subset','observed_minimum_volume'=>10,'reported_returned'=>$record['returned'],'retrieved_at'=>$record['retrieved_at'],'saved_at'=>time()];}
        foreach($state['candidates'] as $id=>$row){if(!is_array($row)||!isset($defs[$row['seed']??'']))throw new RuntimeException('cache_legacy_invalid');$seed=$defs[$row['seed']];$clean=self::row($row,$seed,$identity);if($clean['id']!==$id)throw new RuntimeException('cache_legacy_invalid');$entries[$seed['id']]['rows'][$id]=$clean;if(count($entries[$seed['id']]['rows'])>50)throw new RuntimeException('cache_legacy_invalid');}
        $approval=['actor'=>$identity['actor'],'approved_at'=>time(),'source_auto_id'=>$state['id'],'source_state_hash'=>hash('sha256',wp_json_encode([$state['seed_results'],$state['candidates']])),'historical_connection_verified'=>false];self::save(['identity'=>$identity,'seeds'=>$entries,'legacy_approval'=>$approval]);return $entries;
    }
    public static function summary(array $identity,array $catalog): array {
        $entries=self::read($identity,$catalog);$ids=[];$eligible=[];$legacy=false;foreach($entries as $e){foreach($e['rows'] as $id=>$row){$ids[$id]=true;if(ZP_Content_Preparation_20261010::eligible_volume($row))$eligible[$id]=true;}if($e['kind']==='legacy_approved_subset')$legacy=true;}return ['date'=>$identity['date'],'seeds'=>count($entries),'total_seeds'=>count($catalog['seeds']),'candidates'=>count($ids),'eligible_candidates'=>count($eligible),'legacy_subset'=>$legacy];
    }
}
