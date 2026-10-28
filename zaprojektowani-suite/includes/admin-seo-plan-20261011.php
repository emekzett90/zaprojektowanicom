<?php
/** Native publication plan and weekly snapshots of existing Senuto cache. No provider requests. */
if (!defined('ABSPATH')) { exit; }

final class ZP_Admin_SEO_Plan_20261011 {
    public const PAGE = 'zp-suite-seo-plan';
    public const REPORTS = 'zps_seo_plan_20261011_reports';
    public const LOCK = 'zps_seo_plan_20261011_lock';
    public const WEEKLY = 'zp_seo_plan_weekly_20261011';
    private const MAX_ROWS = 500;
    private static function zone(): DateTimeZone { return new DateTimeZone('Europe/Warsaw'); }
    private static function day($value): ?string {
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D',$value)) return null;
        $date = DateTimeImmutable::createFromFormat('!Y-m-d',$value,self::zone());
        return $date && $date->format('Y-m-d') === $value ? $value : null;
    }
    private static function number($value,int $min=0,int $max=100000000): ?int { return is_int($value) && $value >= $min && $value <= $max ? $value : null; }
    private static function text($value,int $max): string { return is_string($value) ? mb_substr($value,0,$max,'UTF-8') : ''; }
    private static function url($value): string {
        if (!is_string($value)) return ''; $url = wp_parse_url($value); $home = wp_parse_url(home_url('/'));
        return is_array($url) && is_array($home) && ($url['scheme']??'') === 'https' && strtolower($url['host']??'') === strtolower($home['host']??'')
            && !array_intersect(['user','pass','port'],array_keys($url)) && str_starts_with($url['path']??'/','/') && !str_starts_with($url['path']??'/','//') ? $value : '';
    }
    private static function admin_url(string $page,array $extra=[]): string {
        if (function_exists('zp_admin20_url')) return zp_admin20_url($page,$extra);
        if (function_exists('zp_suite_workspace_active') && zp_suite_workspace_active()) $extra['zp_workspace']='1';
        return add_query_arg(array_merge(['page'=>$page],$extra),admin_url('admin.php'));
    }
    public static function calendar(?array $records=null,?int $now=null): array {
        if (!current_user_can('manage_options')) return [];
        $now = $now ?? time(); $end = (new DateTimeImmutable('@'.$now))->setTimezone(self::zone())->modify('+30 days')->getTimestamp();
        if ($records === null) $records = class_exists('ZP_Admin_Calendar_20261009') ? ZP_Admin_Calendar_20261009::records() : [];
        $out=[]; foreach (array_slice($records,0,500) as $record) {
            if (!is_array($record) || ($record['status']??'') !== 'future' || !(($record['date']??null) instanceof DateTimeInterface)) continue;
            $id = self::number($record['id']??null,1); $at = $record['date']->getTimestamp();
            if (!$id || $at < $now || $at >= $end) continue;
            $date = (new DateTimeImmutable('@'.$at))->setTimezone(self::zone());
            $out[]=['id'=>$id,'title'=>self::text($record['title']??'',240),'day'=>$date->format('Y-m-d'),'time'=>$date->format('H:i'),'at'=>$at,
                'preview'=>current_user_can('edit_post',$id) ? self::url($record['preview']??'') : '',
                'publish_job'=>is_int($record['publish_job_at']??null) && $record['publish_job_at'] > 0 ? $record['publish_job_at'] : null];
        }
        usort($out,static fn($a,$b)=>($a['at']<=>$b['at'])?:($a['id']<=>$b['id'])); return $out;
    }
    /** Whitelist only report fields; cached connection fingerprints and private configuration never enter a snapshot. */
    public static function snapshots(array $data,array $config,?int $now=null): array {
        $now = $now ?? time(); $today = wp_date('Y-m-d',$now,self::zone()); $out=[];
        foreach (['polska','katowice'] as $slot) foreach (['desktop','mobile'] as $device) {
            $id=self::number($config[$slot]??null,1); $key=($id??0).'_'.$device; $project=$data['projects'][$id??0]??null; $report=$data['reports'][$key]??null;
            $base=['slot'=>$slot,'project'=>$id,'device'=>$device,'name'=>is_array($project)?self::text($project['name']??'',240):'',
                'country'=>is_array($project)?self::number($project['country_id']??null):null,'region'=>is_array($project)?self::number($project['region_id']??null):null,
                'measured'=>null,'project_measured'=>is_array($project)?self::day($project['last_date']??null):null,'fetched_at'=>null,'limited'=>null,'status'=>'missing','rows'=>[]];
            $day=is_array($report)?self::day($report['range']['end']??null):null; $fetched=is_array($report)?self::number($report['fetched_at']??null,1,PHP_INT_MAX):null;
            if (!$id || !is_array($project) || !is_array($report) || ($report['project']??null) !== $id || ($report['device']??null) !== $device || !$day || $day > $today || !$fetched || $fetched > $now+300 || !is_array($report['rows']??null)) { $out[$slot.'_'.$device]=$base; continue; }
            $rows=[]; $invalid=false;
            foreach (array_slice($report['rows'],0,self::MAX_ROWS,true) as $row) {
                if (!is_array($row) || !is_int($row['id']??null) || $row['id']<=0 || !is_string($row['keyword']??null) || $row['keyword']==='' || strlen($row['keyword'])>500 || isset($rows[$row['id']])) { $invalid=true; continue; }
                $rows[$row['id']]=['id'=>$row['id'],'keyword'=>$row['keyword'],'position'=>self::number($row['position']??null,1,1000),'volume'=>self::number($row['volume']??null)];
            }
            $base['measured']=$day; $base['fetched_at']=$fetched; $base['limited']=($report['limited']??false)===true || count($report['rows'])>self::MAX_ROWS || $invalid;
            $base['status']='available'; $base['rows']=$rows; $out[$slot.'_'.$device]=$base;
        }
        return $out;
    }
    public static function counts(array $snapshot): array {
        if (($snapshot['status']??'') !== 'available') return ['known'=>null,'top3'=>null,'top10'=>null,'top20'=>null,'top50'=>null];
        $known=0; $top=[3=>0,10=>0,20=>0,50=>0];
        foreach ($snapshot['rows']??[] as $row) { $position=self::number($row['position']??null,1,1000); if ($position===null) continue; $known++; foreach ($top as $limit=>$count) if ($position <= $limit) $top[$limit]++; }
        return ['known'=>$known,'top3'=>$known?$top[3]:null,'top10'=>$known?$top[10]:null,'top20'=>$known?$top[20]:null,'top50'=>$known?$top[50]:null];
    }
    public static function compare(array $before,array $after): array {
        $result=['status'=>'unavailable','before'=>$before['measured']??null,'after'=>$after['measured']??null,'rise'=>null,'fall'=>null,'same'=>null,'entered'=>null,'left'=>null,'comparable'=>0,'rows'=>[]];
        if (($before['status']??'')!=='available' || ($after['status']??'')!=='available' || !self::day($before['measured']??null) || !self::day($after['measured']??null)
            || ($before['project']??null)!==($after['project']??null) || ($before['device']??null)!==($after['device']??null) || ($before['country']??null)!==($after['country']??null) || ($before['region']??null)!==($after['region']??null)) return $result;
        if ($after['measured'] <= $before['measured']) { $result['status']=$after['measured']===$before['measured']?'no_new_measurement':'older_measurement'; return $result; }
        $counts=['rise'=>0,'fall'=>0,'same'=>0,'entered'=>0,'left'=>0];
        foreach ($after['rows']??[] as $id=>$row) {
            $old=$before['rows'][$id]??null; if (!is_array($old) || !is_array($row) || ($old['keyword']??null)!==($row['keyword']??null)) continue;
            $a=self::number($old['position']??null,1,1000); $b=self::number($row['position']??null,1,1000); if ($a===null || $b===null || ($a>50 && $b>50)) continue;
            $kind=$a>50?'entered':($b>50?'left':($a>$b?'rise':($a<$b?'fall':'same'))); $delta=$a<=50 && $b<=50 ? $a-$b : null;
            $counts[$kind]++; $result['comparable']++; if ($kind!=='same') $result['rows'][]=['keyword'=>$row['keyword'],'before'=>$a,'after'=>$b,'kind'=>$kind,'delta'=>$delta];
        }
        if (!$result['comparable']) { $result['status']='no_comparable_rows'; return $result; }
        $result=array_merge($result,$counts); $result['status']='compared'; usort($result['rows'],static fn($a,$b)=>abs($b['delta']??1000)<=>abs($a['delta']??1000)); $result['rows']=array_slice($result['rows'],0,20); return $result;
    }
    public static function next_weekly(?int $now=null): int {
        $date=(new DateTimeImmutable('@'.($now??time())))->setTimezone(self::zone()); $next=$date->setTime(9,30);
        $offset=(8-(int)$date->format('N'))%7; if ($offset) $next=$next->modify('+'.$offset.' days'); if ($next<=$date) $next=$next->modify('+7 days'); return $next->getTimestamp();
    }
    private static function actor(): ?int {
        if (!class_exists('ZP_Senuto_Positions_20261010')) return null; $config=ZP_Senuto_Positions_20261010::config(); $id=self::number($config['actor']??null,1);
        $user=$id?get_userdata($id):false; return $user && user_can($user,'manage_options') ? $id : null;
    }
    public static function setup(): void {
        if (!current_user_can('manage_options') || !self::actor() || wp_next_scheduled(self::WEEKLY)) return;
        $history=get_option(self::REPORTS,[]); $at=is_array($history) && $history ? self::next_weekly() : time()+10;
        wp_schedule_single_event($at,self::WEEKLY);
    }
    private static function owns(string $token): bool { $raw=get_option(self::LOCK,''); $lock=is_string($raw)?json_decode($raw,true):null; return is_array($lock) && ($lock['token']??null)===$token && ($lock['expires']??0)>time(); }
    private static function unlock(string $token): void {
        $raw=get_option(self::LOCK,''); $lock=is_string($raw)?json_decode($raw,true):null; if (!is_array($lock) || ($lock['token']??null)!==$token) return;
        global $wpdb; $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",self::LOCK,$raw)); wp_cache_delete(self::LOCK,'options'); wp_cache_delete('notoptions','options');
    }
    public static function weekly(): void {
        $actor=self::actor(); if (!$actor) return; $token=wp_generate_uuid4(); $raw=wp_json_encode(['token'=>$token,'expires'=>time()+90]);
        $old=get_option(self::LOCK,''); $lock=is_string($old)?json_decode($old,true):null;
        if (is_array($lock) && is_int($lock['expires']??null) && $lock['expires']<time()) { global $wpdb; $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s",self::LOCK,$old)); wp_cache_delete(self::LOCK,'options'); wp_cache_delete('notoptions','options'); }
        if (!add_option(self::LOCK,$raw,'',false)) return; $previous=get_current_user_id();
        try {
            wp_set_current_user($actor); if (!current_user_can('manage_options')) return;
            $history=get_option(self::REPORTS,[]); if (!is_array($history)) return; $week=wp_date('o-\WW',time(),self::zone());
            if (!isset($history[$week])) {
                $snapshot=self::snapshots(ZP_Senuto_Positions_20261010::cached(),ZP_Senuto_Positions_20261010::config());
                if (!self::owns($token) || !current_user_can('manage_options')) return;
                $history[$week]=['created_at'=>time(),'snapshots'=>$snapshot]; ksort($history); $history=array_slice($history,-8,null,true); update_option(self::REPORTS,$history,false);
            }
        } catch (Throwable $ignored) { /* Missing source/storage remains missing; never fetch or fabricate a report. */ }
        finally { wp_set_current_user($previous); self::unlock($token); if (self::actor() && !wp_next_scheduled(self::WEEKLY)) wp_schedule_single_event(self::next_weekly(),self::WEEKLY); }
    }
    private static function num($value): string { return is_int($value)?(string)$value:'—'; }
    private static function stamp($at): string { return is_int($at) && $at>0 ? wp_date('d.m.Y H:i',$at,self::zone()) : '—'; }
    public static function render(): void {
        if (!current_user_can('manage_options')) { wp_die('Brak uprawnień do planu SEO.','',['response'=>403]); return; }
        if (function_exists('zp_admin20_open')) zp_admin20_open('Plan SEO','plan','Najbliższe publikacje i tygodniowy przegląd pozycji'); else echo '<div class="wrap zpAdmin20"><h1>Plan SEO</h1>';
        $now=time(); $end=(new DateTimeImmutable('@'.$now))->setTimezone(self::zone())->modify('+30 days'); $calendar=self::calendar();
        echo '<div class="zpAdmin20__tabs"><a href="#zp-plan-publications">Najbliższe 30 dni</a><a href="#zp-plan-positions">Polska i Katowice</a><a href="#zp-plan-weekly">Raport tygodniowy</a></div>';
        echo '<section id="zp-plan-publications" class="zpAdmin20__card"><h2>Publikacje: '.esc_html(wp_date('d.m.Y',$now,self::zone()).'–'.$end->format('d.m.Y')).'</h2><p>'.count($calendar).' wpisów ma rzeczywistą przyszłą datę w WordPressie. Automatyczna publikacja używa istniejącego harmonogramu; plan nie zmienia dat ani nie generuje treści.</p>';
        $day=''; foreach ($calendar as $row) { if ($day!==$row['day']) { if ($day!=='') echo '</ul>'; $day=$row['day']; echo '<h3>'.esc_html($day).'</h3><ul>'; } echo '<li><strong>'.esc_html($row['time'].' · '.$row['title']).'</strong>'; if ($row['preview']!=='') echo ' · <a href="'.esc_url($row['preview']).'" target="_blank" rel="noopener">Podgląd ↗</a>'; if ($row['publish_job']===null) echo ' <span class="zpAdmin20__pill">Brak zdarzenia publikacji — sprawdź harmonogram</span>'; echo '</li>'; } if ($day!=='') echo '</ul>';
        if (!$calendar) echo '<p class="zpAdmin20__muted">Nie znaleziono zaplanowanych publikacji w tym okresie. Szkic bez przyszłej daty nie jest automatycznie planem publikacji.</p>'; echo '<a class="button" href="'.esc_url(self::admin_url('zp-suite-calendar')).'">Cały harmonogram</a></section>';
        $device=is_string($_GET['zp_plan_device']??null) && $_GET['zp_plan_device']==='mobile'?'mobile':'desktop';
        $data=class_exists('ZP_Senuto_Positions_20261010')?ZP_Senuto_Positions_20261010::cached():[]; $config=class_exists('ZP_Senuto_Positions_20261010')?ZP_Senuto_Positions_20261010::config():[]; $current=self::snapshots($data,$config);
        echo '<section id="zp-plan-positions" class="zpAdmin20__card"><h2>Bieżący zapis pozycji Senuto</h2><div class="zpAdmin20__tabs">'; foreach (['desktop'=>'Desktop','mobile'=>'Mobile'] as $key=>$label) echo '<a'.($key===$device?' class="is-active" aria-current="page"':'').' href="'.esc_url(self::admin_url(self::PAGE,['zp_plan_device'=>$key])).'">'.$label.'</a>'; echo '</div><p class="zpAdmin20__muted">Data pomiaru i data pobrania to różne informacje. Codzienne pobieranie nie tworzy codziennych pomiarów Senuto.</p>';
        foreach (['polska'=>'Polska','katowice'=>'Katowice'] as $slot=>$label) { $s=$current[$slot.'_'.$device]; $n=self::counts($s); echo '<h3>'.esc_html($label.' · '.$s['name']).'</h3><p>Projekt #'.esc_html(self::num($s['project'])).' · '.esc_html($device).' · kraj API: '.esc_html(self::num($s['country'])).' · region API: '.esc_html(self::num($s['region'])).'<br>Pomiar: <strong>'.esc_html($s['measured']??'brak danych').'</strong> · pobrano: '.esc_html(self::stamp($s['fetched_at'])).'</p>';
            if ($s['measured']) { $age=(new DateTimeImmutable($s['measured'],self::zone()))->diff(new DateTimeImmutable(wp_date('Y-m-d',$now,self::zone()),self::zone()))->days; if ($age>2) echo '<p><span class="zpAdmin20__pill">Pomiar ma '.(int)$age.' dni</span></p>'; } if ($s['limited']) echo '<p class="zpAdmin20__muted">Dane częściowe; liczby dotyczą tylko pobranych fraz.</p>'; if ($slot==='katowice' && !$s['region']) echo '<p class="zpAdmin20__muted">Nazwa projektu nie potwierdza pomiaru dla miasta Katowice; API nie wskazuje regionu.</p>';
            echo '<p>Znane pozycje: '.esc_html(self::num($n['known'])).' · TOP3: '.esc_html(self::num($n['top3'])).' · TOP10: '.esc_html(self::num($n['top10'])).' · TOP20: '.esc_html(self::num($n['top20'])).' · TOP50: '.esc_html(self::num($n['top50'])).'</p>'; }
        echo '<a class="button" href="'.esc_url(self::admin_url('zp-suite-positions')).'">Pełny raport pozycji</a></section>';
        $history=get_option(self::REPORTS,[]); $history=is_array($history)?array_slice($history,-8,null,true):[]; ksort($history); $entries=array_values($history); $last=$entries?end($entries):null; $previous=count($entries)>1?$entries[count($entries)-2]:null;
        echo '<section id="zp-plan-weekly" class="zpAdmin20__card"><h2>Tygodniowy raport wzrostów i spadków</h2><p>Co poniedziałek o 09:30 czasu Warszawy zapisujemy dostępny raport z istniejącego pobierania Senuto. Bez dodatkowych zapytań API i bez wysyłki e-mail. WP-Cron może wykonać zadanie później, gdy witryna otrzyma ruch.</p><p>Ostatni zapis: '.esc_html(self::stamp(is_array($last)?($last['created_at']??null):null)).' · następne zdarzenie: '.esc_html(self::stamp(($next=wp_next_scheduled(self::WEEKLY))!==false?(int)$next:null)).'</p>';
        if (!self::actor()) echo '<p>Raport tygodniowy czeka na poprawną konfigurację istniejącego modułu Pozycje i uprawnionego użytkownika.</p>';
        foreach (['polska'=>'Polska','katowice'=>'Katowice'] as $slot=>$label) { $key=$slot.'_'.$device; $a=is_array($previous)?($previous['snapshots'][$key]??[]):[]; $b=is_array($last)?($last['snapshots'][$key]??[]):[]; $comparison=self::compare(is_array($a)?$a:[],is_array($b)?$b:[]); echo '<h3>'.esc_html($label.' · '.$device).'</h3>';
            $messages=['unavailable'=>'Czekamy na dwa porównywalne tygodniowe zapisy. Brak danych nie oznacza pozycji0.','no_new_measurement'=>'Senuto nie udostępniło nowego pomiaru między tymi zapisami. Nie wyliczamy pozornych zmian.','older_measurement'=>'Nowszy zapis zawiera wcześniejszy pomiar; porównanie wymaga sprawdzenia.','no_comparable_rows'=>'Brak wspólnych fraz z porównywalnymi pozycjami.','compared'=>'Porównujemy tylko te same frazy, projekt i urządzenie z dwóch rzeczywistych dat pomiaru.']; echo '<p>'.esc_html($messages[$comparison['status']]).'</p>';
            if ($comparison['status']!=='compared') continue; echo '<p>Pomiar '.esc_html($comparison['before'].' → '.$comparison['after']).' · porównywalne frazy: '.(int)$comparison['comparable'].' · wzrosty: '.(int)$comparison['rise'].' · spadki: '.(int)$comparison['fall'].' · wejścia TOP50: '.(int)$comparison['entered'].' · wyjścia TOP50: '.(int)$comparison['left'].'</p><div class="zpAdmin20__tableWrap"><table class="zpAdmin20__table"><thead><tr><th>Fraza</th><th>Poprzednio</th><th>Teraz</th><th>Zmiana</th></tr></thead><tbody>';
            foreach ($comparison['rows'] as $row) echo '<tr><td>'.esc_html($row['keyword']).'</td><td>'.esc_html($row['before']>50?'50+':self::num($row['before'])).'</td><td>'.esc_html($row['after']>50?'50+':self::num($row['after'])).'</td><td>'.esc_html($row['delta']!==null?($row['delta']>0?'+':'').$row['delta']:($row['kind']==='entered'?'Wejście TOP50':'Wyjście TOP50')).'</td></tr>'; echo '</tbody></table></div>'; }
        if ($history) { echo '<details class="zpAdmin20__details"><summary>Historia zapisów: '.count($history).' / 8 tygodni</summary><ul>'; foreach ($history as $week=>$entry) echo '<li>'.esc_html((string)$week).' · '.esc_html(self::stamp(is_array($entry)?($entry['created_at']??null):null)).'</li>'; echo '</ul></details>'; } echo '<p class="zpAdmin20__muted">Pozycje nie są liczbą wizyt ani gwarancją efektów SEO. Ten plan nie uruchamia generowania artykułów, zdjęć ani nowego pomiaru SERP.</p></section>';
        if (function_exists('zp_admin20_close')) zp_admin20_close(); else echo '</div>';
    }
    public static function menu(): void { add_submenu_page('zp-suite','Plan SEO','Plan SEO','manage_options',self::PAGE,[self::class,'render']); }
    public static function register(bool $menu=true): void { if ($menu) add_action('admin_menu',[self::class,'menu'],99); add_action('admin_init',[self::class,'setup'],65); add_action(self::WEEKLY,[self::class,'weekly']); }
}
function zp_admin_seo_plan_render(): void { ZP_Admin_SEO_Plan_20261011::render(); }
