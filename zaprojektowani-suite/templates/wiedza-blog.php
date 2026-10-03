<?php if (!defined('ABSPATH')) { exit; }
$zp_kb_ajax = admin_url('admin-ajax.php');
$zp_kb_nonce = wp_create_nonce('zp_wiedza_nonce');
$zp_kb_cats = get_categories(['hide_empty'=>true,'orderby'=>'name']);
$zp_kb_topics = isset($zp_kb_topics) && is_array($zp_kb_topics) ? $zp_kb_topics : (function_exists('zp_suite_wiedza_topic_groups_resolved') ? zp_suite_wiedza_topic_groups_resolved() : []);
$zp_kb_stats_posts = get_posts([
  'post_type'              => 'post',
  'post_status'            => 'publish',
  'posts_per_page'         => -1,
  'orderby'                => 'ID',
  'order'                  => 'ASC',
  'ignore_sticky_posts'    => true,
  'no_found_rows'          => true,
  'suppress_filters'       => false,
  'update_post_meta_cache' => false,
  'update_post_term_cache' => false,
]);
$zp_kb_stats_ids = wp_list_pluck($zp_kb_stats_posts, 'ID');
if ($zp_kb_stats_ids) { update_meta_cache('post', array_map('intval', $zp_kb_stats_ids)); }
$zp_kb_total_articles = count($zp_kb_stats_posts);
$zp_kb_topic_count = count($zp_kb_topics);
$zp_kb_total_reads = 0;
$zp_kb_total_minutes = 0;
$zp_kb_latest_modified = 0;
foreach ($zp_kb_stats_posts as $zp_kb_stats_post) {
  $zp_kb_sid = (int) $zp_kb_stats_post->ID;
  $zp_kb_reads = (int) get_post_meta($zp_kb_sid, '_zp_post_reads', true);
  if ($zp_kb_reads < 1) { $zp_kb_reads = (int) get_post_meta($zp_kb_sid, '_zp_post_views', true); }
  $zp_kb_total_reads += max(0, $zp_kb_reads);

  $zp_kb_saved_read = get_post_meta($zp_kb_sid, '_zp_blog_read', true);
  if ($zp_kb_saved_read && preg_match('/(\\d+)/', (string) $zp_kb_saved_read, $zp_kb_read_match)) {
    $zp_kb_minutes = max(1, (int) $zp_kb_read_match[1]);
  } else {
    $zp_kb_plain = trim(wp_strip_all_tags(strip_shortcodes((string) $zp_kb_stats_post->post_content)));
    $zp_kb_words = preg_split('/\\s+/u', $zp_kb_plain, -1, PREG_SPLIT_NO_EMPTY);
    $zp_kb_minutes = max(1, (int) ceil((is_array($zp_kb_words) ? count($zp_kb_words) : 0) / 220));
  }
  $zp_kb_total_minutes += $zp_kb_minutes;

  $zp_kb_mod = strtotime((string) $zp_kb_stats_post->post_modified_gmt . ' GMT');
  if ($zp_kb_mod > $zp_kb_latest_modified) { $zp_kb_latest_modified = $zp_kb_mod; }
}
$zp_kb_avg_read_minutes = $zp_kb_total_articles > 0 ? max(1, (int) round($zp_kb_total_minutes / $zp_kb_total_articles)) : 0;
$zp_kb_latest_label = $zp_kb_latest_modified ? wp_date('d.m.Y', $zp_kb_latest_modified) : wp_date('d.m.Y');
$zp_kb_total_reads_label = number_format_i18n($zp_kb_total_reads);
$zp_kb_featured = new WP_Query(['post_type'=>'post','post_status'=>'publish','posts_per_page'=>1,'ignore_sticky_posts'=>false,'orderby'=>'date','order'=>'DESC','no_found_rows'=>true,'update_post_meta_cache'=>true,'update_post_term_cache'=>true]);
$zp_kb_selected = new WP_Query(['post_type'=>'post','post_status'=>'publish','posts_per_page'=>5,'ignore_sticky_posts'=>true,'orderby'=>'comment_count','order'=>'DESC','no_found_rows'=>true,'update_post_meta_cache'=>true,'update_post_term_cache'=>true]);
if (!$zp_kb_selected->have_posts()) { $zp_kb_selected = new WP_Query(['post_type'=>'post','post_status'=>'publish','posts_per_page'=>5,'ignore_sticky_posts'=>true,'offset'=>1,'orderby'=>'date','order'=>'DESC','no_found_rows'=>true,'update_post_meta_cache'=>true,'update_post_term_cache'=>true]); }
$zp_kb_featured_id = (!empty($zp_kb_featured->posts[0]) && !empty($zp_kb_featured->posts[0]->ID)) ? (int) $zp_kb_featured->posts[0]->ID : 0;
$zp_wiedza_person_scale_d = esc_attr(zp_suite_opt('wiedza.hero_person_scale_desktop', zp_suite_opt('wiedza.hero_person_scale','1')));
$zp_wiedza_person_y_d = (int) zp_suite_opt('wiedza.hero_person_y_desktop', zp_suite_opt('wiedza.hero_person_y','0'));
$zp_wiedza_person_x_d = (int) zp_suite_opt('wiedza.hero_person_x_desktop', zp_suite_opt('wiedza.hero_person_x','0'));
$zp_wiedza_person_scale_m = esc_attr(zp_suite_opt('wiedza.hero_person_scale_mobile', zp_suite_opt('wiedza.hero_person_scale','1')));
$zp_wiedza_person_y_m = (int) zp_suite_opt('wiedza.hero_person_y_mobile', zp_suite_opt('wiedza.hero_person_y','0'));
$zp_wiedza_person_x_m = (int) zp_suite_opt('wiedza.hero_person_x_mobile', zp_suite_opt('wiedza.hero_person_x','0'));
$zp_wiedza_video_opacity = max(0, min(100, (int) zp_suite_opt('wiedza.hero_video_opacity', '46')));
$zp_wiedza_video_brightness = max(0, min(100, (int) zp_suite_opt('wiedza.hero_video_brightness', '58')));
$zp_wiedza_video_navy = max(0, min(100, (int) zp_suite_opt('wiedza.hero_video_navy_mask', '96')));
$zp_wiedza_text_shadow = max(0, min(100, (int) zp_suite_opt('wiedza.hero_text_shadow', '86')));
?>
<style>:root{--bg:#010309;--ink:#fff;--mut:rgba(255,255,255,.66);--mut2:rgba(255,255,255,.42);--line:rgba(255,255,255,.12);--acc-deep:#0a1a30;--acc:#1c477a;--acc-mid:#3b6ea8;--acc-ice:#8ec8f7;--gold:#ffc246;--font:"Plus Jakarta Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}*{box-sizing:border-box}html,body{margin:0;background:var(--bg);color:var(--ink);font-family:var(--font);-webkit-font-smoothing:antialiased}.zh{position:relative;isolation:isolate;min-height:100vh;overflow:hidden;background:var(--bg)}.zh__bg{position:absolute;inset:0;z-index:0;background:radial-gradient(120% 90% at 74% 44%,rgba(18,48,88,.22),transparent 56%),radial-gradient(80% 80% at 94% 82%,rgba(8,26,52,.3),transparent 50%),radial-gradient(70% 70% at 12% 22%,rgba(14,38,72,.12),transparent 46%),linear-gradient(140deg,#010308 0%,#030a16 50%,#01040c 100%)}.zh__aurora{position:absolute;left:36%;top:16%;width:54%;height:66%;z-index:1;pointer-events:none;background:radial-gradient(closest-side,rgba(24,60,104,.2),transparent 70%),radial-gradient(closest-side,rgba(45,92,149,.09),transparent 72%);background-repeat:no-repeat;background-position:32% 34%,72% 64%;background-size:64% 64%,54% 54%;filter:blur(38px);opacity:.6;animation:zhBreath 15s ease-in-out infinite}@keyframes zhBreath{0%,100%{transform:scale(1);opacity:.5}50%{transform:scale(1.05) translateY(-1.1%);opacity:.7}}.zh__grid{position:absolute;inset:0;z-index:1;opacity:.03;pointer-events:none;background-image:linear-gradient(rgba(255,255,255,.1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.07) 1px,transparent 1px);background-size:82px 82px;-webkit-mask-image:radial-gradient(120% 100% at 60% 42%,#000 26%,transparent 74%);mask-image:radial-gradient(120% 100% at 60% 42%,#000 26%,transparent 74%)}.zh__dust{position:absolute;inset:0;z-index:2;pointer-events:none;overflow:hidden}.zh__dust i{position:absolute;bottom:-12px;width:3px;height:3px;border-radius:50%;background:rgba(142,200,247,.55);opacity:0;animation:zhDust linear infinite}@keyframes zhDust{0%{transform:translateY(0) scale(1);opacity:0}12%{opacity:.55}88%{opacity:.45}100%{transform:translateY(-94vh) scale(.6);opacity:0}}.zh__grain{position:absolute;inset:0;z-index:5;pointer-events:none;opacity:.3;mix-blend-mode:overlay;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.5'/%3E%3C/svg%3E")}.zh__vig{position:absolute;inset:0;z-index:5;pointer-events:none;background:radial-gradient(135% 130% at 50% 26%,transparent 48%,rgba(0,0,0,.5))}.zh__hair{position:absolute;left:0;right:0;top:0;height:1px;z-index:7;background:linear-gradient(90deg,transparent,rgba(142,200,247,.5),transparent);opacity:.4}.zh__kin{position:absolute;z-index:2;right:0;top:47%;width:88%;transform:translateY(-52%);text-align:right;pointer-events:none;-webkit-mask-image:radial-gradient(135% 135% at 74% 48%,#000 46%,transparent 90%);mask-image:radial-gradient(135% 135% at 74% 48%,#000 46%,transparent 90%)}.zh__kinWord{display:block;font-weight:800;line-height:.8;letter-spacing:-.05em;text-transform:uppercase;font-size:clamp(150px,25vw,460px);background:linear-gradient(100deg,#16365f 0%,#1c477a 28%,#3b6ea8 50%,#79a6da 64%,#2a558f 84%,#16365f 100%);background-size:260% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;animation:zhShine 11s linear infinite;opacity:.5}@keyframes zhShine{to{background-position:-260% 0}}.zh__rotor{display:inline-block;overflow:hidden;height:.8em;vertical-align:bottom}.zh__rotor>span{display:block;animation:zhRot 13s cubic-bezier(.76,0,.24,1) infinite}.zh__rotor i{display:block;height:.8em;font-style:normal}@keyframes zhRot{0%,15%{transform:translateY(0)}20%,35%{transform:translateY(-.8em)}40%,55%{transform:translateY(-1.6em)}60%,75%{transform:translateY(-2.4em)}80%,95%{transform:translateY(-3.2em)}100%{transform:translateY(-4em)}}.zh__stage{position:absolute;z-index:3;right:-6vw;inset-block:0;width:70vw;min-width:1000px;pointer-events:auto;will-change:transform}.zh__conic{position:absolute;left:52%;top:50%;width:64%;aspect-ratio:1;transform:translate(-50%,-50%);border-radius:50%;pointer-events:none;background:conic-gradient(from 0deg,transparent 0deg,rgba(28,71,122,0) 46deg,rgba(59,110,168,.12) 128deg,rgba(142,200,247,.045) 196deg,rgba(28,71,122,0) 300deg,transparent 360deg);-webkit-mask:radial-gradient(closest-side,transparent 54%,#000 60%,#000 73%,transparent 80%);mask:radial-gradient(closest-side,transparent 54%,#000 60%,#000 73%,transparent 80%);filter:blur(8px);opacity:.34;animation:zhConic 20s linear infinite}@keyframes zhConic{to{transform:translate(-50%,-50%) rotate(360deg)}}.zh__halo{position:absolute;left:52%;top:50%;width:48%;aspect-ratio:1;transform:translate(-50%,-50%);border-radius:50%;background:radial-gradient(circle,rgba(30,78,128,.07),rgba(16,44,82,.025) 44%,transparent 66%);filter:blur(16px);animation:zhPulse 8s ease-in-out infinite;pointer-events:none}@keyframes zhPulse{0%,100%{opacity:.45;transform:translate(-50%,-50%) scale(1)}50%{opacity:.66;transform:translate(-50%,-50%) scale(1.05)}}.zh__ring{position:absolute;left:52%;top:50%;border-radius:50%;border:1px solid rgba(142,200,247,.035);transform:translate(-50%,-50%);pointer-events:none}.zh__ring--a{width:50%;aspect-ratio:1;border-style:dashed;animation:zhSpin 46s linear infinite}.zh__ring--b{width:66%;aspect-ratio:1;border-color:rgba(142,200,247,.018);animation:zhSpin 72s linear infinite reverse}.zh__ring--a::before{content:"";position:absolute;top:-4px;left:50%;width:7px;height:7px;border-radius:50%;background:rgba(142,200,247,.55);box-shadow:0 0 7px 1px rgba(142,200,247,.22);transform:translateX(-50%)}@keyframes zhSpin{to{transform:translate(-50%,-50%) rotate(360deg)}}.zh__spline{position:absolute;inset:0;width:100%;height:100%;border:0;opacity:1;transition:opacity .35s ease;pointer-events:auto}.zh.is-spline-loaded .zh__spline{opacity:1}.zh__poster{position:absolute;inset:0;z-index:1;pointer-events:none;background:var(--robot) no-repeat center calc(50% + 70px)/min(82%,860px) auto;filter:saturate(1.03) brightness(1) drop-shadow(0 40px 80px rgba(0,0,0,.55));opacity:1;transition:opacity .8s ease}.zh.is-spline-loaded .zh__poster{opacity:0}.zh__fade{position:absolute;inset:0;z-index:4;pointer-events:none;background:linear-gradient(90deg,#010309 0%,rgba(1,3,9,.82) 24%,rgba(1,3,9,.42) 50%,rgba(1,3,9,.1) 74%,transparent 100%),linear-gradient(to bottom,transparent 56%,rgba(1,3,9,.7) 100%)}.zh__splineMask{position:absolute;right:0;bottom:0;width:200px;height:54px;z-index:4;pointer-events:none;background:linear-gradient(to top,#010309 30%,transparent)}.zh__rail{position:absolute;right:20px;left:auto;top:clamp(112px,18vh,188px);bottom:auto;z-index:8;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;gap:18px;pointer-events:none}.zh__rail>*{pointer-events:auto}.zh__railTop{display:none}.zh__railIco{display:grid;gap:16px}.zh__railIco a{color:var(--mut2);display:block;transition:color .2s ease,transform .2s ease}.zh__railIco a:hover{color:var(--acc-ice);transform:translateY(-2px)}.zh__railIco svg{width:17px;height:17px;display:block}.zh__railProg{width:2px;height:96px;margin-top:2px;background:rgba(255,255,255,.12);position:relative;border-radius:999px;overflow:hidden}.zh__railProgFill{position:absolute;left:0;top:0;width:100%;height:0%;background:linear-gradient(to bottom,var(--acc-ice),var(--acc));border-radius:999px;box-shadow:0 0 12px rgba(142,200,247,.32);transition:height .08s linear}.zh__inner{position:relative;z-index:6;width:min(1700px,calc(100% - 96px));margin:0 auto;min-height:100vh;display:flex;align-items:center;padding:120px 0 124px;pointer-events:none}.zh__copy{max-width:1000px;pointer-events:none}.zh__eb{display:inline-flex;align-items:center;gap:13px;margin:0 0 18px;padding:0;border:0;border-radius:0;background:transparent;color:#b9b2cf;font-size:10px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;line-height:1.25;-webkit-backdrop-filter:none;backdrop-filter:none}.zh__eb .dot{display:block;position:relative;flex:0 0 42px;width:42px;height:1px;border-radius:999px;background:linear-gradient(90deg,rgba(185,178,207,0),rgba(185,178,207,.72),rgba(142,200,247,.28),rgba(185,178,207,0))}.zh__eb .dot::after{content:"";position:absolute;inset:-1px;border-radius:999px;background:linear-gradient(90deg,transparent,rgba(142,200,247,.34),transparent);filter:blur(3px);opacity:.75}@keyframes zhPing{0%{transform:scale(.6);opacity:1}100%{transform:scale(1.8);opacity:0}}.zh__title{margin:0;max-width:15ch;font-weight:480;letter-spacing:-.046em;line-height:.97;font-size:clamp(46px,7vw,124px);text-wrap:balance;opacity:0;transform:translateY(26px);filter:blur(8px);animation:zhRise 1.1s cubic-bezier(.16,1,.3,1) .1s forwards}@keyframes zhRise{to{opacity:1;transform:translateY(0);filter:blur(0)}}.zh__grad{background:linear-gradient(110deg,var(--acc-mid) 0%,var(--acc-ice) 26%,#fff 42%,var(--acc-ice) 58%,var(--acc-mid) 100%);background-size:240% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:700;background-position:130% 0;animation:zhSheen 1.5s cubic-bezier(.16,1,.3,1) 1s forwards}@keyframes zhSheen{to{background-position:14% 0}}.zh__lead{margin:32px 0 0;max-width:600px;color:var(--mut);font-size:17.5px;line-height:1.72;letter-spacing:-.01em;opacity:0;animation:zhFade .9s ease .5s forwards}.zh__actions{pointer-events:auto;margin-top:36px;display:flex;flex-wrap:wrap;gap:14px;opacity:0;animation:zhFade .9s ease .62s forwards}@keyframes zhFade{to{opacity:1}}.zh__btn{position:relative;display:inline-flex;align-items:center;gap:10px;min-height:58px;padding:0 28px;border-radius:999px;font-size:15px;font-weight:700;letter-spacing:-.01em;white-space:nowrap;text-decoration:none;cursor:pointer}.zh__btn span,.zh__btn svg{position:relative;z-index:2}.zh__btn svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;transition:transform .24s cubic-bezier(.16,1,.3,1)}.zh__btn--p{color:#071426;border:0;background-color:#fff;background-image:linear-gradient(100deg,#020407 0%,#07101e 38%,#102a4f 76%,var(--acc) 100%);background-repeat:no-repeat;background-position:left center;background-size:0% 100%;box-shadow:none;transition:background-size .44s cubic-bezier(.16,1,.3,1),color .2s ease,transform .24s cubic-bezier(.16,1,.3,1)}.zh__btn--p::after{content:"";position:absolute;inset:0;z-index:1;border-radius:inherit;padding:1px;pointer-events:none;background:linear-gradient(120deg,rgba(28,71,122,.75),rgba(16,42,79,.5) 48%,rgba(2,4,7,.55) 100%);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude}.zh__btn--p:hover,.zh__btn--p:focus-visible{color:#fff;background-size:100% 100%}.zh__btn--p:hover svg{transform:translate(3px,-3px)}.zh__btn--g{color:#fff;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.22);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);transition:background .24s ease,color .2s ease,transform .24s cubic-bezier(.16,1,.3,1)}.zh__btn--g:hover{background:#fff;color:#071426;transform:translateY(-2px)}.zh__btn--g:hover svg{transform:translate(3px,-3px)}.zh__chips{pointer-events:auto;margin-top:30px;display:flex;flex-wrap:wrap;gap:9px;opacity:0;animation:zhFade .9s ease .74s forwards}.zh__chip{position:relative;display:inline-flex;align-items:center;gap:8px;padding:9px 15px;border:1px solid var(--line);border-radius:999px;background:rgba(255,255,255,.03);color:var(--mut);font-size:12px;font-weight:600;letter-spacing:-.01em;text-decoration:none;overflow:hidden;transition:border-color .24s ease,color .24s ease,transform .24s cubic-bezier(.16,1,.3,1),background .24s ease,box-shadow .24s ease}.zh__chip::before{content:"";position:absolute;inset:0;background:linear-gradient(100deg,rgba(142,200,247,.18),rgba(28,71,122,.18),transparent 70%);opacity:0;transition:opacity .24s ease;pointer-events:none}.zh__chip b,.zh__chip span{position:relative;z-index:1}.zh__chip b{color:var(--acc-ice);font-weight:800;font-size:10px;letter-spacing:.06em}.zh__chip:hover{border-color:rgba(142,200,247,.5);color:#fff;background:rgba(28,71,122,.18);transform:translateY(-3px);box-shadow:0 14px 32px rgba(0,0,0,.22)}.zh__chip:hover::before{opacity:1}.zh__side{position:absolute;z-index:6;right:max(28px,calc((100% - 1700px)/2 + 28px));top:calc(50% + 144px);transform:translateY(-50%);display:flex;flex-direction:row;gap:14px;width:min(520px,42vw);opacity:0;animation:zhFade 1s ease .9s forwards}.zh__rev{position:relative;isolation:isolate;display:block;flex:1 1 0;min-width:0;min-height:152px;text-decoration:none;color:#fff;overflow:hidden;padding:16px 18px 54px;border-radius:20px;border:1px solid rgba(255,255,255,.11);background:rgba(1,4,12,.34);-webkit-backdrop-filter:blur(28px) saturate(1.42);backdrop-filter:blur(28px) saturate(1.42);box-shadow:0 22px 58px rgba(0,0,0,.34),inset 0 1px 0 rgba(255,255,255,.12),inset 0 -1px 0 rgba(255,255,255,.035);transition:transform .32s cubic-bezier(.16,1,.3,1),border-color .32s,background .32s ease;transform:translateZ(0)}.zh__rev::before{content:"";position:absolute;inset:0;z-index:0;border-radius:inherit;background:linear-gradient(to bottom,rgba(255,255,255,.055),rgba(255,255,255,.012) 42%,rgba(1,4,12,.12));pointer-events:none}.zh__rev::after{content:"";position:absolute;inset:0;z-index:1;border-radius:inherit;padding:1px;background:linear-gradient(180deg,rgba(255,255,255,.18),rgba(142,200,247,.075) 48%,rgba(255,255,255,.035));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:.78;pointer-events:none}.zh__rev>*{position:relative;z-index:2}.zh__rev:hover{transform:translateY(-4px) translateZ(0);border-color:rgba(142,200,247,.26);background:rgba(1,4,12,.42)}.zh__rl{display:block;font-size:9.5px;font-weight:800;letter-spacing:.13em;text-transform:uppercase;color:var(--mut2)}.zh__sc{display:flex;align-items:baseline;gap:8px;margin:12px 0 10px}.zh__sc strong{font-size:30px;font-weight:780;letter-spacing:-.04em;line-height:1}.zh__sc em{font-style:normal;color:var(--gold);font-size:13px;letter-spacing:1.5px}.zh__rt{font-size:14px;font-weight:700;color:#fff;max-width:58%;line-height:1.32;margin-bottom:0}.zh__rt span{display:block;font-size:13px;font-weight:500;color:var(--mut2);margin-top:6px;line-height:1.34}.zh__rlogo{position:absolute;right:18px;bottom:18px;height:18px}.zh__rlogo img{height:100%;width:auto;object-fit:contain;opacity:.92}.zh__strip{position:absolute;left:0;right:0;bottom:0;z-index:6;border-top:1px solid var(--line);background:linear-gradient(to top,rgba(1,4,12,.74),transparent);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px)}.zh__stripIn{width:min(1700px,calc(100% - 96px));margin:0 auto;display:flex;align-items:center;gap:26px;height:78px}.zh__stats{display:flex;align-items:center;gap:26px;flex:none}.zh__stat{display:flex;align-items:baseline;gap:7px;white-space:nowrap}.zh__stat b{font-size:20px;font-weight:800;letter-spacing:-.03em;color:#fff;font-variant-numeric:tabular-nums}.zh__stat i{font-style:normal;color:var(--gold);font-size:12px;margin-left:2px}.zh__stat span{font-size:12px;font-weight:600;color:var(--mut2)}.zh__sep{width:1px;height:28px;background:var(--line);flex:none}.zh__marq{position:relative;flex:1;overflow:hidden;-webkit-mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent);mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent)}.zh__marqTrack{display:flex;align-items:center;width:max-content;animation:zhMarq 28s linear infinite;opacity:.55}.zh__marqGroup{display:flex;align-items:center;gap:42px;padding-right:42px;flex:0 0 auto}.zh__marqTrack img{height:22px;width:auto;object-fit:contain;filter:grayscale(1) brightness(1.6) opacity(.85);transform:scale(1);transition:filter .28s ease,transform .28s ease,opacity .28s ease}.zh__marqTrack img:hover{filter:grayscale(1) brightness(2.65) contrast(1.12) opacity(1);opacity:1;transform:scale(1.1)}@keyframes zhMarq{to{transform:translateX(-50%)}}.zh__mobileStrip{display:none}.zh__mobileScroll{display:none}.zh__scroll{position:absolute;left:50%;bottom:96px;z-index:6;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:8px;color:var(--mut2);font-size:9.5px;font-weight:700;letter-spacing:.26em;text-transform:uppercase;opacity:0;animation:zhFade 1s ease 1.3s forwards}.zh__mouse{position:relative;width:22px;height:34px;border:1.5px solid rgba(255,255,255,.28);border-radius:999px}.zh__mouse::after{content:"";position:absolute;left:50%;top:7px;width:3px;height:7px;border-radius:999px;background:var(--acc-ice);transform:translateX(-50%);animation:zhWheel 1.8s ease-in-out infinite}@keyframes zhWheel{0%,100%{opacity:0;transform:translate(-50%,0)}40%{opacity:1}70%{opacity:0;transform:translate(-50%,9px)}}.zh__wm{position:absolute;left:max(48px,calc((100% - 1700px)/2));right:auto;top:auto;bottom:78px;z-index:5;width:min(1120px,calc(100% - 96px));transform:none;font-size:clamp(76px,8.65vw,158px);font-weight:800;letter-spacing:-.078em;line-height:.72;pointer-events:none;text-transform:lowercase;white-space:nowrap;text-align:left;overflow:hidden;color:rgba(255,255,255,.062);opacity:1;clip-path:inset(0 100% 0 0);animation:zhWmReveal 1.05s steps(13,end) .65s forwards;-webkit-mask-image:linear-gradient(90deg,transparent 0%,#000 8%,#000 86%,transparent 100%);mask-image:linear-gradient(90deg,transparent 0%,#000 8%,#000 86%,transparent 100%);text-shadow:none;mix-blend-mode:normal}.zh__wm::after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:68%;z-index:2;pointer-events:none;background:linear-gradient(to bottom,rgba(1,3,9,0) 0%,rgba(1,3,9,.34) 44%,rgba(1,3,9,.72) 76%,#010309 100%)}@keyframes zhWmReveal{to{clip-path:inset(0 0 0 0)}}@media(max-height:820px) and (min-width:1101px){.zh__rail{top:96px}}@media(max-width:1400px) and (min-width:1101px){.zh__rail{top:92px;right:18px}.zh__railIco{gap:13px}.zh__railProg{height:78px}}@media(max-width:1100px){.zh{min-height:auto}.zh__rail{display:none}.zh__stage{width:122vw;right:calc(-48vw - 120px);left:auto;inset-block:0;opacity:.68;transform:scaleX(-1);transform-origin:center center}.zh__poster{background-position:center calc(50% + 18px);background-size:auto 72vh}.zh__fade{background:linear-gradient(90deg,#010309 0%,rgba(1,3,9,.86) 22%,rgba(1,3,9,.42) 48%,transparent 78%),linear-gradient(to bottom,transparent 50%,#010309 98%)}.zh__kin{width:118%;top:30%;opacity:.5}.zh__inner{align-items:flex-start;min-height:auto;padding:138px 0 36px;width:calc(100% - 36px)}.zh__side{position:relative;z-index:6;right:auto;top:auto;transform:none;flex-direction:row;width:calc(100% - 36px);max-width:none;margin:34px auto 36px;gap:12px}.zh__rev{min-height:150px;padding:14px 14px 50px}.zh__rt{max-width:none;margin-bottom:0}.zh__strip,.zh__scroll{display:none}.zh__title{font-size:clamp(40px,9vw,72px);max-width:none}.zh__wm{position:relative;display:block;left:auto;right:auto;top:auto;bottom:auto;z-index:6;width:calc(100% - 36px);margin:0 auto 0;font-size:clamp(50px,18vw,108px);line-height:.7;letter-spacing:-.078em;opacity:1;color:rgba(255,255,255,.068);-webkit-mask-image:linear-gradient(90deg,transparent 0%,#000 12%,#000 82%,transparent 100%);mask-image:linear-gradient(90deg,transparent 0%,#000 12%,#000 82%,transparent 100%);text-shadow:none;mix-blend-mode:normal}.zh__wm::after{height:62%;background:linear-gradient(to bottom,rgba(1,3,9,0) 0%,rgba(1,3,9,.26) 48%,rgba(1,3,9,.58) 78%,#010309 100%)}.zh__mobileStrip{display:block;position:relative;z-index:6;width:calc(100% - 36px);margin:0 auto 6px;padding:18px 0 4px;border-top:1px solid rgba(255,255,255,.1);border-bottom:0;overflow:hidden;-webkit-mask-image:linear-gradient(90deg,transparent,#000 9%,#000 91%,transparent);mask-image:linear-gradient(90deg,transparent,#000 9%,#000 91%,transparent)}.zh__mobileStrip .zh__marqTrack{animation-duration:24s;opacity:.58}.zh__mobileStrip .zh__marqGroup{gap:34px;padding-right:34px}.zh__mobileStrip img{height:20px;filter:grayscale(1) brightness(1.72) opacity(.86)}.zh__mobileScroll{position:relative;z-index:6;display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 36px);margin:14px auto 38px;color:rgba(255,255,255,.42);font-size:9px;font-weight:800;letter-spacing:.24em;text-transform:uppercase;overflow:hidden}.zh__mobileScroll::before,.zh__mobileScroll::after{content:"";height:1px;flex:1;background:linear-gradient(90deg,transparent,rgba(142,200,247,.34),rgba(255,255,255,.08));opacity:.72}.zh__mobileScroll::after{background:linear-gradient(90deg,rgba(255,255,255,.08),rgba(142,200,247,.34),transparent)}.zh__mobileScroll .zh__mouse{width:18px;height:28px;border-color:rgba(255,255,255,.24);flex:0 0 auto}.zh__mobileScroll .zh__mouse::after{top:6px;height:6px}}@media(max-width:600px){.zh__wm{margin:2px auto 0;font-size:clamp(46px,18.5vw,94px);color:rgba(255,255,255,.068)}.zh__eb{font-size:9px}.zh__inner{padding-top:120px}.zh__lead{font-size:15px}.zh__kinWord{font-size:64vw}.zh__chips{display:none!important}.zh__rev{padding:13px 13px 50px}.zh__sc strong{font-size:26px}}@media(max-width:600px){.zh__stage{width:136vw;right:calc(-68vw - 120px);opacity:.66}.zh__poster{background-position:center calc(50% + 14px);background-size:auto 70vh}}@media(prefers-reduced-motion:reduce){.zh__aurora,.zh__rotor>span,.zh__kinWord,.zh__ring--a,.zh__ring--b,.zh__halo,.zh__conic,.zh__eb .dot::after,.zh__dust i,.zh__marqTrack,.zh__mouse::after,.zh__grad{animation:none!important}.zh__title,.zh__lead,.zh__actions,.zh__chips,.zh__side,.zh__scroll{animation:none!important;opacity:1!important;transform:none!important;filter:none!important}.zh__rotor{height:auto}.zh__rotor>span{transform:none}.zh__grad{background-position:14% 0}.zh__wm{animation:none!important;clip-path:inset(0 0 0 0)!important}}
.zh--logoBranding .zh__title{max-width:13.8ch}.zh--logoBranding .zh__lead{max-width:680px}.zh--logoBranding .zh__kin{opacity:.72}.zh__stage--brand{right:-3vw;width:64vw;min-width:900px;pointer-events:none}.zh__brandMockWrap{position:absolute;inset:0;z-index:2;display:grid;place-items:center;pointer-events:none;transform:translate3d(2vw,28px,0) rotate(-2deg)}.zh__brandMock{width:min(78%,760px);height:auto;display:block;filter:saturate(1.03) brightness(1.02) drop-shadow(0 44px 82px rgba(0,0,0,.58));animation:zhBrandFloat 8s ease-in-out infinite}.zh__brandMini{position:absolute;z-index:5;left:14%;bottom:19%;width:min(330px,38vw);padding:18px;border-radius:22px;border:1px solid rgba(255,255,255,.14);background:rgba(1,4,12,.40);-webkit-backdrop-filter:blur(24px) saturate(1.35);backdrop-filter:blur(24px) saturate(1.35);box-shadow:0 22px 58px rgba(0,0,0,.36),inset 0 1px 0 rgba(255,255,255,.12);color:#fff}.zh__brandMini span{display:block;margin-bottom:10px;color:var(--mut2);font-size:9.5px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}.zh__brandMini strong{display:block;font-size:25px;line-height:1.04;letter-spacing:-.04em}.zh__brandMini em{font-style:normal;color:var(--acc-ice)}.zh__brandMini p{margin:10px 0 0;color:var(--mut);font-size:12.5px;line-height:1.48}.zh__brandPill{position:absolute;z-index:5;display:inline-flex;align-items:center;min-height:38px;padding:0 15px;border-radius:999px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08);-webkit-backdrop-filter:blur(16px) saturate(1.25);backdrop-filter:blur(16px) saturate(1.25);color:#fff;font-size:12px;font-weight:800;letter-spacing:-.02em;box-shadow:0 16px 40px rgba(0,0,0,.26)}.zh__brandPill--a{right:19%;top:30%}.zh__brandPill--b{right:9%;top:48%}.zh__brandPill--c{left:22%;top:37%}@keyframes zhBrandFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}@media(max-width:1100px){.zh--logoBranding .zh__stage{width:124vw;right:calc(-50vw - 18px);opacity:.72;transform:none!important}.zh__brandMockWrap{transform:translate3d(12vw,42px,0) rotate(-2deg)}.zh__brandMock{width:min(94%,620px)}.zh__brandMini{left:4%;bottom:16%;width:min(340px,86vw);padding:17px}.zh__brandPill{font-size:11px;min-height:34px;padding:0 12px}.zh__brandPill--a{right:2%;top:24%}.zh__brandPill--b{right:4%;top:43%}.zh__brandPill--c{left:3%;top:34%}}@media(max-width:600px){.zh--logoBranding .zh__stage{width:148vw;right:calc(-80vw - 10px);opacity:.70}.zh__brandMockWrap{transform:translate3d(18vw,64px,0) rotate(-2deg)}.zh__brandMock{width:min(98%,540px)}.zh__brandMini{bottom:11%;left:7%;width:min(320px,86vw)}.zh__brandPill--a{right:0;top:22%}.zh__brandPill--b{right:0;top:39%}.zh__brandPill--c{left:5%;top:31%}}
.zh--logoBranding .zh__side{display:none!important}
.zh--logoBranding .zh__brandMock{width:min(101%,988px)}
.zh__mobileBrandMock{display:none}
@media(min-width:1101px){.zh--logoBranding .zh__stage--brand{width:72vw;right:-6vw}}
@media(max-width:1100px){.zh--logoBranding .zh__stage--brand{display:none!important}.zh__mobileBrandMock{display:block;position:relative;z-index:7;width:min(620px,112vw);margin:28px 0 4px;pointer-events:none;transform:translateX(7vw) rotate(-2deg)}.zh__mobileBrandMock img{display:block;width:100%;height:auto;filter:saturate(1.03) brightness(1.02) drop-shadow(0 34px 70px rgba(0,0,0,.54));animation:zhBrandFloat 8s ease-in-out infinite}.zh--logoBranding .zh__inner{padding-bottom:28px}.zh--logoBranding .zh__mobileStrip{margin-top:0}}
@media(max-width:600px){.zh__mobileBrandMock{width:128vw;margin:24px 0 2px;transform:translateX(9vw) rotate(-2deg)}}
.zh--logoBranding .zh__title .zh__grad{font-weight:760}.zh--logoBranding .zh__copy>.zh__chips{display:none!important}@media(min-width:1101px){.zh--logoBranding .zh__wm{display:none!important}}
.zh__brandMini{z-index:8!important;width:min(390px,32vw)!important;padding:22px 24px 24px!important;border-color:rgba(142,200,247,.28)!important;background:rgba(2,8,18,.58)!important;-webkit-backdrop-filter:blur(28px) saturate(1.38)!important;backdrop-filter:blur(28px) saturate(1.38)!important;box-shadow:0 28px 80px rgba(0,0,0,.48),inset 0 1px 0 rgba(255,255,255,.13),inset 0 -1px 0 rgba(142,200,247,.08)!important}.zh__brandMini:before{content:"";position:absolute;inset:0;border-radius:inherit;background:linear-gradient(180deg,rgba(255,255,255,.07),rgba(255,255,255,.018) 48%,rgba(1,4,12,.08));pointer-events:none}.zh__brandMini>*{position:relative;z-index:1}.zh__brandMini span{font-size:10px!important;color:rgba(142,200,247,.92)!important}.zh__brandMini strong{font-size:clamp(22px,1.7vw,31px)!important}.zh__brandMini p{font-size:13.5px!important;color:rgba(255,255,255,.72)!important}.zh__photoChips{position:absolute;inset:0;z-index:7;pointer-events:none}.zh__photoChip{position:absolute;display:inline-flex;align-items:center;gap:9px;min-height:42px;padding:0 16px;border-radius:999px;border:1px solid rgba(255,255,255,.18);background:rgba(1,4,12,.38);-webkit-backdrop-filter:blur(18px) saturate(1.28);backdrop-filter:blur(18px) saturate(1.28);color:rgba(255,255,255,.86);font-size:12px;font-weight:800;letter-spacing:-.02em;text-decoration:none;box-shadow:0 18px 50px rgba(0,0,0,.32),inset 0 1px 0 rgba(255,255,255,.1);pointer-events:auto;animation:zhPhotoChipFloat 7.5s ease-in-out infinite;transition:transform .26s cubic-bezier(.16,1,.3,1),border-color .24s ease,background .24s ease,color .24s ease,box-shadow .24s ease}.zh__photoChip svg{width:15px;height:15px;stroke-width:2.15;color:var(--acc-ice);flex:0 0 auto}.zh__photoChip:hover{color:#fff;border-color:rgba(142,200,247,.42);background:rgba(8,22,44,.52);box-shadow:0 24px 64px rgba(0,0,0,.42),0 0 0 1px rgba(142,200,247,.08) inset}.zh__photoChip--a{right:21%;top:25%;animation-delay:-.2s}.zh__photoChip--b{right:4%;top:42%;animation-delay:-1.4s}.zh__photoChip--c{left:17%;top:31%;animation-delay:-2.1s}.zh__photoChip--d{left:10%;bottom:27%;animation-delay:-3s}.zh__photoChip--e{right:12%;bottom:20%;animation-delay:-4.2s}@keyframes zhPhotoChipFloat{0%,100%{translate:0 0}50%{translate:0 -12px}}
@media(max-width:1100px){.zh__photoChips{display:none}.zh--logoBranding .zh__mobileBrandMock{display:block;width:min(620px,100%);max-width:100%;margin:28px auto 6px!important;transform:rotate(-2deg)!important}.zh--logoBranding .zh__mobileBrandMock img{width:100%;margin:0 auto}.zh__brandMini{width:min(340px,86vw)!important}}
@media(max-width:600px){.zh--logoBranding .zh__mobileBrandMock{width:min(500px,100%);margin:26px auto 4px!important;transform:rotate(-2deg)!important}.zh--logoBranding .zh__inner{width:calc(100% - 36px)}}
.zh__photoChip{z-index:8;white-space:nowrap}
.zh__photoChip--a{right:27%;top:27%}
.zh__photoChip--b{right:22%;top:45%}
.zh__photoChip--c{left:28%;top:33%}
.zh__photoChip--d{left:25%;bottom:25%}
.zh__photoChip--e{right:24%;bottom:20%}
@media(min-width:1101px) and (max-width:1380px){.zh__photoChip{font-size:11px;min-height:38px;padding:0 13px}.zh__photoChip--a{right:25%;top:27%}.zh__photoChip--b{right:18%;top:45%}.zh__photoChip--c{left:26%;top:33%}.zh__photoChip--d{left:23%;bottom:26%}.zh__photoChip--e{right:20%;bottom:20%}}
@media(max-width:1100px){.zh--logoBranding .zh__mobileBrandMock{display:block!important;position:relative;z-index:7;width:min(620px,100%);max-width:100%;margin:28px auto 6px!important;transform:rotate(-2deg)!important;pointer-events:none}.zh--logoBranding .zh__mobileBrandMock img{display:block;width:100%;height:auto;margin:0 auto;filter:saturate(1.03) brightness(1.02) drop-shadow(0 34px 70px rgba(0,0,0,.54));animation:zhBrandFloat 8s ease-in-out infinite}}
@media(max-width:600px){.zh--logoBranding .zh__mobileBrandMock{width:min(500px,100%);margin:26px auto 4px!important;transform:rotate(-2deg)!important}}
.zh--logoBranding .zh__title{overflow:visible;line-height:1.01;padding-bottom:.06em;margin-bottom:-.06em}
.zh--logoBranding .zh__title .zh__grad{display:inline-block;overflow:visible;padding:.02em .055em .08em;margin:-.02em -.055em -.08em;line-height:1.04;-webkit-box-decoration-break:clone;box-decoration-break:clone}
@media(min-width:1101px){
.zh--logoBranding .zh__brandMock{width:min(131%,1284px)!important}
.zh--logoBranding .zh__brandMockWrap{transform:translate3d(2vw,28px,0) rotate(-2deg) scale(1.08);transform-origin:center center}
.zh--logoBranding .zh__brandMini{left:auto!important;right:13%!important;bottom:13%!important;width:min(380px,30vw)!important;background:rgba(5,8,15,.62)!important;border-color:rgba(255,255,255,.18)!important;box-shadow:0 30px 84px rgba(0,0,0,.54),inset 0 1px 0 rgba(255,255,255,.14)!important}
.zh--logoBranding .zh__brandMini span{color:rgba(255,255,255,.46)!important}
.zh--logoBranding .zh__brandMini em{color:#fff!important}
.zh--logoBranding .zh__photoChip--a{right:29%;top:23%}
.zh--logoBranding .zh__photoChip--b{right:12%;top:40%}
.zh--logoBranding .zh__photoChip--c{left:28%;top:31%}
.zh--logoBranding .zh__photoChip--d{left:24%;bottom:27%}
.zh--logoBranding .zh__photoChip--e{right:20%;bottom:20%}
}
.zh__photoChips--mobile{display:none}
@media(max-width:1100px){
.zh__mobileBrandMock{position:relative;overflow:visible;isolation:isolate}
.zh__mobileBrandMock .zh__photoChips--mobile{display:block;position:absolute;inset:0;z-index:3;pointer-events:none}
.zh__mobileBrandMock .zh__photoChip{display:inline-flex;min-height:35px;padding:0 12px;gap:7px;font-size:10.5px;border-color:rgba(255,255,255,.16);background:rgba(1,4,12,.46);-webkit-backdrop-filter:blur(16px) saturate(1.24);backdrop-filter:blur(16px) saturate(1.24);box-shadow:0 14px 38px rgba(0,0,0,.32),inset 0 1px 0 rgba(255,255,255,.1)}
.zh__mobileBrandMock .zh__photoChip svg{width:13px;height:13px;color:rgba(142,200,247,.86)}
.zh__mobileBrandMock .zh__photoChip--a{left:12%;top:18%}
.zh__mobileBrandMock .zh__photoChip--b{right:7%;top:35%}
.zh__mobileBrandMock .zh__photoChip--c{left:4%;top:49%}
.zh__mobileBrandMock .zh__photoChip--d{right:10%;bottom:19%}
.zh__mobileBrandMock .zh__photoChip--e{left:16%;bottom:10%}
}
@media(max-width:600px){
.zh__mobileBrandMock{width:128vw;margin:24px 0 18px;transform:translateX(-14vw) rotate(-2deg)}
.zh__mobileBrandMock .zh__photoChip{min-height:32px;padding:0 10px;font-size:9.5px}
.zh__mobileBrandMock .zh__photoChip--a{left:16%;top:15%}
.zh__mobileBrandMock .zh__photoChip--b{right:12%;top:32%}
.zh__mobileBrandMock .zh__photoChip--c{left:9%;top:48%}
.zh__mobileBrandMock .zh__photoChip--d{right:14%;bottom:20%}
.zh__mobileBrandMock .zh__photoChip--e{left:20%;bottom:9%}
}
@media(min-width:1101px) and (max-width:1500px){
.zh--logoBranding .zh__inner{width:min(1500px,calc(100% - 72px))!important}
.zh--logoBranding .zh__title{font-size:clamp(64px,5.85vw,94px)!important;max-width:12.2ch!important;line-height:1.01!important}
.zh--logoBranding .zh__lead{max-width:590px!important;font-size:16.5px!important;line-height:1.66!important}
.zh--logoBranding .zh__stage--brand{width:61vw!important;right:-8vw!important}
.zh--logoBranding .zh__brandMock{width:min(112%,980px)!important}
.zh--logoBranding .zh__brandMockWrap{transform:translate3d(1vw,34px,0) rotate(-2deg) scale(.98)!important}
.zh--logoBranding .zh__brandMini{right:9%!important;bottom:15%!important;width:min(340px,29vw)!important;padding:18px 20px 20px!important}
.zh--logoBranding .zh__brandMini strong{font-size:clamp(19px,1.45vw,25px)!important}
.zh--logoBranding .zh__brandMini p{font-size:12.5px!important;line-height:1.48!important}
.zh--logoBranding .zh__photoChip{font-size:11px!important;min-height:38px!important;padding:0 13px!important}
.zh--logoBranding .zh__photoChip--a{right:25%!important;top:24%!important;left:auto!important}
.zh--logoBranding .zh__photoChip--b{right:10%!important;top:40%!important;left:auto!important}
.zh--logoBranding .zh__photoChip--c{left:29%!important;top:32%!important;right:auto!important}
.zh--logoBranding .zh__photoChip--d{left:25%!important;right:auto!important;bottom:29%!important}
.zh--logoBranding .zh__photoChip--e{right:19%!important;left:auto!important;bottom:21%!important}
}
@media(min-width:1101px) and (max-width:1260px){
.zh--logoBranding .zh__title{font-size:clamp(58px,5.45vw,76px)!important;max-width:12.6ch!important}
.zh--logoBranding .zh__lead{max-width:540px!important;font-size:15.8px!important}
.zh--logoBranding .zh__stage--brand{width:58vw!important;right:-10vw!important;opacity:.92!important}
.zh--logoBranding .zh__brandMock{width:min(104%,840px)!important}
.zh--logoBranding .zh__brandMockWrap{transform:translate3d(2vw,42px,0) rotate(-2deg) scale(.93)!important}
}
@media(max-width:1100px){
.zh--logoBranding .zh__title{font-size:clamp(42px,8.2vw,68px)!important;max-width:13.2ch!important;line-height:1.02!important}
.zh--logoBranding .zh__lead{font-size:clamp(14.8px,2vw,16.5px)!important;line-height:1.64!important;max-width:680px!important}
.zh--logoBranding .zh__mobileBrandMock{width:min(620px,94vw)!important;max-width:94vw!important;margin:28px auto 12px!important;transform:rotate(-2deg)!important;overflow:visible!important}
.zh__mobileBrandMock .zh__photoChip{width:auto!important;max-width:48vw!important;min-width:0!important;white-space:nowrap!important;overflow:hidden!important;text-overflow:ellipsis!important;min-height:33px!important;padding:0 11px!important;font-size:10.2px!important;line-height:1!important}
.zh__mobileBrandMock .zh__photoChip svg{width:12px!important;height:12px!important;min-width:12px!important}
.zh__mobileBrandMock .zh__photoChip--a{left:10%!important;right:auto!important;top:14%!important}
.zh__mobileBrandMock .zh__photoChip--b{left:auto!important;right:6%!important;top:33%!important}
.zh__mobileBrandMock .zh__photoChip--c{left:5%!important;right:auto!important;top:48%!important}
.zh__mobileBrandMock .zh__photoChip--d{left:auto!important;right:8%!important;bottom:20%!important}
.zh__mobileBrandMock .zh__photoChip--e{left:14%!important;right:auto!important;bottom:9%!important}
}
@media(max-width:600px){
.zh--logoBranding .zh__title{font-size:clamp(38px,11.2vw,52px)!important;max-width:12.9ch!important;line-height:1.03!important}
.zh--logoBranding .zh__lead{font-size:14.8px!important;line-height:1.62!important}
.zh--logoBranding .zh__mobileBrandMock{width:min(500px,96vw)!important;max-width:96vw!important;margin:24px auto 14px!important;transform:rotate(-2deg)!important}
.zh__mobileBrandMock .zh__photoChip{max-width:50vw!important;min-height:30px!important;padding:0 9px!important;font-size:9.2px!important;gap:6px!important}
.zh__mobileBrandMock .zh__photoChip--a{left:8%!important;right:auto!important;top:13%!important}
.zh__mobileBrandMock .zh__photoChip--b{left:auto!important;right:5%!important;top:31%!important}
.zh__mobileBrandMock .zh__photoChip--c{left:4%!important;right:auto!important;top:47%!important}
.zh__mobileBrandMock .zh__photoChip--d{left:auto!important;right:7%!important;bottom:19%!important}
.zh__mobileBrandMock .zh__photoChip--e{left:12%!important;right:auto!important;bottom:8%!important}
}
@media(max-width:390px){
.zh--logoBranding .zh__title{font-size:clamp(34px,10.7vw,42px)!important}
.zh__mobileBrandMock .zh__photoChip{font-size:8.7px!important;max-width:46vw!important}
}
.zh--logoBranding .zh__stage--brand{transform:none!important}
.zh--logoBranding .zh__brandMockWrap{transform:translate3d(2vw,28px,0) scale(1.08)!important}
.zh--logoBranding .zh__brandMock{animation:zhBrandFloatSoft 8s ease-in-out infinite!important}
.zh--logoBranding .zh__brandMini,.zh--logoBranding .zh__brandMini:hover{opacity:1!important;filter:none!important}
.zh--logoBranding .zh__brandMini:hover{background:rgba(5,8,15,.62)!important;border-color:rgba(255,255,255,.18)!important;box-shadow:0 30px 84px rgba(0,0,0,.54),inset 0 1px 0 rgba(255,255,255,.14)!important}
@keyframes zhBrandFloatSoft{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(0,-10px,0)}}
@media(max-width:1500px){.zh--logoBranding .zh__brandMockWrap{transform:translate3d(1vw,34px,0) scale(.98)!important}}
@media(max-width:1260px){.zh--logoBranding .zh__brandMockWrap{transform:translate3d(2vw,42px,0) scale(.93)!important}}
@media(max-width:1100px){.zh--logoBranding .zh__stage--brand{transform:none!important}.zh--logoBranding .zh__brandMockWrap{transform:translate3d(0,34px,0) scale(.98)!important}.zh--logoBranding .zh__mobileBrandMock{transform:none!important}}
@media(max-width:600px){.zh--logoBranding .zh__brandMockWrap{transform:translate3d(0,46px,0) scale(.94)!important}.zh--logoBranding .zh__mobileBrandMock{transform:none!important}}
.zh--logoBranding .zh__title{overflow:visible!important;padding-bottom:.12em!important;margin-bottom:-.12em!important;line-height:1.01!important}
.zh--logoBranding .zh__title .zh__grad{display:inline-block!important;overflow:visible!important;line-height:1.18!important;padding:.01em .065em .22em!important;margin:-.01em -.065em -.22em!important;vertical-align:baseline!important;-webkit-box-decoration-break:clone;box-decoration-break:clone}
.zh--logoBranding .zh__brandMini,.zh--logoBranding .zh__brandMini:hover,.zh--logoBranding .zh__brandMini:focus-within{opacity:1!important;filter:none!important;mix-blend-mode:normal!important;transform:none!important;background:rgba(5,8,15,.62)!important;border-color:rgba(255,255,255,.18)!important;box-shadow:0 30px 84px rgba(0,0,0,.54),inset 0 1px 0 rgba(255,255,255,.14)!important}
.zh--logoBranding .zh__brandMini:hover:before,.zh--logoBranding .zh__brandMini:focus-within:before{opacity:1!important;filter:none!important;background:linear-gradient(180deg,rgba(255,255,255,.07),rgba(255,255,255,.018) 48%,rgba(1,4,12,.08))!important}
.zh--logoBranding .zh__brandMini:hover *,.zh--logoBranding .zh__brandMini:focus-within *{opacity:1!important;filter:none!important}
.zh--logoBranding .zh__brandMini span,.zh--logoBranding .zh__brandMini:hover span{color:rgba(255,255,255,.46)!important}
.zh--logoBranding .zh__brandMini p,.zh--logoBranding .zh__brandMini:hover p{color:rgba(255,255,255,.72)!important}
.zh--logoBranding .zh__brandMini em,.zh--logoBranding .zh__brandMini:hover em{color:#fff!important}
@media(max-width:600px){.zh--logoBranding .zh__title{line-height:1.03!important;padding-bottom:.14em!important;margin-bottom:-.14em!important}.zh--logoBranding .zh__title .zh__grad{line-height:1.2!important;padding-bottom:.24em!important;margin-bottom:-.24em!important}}
@media(max-width:1100px){
.zh--logoBranding{overflow:hidden!important;}
.zh--logoBranding .zh__inner{width:calc(100% - 36px)!important;margin-left:auto!important;margin-right:auto!important;padding-left:0!important;padding-right:0!important;}
.zh--logoBranding .zh__copy{width:100%!important;max-width:100%!important;}
.zh--logoBranding .zh__mobileBrandMock{width:min(682px,96vw)!important;max-width:calc(100vw - 36px)!important;margin-left:auto!important;margin-right:auto!important;left:auto!important;right:auto!important;transform:none!important;}
.zh--logoBranding .zh__mobileBrandMock img,.zh--logoBranding .zh__mobileBrandMock .zh__brandMock{width:110%!important;max-width:110%!important;margin-left:50%!important;transform:translateX(-50%)!important;}
.zh--logoBranding .zh__mobileBrandMock .zh__photoChip{max-width:44vw!important;}
}
@media(max-width:600px){
.zh--logoBranding .zh__inner{width:calc(100% - 36px)!important;}
.zh--logoBranding .zh__mobileBrandMock{width:min(550px,96vw)!important;max-width:calc(100vw - 36px)!important;margin-left:auto!important;margin-right:auto!important;}
.zh--logoBranding .zh__mobileBrandMock img,.zh--logoBranding .zh__mobileBrandMock .zh__brandMock{width:110%!important;max-width:110%!important;margin-left:50%!important;transform:translateX(-50%)!important;}
}
@media(max-width:390px){
.zh--logoBranding .zh__mobileBrandMock{max-width:calc(100vw - 32px)!important;}
.zh--logoBranding .zh__inner{width:calc(100% - 32px)!important;}
}
.zh--logoBranding .zh__title{overflow:visible!important;padding-bottom:.2em!important;margin-bottom:-.14em!important;}
.zh--logoBranding .zh__title .zh__grad{display:inline-block!important;overflow:visible!important;line-height:1.28!important;padding:.01em .08em .32em!important;margin:-.01em -.08em -.28em!important;vertical-align:baseline!important;}
@media(max-width:600px){.zh__btn{font-size:14px}.zh__btn span{white-space:nowrap}}
@media(max-width:390px){.zh__btn{font-size:13.5px;padding:0 22px;gap:8px}.zh__btn svg{width:16px;height:16px}}
@media(max-width:1100px){
.zh--logoBranding .zh__mobileBrandMock{margin-bottom:22px!important;}
.zh--logoBranding .zh__mobileStrip{margin-top:0!important;margin-bottom:0!important;padding-bottom:10px!important;}
.zh--logoBranding .zh__mobileScroll{
position:relative!important;
z-index:9!important;
display:flex!important;
min-height:66px!important;
width:calc(100% - 36px)!important;
margin:0 auto 38px!important;
padding:16px 0 18px!important;
align-items:center!important;
justify-content:center!important;
clear:both!important;
isolation:isolate!important;
}
}
@media(max-width:600px){
.zh--logoBranding .zh__mobileBrandMock{margin-bottom:24px!important;}
.zh--logoBranding .zh__mobileStrip{padding-top:16px!important;padding-bottom:8px!important;}
.zh--logoBranding .zh__mobileScroll{
min-height:72px!important;
margin:2px auto 42px!important;
padding:18px 0 20px!important;
}
}
@media(max-width:380px){
.zh--logoBranding .zh__mobileScroll{min-height:76px!important;margin-bottom:46px!important;}
}
@media(min-width:1101px){
.zh--logoBranding .zh__title{
font-size:clamp(46px,7vw,124px)!important;
line-height:.97!important;
letter-spacing:-.046em!important;
font-weight:480!important;
max-width:15ch!important;
padding-bottom:.08em!important;
margin-bottom:-.08em!important;
overflow:visible!important;
}
.zh--logoBranding .zh__title .zh__grad{
display:inline-block!important;
line-height:1.12!important;
padding:.01em .055em .18em!important;
margin:-.01em -.055em -.18em!important;
overflow:visible!important;
vertical-align:baseline!important;
-webkit-box-decoration-break:clone;
box-decoration-break:clone;
}
}
@media(min-width:1101px) and (max-width:1500px){
.zh--logoBranding .zh__title{font-size:clamp(46px,6.15vw,86px)!important;line-height:.99!important;max-width:14.4ch!important}
}
@media(min-width:1101px) and (max-width:1260px){
.zh--logoBranding .zh__title{font-size:clamp(44px,5.85vw,72px)!important;line-height:1!important;max-width:14.4ch!important}
}
.zh--logoBranding .zh__title{font-size:clamp(46px,7vw,114px)!important;line-height:.99!important;letter-spacing:-.046em!important;font-weight:480!important;max-width:15ch!important;overflow:visible!important;padding-bottom:.16em!important;margin-bottom:-.16em!important;text-wrap:balance!important}
.zh--logoBranding .zh__title .zh__grad{display:inline-block!important;line-height:1.2!important;padding:.01em .065em .24em!important;margin:-.01em -.065em -.24em!important;vertical-align:baseline!important;-webkit-box-decoration-break:clone;box-decoration-break:clone}
.zh--logoBranding .zh__lead{font-size:17.5px!important;line-height:1.72!important;letter-spacing:-.01em!important;max-width:600px!important;color:var(--mut)!important}
.zh__lead strong{font-weight:760;color:rgba(255,255,255,.92)}
.zh--logoBranding .zh__scroll{bottom:112px!important;gap:11px!important}
.zh--logoBranding .zh__mobileScroll{min-height:44px!important;margin:24px auto 50px!important}
@media(max-width:1500px) and (min-width:1101px){.zh--logoBranding .zh__title{font-size:clamp(46px,6.35vw,104px)!important;line-height:1!important;max-width:15ch!important}.zh--logoBranding .zh__lead{font-size:16.7px!important;line-height:1.68!important;max-width:590px!important}}
@media(max-width:1260px) and (min-width:1101px){.zh--logoBranding .zh__title{font-size:clamp(46px,5.75vw,82px)!important;line-height:1.02!important;max-width:15ch!important}.zh--logoBranding .zh__lead{font-size:15.8px!important;line-height:1.66!important;max-width:540px!important}}
@media(max-width:1100px){.zh--logoBranding .zh__title{font-size:clamp(40px,9vw,72px)!important;line-height:1.02!important;letter-spacing:-.044em!important;max-width:none!important;padding-bottom:.16em!important;margin-bottom:-.16em!important}.zh--logoBranding .zh__lead{font-size:15.5px!important;line-height:1.66!important;max-width:680px!important}.zh--logoBranding .zh__mobileScroll{margin:26px auto 52px!important;min-height:46px!important}}
@media(max-width:600px){.zh--logoBranding .zh__title{font-size:clamp(38px,10.8vw,48px)!important;line-height:1.045!important;letter-spacing:-.042em!important;max-width:none!important}.zh--logoBranding .zh__lead{font-size:15px!important;line-height:1.64!important}.zh--logoBranding .zh__mobileScroll{margin:28px auto 54px!important}}
.zh__lead strong{color:rgba(255,255,255,.92);font-weight:760}
@media(min-width:1101px){
.zh__title,.zh--logoBranding .zh__title{font-size:clamp(46px,6.75vw,114px)!important;line-height:.99!important;letter-spacing:-.046em!important;font-weight:480!important;max-width:15ch!important;overflow:visible!important;padding-bottom:.16em!important;margin-bottom:-.12em!important}
.zh__lead,.zh--logoBranding .zh__lead{font-size:17.5px!important;line-height:1.72!important;letter-spacing:-.01em!important;max-width:600px!important}
.zh__scroll{bottom:158px!important}
}
@media(max-width:1100px){
.zh__title,.zh--logoBranding .zh__title{font-size:clamp(40px,9vw,72px)!important;line-height:1.02!important;letter-spacing:-.044em!important;max-width:none!important;padding-bottom:.16em!important;margin-bottom:-.14em!important}
.zh__lead,.zh--logoBranding .zh__lead{font-size:15.5px!important;line-height:1.68!important;max-width:620px!important}
.zh__mobileScroll{min-height:40px!important;margin:60px auto 80px!important}
}
@media(max-width:600px){
.zh__title,.zh--logoBranding .zh__title{font-size:clamp(38px,10.8vw,48px)!important;line-height:1.045!important;letter-spacing:-.042em!important;max-width:none!important}
.zh__lead,.zh--logoBranding .zh__lead{font-size:15px!important;line-height:1.68!important}
.zh__mobileScroll{margin:56px auto 78px!important}
}
@media(max-width:1100px){
.zh--logoBranding .zh__mobileStrip{margin-bottom:0!important;}
.zh--logoBranding .zh__mobileScroll{display:flex!important;position:relative!important;z-index:8!important;width:calc(100% - 36px)!important;min-height:76px!important;margin:60px auto 80px!important;padding:18px 0!important;align-items:center!important;justify-content:center!important;clear:both!important;}
}
@media(max-width:600px){
.zh--logoBranding .zh__mobileScroll{min-height:78px!important;margin:60px auto 82px!important;padding:18px 0!important;}
}
@media(min-width:1101px){
.zh--logoBranding .zh__scroll{
left:0!important;right:0!important;bottom:78px!important;width:100%!important;height:138px!important;transform:none!important;
display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;gap:12px!important;
padding:60px 0 24px!important;z-index:7!important;pointer-events:none!important;
background:linear-gradient(to bottom,rgba(1,3,9,0) 0%,rgba(1,3,9,.08) 38%,rgba(1,3,9,.28) 100%)!important;
}
.zh--logoBranding .zh__strip{z-index:8!important}
}
@media(min-width:1501px) and (max-width:1680px){
.zh--logoBranding .zh__stage--brand{width:69vw!important;right:-10vw!important;opacity:.98!important}
.zh--logoBranding .zh__brandMock{width:min(124%,1120px)!important}
.zh--logoBranding .zh__brandMockWrap{transform:translate3d(1.4vw,34px,0) scale(1.02)!important}
.zh--logoBranding .zh__brandMini{right:10%!important;bottom:14%!important;width:min(350px,30vw)!important}
}
@media(min-width:1381px) and (max-width:1500px){
.zh--logoBranding .zh__stage--brand{width:67vw!important;right:-10vw!important;opacity:.96!important}
.zh--logoBranding .zh__brandMock{width:min(118%,1040px)!important}
.zh--logoBranding .zh__brandMockWrap{transform:translate3d(1.6vw,38px,0) scale(.99)!important}
.zh--logoBranding .zh__brandMini{right:9%!important;bottom:15%!important;width:min(330px,29vw)!important}
}
@media(min-width:1101px) and (max-width:1380px){
.zh--logoBranding .zh__stage--brand{width:64vw!important;right:-10vw!important;opacity:.94!important}
.zh--logoBranding .zh__brandMock{width:min(112%,920px)!important}
.zh--logoBranding .zh__brandMockWrap{transform:translate3d(2vw,42px,0) scale(.95)!important}
}
@media(min-width:1101px){
.zh--logoBranding .zh__title{
font-size:clamp(46px,6.75vw,114px)!important;
line-height:.99!important;
letter-spacing:-.046em!important;
font-weight:480!important;
max-width:15ch!important;
overflow:visible!important;
padding-bottom:.16em!important;
margin-bottom:-.12em!important;
text-wrap:balance!important;
}
.zh--logoBranding .zh__title .zh__grad{
line-height:1.08!important;
padding:.01em .055em .14em!important;
margin:-.01em -.055em -.14em!important;
}
}
@media(max-width:1100px){
.zh--logoBranding .zh__wm{
margin-bottom:0!important;
}
.zh--logoBranding .zh__mobileScroll{
display:flex!important;
width:calc(100% - 36px)!important;
margin:6px auto 22px!important;
min-height:34px!important;
padding:0!important;
border:0!important;
background:transparent!important;
-webkit-backdrop-filter:none!important;
backdrop-filter:none!important;
clear:both!important;
}
}
@media(max-width:600px){
.zh--logoBranding .zh__mobileScroll{
margin:4px auto 20px!important;
min-height:32px!important;
}
}
@media(min-width:1101px){
.zh--logoBranding .zh__actions{
margin-bottom:100px!important;
}
.zh--logoBranding .zh__inner{
padding-bottom:178px!important;
}
}
@media(min-width:1101px) and (max-width:1450px){
.zh--logoBranding .zh__copy{
max-width:760px!important;
}
.zh--logoBranding .zh__title{
font-size:clamp(58px,5.45vw,78px)!important;
line-height:.99!important;
letter-spacing:-.046em!important;
max-width:12.9ch!important;
padding-bottom:.14em!important;
margin-bottom:-.10em!important;
}
.zh--logoBranding .zh__lead{
max-width:520px!important;
font-size:15.8px!important;
line-height:1.66!important;
}
.zh--logoBranding .zh__actions{
margin-top:30px!important;
margin-bottom:100px!important;
}
.zh--logoBranding .zh__stage--brand{
width:61vw!important;
right:-12vw!important;
opacity:.95!important;
}
.zh--logoBranding .zh__brandMock{
width:min(108%,900px)!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(3vw,42px,0) scale(.94)!important;
}
.zh--logoBranding .zh__brandMini{
right:5%!important;
bottom:18%!important;
width:min(300px,25vw)!important;
}
.zh--logoBranding .zh__photoChip{
transform:scale(.94);
transform-origin:center center;
}
}
@media(min-width:1101px) and (max-width:1360px){
.zh--logoBranding .zh__copy{
max-width:700px!important;
}
.zh--logoBranding .zh__title{
font-size:clamp(54px,5.2vw,70px)!important;
line-height:1!important;
max-width:12.6ch!important;
}
.zh--logoBranding .zh__lead{
max-width:500px!important;
font-size:15.2px!important;
}
.zh--logoBranding .zh__stage--brand{
width:59vw!important;
right:-14vw!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(4vw,46px,0) scale(.90)!important;
}
.zh--logoBranding .zh__brandMini{
right:3%!important;
bottom:19%!important;
width:min(280px,24vw)!important;
padding:16px 18px 18px!important;
}
}
@media(min-width:1101px) and (max-width:1450px){
.zh--logoBranding .zh__stage--brand{
width:66vw!important;
right:-8vw!important;
min-width:0!important;
opacity:.98!important;
}
.zh--logoBranding .zh__brandMock{
width:min(124%,1060px)!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(2vw,40px,0) scale(1)!important;
transform-origin:center center!important;
}
.zh--logoBranding .zh__brandMini{
right:22%!important;
bottom:15%!important;
width:min(312px,23vw)!important;
max-width:312px!important;
padding:16px 18px 18px!important;
}
.zh--logoBranding .zh__brandMini strong{font-size:clamp(18px,1.35vw,24px)!important;}
.zh--logoBranding .zh__brandMini p{font-size:12px!important;line-height:1.45!important;}
.zh--logoBranding .zh__photoChip{
font-size:10.8px!important;
min-height:36px!important;
padding:0 12px!important;
max-width:220px!important;
white-space:nowrap!important;
}
.zh--logoBranding .zh__photoChip--a{right:31%!important;top:23%!important;left:auto!important;}
.zh--logoBranding .zh__photoChip--b{right:21%!important;top:39%!important;left:auto!important;}
.zh--logoBranding .zh__photoChip--c{left:auto!important;right:54%!important;top:33%!important;}
.zh--logoBranding .zh__photoChip--d{left:auto!important;right:48%!important;bottom:28%!important;}
.zh--logoBranding .zh__photoChip--e{right:29%!important;bottom:21%!important;left:auto!important;}
}
@media(min-width:1101px) and (max-width:1360px){
.zh--logoBranding .zh__copy{max-width:690px!important;}
.zh--logoBranding .zh__title{
font-size:clamp(53px,5vw,68px)!important;
line-height:1!important;
max-width:12.4ch!important;
}
.zh--logoBranding .zh__lead{max-width:490px!important;font-size:15px!important;}
.zh--logoBranding .zh__stage--brand{
width:65vw!important;
right:-6vw!important;
}
.zh--logoBranding .zh__brandMock{
width:min(122%,960px)!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(1vw,44px,0) scale(.97)!important;
}
.zh--logoBranding .zh__brandMini{
right:24%!important;
bottom:16%!important;
width:min(282px,22vw)!important;
padding:15px 16px 16px!important;
}
.zh--logoBranding .zh__photoChip{font-size:10px!important;min-height:34px!important;padding:0 10px!important;max-width:190px!important;}
.zh--logoBranding .zh__photoChip--a{right:34%!important;top:24%!important;}
.zh--logoBranding .zh__photoChip--b{right:24%!important;top:40%!important;}
.zh--logoBranding .zh__photoChip--c{right:58%!important;top:35%!important;left:auto!important;}
.zh--logoBranding .zh__photoChip--d{right:50%!important;bottom:29%!important;left:auto!important;}
.zh--logoBranding .zh__photoChip--e{right:33%!important;bottom:22%!important;}
}
@media(min-width:1101px) and (max-width:1220px){
.zh--logoBranding .zh__stage--brand{
width:62vw!important;
right:-5vw!important;
}
.zh--logoBranding .zh__brandMock{
width:min(116%,860px)!important;
}
.zh--logoBranding .zh__brandMini{
right:26%!important;
width:min(248px,21vw)!important;
padding:14px 15px!important;
}
.zh--logoBranding .zh__brandMini strong{font-size:18px!important;}
.zh--logoBranding .zh__brandMini p{font-size:11px!important;}
.zh--logoBranding .zh__photoChip{font-size:9.5px!important;max-width:170px!important;}
.zh--logoBranding .zh__photoChip--b{right:28%!important;}
}
@media(min-width:1361px) and (max-width:1680px){
.zh--logoBranding .zh__stage--brand{
width:66vw!important;
right:-7vw!important;
min-width:0!important;
opacity:.98!important;
}
.zh--logoBranding .zh__brandMock{
width:min(121%,1080px)!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(.8vw,8px,0) scale(1.02)!important;
transform-origin:center center!important;
}
.zh--logoBranding .zh__brandMini{
right:clamp(42px,5.8vw,92px)!important;
left:auto!important;
bottom:clamp(112px,16vh,178px)!important;
width:min(340px,25vw)!important;
max-width:calc(100vw - 64px)!important;
}
.zh--logoBranding .zh__photoChip{
max-width:24vw!important;
white-space:nowrap!important;
overflow:hidden!important;
text-overflow:ellipsis!important;
}
.zh--logoBranding .zh__photoChip--a{right:clamp(108px,12vw,178px)!important;top:21%!important;left:auto!important}
.zh--logoBranding .zh__photoChip--b{right:clamp(34px,4.6vw,82px)!important;top:38%!important;left:auto!important}
.zh--logoBranding .zh__photoChip--c{left:auto!important;right:clamp(250px,28vw,430px)!important;top:31%!important}
.zh--logoBranding .zh__photoChip--d{left:auto!important;right:clamp(220px,24vw,370px)!important;bottom:30%!important}
.zh--logoBranding .zh__photoChip--e{right:clamp(90px,10vw,150px)!important;bottom:20%!important;left:auto!important}
}
@media(min-width:1101px) and (max-width:1360px){
.zh--logoBranding .zh__stage--brand{
width:62vw!important;
right:-6vw!important;
min-width:0!important;
opacity:.96!important;
}
.zh--logoBranding .zh__brandMock{
width:min(116%,930px)!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(1vw,-18px,0) scale(.99)!important;
transform-origin:center center!important;
}
.zh--logoBranding .zh__brandMini{
right:clamp(34px,4.8vw,70px)!important;
left:auto!important;
bottom:clamp(106px,15vh,150px)!important;
width:min(298px,24vw)!important;
max-width:calc(100vw - 56px)!important;
padding:15px 16px 17px!important;
}
.zh--logoBranding .zh__brandMini strong{font-size:clamp(18px,1.45vw,22px)!important}
.zh--logoBranding .zh__brandMini p{font-size:11.5px!important;line-height:1.43!important}
.zh--logoBranding .zh__photoChip{
max-width:23vw!important;
font-size:10.5px!important;
min-height:35px!important;
padding:0 11px!important;
white-space:nowrap!important;
overflow:hidden!important;
text-overflow:ellipsis!important;
}
.zh--logoBranding .zh__photoChip--a{right:clamp(82px,10vw,132px)!important;top:20%!important;left:auto!important}
.zh--logoBranding .zh__photoChip--b{right:clamp(28px,3.8vw,60px)!important;top:37%!important;left:auto!important}
.zh--logoBranding .zh__photoChip--c{left:auto!important;right:clamp(210px,24vw,310px)!important;top:30%!important}
.zh--logoBranding .zh__photoChip--d{left:auto!important;right:clamp(180px,21vw,280px)!important;bottom:31%!important}
.zh--logoBranding .zh__photoChip--e{right:clamp(70px,8vw,112px)!important;bottom:20%!important;left:auto!important}
}
@media(min-width:1101px) and (max-width:1260px){
.zh--logoBranding .zh__brandMockWrap{transform:translate3d(1.5vw,-28px,0) scale(.96)!important}
.zh--logoBranding .zh__stage--brand{width:60vw!important;right:-5vw!important}
.zh--logoBranding .zh__brandMini{bottom:132px!important;right:34px!important;width:276px!important}
.zh--logoBranding .zh__photoChip--b{right:28px!important}
.zh--logoBranding .zh__photoChip--a{right:96px!important}
}
@media(min-width:1101px) and (max-width:1680px){
.zh--logoBranding .zh__stage--brand{
right:-2vw!important;
width:66vw!important;
min-width:0!important;
max-width:980px!important;
opacity:.98!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(-1.2vw,-18px,0) scale(1.05)!important;
transform-origin:center center!important;
}
.zh--logoBranding .zh__brandMock{
width:min(124%,1120px)!important;
}
.zh--logoBranding .zh__brandMini{
right:clamp(36px,5.4vw,92px)!important;
left:auto!important;
bottom:clamp(74px,13vh,128px)!important;
width:min(318px,24vw)!important;
max-width:calc(100vw - 72px)!important;
}
.zh--logoBranding .zh__photoChip{
max-width:min(230px,22vw)!important;
overflow:hidden!important;
text-overflow:ellipsis!important;
white-space:nowrap!important;
}
.zh--logoBranding .zh__photoChip span{
overflow:hidden!important;
text-overflow:ellipsis!important;
white-space:nowrap!important;
min-width:0!important;
}
.zh--logoBranding .zh__photoChip--a{right:clamp(74px,8vw,145px)!important;left:auto!important;top:18%!important}
.zh--logoBranding .zh__photoChip--b{right:clamp(58px,5.8vw,112px)!important;left:auto!important;top:34%!important}
.zh--logoBranding .zh__photoChip--c{right:clamp(170px,28vw,410px)!important;left:auto!important;top:31%!important}
.zh--logoBranding .zh__photoChip--d{right:clamp(190px,30vw,430px)!important;left:auto!important;bottom:24%!important}
.zh--logoBranding .zh__photoChip--e{right:clamp(126px,16vw,250px)!important;left:auto!important;bottom:16%!important}
}
@media(min-width:1101px) and (max-width:1360px){
.zh--logoBranding .zh__stage--brand{
right:-1vw!important;
width:64vw!important;
max-width:820px!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(-.5vw,-54px,0) scale(1.02)!important;
}
.zh--logoBranding .zh__brandMock{width:min(118%,920px)!important}
.zh--logoBranding .zh__brandMini{
right:clamp(42px,5vw,70px)!important;
bottom:clamp(86px,15vh,142px)!important;
width:min(286px,23vw)!important;
padding:15px 17px 17px!important;
}
.zh--logoBranding .zh__brandMini strong{font-size:clamp(18px,1.45vw,22px)!important}
.zh--logoBranding .zh__brandMini p{font-size:11.8px!important;line-height:1.42!important}
.zh--logoBranding .zh__photoChip{max-width:min(190px,19vw)!important;font-size:10.5px!important;min-height:36px!important;padding:0 12px!important}
.zh--logoBranding .zh__photoChip--a{right:clamp(70px,7vw,100px)!important;top:17%!important}
.zh--logoBranding .zh__photoChip--b{right:clamp(52px,5vw,82px)!important;top:33%!important}
.zh--logoBranding .zh__photoChip--c{right:clamp(190px,27vw,330px)!important;top:30%!important}
.zh--logoBranding .zh__photoChip--d{right:clamp(180px,28vw,330px)!important;bottom:25%!important}
.zh--logoBranding .zh__photoChip--e{right:clamp(112px,15vw,190px)!important;bottom:15%!important}
}
@media(min-width:901px) and (max-width:1100px){
.zh--logoBranding .zh__mobileBrandMock{
margin-top:-18px!important;
margin-bottom:8px!important;
transform:translateY(-24px)!important;
}
.zh--logoBranding .zh__mobileBrandMock img,
.zh--logoBranding .zh__mobileBrandMock .zh__brandMock{
width:106%!important;
max-width:106%!important;
}
.zh__mobileBrandMock .zh__photoChip{
max-width:42vw!important;
}
.zh__mobileBrandMock .zh__photoChip--b,
.zh__mobileBrandMock .zh__photoChip--d{
right:9%!important;
}
}
@media(min-width:1681px){
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(2vw,-52px,0) scale(1.08)!important;
transform-origin:center center!important;
}
}
@media(min-width:1361px) and (max-width:1680px){
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(-1.2vw,-98px,0) scale(1.05)!important;
transform-origin:center center!important;
}
}
@media(min-width:1101px){
.zh--logoBranding .zh__actions{
margin-bottom:50px!important;
}
.zh--logoBranding .zh__inner{
padding-bottom:128px!important;
}
}
@media(min-width:1451px) and (max-width:1580px){
.zh--logoBranding .zh__copy{
max-width:760px!important;
}
.zh--logoBranding .zh__title{
font-size:clamp(58px,5.25vw,82px)!important;
max-width:12.7ch!important;
line-height:.99!important;
}
.zh--logoBranding .zh__lead{
max-width:530px!important;
font-size:16px!important;
line-height:1.66!important;
}
.zh--logoBranding .zh__stage--brand{
width:61vw!important;
right:-1vw!important;
max-width:900px!important;
}
.zh--logoBranding .zh__brandMock{
width:min(116%,980px)!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(.4vw,-72px,0) scale(1.01)!important;
}
.zh--logoBranding .zh__brandMini{
right:clamp(46px,5.5vw,86px)!important;
bottom:clamp(82px,13vh,128px)!important;
width:min(312px,23vw)!important;
}
.zh--logoBranding .zh__photoChip{
max-width:min(220px,20vw)!important;
}
.zh--logoBranding .zh__photoChip--b{right:clamp(58px,5.5vw,100px)!important;}
}
@media(min-width:1381px) and (max-width:1450px){
.zh--logoBranding .zh__actions{margin-bottom:50px!important;}
.zh--logoBranding .zh__inner{padding-bottom:128px!important;}
}
@media(min-width:1581px) and (max-width:1640px){
.zh--logoBranding .zh__copy{
max-width:740px!important;
}
.zh--logoBranding .zh__title{
font-size:clamp(58px,5.08vw,80px)!important;
max-width:12.35ch!important;
line-height:.99!important;
}
.zh--logoBranding .zh__lead{
max-width:520px!important;
font-size:15.9px!important;
line-height:1.66!important;
}
.zh--logoBranding .zh__stage--brand{
width:60vw!important;
right:-.5vw!important;
max-width:900px!important;
}
.zh--logoBranding .zh__brandMock{
width:min(114%,960px)!important;
}
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(.2vw,-74px,0) scale(1)!important;
transform-origin:center center!important;
}
.zh--logoBranding .zh__brandMini{
right:clamp(52px,5.8vw,92px)!important;
width:min(312px,22vw)!important;
}
.zh--logoBranding .zh__photoChip{
max-width:min(218px,19vw)!important;
}
}
@media(min-width:1641px) and (max-width:1680px){
.zh--logoBranding .zh__title{
max-width:13.25ch!important;
}
.zh--logoBranding .zh__stage--brand{
right:-1.5vw!important;
}
}
@media(max-width:1100px){
.zh--logoBranding .zh__mobileBrandStatsWrap{
display:block!important;
width:calc(100% - 36px)!important;
margin:0 auto 6px!important;
padding:16px 0 6px!important;
border-top:1px solid rgba(255,255,255,.10)!important;
border-bottom:0!important;
overflow:visible!important;
-webkit-mask-image:none!important;
mask-image:none!important;
}
.zh--logoBranding .zhMobileBrandStats{
display:grid!important;
grid-template-columns:repeat(3,minmax(0,1fr))!important;
gap:8px!important;
width:100%!important;
}
.zh--logoBranding .zhMobileBrandStat{
min-width:0!important;
padding:12px 8px 11px!important;
border-radius:16px!important;
border:1px solid rgba(255,255,255,.10)!important;
background:rgba(255,255,255,.035)!important;
text-align:center!important;
}
.zh--logoBranding .zhMobileBrandStat b{
display:inline-block!important;
color:#fff!important;
font-size:20px!important;
line-height:1!important;
font-weight:820!important;
letter-spacing:-.045em!important;
font-variant-numeric:tabular-nums!important;
}
.zh--logoBranding .zhMobileBrandStat i{
font-style:normal!important;
color:var(--gold)!important;
font-size:11px!important;
margin-left:3px!important;
}
.zh--logoBranding .zhMobileBrandStat span{
display:block!important;
margin-top:5px!important;
color:rgba(255,255,255,.48)!important;
font-size:9.5px!important;
line-height:1.22!important;
font-weight:700!important;
letter-spacing:-.02em!important;
}
}
@media(max-width:600px){
.zh--logoBranding .zh__mobileBrandStatsWrap{
padding:14px 0 4px!important;
margin-bottom:4px!important;
}
.zh--logoBranding .zhMobileBrandStats{gap:6px!important}
.zh--logoBranding .zhMobileBrandStat{
border-radius:14px!important;
padding:10px 5px 9px!important;
}
.zh--logoBranding .zhMobileBrandStat b{font-size:18px!important}
.zh--logoBranding .zhMobileBrandStat i{font-size:10px!important;margin-left:2px!important}
.zh--logoBranding .zhMobileBrandStat span{font-size:8.2px!important;line-height:1.18!important}
}
@media(min-width:1580px) and (max-width:1630px){
.zh--logoBranding .zh__brandMockWrap{
transform:translate3d(.2vw,-74px,0) scale(1.20)!important;
transform-origin:center center!important;
}
.zh--logoBranding .zh__brandMock{
width:min(114%,960px)!important;
}
}
@media(max-width:1100px){
.zh--logoBranding .zh__mobileBrandStatsWrap{
width:calc(100% - 36px)!important;
margin:0 auto 6px!important;
padding:15px 0 5px!important;
border-top:1px solid rgba(255,255,255,.11)!important;
background:transparent!important;
-webkit-backdrop-filter:none!important;
backdrop-filter:none!important;
overflow:visible!important;
}
.zh--logoBranding .zhMobileBrandStats{
display:flex!important;
align-items:center!important;
justify-content:center!important;
gap:0!important;
width:100%!important;
}
.zh--logoBranding .zhMobileBrandStat{
position:relative!important;
flex:1 1 0!important;
min-width:0!important;
padding:0 10px!important;
border:0!important;
border-radius:0!important;
background:transparent!important;
box-shadow:none!important;
text-align:center!important;
}
.zh--logoBranding .zhMobileBrandStat + .zhMobileBrandStat::before{
content:""!important;
position:absolute!important;
left:0!important;
top:50%!important;
width:1px!important;
height:30px!important;
transform:translateY(-50%)!important;
background:linear-gradient(to bottom,transparent,rgba(255,255,255,.16),transparent)!important;
}
.zh--logoBranding .zhMobileBrandStat b{
font-size:19px!important;
line-height:1!important;
letter-spacing:-.04em!important;
}
.zh--logoBranding .zhMobileBrandStat i{
font-size:10px!important;
margin-left:2px!important;
}
.zh--logoBranding .zhMobileBrandStat span{
margin-top:4px!important;
font-size:9.6px!important;
line-height:1.15!important;
letter-spacing:-.025em!important;
color:rgba(255,255,255,.48)!important;
white-space:nowrap!important;
}
.zh__mobileBrandMock .zh__photoChip{
border-color:rgba(255,255,255,.14)!important;
background:rgba(1,4,12,.44)!important;
}
}
@media(max-width:600px){
.zh--logoBranding .zh__mobileBrandStatsWrap{
padding:13px 0 4px!important;
margin-bottom:4px!important;
}
.zh--logoBranding .zhMobileBrandStat{padding:0 7px!important}
.zh--logoBranding .zhMobileBrandStat + .zhMobileBrandStat::before{height:28px!important}
.zh--logoBranding .zhMobileBrandStat b{font-size:18px!important}
.zh--logoBranding .zhMobileBrandStat span{font-size:8.8px!important}
}
@media(max-width:380px){
.zh--logoBranding .zhMobileBrandStat{padding:0 5px!important}
.zh--logoBranding .zhMobileBrandStat b{font-size:17px!important}
.zh--logoBranding .zhMobileBrandStat span{font-size:8.2px!important}
}
@media(min-width:1101px){
.zh .zh__inner{
padding-top:120px!important;
}
}
@media(max-width:1100px){
.zh .zh__inner{
padding-top:120px!important;
}
}
@media(max-width:600px){
.zh .zh__inner{
padding-top:112px!important;
}
}
@media(max-width:1100px){
.zh--logoBranding .zh__mobileBrandMock{
margin-top:22px!important;
}
}
@media(max-width:600px){
.zh--logoBranding .zh__mobileBrandMock{
margin-top:20px!important;
}
}
.zh .zh__wm{
clip-path:inset(0 100% 0 0)!important;
opacity:0!important;
transform:translate3d(-16px,0,0)!important;
animation:zhWmRevealSmooth 1.35s cubic-bezier(.16,1,.3,1) .55s forwards!important;
will-change:clip-path,opacity,transform!important;
}
@keyframes zhWmRevealSmooth{
0%{clip-path:inset(0 100% 0 0);opacity:0;transform:translate3d(-16px,0,0)}
55%{opacity:1}
100%{clip-path:inset(0 0 0 0);opacity:1;transform:translate3d(0,0,0)}
}
@media(prefers-reduced-motion:reduce){
.zh .zh__wm{animation:none!important;clip-path:inset(0 0 0 0)!important;opacity:1!important;transform:none!important}
}
.zh .zh__wm{
display:block!important;
visibility:visible!important;
opacity:0!important;
clip-path:none!important;
overflow:visible!important;
transform:translate3d(0,10px,0)!important;
animation:zhWmFadeVisible .95s cubic-bezier(.16,1,.3,1) .45s forwards!important;
will-change:opacity,transform!important;
}
.zh .zh__wm::after{
display:block!important;
}
@keyframes zhWmFadeVisible{
0%{opacity:0;transform:translate3d(0,10px,0)}
100%{opacity:1;transform:translate3d(0,0,0)}
}
@media(prefers-reduced-motion:reduce){
.zh .zh__wm{animation:none!important;opacity:1!important;transform:none!important;clip-path:none!important}
}
.zh .zh__wm,
.zh.zh--logoBranding .zh__wm{
display:block!important;
visibility:visible!important;
opacity:1!important;
clip-path:none!important;
-webkit-clip-path:none!important;
transform:none!important;
animation:none!important;
overflow:visible!important;
color:rgba(255,255,255,.072)!important;
z-index:6!important;
pointer-events:none!important;
}
.zh .zh__wm::after,
.zh.zh--logoBranding .zh__wm::after{
display:block!important;
opacity:1!important;
}
@media(min-width:1101px){
.zh .zh__wm,
.zh.zh--logoBranding .zh__wm{
position:absolute!important;
left:max(48px,calc((100% - 1700px)/2))!important;
right:auto!important;
top:auto!important;
bottom:78px!important;
width:min(1120px,calc(100% - 96px))!important;
}
}
@media(max-width:1100px){
.zh .zh__wm,
.zh.zh--logoBranding .zh__wm{
position:relative!important;
left:auto!important;
right:auto!important;
top:auto!important;
bottom:auto!important;
width:calc(100% - 36px)!important;
margin:0 auto!important;
}
}
@media(min-width:1101px) and (max-width:1400px){
.zh .zh__title,
.zh--logoBranding .zh__title{
line-height:.965!important;
letter-spacing:-.046em!important;
}
.zh .zh__lead,
.zh--logoBranding .zh__lead{
line-height:1.58!important;
}
}
@media(max-width:1100px){
.zh .zh__title,
.zh--logoBranding .zh__title{
font-size:clamp(40px,9vw,72px)!important;
line-height:.985!important;
letter-spacing:-.044em!important;
font-weight:480!important;
max-width:none!important;
padding-bottom:.10em!important;
margin-bottom:-.08em!important;
text-wrap:balance!important;
}
.zh .zh__lead,
.zh--logoBranding .zh__lead{
font-size:15.5px!important;
line-height:1.58!important;
letter-spacing:-.01em!important;
max-width:620px!important;
}
}
@media(max-width:600px){
.zh .zh__title,
.zh--logoBranding .zh__title{
font-size:clamp(38px,10.8vw,48px)!important;
line-height:.995!important;
letter-spacing:-.042em!important;
padding-bottom:.08em!important;
margin-bottom:-.06em!important;
}
.zh .zh__lead,
.zh--logoBranding .zh__lead{
font-size:15px!important;
line-height:1.56!important;
}
}
@media(max-width:390px){
.zh .zh__title,
.zh--logoBranding .zh__title{
line-height:1!important;
letter-spacing:-.04em!important;
}
.zh .zh__lead,
.zh--logoBranding .zh__lead{
line-height:1.54!important;
}
}
.zh--logoBranding .zh__wm{
display:none!important;
visibility:hidden!important;
opacity:0!important;
}
@media(max-width:1100px){
.zh--logoBranding .zh__title{
line-height:1.02!important;
padding-bottom:0!important;
margin-bottom:0!important;
}
.zh--logoBranding .zh__title .zh__grad{
display:inline!important;
line-height:inherit!important;
padding:0!important;
margin:0!important;
vertical-align:baseline!important;
-webkit-box-decoration-break:slice!important;
box-decoration-break:slice!important;
}
}
@media(max-width:600px){
.zh--logoBranding .zh__title{
line-height:1.025!important;
padding-bottom:0!important;
margin-bottom:0!important;
}
.zh--logoBranding .zh__title .zh__grad{
display:inline!important;
line-height:inherit!important;
padding:0!important;
margin:0!important;
vertical-align:baseline!important;
}
}
@media(min-width:1101px) and (max-width:1500px){
.zh--logoBranding .zh__title{
line-height:1!important;
padding-bottom:0!important;
margin-bottom:0!important;
}
.zh--logoBranding .zh__title .zh__grad{
display:inline!important;
line-height:inherit!important;
padding:0!important;
margin:0!important;
vertical-align:baseline!important;
}
}
.zh--logoBranding .zh__wm,
.zh--logoBranding [class*="zh__wm"]{
display:none!important;
visibility:hidden!important;
opacity:0!important;
pointer-events:none!important;
width:0!important;
height:0!important;
margin:0!important;
padding:0!important;
overflow:hidden!important;
}
@media(max-width:1100px){
.zh--logoBranding .zh__mobileBrandMock img,
.zh--logoBranding .zh__mobileBrandMock .zh__brandMock{
width:121%!important;
max-width:121%!important;
margin-left:50%!important;
transform:translateX(-50%)!important;
}
}
@media(max-width:600px){
.zh--logoBranding .zh__mobileBrandMock img,
.zh--logoBranding .zh__mobileBrandMock .zh__brandMock{
width:121%!important;
max-width:121%!important;
margin-left:50%!important;
transform:translateX(-50%)!important;
}
}
/* PATCH — unified H1 rhythm across all hero variants + gradient edge safe */
.zh .zh__title,
.zh.zh--logoBranding .zh__title,
.zh.zh--websites .zh__title,
.zh.zh--shops .zh__title{
  line-height:.90!important;
  padding-top:0!important;
  padding-bottom:0!important;
  margin-top:0!important;
  margin-bottom:0!important;
  overflow:visible!important;
  text-wrap:balance!important;
}
.zh .zh__title .zh__grad,
.zh.zh--logoBranding .zh__title .zh__grad,
.zh.zh--websites .zh__title .zh__grad,
.zh.zh--shops .zh__title .zh__grad{
  display:inline-block!important;
  line-height:inherit!important;
  padding:0 .105em 0 .025em!important;
  margin:0 -.085em 0 -.025em!important;
  vertical-align:baseline!important;
  overflow:visible!important;
  -webkit-box-decoration-break:clone!important;
  box-decoration-break:clone!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
}
@media(min-width:1681px){
  .zh .zh__title,
  .zh.zh--logoBranding .zh__title,
  .zh.zh--websites .zh__title,
  .zh.zh--shops .zh__title{line-height:.89!important;}
}
@media(min-width:1101px) and (max-width:1500px){
  .zh .zh__title,
  .zh.zh--logoBranding .zh__title,
  .zh.zh--websites .zh__title,
  .zh.zh--shops .zh__title{line-height:.91!important;}
}
@media(max-width:1100px){
  .zh .zh__title,
  .zh.zh--logoBranding .zh__title,
  .zh.zh--websites .zh__title,
  .zh.zh--shops .zh__title{
    line-height:.94!important;
    padding-top:0!important;
    padding-bottom:0!important;
    margin-top:0!important;
    margin-bottom:0!important;
    overflow:visible!important;
  }
  .zh .zh__title .zh__grad,
  .zh.zh--logoBranding .zh__title .zh__grad,
  .zh.zh--websites .zh__title .zh__grad,
  .zh.zh--shops .zh__title .zh__grad{
    display:inline-block!important;
    line-height:inherit!important;
    padding:0 .105em 0 .025em!important;
    margin:0 -.085em 0 -.025em!important;
    vertical-align:baseline!important;
    overflow:visible!important;
  }
}
@media(max-width:600px){
  .zh .zh__title,
  .zh.zh--logoBranding .zh__title,
  .zh.zh--websites .zh__title,
  .zh.zh--shops .zh__title{line-height:.965!important;}
}
/* PATCH — balanced global H1 rhythm + gradient safe area
   Cel: spójna interlinia we wszystkich hero; bez zbyt ciasnego Home/Logo i bez clipu gradientu. */
.zh .zh__title,
.zh.zh--logoBranding .zh__title,
.zh.zh--websites .zh__title,
.zh.zh--shops .zh__title{
  line-height:1.025!important;
  overflow:visible!important;
  padding-top:.02em!important;
  padding-bottom:.10em!important;
  margin-bottom:-.06em!important;
  text-wrap:balance!important;
}

.zh .zh__title .zh__grad,
.zh.zh--logoBranding .zh__title .zh__grad,
.zh.zh--websites .zh__title .zh__grad,
.zh.zh--shops .zh__title .zh__grad{
  display:inline-block!important;
  overflow:visible!important;
  line-height:1.04!important;
  padding:.015em .115em .075em .025em!important;
  margin:-.015em -.055em -.025em -.025em!important;
  vertical-align:baseline!important;
  -webkit-box-decoration-break:clone!important;
  box-decoration-break:clone!important;
  background-clip:text!important;
  -webkit-background-clip:text!important;
}

@media(min-width:1101px) and (max-width:1500px){
  .zh .zh__title,
  .zh.zh--logoBranding .zh__title,
  .zh.zh--websites .zh__title,
  .zh.zh--shops .zh__title{
    line-height:1.03!important;
    padding-bottom:.105em!important;
    margin-bottom:-.065em!important;
  }
  .zh .zh__title .zh__grad,
  .zh.zh--logoBranding .zh__title .zh__grad,
  .zh.zh--websites .zh__title .zh__grad,
  .zh.zh--shops .zh__title .zh__grad{
    line-height:1.045!important;
    padding-right:.12em!important;
    padding-bottom:.08em!important;
    margin-right:-.058em!important;
    margin-bottom:-.028em!important;
  }
}

@media(max-width:1100px){
  .zh .zh__title,
  .zh.zh--logoBranding .zh__title,
  .zh.zh--websites .zh__title,
  .zh.zh--shops .zh__title{
    line-height:1.045!important;
    padding-bottom:.115em!important;
    margin-bottom:-.07em!important;
  }
  .zh .zh__title .zh__grad,
  .zh.zh--logoBranding .zh__title .zh__grad,
  .zh.zh--websites .zh__title .zh__grad,
  .zh.zh--shops .zh__title .zh__grad{
    line-height:1.055!important;
    padding:.015em .12em .085em .025em!important;
    margin:-.015em -.06em -.035em -.025em!important;
  }
}

@media(max-width:600px){
  .zh .zh__title,
  .zh.zh--logoBranding .zh__title,
  .zh.zh--websites .zh__title,
  .zh.zh--shops .zh__title{
    line-height:1.055!important;
    padding-bottom:.12em!important;
    margin-bottom:-.075em!important;
  }
  .zh .zh__title .zh__grad,
  .zh.zh--logoBranding .zh__title .zh__grad,
  .zh.zh--websites .zh__title .zh__grad,
  .zh.zh--shops .zh__title .zh__grad{
    line-height:1.065!important;
    padding-right:.125em!important;
    padding-bottom:.09em!important;
    margin-right:-.062em!important;
    margin-bottom:-.038em!important;
  }
}


/* PATCH v45 — Logo Branding: anti-clip gradient word, balanced line-height */
.zh--logoBranding .zh__title{
  overflow:visible!important;
  line-height:1.055!important;
  padding-top:.03em!important;
  margin-top:-.03em!important;
  padding-bottom:.22em!important;
  margin-bottom:-.16em!important;
}
.zh--logoBranding .zh__title .zh__grad{
  display:inline-block!important;
  overflow:visible!important;
  line-height:1.12!important;
  padding:.025em .14em .36em .07em!important;
  margin:-.025em -.14em -.30em -.07em!important;
  vertical-align:baseline!important;
  -webkit-box-decoration-break:clone!important;
  box-decoration-break:clone!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  color:transparent!important;
}
@media(max-width:1100px){
  .zh--logoBranding .zh__title{
    line-height:1.065!important;
    padding-bottom:.24em!important;
    margin-bottom:-.17em!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    line-height:1.14!important;
    padding:.025em .145em .38em .07em!important;
    margin:-.025em -.145em -.32em -.07em!important;
  }
}
@media(max-width:600px){
  .zh--logoBranding .zh__title{
    line-height:1.075!important;
    padding-bottom:.25em!important;
    margin-bottom:-.18em!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    line-height:1.16!important;
    padding:.025em .15em .40em .07em!important;
    margin:-.025em -.15em -.34em -.07em!important;
  }
}

/* PATCH v46 — Logo Branding: hard unclip descender in gradient word */
.zh--logoBranding .zh__title{
  overflow:visible!important;
  line-height:1.085!important;
  padding-top:.035em!important;
  margin-top:-.035em!important;
  padding-bottom:.34em!important;
  margin-bottom:-.20em!important;
  -webkit-font-smoothing:antialiased!important;
}
.zh--logoBranding .zh__title .zh__grad{
  display:inline-block!important;
  overflow:visible!important;
  line-height:1.22!important;
  padding:.03em .18em .56em .08em!important;
  margin:-.03em -.18em -.43em -.08em!important;
  vertical-align:baseline!important;
  transform:translateZ(0)!important;
  -webkit-box-decoration-break:clone!important;
  box-decoration-break:clone!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  color:transparent!important;
}
@media(max-width:1100px){
  .zh--logoBranding .zh__title{
    line-height:1.095!important;
    padding-bottom:.36em!important;
    margin-bottom:-.21em!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    line-height:1.24!important;
    padding:.03em .18em .58em .08em!important;
    margin:-.03em -.18em -.45em -.08em!important;
  }
}
@media(max-width:600px){
  .zh--logoBranding .zh__title{
    line-height:1.105!important;
    padding-bottom:.38em!important;
    margin-bottom:-.22em!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    line-height:1.26!important;
    padding:.03em .19em .62em .08em!important;
    margin:-.03em -.19em -.48em -.08em!important;
  }
}


/* PATCH v47 — Logo Branding: real descender unclip for gradient word */
.zh--logoBranding .zh__title{
  overflow:visible!important;
  line-height:1.14!important;
  padding-top:.02em!important;
  margin-top:-.02em!important;
  padding-bottom:.16em!important;
  margin-bottom:-.10em!important;
  contain:none!important;
  clip-path:none!important;
  -webkit-clip-path:none!important;
}
.zh--logoBranding .zh__title .zh__grad{
  display:inline!important;
  position:relative!important;
  overflow:visible!important;
  line-height:inherit!important;
  padding:0 .16em .02em .035em!important;
  margin:0 -.08em 0 -.035em!important;
  vertical-align:baseline!important;
  contain:none!important;
  clip-path:none!important;
  -webkit-clip-path:none!important;
  -webkit-box-decoration-break:clone!important;
  box-decoration-break:clone!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  -webkit-text-fill-color:transparent!important;
  color:transparent!important;
}
@media(max-width:1100px){
  .zh--logoBranding .zh__title{
    line-height:1.13!important;
    padding-bottom:.15em!important;
    margin-bottom:-.09em!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    padding-right:.16em!important;
    margin-right:-.08em!important;
  }
}
@media(max-width:600px){
  .zh--logoBranding .zh__title{
    line-height:1.12!important;
    padding-bottom:.16em!important;
    margin-bottom:-.10em!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    padding-right:.17em!important;
    margin-right:-.085em!important;
  }
}



/* PATCH v48 — FINAL: realny zapas na ogonek litery „g” w gradientowym słowie */
.zh--logoBranding .zh__copy,
.zh--logoBranding .zh__title,
.zh--logoBranding .zh__title *{
  overflow:visible!important;
  contain:none!important;
  clip-path:none!important;
  -webkit-clip-path:none!important;
}
.zh--logoBranding .zh__title{
  line-height:1.115!important;
  padding-top:.015em!important;
  padding-bottom:.075em!important;
  margin-bottom:0!important;
}
.zh--logoBranding .zh__title .zh__grad{
  display:inline-block!important;
  position:relative!important;
  overflow:visible!important;
  line-height:1.26!important;
  padding:.015em .14em .20em .025em!important;
  margin:-.015em -.07em -.055em -.025em!important;
  vertical-align:baseline!important;
  transform:translateY(.035em)!important;
  -webkit-box-decoration-break:clone!important;
  box-decoration-break:clone!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  -webkit-text-fill-color:transparent!important;
  color:transparent!important;
}
@media(max-width:1100px){
  .zh--logoBranding .zh__title{
    line-height:1.13!important;
    padding-bottom:.08em!important;
    margin-bottom:0!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    line-height:1.28!important;
    padding-bottom:.22em!important;
    margin-bottom:-.06em!important;
    transform:translateY(.035em)!important;
  }
}
@media(max-width:600px){
  .zh--logoBranding .zh__title{
    line-height:1.145!important;
    padding-bottom:.09em!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    line-height:1.30!important;
    padding-bottom:.24em!important;
    margin-bottom:-.065em!important;
  }
}


/* PATCH v49 — REALNY FIX CLIPU LITERY „g” W GRADIENTOWYM SŁOWIE
   Problem nie był w samym paddingu spana, tylko w zbyt niskim line-boxie H1 + inline-block.
   Dlatego gradient wraca jako inline, a realny zapas dostaje całe H1. */
.zh--logoBranding .zh__copy,
.zh--logoBranding .zh__title,
.zh--logoBranding .zh__title *,
.zh--logoBranding .zh__title .zh__grad{
  overflow:visible!important;
  clip-path:none!important;
  -webkit-clip-path:none!important;
  contain:none!important;
  max-height:none!important;
}
.zh--logoBranding .zh__title{
  line-height:1.20!important;
  padding-top:0!important;
  padding-bottom:.30em!important;
  margin-bottom:-.20em!important;
}
.zh--logoBranding .zh__title .zh__grad{
  display:inline!important;
  position:static!important;
  line-height:inherit!important;
  padding:0!important;
  margin:0!important;
  vertical-align:baseline!important;
  transform:none!important;
  -webkit-box-decoration-break:clone!important;
  box-decoration-break:clone!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  -webkit-text-fill-color:transparent!important;
  color:transparent!important;
}
@media(max-width:1100px){
  .zh--logoBranding .zh__title{
    line-height:1.18!important;
    padding-bottom:.28em!important;
    margin-bottom:-.18em!important;
  }
}
@media(max-width:600px){
  .zh--logoBranding .zh__title{
    line-height:1.16!important;
    padding-bottom:.26em!important;
    margin-bottom:-.16em!important;
  }
}


/* PATCH v50-normal — bez SVG: gradientowe słowo jako normalny span + pseudo-warstwa anty-clip */
.zh--logoBranding .zh__title,
.zh--logoBranding .zh__title *{
  overflow:visible!important;
  contain:none!important;
  clip-path:none!important;
  -webkit-clip-path:none!important;
  max-height:none!important;
}

.zh--logoBranding .zh__title{
  line-height:1.075!important;
  padding-top:.02em!important;
  padding-bottom:.16em!important;
  margin-bottom:-.07em!important;
}

.zh--logoBranding .zh__title .zh__grad{
  position:relative!important;
  display:inline-block!important;
  overflow:visible!important;
  line-height:1.12!important;
  padding:0 .10em .16em .015em!important;
  margin:0 -.055em -.07em -.015em!important;
  vertical-align:baseline!important;

  /* tekst właściwy zostaje w układzie, ale gradient rysuje pseudo-warstwa */
  background:none!important;
  color:transparent!important;
  -webkit-text-fill-color:transparent!important;
  text-shadow:none!important;
}

.zh--logoBranding .zh__title .zh__grad::before{
  content:attr(data-text);
  position:absolute!important;
  left:0!important;
  right:-.16em!important;
  top:-.015em!important;
  bottom:-.26em!important;
  display:block!important;
  overflow:visible!important;
  line-height:1.12!important;
  padding:0 .16em .26em 0!important;
  margin:0!important;
  background:linear-gradient(110deg,var(--acc-mid) 0%,var(--acc-ice) 26%,#fff 42%,var(--acc-ice) 58%,var(--acc-mid) 100%)!important;
  background-size:240% 100%!important;
  background-position:14% 0!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  color:transparent!important;
  -webkit-text-fill-color:transparent!important;
  pointer-events:none!important;
  z-index:2!important;
  transform:none!important;
  contain:none!important;
  clip-path:none!important;
  -webkit-clip-path:none!important;
}

@media(max-width:1100px){
  .zh--logoBranding .zh__title{
    line-height:1.085!important;
    padding-bottom:.17em!important;
    margin-bottom:-.075em!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    line-height:1.14!important;
    padding-bottom:.17em!important;
    margin-bottom:-.075em!important;
  }
  .zh--logoBranding .zh__title .zh__grad::before{
    line-height:1.14!important;
    bottom:-.28em!important;
    padding-bottom:.28em!important;
  }
}

@media(max-width:600px){
  .zh--logoBranding .zh__title{
    line-height:1.095!important;
    padding-bottom:.18em!important;
    margin-bottom:-.08em!important;
  }
  .zh--logoBranding .zh__title .zh__grad{
    line-height:1.15!important;
  }
  .zh--logoBranding .zh__title .zh__grad::before{
    line-height:1.15!important;
    bottom:-.30em!important;
    padding-bottom:.30em!important;
  }
}



/* PATCH — Wiedza hero 1:1 w stylu usługowych podstron */
.zh--knowledge .zh__title{max-width:14.2ch!important}
.zh--knowledge .zh__lead{max-width:650px!important}
.zh--knowledge .zh__stage--knowledge{
  right:-3vw!important;
  width:64vw!important;
  min-width:900px!important;
  pointer-events:none!important;
}
.zh--knowledge .zh__brandMockWrap{
  transform:translate3d(2vw,-18px,0) scale(1)!important;
  rotate:-2deg;
}
.zh--knowledge .zh__brandMini{
  left:auto!important;
  right:13%!important;
  bottom:13%!important;
  width:min(390px,30vw)!important;
  background:rgba(5,8,15,.62)!important;
  border-color:rgba(255,255,255,.18)!important;
  box-shadow:0 30px 84px rgba(0,0,0,.54),inset 0 1px 0 rgba(255,255,255,.14)!important;
}
.zh--knowledge .zh__brandMini span{color:rgba(255,255,255,.46)!important}
.zh--knowledge .zh__brandMini em{color:#fff!important}
.zkViz{
  position:relative;
  width:min(760px,70vw);
  height:min(620px,66vh);
  min-height:470px;
  pointer-events:auto;
}
.zkCard{
  position:absolute;
  display:block;
  width:min(360px,30vw);
  min-height:188px;
  padding:22px 24px 24px;
  border-radius:28px;
  text-decoration:none;
  color:#fff;
  border:1px solid rgba(255,255,255,.13);
  background:
    linear-gradient(180deg,rgba(255,255,255,.072),rgba(255,255,255,.018) 50%,rgba(1,4,12,.18)),
    rgba(1,4,12,.46);
  -webkit-backdrop-filter:blur(24px) saturate(1.35);
  backdrop-filter:blur(24px) saturate(1.35);
  box-shadow:0 30px 76px rgba(0,0,0,.45),inset 0 1px 0 rgba(255,255,255,.12);
  transform:translateZ(0);
  transition:transform .28s cubic-bezier(.16,1,.3,1),border-color .24s ease,background .24s ease;
  animation:zkFloat 8s ease-in-out infinite;
}
.zkCard:hover{
  transform:translateY(-6px) translateZ(0);
  border-color:rgba(142,200,247,.36);
  background:rgba(8,22,44,.54);
}
.zkCard__tag{
  display:inline-flex;
  align-items:center;
  min-height:28px;
  padding:0 11px;
  border-radius:999px;
  color:rgba(142,200,247,.95);
  background:rgba(142,200,247,.08);
  border:1px solid rgba(142,200,247,.16);
  font-size:9.5px;
  font-weight:850;
  letter-spacing:.16em;
  text-transform:uppercase;
  margin-bottom:18px;
}
.zkCard strong{
  display:block;
  font-size:clamp(23px,1.8vw,31px);
  line-height:1.02;
  letter-spacing:-.05em;
  font-weight:620;
}
.zkCard p{
  margin:14px 0 0;
  max-width:285px;
  color:rgba(255,255,255,.62);
  font-size:13.5px;
  line-height:1.56;
  letter-spacing:-.01em;
}
.zkCard--1{left:8%;top:7%;z-index:3}
.zkCard--2{right:2%;top:35%;z-index:2;animation-delay:-1.6s}
.zkCard--3{left:16%;bottom:7%;z-index:4;animation-delay:-3s}
@keyframes zkFloat{0%,100%{translate:0 0}50%{translate:0 -13px}}
.zh--knowledge .zh__photoChip--a{right:29%;top:20%}
.zh--knowledge .zh__photoChip--b{right:12%;top:38%}
.zh--knowledge .zh__photoChip--c{left:28%;top:31%}
.zh--knowledge .zh__photoChip--d{left:24%;bottom:29%}
.zh--knowledge .zh__photoChip--e{right:20%;bottom:18%}
.zh--knowledge .zh__wm{display:none!important}
.zh--knowledge .zh__side{display:none!important}

@media(min-width:1451px) and (max-width:1640px){
  .zh--knowledge .zh__copy{max-width:760px!important}
  .zh--knowledge .zh__title{font-size:clamp(58px,5.08vw,80px)!important;max-width:12.8ch!important}
  .zh--knowledge .zh__lead{max-width:540px!important;font-size:15.9px!important;line-height:1.66!important}
  .zh--knowledge .zh__stage--knowledge{width:60vw!important;right:-.5vw!important;max-width:900px!important;min-width:0!important}
  .zh--knowledge .zkViz{width:min(720px,58vw);height:min(580px,62vh)}
  .zh--knowledge .zkCard{width:min(330px,24vw)}
  .zh--knowledge .zh__brandMini{right:clamp(52px,5.8vw,92px)!important;width:min(318px,23vw)!important}
}
@media(min-width:1101px) and (max-width:1450px){
  .zh--knowledge .zh__copy{max-width:700px!important}
  .zh--knowledge .zh__title{font-size:clamp(53px,5vw,70px)!important;max-width:12.6ch!important}
  .zh--knowledge .zh__lead{max-width:500px!important;font-size:15.2px!important;line-height:1.66!important}
  .zh--knowledge .zh__stage--knowledge{width:64vw!important;right:-1vw!important;min-width:0!important;max-width:820px!important}
  .zh--knowledge .zh__brandMockWrap{transform:translate3d(-.5vw,-54px,0) scale(.98)!important}
  .zh--knowledge .zkViz{width:min(660px,58vw);height:min(540px,60vh)}
  .zh--knowledge .zkCard{width:min(300px,23vw);min-height:172px;padding:19px 20px 21px}
  .zh--knowledge .zkCard strong{font-size:clamp(20px,1.55vw,25px)}
  .zh--knowledge .zkCard p{font-size:12.3px}
  .zh--knowledge .zh__brandMini{right:clamp(42px,5vw,70px)!important;bottom:clamp(86px,15vh,142px)!important;width:min(286px,23vw)!important}
}
@media(max-width:1100px){
  .zh--knowledge{overflow:hidden!important}
  .zh--knowledge .zh__stage--knowledge{display:none!important}
  .zh--knowledge .zh__inner{width:calc(100% - 36px)!important;padding-bottom:28px!important}
  .zh--knowledge .zh__copy{width:100%!important;max-width:100%!important}
  .zh--knowledge .zh__mobileBrandMock{
    display:block!important;
    position:relative!important;
    z-index:7!important;
    width:min(620px,94vw)!important;
    max-width:94vw!important;
    margin:28px auto 14px!important;
    transform:none!important;
    pointer-events:auto!important;
  }
  .zh--knowledge .zkViz--mobile{
    width:100%;
    height:auto;
    min-height:0;
    display:grid;
    gap:12px;
  }
  .zh--knowledge .zkViz--mobile .zkCard{
    position:relative;
    inset:auto!important;
    width:100%;
    min-height:auto;
    padding:17px 18px 18px;
    border-radius:22px;
    animation:none;
  }
  .zh--knowledge .zkViz--mobile .zkCard__tag{margin-bottom:12px}
  .zh--knowledge .zkViz--mobile .zkCard strong{font-size:clamp(20px,5vw,27px)}
  .zh--knowledge .zkViz--mobile .zkCard p{font-size:12.6px;line-height:1.5;max-width:none}
  .zh--knowledge .zh__mobileBrandStatsWrap{
    display:block!important;
    width:calc(100% - 36px)!important;
    margin:0 auto 6px!important;
    padding:15px 0 5px!important;
    border-top:1px solid rgba(255,255,255,.11)!important;
    background:transparent!important;
    overflow:visible!important;
  }
  .zh--knowledge .zhMobileBrandStats{
    display:flex!important;
    align-items:center!important;
    justify-content:center!important;
    gap:0!important;
    width:100%!important;
  }
  .zh--knowledge .zhMobileBrandStat{
    position:relative!important;
    flex:1 1 0!important;
    min-width:0!important;
    padding:0 10px!important;
    border:0!important;
    background:transparent!important;
    text-align:center!important;
  }
  .zh--knowledge .zhMobileBrandStat + .zhMobileBrandStat::before{
    content:""!important;
    position:absolute!important;
    left:0!important;
    top:50%!important;
    width:1px!important;
    height:30px!important;
    transform:translateY(-50%)!important;
    background:linear-gradient(to bottom,transparent,rgba(255,255,255,.16),transparent)!important;
  }
  .zh--knowledge .zhMobileBrandStat b{font-size:19px!important;line-height:1!important;letter-spacing:-.04em!important;color:#fff!important}
  .zh--knowledge .zhMobileBrandStat i{font-size:10px!important;margin-left:2px!important;color:var(--gold)!important}
  .zh--knowledge .zhMobileBrandStat span{display:block!important;margin-top:4px!important;font-size:9.6px!important;line-height:1.15!important;color:rgba(255,255,255,.48)!important;white-space:nowrap!important}
}
@media(max-width:600px){
  .zh--knowledge .zh__mobileBrandMock{width:min(500px,96vw)!important;max-width:96vw!important;margin:24px auto 16px!important}
  .zh--knowledge .zkViz--mobile{gap:10px}
  .zh--knowledge .zkViz--mobile .zkCard{padding:16px 16px 17px;border-radius:20px}
  .zh--knowledge .zh__mobileBrandStatsWrap{padding:13px 0 4px!important;margin-bottom:4px!important}
  .zh--knowledge .zhMobileBrandStat{padding:0 7px!important}
  .zh--knowledge .zhMobileBrandStat + .zhMobileBrandStat::before{height:28px!important}
  .zh--knowledge .zhMobileBrandStat b{font-size:18px!important}
  .zh--knowledge .zhMobileBrandStat span{font-size:8.8px!important}
}


/* PATCH v2 — Wiedza: zdjęcie z aktualnej podstrony + spójna interlinia H1 */
.zh--knowledge .zh__title{
  max-width:14.2ch!important;
  line-height:1.025!important;
  padding:0!important;
  margin:0!important;
  overflow:visible!important;
  text-wrap:balance!important;
}
.zh--knowledge .zh__title .zh__grad{
  display:inline!important;
  line-height:inherit!important;
  padding:0!important;
  margin:0!important;
  vertical-align:baseline!important;
  overflow:visible!important;
  -webkit-box-decoration-break:slice!important;
  box-decoration-break:slice!important;
}
.zh--knowledge .zkViz,
.zh--knowledge .zkCard{display:none!important}
.zh--knowledge .zh__stage--knowledge{right:-2vw!important;width:66vw!important;min-width:860px!important;max-width:1120px!important}
.zh--knowledge .zh__brandMockWrap{transform:translate3d(-1vw,-42px,0) scale(1.02)!important;rotate:0deg!important}
.zh--knowledge .zkPerson{position:absolute;inset:0;margin:0;display:flex;align-items:flex-end;justify-content:center;pointer-events:none;overflow:visible;isolation:isolate}
.zh--knowledge .zkPerson::before{content:"";position:absolute;left:50%;bottom:6%;width:72%;height:24%;transform:translateX(-50%);background:radial-gradient(ellipse at center,rgba(0,0,0,.38),rgba(0,0,0,.13) 48%,transparent 74%);opacity:.58;z-index:0}
.zh--knowledge .zkPerson__img{position:relative;z-index:1;display:block;width:clamp(460px,38vw,760px);max-width:none;height:auto;filter:saturate(.96) brightness(.98) drop-shadow(0 42px 72px rgba(0,0,0,.38));-webkit-mask-image:linear-gradient(to bottom,#000 0%,#000 53%,rgba(0,0,0,.92) 66%,rgba(0,0,0,.62) 79%,rgba(0,0,0,.28) 90%,transparent 99%);mask-image:linear-gradient(to bottom,#000 0%,#000 53%,rgba(0,0,0,.92) 66%,rgba(0,0,0,.62) 79%,rgba(0,0,0,.28) 90%,transparent 99%);animation:zhBrandFloatSoft 8s ease-in-out infinite!important}
.zh--knowledge .zkStats{position:absolute;z-index:8;right:clamp(42px,5.2vw,98px);bottom:clamp(72px,12vh,128px);display:flex;align-items:center;gap:0;border:1px solid rgba(255,255,255,.13);border-radius:22px;background:rgba(1,4,12,.46);-webkit-backdrop-filter:blur(22px) saturate(1.28);backdrop-filter:blur(22px) saturate(1.28);box-shadow:0 24px 70px rgba(0,0,0,.42),inset 0 1px 0 rgba(255,255,255,.1);overflow:hidden}
.zh--knowledge .zkStats span{position:relative;display:block;min-width:96px;padding:14px 15px;text-align:center;color:#fff}
.zh--knowledge .zkStats span+span::before{content:"";position:absolute;left:0;top:50%;width:1px;height:32px;transform:translateY(-50%);background:linear-gradient(to bottom,transparent,rgba(255,255,255,.16),transparent)}
.zh--knowledge .zkStats strong{display:block;font-size:22px;line-height:1;font-weight:820;letter-spacing:-.045em;color:#fff}
.zh--knowledge .zkStats em{display:block;margin-top:5px;font-style:normal;font-size:9.5px;line-height:1.15;font-weight:740;color:rgba(255,255,255,.50);white-space:nowrap}
.zh--knowledge .zh__brandMini{right:clamp(54px,5.8vw,112px)!important;bottom:clamp(164px,24vh,238px)!important;width:min(360px,28vw)!important}
@media(min-width:1451px) and (max-width:1640px){
  .zh--knowledge .zh__title{font-size:clamp(58px,5.08vw,80px)!important;max-width:12.8ch!important;line-height:1.025!important}
  .zh--knowledge .zh__stage--knowledge{width:62vw!important;right:-.5vw!important;max-width:980px!important;min-width:0!important}
  .zh--knowledge .zh__brandMockWrap{transform:translate3d(-.5vw,-58px,0) scale(1)!important}
  .zh--knowledge .zkPerson__img{width:clamp(440px,39vw,680px)}
  .zh--knowledge .zh__brandMini{right:clamp(46px,5vw,86px)!important;bottom:clamp(150px,22vh,208px)!important;width:min(330px,25vw)!important}
  .zh--knowledge .zkStats{right:clamp(40px,4.8vw,86px);bottom:clamp(66px,11vh,116px)}
}
@media(min-width:1101px) and (max-width:1450px){
  .zh--knowledge .zh__title{font-size:clamp(53px,5vw,70px)!important;max-width:12.6ch!important;line-height:1.03!important}
  .zh--knowledge .zh__stage--knowledge{width:64vw!important;right:-1vw!important;min-width:0!important;max-width:850px!important}
  .zh--knowledge .zh__brandMockWrap{transform:translate3d(-.5vw,-64px,0) scale(.98)!important}
  .zh--knowledge .zkPerson__img{width:clamp(390px,40vw,620px)}
  .zh--knowledge .zh__brandMini{right:clamp(34px,4.5vw,70px)!important;bottom:clamp(138px,21vh,186px)!important;width:min(300px,24vw)!important;padding:16px 18px 18px!important}
  .zh--knowledge .zkStats{right:clamp(32px,4vw,66px);bottom:clamp(62px,10vh,104px)}
  .zh--knowledge .zkStats span{min-width:82px;padding:12px 12px}
}
@media(max-width:1100px){
  .zh--knowledge .zh__title{line-height:1.045!important;padding:0!important;margin:0!important;max-width:none!important}
  .zh--knowledge .zh__title .zh__grad{display:inline!important;line-height:inherit!important;padding:0!important;margin:0!important}
  .zh--knowledge .zh__mobileBrandMock{width:min(620px,96vw)!important;max-width:96vw!important;margin:28px auto 18px!important;overflow:visible!important;pointer-events:none!important}
  .zh--knowledge .zkPerson--mobile{position:relative!important;inset:auto!important;height:auto!important;display:block!important;width:100%!important;overflow:visible!important}
  .zh--knowledge .zkPerson--mobile::before{bottom:4%;width:78%;height:22%}
  .zh--knowledge .zkPerson--mobile .zkPerson__img{width:112%!important;max-width:112%!important;margin-left:50%!important;transform:translateX(-50%)!important;animation:zhBrandFloatSoft 8s ease-in-out infinite!important}
  .zh--knowledge .zkStats--mobile{position:relative!important;right:auto!important;bottom:auto!important;width:100%;margin:0 auto 0;justify-content:center;background:transparent!important;border:0!important;box-shadow:none!important;-webkit-backdrop-filter:none!important;backdrop-filter:none!important}
  .zh--knowledge .zkStats--mobile span{flex:1 1 0;min-width:0;padding:0 10px}
}
@media(max-width:600px){
  .zh--knowledge .zh__title{line-height:1.055!important}
  .zh--knowledge .zh__mobileBrandMock{width:min(520px,96vw)!important;margin:24px auto 18px!important}
  .zh--knowledge .zkPerson--mobile .zkPerson__img{width:118%!important;max-width:118%!important}
  .zh--knowledge .zkStats--mobile strong{font-size:18px}
  .zh--knowledge .zkStats--mobile em{font-size:8.8px}
}



/* PATCH v3 — Wiedza: remove mobile stats/chips + enlarge right image by 25% */
.zh--knowledge .zh__chips,
.zh--knowledge .zh__mobileBrandStatsWrap,
.zh--knowledge .zkStats--mobile{
  display:none!important;
  visibility:hidden!important;
  opacity:0!important;
  pointer-events:none!important;
}
@media(min-width:1101px){
  .zh--knowledge .zkPerson__img{
    width:clamp(575px,47.5vw,950px)!important;
  }
}
@media(min-width:1451px) and (max-width:1640px){
  .zh--knowledge .zkPerson__img{
    width:clamp(550px,48.75vw,850px)!important;
  }
}
@media(min-width:1101px) and (max-width:1450px){
  .zh--knowledge .zkPerson__img{
    width:clamp(488px,50vw,775px)!important;
  }
}
@media(max-width:1100px){
  .zh--knowledge .zkPerson--mobile .zkPerson__img{
    width:140%!important;
    max-width:140%!important;
    margin-left:50%!important;
    transform:translateX(-50%)!important;
  }
  .zh--knowledge .zh__mobileBrandMock{
    margin-bottom:24px!important;
  }
}
@media(max-width:600px){
  .zh--knowledge .zkPerson--mobile .zkPerson__img{
    width:148%!important;
    max-width:148%!important;
  }
}


/* PATCH v4 — Wiedza mobile: równe marginesy, krótsze zdjęcie, statystyki + scroll zostają */
@media(max-width:1100px){
  .zh--knowledge .zh__inner,
  .zh--knowledge .zh__mobileBrandMock,
  .zh--knowledge .zkStats--mobile,
  .zh--knowledge .zh__mobileScroll{
    width:calc(100% - 36px)!important;
    max-width:calc(100vw - 36px)!important;
    margin-left:auto!important;
    margin-right:auto!important;
  }
  .zh--knowledge .zh__mobileBrandMock{
    display:block!important;
    position:relative!important;
    overflow:hidden!important;
    isolation:isolate!important;
    height:clamp(330px,58vw,440px)!important;
    margin-top:26px!important;
    margin-bottom:12px!important;
    border-radius:28px!important;
    pointer-events:none!important;
  }
  .zh--knowledge .zkPerson--mobile{
    position:absolute!important;
    inset:0!important;
    width:100%!important;
    height:100%!important;
    display:flex!important;
    align-items:flex-end!important;
    justify-content:center!important;
    overflow:visible!important;
  }
  .zh--knowledge .zkPerson--mobile .zkPerson__img{
    width:124%!important;
    max-width:124%!important;
    margin-left:0!important;
    transform:none!important;
    object-fit:contain!important;
    object-position:center bottom!important;
    -webkit-mask-image:linear-gradient(to bottom,#000 0%,#000 58%,rgba(0,0,0,.88) 74%,rgba(0,0,0,.38) 91%,transparent 100%)!important;
    mask-image:linear-gradient(to bottom,#000 0%,#000 58%,rgba(0,0,0,.88) 74%,rgba(0,0,0,.38) 91%,transparent 100%)!important;
  }
  .zh--knowledge .zkStats--mobile{
    display:flex!important;
    visibility:visible!important;
    opacity:1!important;
    pointer-events:none!important;
    position:relative!important;
    right:auto!important;
    bottom:auto!important;
    z-index:8!important;
    justify-content:center!important;
    gap:0!important;
    margin-top:0!important;
    margin-bottom:10px!important;
    padding:15px 0 6px!important;
    border:0!important;
    border-top:1px solid rgba(255,255,255,.11)!important;
    border-radius:0!important;
    background:transparent!important;
    box-shadow:none!important;
    -webkit-backdrop-filter:none!important;
    backdrop-filter:none!important;
    overflow:visible!important;
  }
  .zh--knowledge .zkStats--mobile span{
    position:relative!important;
    flex:1 1 0!important;
    min-width:0!important;
    padding:0 10px!important;
    text-align:center!important;
    color:#fff!important;
  }
  .zh--knowledge .zkStats--mobile span+span::before{
    content:""!important;
    position:absolute!important;
    left:0!important;
    top:50%!important;
    width:1px!important;
    height:30px!important;
    transform:translateY(-50%)!important;
    background:linear-gradient(to bottom,transparent,rgba(255,255,255,.16),transparent)!important;
  }
  .zh--knowledge .zkStats--mobile strong{
    display:block!important;
    font-size:19px!important;
    line-height:1!important;
    font-weight:820!important;
    letter-spacing:-.04em!important;
    color:#fff!important;
  }
  .zh--knowledge .zkStats--mobile em{
    display:block!important;
    margin-top:4px!important;
    font-style:normal!important;
    font-size:9.6px!important;
    line-height:1.15!important;
    font-weight:740!important;
    color:rgba(255,255,255,.48)!important;
    white-space:nowrap!important;
  }
  .zh--knowledge .zh__mobileScroll{
    display:flex!important;
    min-height:34px!important;
    margin-top:4px!important;
    margin-bottom:24px!important;
    padding:0!important;
    border:0!important;
    background:transparent!important;
    -webkit-backdrop-filter:none!important;
    backdrop-filter:none!important;
    clear:both!important;
  }
}
@media(max-width:600px){
  .zh--knowledge .zh__inner,
  .zh--knowledge .zh__mobileBrandMock,
  .zh--knowledge .zkStats--mobile,
  .zh--knowledge .zh__mobileScroll{
    width:calc(100% - 36px)!important;
    max-width:calc(100vw - 36px)!important;
  }
  .zh--knowledge .zh__mobileBrandMock{
    height:clamp(300px,82vw,380px)!important;
    margin-top:22px!important;
    margin-bottom:10px!important;
    border-radius:24px!important;
  }
  .zh--knowledge .zkPerson--mobile .zkPerson__img{
    width:132%!important;
    max-width:132%!important;
  }
  .zh--knowledge .zkStats--mobile{padding:13px 0 5px!important;margin-bottom:8px!important;}
  .zh--knowledge .zkStats--mobile span{padding:0 7px!important;}
  .zh--knowledge .zkStats--mobile span+span::before{height:28px!important;}
  .zh--knowledge .zkStats--mobile strong{font-size:18px!important;}
  .zh--knowledge .zkStats--mobile em{font-size:8.8px!important;}
  .zh--knowledge .zh__mobileScroll{margin-top:4px!important;margin-bottom:22px!important;}
}
@media(max-width:390px){
  .zh--knowledge .zh__inner,
  .zh--knowledge .zh__mobileBrandMock,
  .zh--knowledge .zkStats--mobile,
  .zh--knowledge .zh__mobileScroll{
    width:calc(100% - 32px)!important;
    max-width:calc(100vw - 32px)!important;
  }
  .zh--knowledge .zkStats--mobile span{padding:0 5px!important;}
  .zh--knowledge .zkStats--mobile strong{font-size:17px!important;}
  .zh--knowledge .zkStats--mobile em{font-size:8.2px!important;}
}



/* PATCH v5 — Wiedza mobile: zdjęcie luzem + równe marginesy treści/CTA */
@media(max-width:1100px){
  .zh--knowledge,
  .zh--knowledge *{
    box-sizing:border-box!important;
  }
  .zh--knowledge{
    overflow:hidden!important;
  }
  .zh--knowledge .zh__inner,
  .zh--knowledge .zh__copy,
  .zh--knowledge .zh__actions,
  .zh--knowledge .zh__mobileBrandMock,
  .zh--knowledge .zkStats--mobile,
  .zh--knowledge .zh__mobileScroll{
    width:calc(100vw - 36px)!important;
    max-width:calc(100vw - 36px)!important;
    margin-left:auto!important;
    margin-right:auto!important;
    left:auto!important;
    right:auto!important;
  }
  .zh--knowledge .zh__inner{
    padding-left:0!important;
    padding-right:0!important;
  }
  .zh--knowledge .zh__actions{
    display:flex!important;
    justify-content:flex-start!important;
    gap:12px!important;
  }
  .zh--knowledge .zh__actions .zh__btn{
    min-width:0!important;
  }

  /* zdjęcie bez karty/kwadratu, bez ucięcia i bez zaokrąglonych rogów */
  .zh--knowledge .zh__mobileBrandMock{
    display:block!important;
    position:relative!important;
    height:auto!important;
    min-height:0!important;
    overflow:visible!important;
    isolation:isolate!important;
    margin-top:22px!important;
    margin-bottom:8px!important;
    border-radius:0!important;
    background:transparent!important;
    box-shadow:none!important;
    border:0!important;
    pointer-events:none!important;
  }
  .zh--knowledge .zkPerson--mobile{
    position:relative!important;
    inset:auto!important;
    display:block!important;
    width:100%!important;
    height:auto!important;
    margin:0!important;
    overflow:visible!important;
    border-radius:0!important;
    background:transparent!important;
  }
  .zh--knowledge .zkPerson--mobile::before{
    left:50%!important;
    bottom:3%!important;
    width:78%!important;
    height:20%!important;
    transform:translateX(-50%)!important;
    border-radius:999px!important;
  }
  .zh--knowledge .zkPerson--mobile .zkPerson__img{
    display:block!important;
    width:108%!important;
    max-width:108%!important;
    height:auto!important;
    margin-left:50%!important;
    margin-right:0!important;
    transform:translateX(-50%)!important;
    object-fit:contain!important;
    object-position:center bottom!important;
    border-radius:0!important;
    filter:saturate(.96) brightness(.98) drop-shadow(0 30px 56px rgba(0,0,0,.40))!important;
    -webkit-mask-image:linear-gradient(to bottom,#000 0%,#000 66%,rgba(0,0,0,.88) 78%,rgba(0,0,0,.45) 91%,transparent 100%)!important;
    mask-image:linear-gradient(to bottom,#000 0%,#000 66%,rgba(0,0,0,.88) 78%,rgba(0,0,0,.45) 91%,transparent 100%)!important;
  }
}
@media(max-width:600px){
  .zh--knowledge .zh__inner,
  .zh--knowledge .zh__copy,
  .zh--knowledge .zh__actions,
  .zh--knowledge .zh__mobileBrandMock,
  .zh--knowledge .zkStats--mobile,
  .zh--knowledge .zh__mobileScroll{
    width:calc(100vw - 36px)!important;
    max-width:calc(100vw - 36px)!important;
    margin-left:auto!important;
    margin-right:auto!important;
  }
  .zh--knowledge .zh__actions{
    flex-direction:column!important;
    align-items:stretch!important;
  }
  .zh--knowledge .zh__actions .zh__btn{
    width:100%!important;
    justify-content:center!important;
  }
  .zh--knowledge .zh__mobileBrandMock{
    margin-top:18px!important;
    margin-bottom:6px!important;
  }
  .zh--knowledge .zkPerson--mobile .zkPerson__img{
    width:112%!important;
    max-width:112%!important;
  }
}
@media(max-width:390px){
  .zh--knowledge .zh__inner,
  .zh--knowledge .zh__copy,
  .zh--knowledge .zh__actions,
  .zh--knowledge .zh__mobileBrandMock,
  .zh--knowledge .zkStats--mobile,
  .zh--knowledge .zh__mobileScroll{
    width:calc(100vw - 32px)!important;
    max-width:calc(100vw - 32px)!important;
  }
}



/* PATCH Wiedza v6 — 80px przestrzeni pod CTA jak w Kontakt */
@media(min-width:1101px){
  .zh--knowledge .zh__actions{
    margin-bottom:80px!important;
  }
  .zh--knowledge .zh__inner{
    padding-bottom:158px!important;
  }
}


/* PATCH v7 — Wiedza: mniejsze desktop/responsive jak Kontakt + bez kolizji pillów ze zdjęciem */
@media(min-width:1101px) and (max-width:1450px){
  .zh--knowledge .zh__stage--knowledge{
    width:65vw!important;
    right:-2vw!important;
    max-width:860px!important;
  }
  .zh--knowledge .zh__brandMockWrap{
    transform:translate3d(-.8vw,-118px,0) scale(1.03)!important;
    transform-origin:center center!important;
  }
  .zh--knowledge .zkPerson__img{
    width:clamp(500px,51vw,790px)!important;
  }
  .zh--knowledge .zh__brandMini{
    right:clamp(34px,4.4vw,66px)!important;
    bottom:clamp(78px,11vh,112px)!important;
    width:min(292px,24vw)!important;
    padding:15px 17px 17px!important;
  }
  .zh--knowledge .zkStats{
    right:clamp(32px,4vw,62px)!important;
    bottom:clamp(22px,4.6vh,56px)!important;
  }

  /* pill'e odsunięte od twarzy i od karty "Baza wiedzy" */
  .zh--knowledge .zh__photoChip{
    max-width:min(205px,19vw)!important;
    min-height:35px!important;
    padding:0 12px!important;
    font-size:10.4px!important;
    white-space:nowrap!important;
    overflow:hidden!important;
    text-overflow:ellipsis!important;
  }
  .zh--knowledge .zh__photoChip span{
    overflow:hidden!important;
    text-overflow:ellipsis!important;
    white-space:nowrap!important;
    min-width:0!important;
  }
  .zh--knowledge .zh__photoChip--a{
    right:auto!important;
    left:clamp(16px,2.4vw,38px)!important;
    top:18%!important;
  }
  .zh--knowledge .zh__photoChip--b{
    right:clamp(26px,4vw,58px)!important;
    top:24%!important;
    left:auto!important;
  }
  .zh--knowledge .zh__photoChip--c{
    right:auto!important;
    left:clamp(28px,3.4vw,54px)!important;
    top:54%!important;
  }
  .zh--knowledge .zh__photoChip--d{
    right:clamp(34px,5vw,72px)!important;
    left:auto!important;
    bottom:33%!important;
  }
  .zh--knowledge .zh__photoChip--e{
    right:clamp(34px,5vw,76px)!important;
    left:auto!important;
    bottom:17%!important;
  }
}

@media(min-width:1101px) and (max-width:1260px){
  .zh--knowledge .zh__stage--knowledge{
    width:64vw!important;
    right:-2.5vw!important;
    max-width:790px!important;
  }
  .zh--knowledge .zh__brandMockWrap{
    transform:translate3d(-.5vw,-138px,0) scale(1.02)!important;
  }
  .zh--knowledge .zkPerson__img{
    width:clamp(485px,52vw,720px)!important;
  }
  .zh--knowledge .zh__brandMini{
    right:32px!important;
    bottom:82px!important;
    width:274px!important;
  }
  .zh--knowledge .zkStats{
    right:32px!important;
    bottom:24px!important;
  }
  .zh--knowledge .zkStats span{
    min-width:76px!important;
    padding:11px 10px!important;
  }
  .zh--knowledge .zh__photoChip{
    max-width:168px!important;
    font-size:9.7px!important;
    min-height:33px!important;
    padding:0 10px!important;
  }
  .zh--knowledge .zh__photoChip--a{
    left:18px!important;
    top:17%!important;
  }
  .zh--knowledge .zh__photoChip--b{
    right:30px!important;
    top:23%!important;
  }
  .zh--knowledge .zh__photoChip--c{
    left:20px!important;
    top:56%!important;
  }
  .zh--knowledge .zh__photoChip--d{
    right:44px!important;
    bottom:35%!important;
  }
  .zh--knowledge .zh__photoChip--e{
    right:38px!important;
    bottom:16%!important;
  }
}

@media(min-width:1451px) and (max-width:1680px){
  .zh--knowledge .zh__brandMockWrap{
    transform:translate3d(-.9vw,-90px,0) scale(1.01)!important;
  }
  .zh--knowledge .zh__photoChip--a{
    right:auto!important;
    left:clamp(42px,4vw,72px)!important;
    top:18%!important;
  }
  .zh--knowledge .zh__photoChip--b{
    right:clamp(70px,7vw,116px)!important;
    top:24%!important;
  }
  .zh--knowledge .zh__photoChip--c{
    right:auto!important;
    left:clamp(52px,5vw,92px)!important;
    top:55%!important;
  }
  .zh--knowledge .zh__photoChip--d{
    right:clamp(88px,8vw,136px)!important;
    bottom:33%!important;
  }
  .zh--knowledge .zh__photoChip--e{
    right:clamp(84px,8vw,132px)!important;
    bottom:17%!important;
  }
}



/* PATCH v8 — Wiedza: brak lustrzanego odwracania, zdjęcie wyżej, pille poza twarzą/kartą */
.zh--knowledge .zh__stage--knowledge,
.zh--knowledge .zh__brandMockWrap,
.zh--knowledge .zkPerson,
.zh--knowledge .zkPerson__img{
  transform-style:flat!important;
  scale:1!important;
}
@media(min-width:1681px){
  .zh--knowledge .zh__stage--knowledge{right:-1vw!important;width:64vw!important;max-width:1080px!important;min-width:0!important;}
  .zh--knowledge .zh__brandMockWrap{transform:translate3d(-1.5vw,-70px,0) scale(1.02)!important;rotate:0deg!important;}
  .zh--knowledge .zkPerson__img{width:clamp(560px,45vw,900px)!important;}
  .zh--knowledge .zh__brandMini{right:clamp(46px,5vw,94px)!important;bottom:clamp(128px,18vh,188px)!important;}
  .zh--knowledge .zh__photoChip--a{left:auto!important;right:clamp(34px,5vw,90px)!important;top:clamp(112px,18vh,170px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--b{left:auto!important;right:clamp(24px,4vw,76px)!important;top:clamp(250px,36vh,330px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--c{left:auto!important;right:clamp(315px,32vw,470px)!important;top:auto!important;bottom:clamp(150px,21vh,230px)!important;}
  .zh--knowledge .zh__photoChip--d{left:auto!important;right:clamp(350px,36vw,540px)!important;top:clamp(255px,36vh,350px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--e{left:auto!important;right:clamp(310px,30vw,450px)!important;top:auto!important;bottom:clamp(74px,11vh,124px)!important;}
}
@media(min-width:1451px) and (max-width:1680px){
  .zh--knowledge .zh__stage--knowledge{right:-1vw!important;width:63vw!important;max-width:980px!important;min-width:0!important;}
  .zh--knowledge .zh__brandMockWrap{transform:translate3d(-1.5vw,-120px,0) scale(1.04)!important;rotate:0deg!important;}
  .zh--knowledge .zkPerson__img{width:clamp(540px,48vw,820px)!important;}
  .zh--knowledge .zh__brandMini{right:clamp(44px,5vw,86px)!important;bottom:clamp(92px,14vh,142px)!important;z-index:9!important;}
  .zh--knowledge .zh__photoChip{z-index:10!important;max-width:230px!important;}
  .zh--knowledge .zh__photoChip--a{left:auto!important;right:clamp(32px,4.5vw,82px)!important;top:clamp(92px,14vh,138px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--b{left:auto!important;right:clamp(22px,3.8vw,68px)!important;top:clamp(214px,31vh,288px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--c{left:auto!important;right:clamp(300px,31vw,430px)!important;top:auto!important;bottom:clamp(146px,20vh,202px)!important;}
  .zh--knowledge .zh__photoChip--d{left:auto!important;right:clamp(330px,34vw,500px)!important;top:clamp(230px,34vh,316px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--e{left:auto!important;right:clamp(292px,29vw,410px)!important;top:auto!important;bottom:clamp(58px,9vh,92px)!important;}
}
@media(min-width:1261px) and (max-width:1450px){
  .zh--knowledge .zh__stage--knowledge{right:-.5vw!important;width:64vw!important;max-width:850px!important;min-width:0!important;}
  .zh--knowledge .zh__brandMockWrap{transform:translate3d(-.5vw,-132px,0) scale(1.03)!important;rotate:0deg!important;}
  .zh--knowledge .zkPerson__img{width:clamp(500px,49vw,730px)!important;}
  .zh--knowledge .zh__brandMini{right:clamp(30px,4vw,62px)!important;bottom:clamp(84px,13vh,126px)!important;width:min(286px,23vw)!important;z-index:9!important;}
  .zh--knowledge .zh__photoChip{font-size:10.4px!important;min-height:35px!important;padding:0 11px!important;z-index:10!important;max-width:190px!important;}
  .zh--knowledge .zh__photoChip--a{left:auto!important;right:clamp(28px,3.8vw,60px)!important;top:clamp(76px,12vh,118px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--b{left:auto!important;right:clamp(20px,3vw,48px)!important;top:clamp(192px,30vh,258px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--c{left:auto!important;right:clamp(250px,28vw,340px)!important;top:auto!important;bottom:clamp(128px,19vh,178px)!important;}
  .zh--knowledge .zh__photoChip--d{left:auto!important;right:clamp(252px,30vw,370px)!important;top:clamp(214px,34vh,296px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--e{left:auto!important;right:clamp(238px,26vw,320px)!important;top:auto!important;bottom:clamp(46px,8vh,76px)!important;}
}
@media(min-width:1101px) and (max-width:1260px){
  .zh--knowledge .zh__stage--knowledge{right:0!important;width:61vw!important;max-width:760px!important;min-width:0!important;}
  .zh--knowledge .zh__brandMockWrap{transform:translate3d(0,-154px,0) scale(1.00)!important;rotate:0deg!important;}
  .zh--knowledge .zkPerson__img{width:clamp(440px,47vw,640px)!important;}
  .zh--knowledge .zh__brandMini{right:24px!important;bottom:94px!important;width:min(255px,22vw)!important;padding:14px 15px 15px!important;z-index:9!important;}
  .zh--knowledge .zh__brandMini strong{font-size:18px!important;}
  .zh--knowledge .zh__brandMini p{font-size:11px!important;line-height:1.42!important;}
  .zh--knowledge .zh__photoChip{font-size:9.5px!important;min-height:32px!important;padding:0 9px!important;z-index:10!important;max-width:158px!important;}
  .zh--knowledge .zh__photoChip svg{width:13px!important;height:13px!important;}
  .zh--knowledge .zh__photoChip--a{left:auto!important;right:24px!important;top:68px!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--b{left:auto!important;right:18px!important;top:178px!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--c{left:auto!important;right:238px!important;top:auto!important;bottom:116px!important;}
  .zh--knowledge .zh__photoChip--d{left:auto!important;right:244px!important;top:204px!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--e{left:auto!important;right:232px!important;top:auto!important;bottom:38px!important;}
}
/* tablet/mobile: upewnij się, że zdjęcie nie odbija się lustrzanie */
@media(max-width:1100px){
  .zh--knowledge .zh__stage,
  .zh--knowledge .zh__stage--knowledge,
  .zh--knowledge .zh__mobileBrandMock,
  .zh--knowledge .zkPerson--mobile,
  .zh--knowledge .zkPerson--mobile .zkPerson__img{
    transform:none!important;
    scale:1!important;
  }
  .zh--knowledge .zkPerson--mobile .zkPerson__img{
    margin-left:50%!important;
    transform:translateX(-50%)!important;
  }
}



/* PATCH v9 — Wiedza: karta Baza wiedzy nie nachodzi na licznik/kategorie na średnich desktopach */
@media(min-width:1451px) and (max-width:1680px){
  .zh--knowledge .zkStats{
    right:clamp(42px,4.8vw,86px)!important;
    bottom:clamp(48px,8vh,82px)!important;
    z-index:11!important;
  }
  .zh--knowledge .zh__brandMini{
    right:clamp(54px,5.4vw,96px)!important;
    bottom:clamp(168px,23vh,222px)!important;
    width:min(330px,24vw)!important;
    z-index:9!important;
  }
  .zh--knowledge .zh__photoChip--e{
    right:clamp(280px,28vw,400px)!important;
    bottom:clamp(92px,13vh,132px)!important;
  }
}
@media(min-width:1261px) and (max-width:1450px){
  .zh--knowledge .zkStats{
    right:clamp(30px,4vw,66px)!important;
    bottom:clamp(44px,7vh,72px)!important;
    z-index:11!important;
  }
  .zh--knowledge .zkStats span{
    min-width:78px!important;
    padding:11px 11px!important;
  }
  .zh--knowledge .zkStats strong{font-size:20px!important;}
  .zh--knowledge .zkStats em{font-size:8.8px!important;}
  .zh--knowledge .zh__brandMini{
    right:clamp(32px,4vw,62px)!important;
    bottom:clamp(154px,22vh,198px)!important;
    width:min(278px,22vw)!important;
    z-index:9!important;
  }
  .zh--knowledge .zh__photoChip--e{
    right:clamp(220px,25vw,310px)!important;
    bottom:clamp(88px,13vh,120px)!important;
  }
}
@media(min-width:1101px) and (max-width:1260px){
  .zh--knowledge .zkStats{
    right:22px!important;
    bottom:44px!important;
    z-index:11!important;
  }
  .zh--knowledge .zkStats span{
    min-width:70px!important;
    padding:10px 9px!important;
  }
  .zh--knowledge .zkStats strong{font-size:18px!important;}
  .zh--knowledge .zkStats em{font-size:8px!important;}
  .zh--knowledge .zh__brandMini{
    right:24px!important;
    bottom:156px!important;
    width:min(252px,22vw)!important;
    padding:13px 14px 14px!important;
    z-index:9!important;
  }
  .zh--knowledge .zh__brandMini strong{font-size:17px!important;}
  .zh--knowledge .zh__brandMini p{font-size:10.5px!important;line-height:1.38!important;}
  .zh--knowledge .zh__photoChip--e{
    right:218px!important;
    bottom:92px!important;
  }
}



/* PATCH v10 — Wiedza: karta/stats wyżej + logiczne ułożenie chipów wokół zdjęcia */
@media(min-width:1681px){
  .zh--knowledge .zh__brandMockWrap{
    transform:translate3d(-1.5vw,-82px,0) scale(1.02)!important;
    rotate:0deg!important;
  }
  .zh--knowledge .zh__brandMini{
    right:clamp(54px,5vw,100px)!important;
    bottom:clamp(190px,26vh,272px)!important;
    z-index:12!important;
  }
  .zh--knowledge .zkStats{
    right:clamp(54px,5vw,100px)!important;
    bottom:clamp(108px,16vh,154px)!important;
    z-index:12!important;
  }
  .zh--knowledge .zh__photoChip{
    z-index:10!important;
    max-width:230px!important;
  }
  /* układ po łuku: góra/prawa, prawa środek, lewy dół, lewy środek, dół-prawo */
  .zh--knowledge .zh__photoChip--a{left:auto!important;right:clamp(76px,7vw,132px)!important;top:clamp(94px,13vh,142px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--b{left:auto!important;right:clamp(30px,4vw,76px)!important;top:clamp(214px,30vh,292px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--c{left:auto!important;right:clamp(390px,38vw,560px)!important;top:clamp(248px,35vh,338px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--d{left:auto!important;right:clamp(420px,40vw,600px)!important;bottom:clamp(150px,21vh,224px)!important;top:auto!important;}
  .zh--knowledge .zh__photoChip--e{left:auto!important;right:clamp(300px,29vw,440px)!important;bottom:clamp(74px,11vh,118px)!important;top:auto!important;}
}

@media(min-width:1451px) and (max-width:1680px){
  .zh--knowledge .zh__brandMockWrap{
    transform:translate3d(-1.3vw,-128px,0) scale(1.04)!important;
    rotate:0deg!important;
  }
  .zh--knowledge .zh__brandMini{
    right:clamp(52px,5vw,92px)!important;
    bottom:clamp(178px,25vh,236px)!important;
    width:min(326px,24vw)!important;
    z-index:12!important;
  }
  .zh--knowledge .zkStats{
    right:clamp(50px,4.8vw,90px)!important;
    bottom:clamp(92px,14vh,132px)!important;
    z-index:12!important;
  }
  .zh--knowledge .zh__photoChip{
    z-index:10!important;
    max-width:min(220px,20vw)!important;
    font-size:10.8px!important;
    min-height:36px!important;
    padding:0 12px!important;
  }
  .zh--knowledge .zh__photoChip--a{left:auto!important;right:clamp(74px,7vw,118px)!important;top:clamp(82px,12vh,122px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--b{left:auto!important;right:clamp(28px,3.8vw,66px)!important;top:clamp(188px,28vh,252px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--c{left:auto!important;right:clamp(320px,34vw,470px)!important;top:clamp(232px,34vh,308px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--d{left:auto!important;right:clamp(330px,36vw,500px)!important;bottom:clamp(138px,20vh,194px)!important;top:auto!important;}
  .zh--knowledge .zh__photoChip--e{left:auto!important;right:clamp(250px,27vw,374px)!important;bottom:clamp(66px,10vh,100px)!important;top:auto!important;}
}

@media(min-width:1261px) and (max-width:1450px){
  .zh--knowledge .zh__stage--knowledge{
    right:-.5vw!important;
    width:64vw!important;
    max-width:850px!important;
    min-width:0!important;
  }
  .zh--knowledge .zh__brandMockWrap{
    transform:translate3d(-.7vw,-148px,0) scale(1.03)!important;
    rotate:0deg!important;
  }
  .zh--knowledge .zh__brandMini{
    right:clamp(30px,4vw,60px)!important;
    bottom:clamp(174px,25vh,216px)!important;
    width:min(276px,22vw)!important;
    padding:14px 16px 16px!important;
    z-index:12!important;
  }
  .zh--knowledge .zkStats{
    right:clamp(30px,4vw,60px)!important;
    bottom:clamp(92px,14vh,122px)!important;
    z-index:12!important;
  }
  .zh--knowledge .zkStats span{min-width:76px!important;padding:10px 10px!important;}
  .zh--knowledge .zkStats strong{font-size:19px!important;}
  .zh--knowledge .zkStats em{font-size:8.6px!important;}
  .zh--knowledge .zh__photoChip{
    z-index:10!important;
    max-width:178px!important;
    font-size:9.8px!important;
    min-height:33px!important;
    padding:0 10px!important;
  }
  .zh--knowledge .zh__photoChip--a{left:auto!important;right:clamp(54px,5vw,84px)!important;top:clamp(72px,11vh,106px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--b{left:auto!important;right:clamp(20px,3vw,46px)!important;top:clamp(164px,26vh,222px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--c{left:auto!important;right:clamp(250px,29vw,350px)!important;top:clamp(214px,34vh,286px)!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--d{left:auto!important;right:clamp(268px,31vw,380px)!important;bottom:clamp(130px,20vh,176px)!important;top:auto!important;}
  .zh--knowledge .zh__photoChip--e{left:auto!important;right:clamp(196px,23vw,286px)!important;bottom:clamp(58px,9vh,88px)!important;top:auto!important;}
}

@media(min-width:1101px) and (max-width:1260px){
  .zh--knowledge .zh__stage--knowledge{
    right:0!important;
    width:61vw!important;
    max-width:760px!important;
    min-width:0!important;
  }
  .zh--knowledge .zh__brandMockWrap{
    transform:translate3d(0,-170px,0) scale(1)!important;
    rotate:0deg!important;
  }
  .zh--knowledge .zh__brandMini{
    right:22px!important;
    bottom:170px!important;
    width:min(246px,21vw)!important;
    padding:12px 13px 13px!important;
    z-index:12!important;
  }
  .zh--knowledge .zh__brandMini strong{font-size:16.5px!important;}
  .zh--knowledge .zh__brandMini p{font-size:10px!important;line-height:1.34!important;}
  .zh--knowledge .zkStats{
    right:22px!important;
    bottom:94px!important;
    z-index:12!important;
  }
  .zh--knowledge .zkStats span{min-width:66px!important;padding:9px 8px!important;}
  .zh--knowledge .zkStats strong{font-size:17px!important;}
  .zh--knowledge .zkStats em{font-size:7.8px!important;}
  .zh--knowledge .zh__photoChip{
    z-index:10!important;
    max-width:148px!important;
    font-size:8.8px!important;
    min-height:30px!important;
    padding:0 8px!important;
    gap:6px!important;
  }
  .zh--knowledge .zh__photoChip svg{width:12px!important;height:12px!important;}
  .zh--knowledge .zh__photoChip--a{left:auto!important;right:38px!important;top:62px!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--b{left:auto!important;right:16px!important;top:146px!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--c{left:auto!important;right:222px!important;top:194px!important;bottom:auto!important;}
  .zh--knowledge .zh__photoChip--d{left:auto!important;right:230px!important;bottom:132px!important;top:auto!important;}
  .zh--knowledge .zh__photoChip--e{left:auto!important;right:178px!important;bottom:58px!important;top:auto!important;}
}



/* PATCH v2.2.504 — Wiedza/Kontakt hero vertical rhythm */
@media (min-width:1101px){
  .zh.zh--knowledge .zh__copy,
  .zh.zh--contact .zh__copy{
    transform:translateY(30px)!important;
  }
}
@media (max-width:1100px){
  .zh.zh--knowledge .zh__inner{
    padding-top:170px!important;
  }
}
@media (max-width:600px){
  .zh.zh--knowledge .zh__inner{
    padding-top:162px!important;
  }
}


/* =========================================================
   v2.2.715 — real data + category navigation polish
========================================================= */
.zh--knowledge .zkStats strong{font-variant-numeric:tabular-nums;letter-spacing:-.04em}
.zh--knowledge .zh__brandMini>span{display:flex!important;align-items:center!important;gap:8px!important}
.zh--knowledge .zh__brandMini>span:before{content:"";display:inline-block;width:6px;height:6px;border-radius:50%;background:#8ec8f7;box-shadow:0 0 0 4px rgba(142,200,247,.09)}
.zh--knowledge .zh__stats{align-items:stretch!important}
.zh--knowledge .zh__stat{min-width:155px!important;display:grid!important;grid-template-columns:auto 1fr!important;grid-template-rows:auto!important;align-items:baseline!important;column-gap:9px!important}
.zh--knowledge .zh__stat b{font-variant-numeric:tabular-nums!important;white-space:nowrap!important}
.zh--knowledge .zh__stat span{max-width:120px!important;line-height:1.22!important}

#zpKnowledgePro .zpKBCats{
  display:flex!important;
  align-items:center!important;
  align-content:center!important;
  flex-wrap:wrap!important;
  gap:10px!important;
  min-height:42px!important;
  margin:0 0 18px!important;
}
#zpKnowledgePro .zpKBCat{
  display:inline-flex!important;
  align-items:center!important;
  justify-content:center!important;
  gap:8px!important;
  flex:0 0 auto!important;
  height:40px!important;
  min-height:40px!important;
  padding:0 14px!important;
  border-radius:999px!important;
  line-height:1!important;
  white-space:nowrap!important;
  vertical-align:middle!important;
  box-shadow:none!important;
}
#zpKnowledgePro .zpKBCat>span{
  display:inline-flex!important;
  align-items:center!important;
  justify-content:center!important;
  min-width:24px!important;
  height:24px!important;
  margin:0!important;
  padding:0 7px!important;
  border-radius:999px!important;
  background:#f0f4f8!important;
  color:#65768b!important;
  font-size:10px!important;
  line-height:1!important;
  font-weight:850!important;
  font-variant-numeric:tabular-nums!important;
}
#zpKnowledgePro .zpKBCat:hover>span,
#zpKnowledgePro .zpKBCat.is-active>span{
  background:rgba(255,255,255,.13)!important;
  color:#fff!important;
}
#zpKnowledgePro .zpKBCatsHint{display:none!important}

#zpKnowledgePro .zpKBIndex__head{
  border:1px solid rgba(7,17,31,.065)!important;
  background:linear-gradient(180deg,#fff 0%,#fbfcfe 100%)!important;
  border-radius:28px!important;
}
#zpKnowledgePro .zpKBToolbar{
  display:grid!important;
  grid-template-columns:minmax(320px,1fr) auto auto!important;
  align-items:center!important;
  gap:10px!important;
  min-height:60px!important;
  padding:8px!important;
  border-radius:20px!important;
  background:#f6f8fb!important;
}
#zpKnowledgePro .zpKBSearch,
#zpKnowledgePro .zpKBSort,
#zpKnowledgePro .zpKBView{
  min-height:44px!important;
  height:44px!important;
  align-items:center!important;
}
#zpKnowledgePro .zpKBView button{height:36px!important;width:36px!important;display:grid!important;place-items:center!important}

@media(max-width:900px){
  #zpKnowledgePro .zpKBToolbar{grid-template-columns:1fr!important;height:auto!important}
  #zpKnowledgePro .zpKBSort,#zpKnowledgePro .zpKBView{width:100%!important}
}
@media(max-width:760px){
  #zpKnowledgePro .zpKBCats{
    flex-wrap:nowrap!important;
    overflow-x:auto!important;
    overflow-y:hidden!important;
    scroll-snap-type:x proximity!important;
    scrollbar-width:none!important;
    padding:2px 18px 7px 0!important;
    margin-right:-18px!important;
  }
  #zpKnowledgePro .zpKBCats::-webkit-scrollbar{display:none!important}
  #zpKnowledgePro .zpKBCat{scroll-snap-align:start!important}
  #zpKnowledgePro .zpKBCatsHint{
    display:flex!important;
    align-items:center!important;
    justify-content:flex-end!important;
    gap:6px!important;
    margin:0 2px 8px auto!important;
    font-size:10px!important;
    opacity:.64!important;
  }
  #zpKnowledgePro .zpKBIndex__head{border-radius:22px!important}
}



/* =========================================================
   v2.2.716 — hero copy + quick topic rail + cleaner stat strip
========================================================= */
.zh--knowledge .zh__copy{max-width:920px!important}
.zh--knowledge .zh__title{max-width:12.6ch!important;font-size:clamp(48px,6.3vw,108px)!important;line-height:.98!important}
.zh--knowledge .zh__lead{max-width:650px!important;font-size:17px!important;line-height:1.68!important}
.zh--knowledge .zh__brandMini{width:min(355px,28vw)!important}
.zh--knowledge .zh__brandMini strong{font-size:clamp(21px,1.65vw,29px)!important;line-height:1.03!important}
.zh--knowledge .zh__brandMini strong em{display:inline;color:#d9e9f8!important;font-style:normal!important}
.zh--knowledge .zh__brandMini p{font-size:12.5px!important;line-height:1.52!important;color:rgba(255,255,255,.58)!important}

.zh--knowledge .zh__stripIn{gap:20px!important}
.zh--knowledge .zh__stats{gap:22px!important}
.zh--knowledge .zh__stat{min-width:auto!important;grid-template-columns:auto auto!important;column-gap:7px!important}
.zh--knowledge .zh__stat b{font-size:20px!important;letter-spacing:-.035em!important}
.zh--knowledge .zh__stat span{max-width:none!important;font-size:11px!important;white-space:nowrap!important}
.zh--knowledge .zh__sep{margin:0 2px!important}

.zh--knowledge .zh__quickTopics{
  flex:1;
  min-width:0;
  display:flex;
  align-items:center;
  gap:14px;
  overflow:hidden;
}
.zh--knowledge .zh__quickTopicsLabel{
  flex:0 0 auto;
  display:inline-flex;
  align-items:center;
  gap:7px;
  color:rgba(255,255,255,.42);
  font-size:9px;
  line-height:1;
  font-weight:800;
  letter-spacing:.10em;
  text-transform:uppercase;
  white-space:nowrap;
}
.zh--knowledge .zh__quickTopicsLabel svg{width:13px;height:13px;color:#8ec8f7}
.zh--knowledge .zh__quickTopicsList{
  min-width:0;
  display:flex;
  align-items:center;
  gap:8px;
  overflow-x:auto;
  scrollbar-width:none;
  -webkit-mask-image:linear-gradient(90deg,#000 0%,#000 92%,transparent 100%);
  mask-image:linear-gradient(90deg,#000 0%,#000 92%,transparent 100%);
}
.zh--knowledge .zh__quickTopicsList::-webkit-scrollbar{display:none}
.zh--knowledge .zh__quickTopicsList a{
  flex:0 0 auto;
  display:inline-flex;
  align-items:center;
  gap:7px;
  min-height:36px;
  padding:0 11px 0 13px;
  border:1px solid rgba(255,255,255,.10);
  border-radius:999px;
  background:rgba(255,255,255,.035);
  color:rgba(255,255,255,.72)!important;
  text-decoration:none!important;
  font-size:10px;
  line-height:1;
  font-weight:700;
  transition:background .2s ease,border-color .2s ease,color .2s ease;
}
.zh--knowledge .zh__quickTopicsList a:hover{
  background:rgba(255,255,255,.08);
  border-color:rgba(142,200,247,.26);
  color:#fff!important;
}
.zh--knowledge .zh__quickTopicsList a em{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:22px;
  height:22px;
  padding:0 6px;
  border-radius:999px;
  background:rgba(142,200,247,.10);
  color:#b9d8f4;
  font-size:9px;
  line-height:1;
  font-style:normal;
  font-weight:850;
  font-variant-numeric:tabular-nums;
}
.zh--knowledge .zh__quickTopicsList a svg{width:11px;height:11px;color:#8ec8f7}

@media(max-width:1450px) and (min-width:1101px){
  .zh--knowledge .zh__title{font-size:clamp(52px,5.55vw,86px)!important;max-width:12.8ch!important}
  .zh--knowledge .zh__lead{max-width:570px!important;font-size:16px!important}
  .zh--knowledge .zh__quickTopicsLabel{display:none!important}
  .zh--knowledge .zh__stat span{font-size:10px!important}
}
@media(max-width:1220px) and (min-width:1101px){
  .zh--knowledge .zh__quickTopicsList a{padding:0 9px!important;font-size:9px!important}
  .zh--knowledge .zh__quickTopicsList a em{display:none!important}
}

/* 2.2.717 — dekoracyjne „wiedza” w tle hero: lowercase, nisko i miękko wtopione */
.zh--knowledge .zh__kin{
  position:absolute!important;
  z-index:2!important;
  left:auto!important;
  right:-7.5vw!important;
  top:auto!important;
  bottom:-1.9vw!important;
  width:min(79vw,1500px)!important;
  height:auto!important;
  transform:none!important;
  text-align:right!important;
  pointer-events:none!important;
  overflow:visible!important;
  opacity:1!important;
  -webkit-mask-image:linear-gradient(90deg,transparent 0%,#000 9%,#000 89%,transparent 100%)!important;
  mask-image:linear-gradient(90deg,transparent 0%,#000 9%,#000 89%,transparent 100%)!important;
}
.zh--knowledge .zh__kin::after{
  content:"";
  position:absolute;
  left:-4%;
  right:-4%;
  bottom:-2px;
  height:46%;
  pointer-events:none;
  background:linear-gradient(180deg,rgba(1,3,9,0) 0%,rgba(1,3,9,.22) 34%,rgba(1,3,9,.64) 72%,#010309 100%);
}
.zh--knowledge .zh__kinWord{
  display:block!important;
  margin:0!important;
  padding:0!important;
  font-size:clamp(190px,25.5vw,490px)!important;
  font-weight:760!important;
  line-height:.68!important;
  letter-spacing:-.092em!important;
  text-transform:none!important;
  white-space:nowrap!important;
  color:transparent!important;
  background:linear-gradient(105deg,rgba(42,85,143,.18) 0%,rgba(121,166,218,.18) 36%,rgba(255,255,255,.095) 57%,rgba(59,110,168,.15) 82%,rgba(22,54,95,.12) 100%)!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  opacity:1!important;
  animation:none!important;
  transform:translateY(8%)!important;
  filter:none!important;
  text-shadow:0 28px 90px rgba(0,0,0,.16)!important;
}
.zh--knowledge .zh__rotor,
.zh--knowledge .zh__rotor>span,
.zh--knowledge .zh__rotor i{
  animation:none!important;
}
@media(max-width:1450px) and (min-width:1101px){
  .zh--knowledge .zh__kin{right:-9vw!important;bottom:-1vw!important;width:82vw!important}
  .zh--knowledge .zh__kinWord{font-size:clamp(170px,25vw,390px)!important}
}
@media(max-width:1100px){
  .zh--knowledge .zh__kin{
    right:-12vw!important;
    bottom:1px!important;
    top:auto!important;
    width:112vw!important;
    opacity:.72!important;
  }
  .zh--knowledge .zh__kinWord{
    font-size:clamp(112px,28vw,245px)!important;
    line-height:.7!important;
    transform:translateY(14%)!important;
  }
}
@media(max-width:600px){
  .zh--knowledge .zh__kin{
    right:-22vw!important;
    bottom:4px!important;
    width:132vw!important;
    opacity:.54!important;
  }
  .zh--knowledge .zh__kinWord{
    font-size:clamp(94px,31vw,178px)!important;
    letter-spacing:-.085em!important;
  }
}


/* =========================================================
   v2.2.718 — cleaner white knowledge hub + higher background word
========================================================= */

/* Po hero przechodzimy w czystą biel — bez jasnoniebieskiego odcięcia. */
#zpKnowledgePro,
#zpKnowledgePro .zpKBSeoHub,
#zpKnowledgePro .zpKBFeatured,
#zpKnowledgePro .zpKBIndex,
#zpKnowledgePro .zpKBInner,
#zpKnowledgePro .zpKBIndex__layout,
#zpKnowledgePro .zpKBPostsWrap{
  background:#fff!important;
  background-color:#fff!important;
}
#zpKnowledgePro .zpKBSeoHub{
  border-top:0!important;
}
#zpKnowledgePro .zpKBSeoHub::before{
  display:none!important;
  content:none!important;
}

/* „wiedza” wraca wysoko pod header — za zdjęciem, miękko wtopiona. */
.zh--knowledge .zh__kin{
  display:block!important;
  position:absolute!important;
  z-index:2!important;
  left:auto!important;
  right:-8vw!important;
  top:clamp(70px,9.2vh,112px)!important;
  bottom:auto!important;
  width:min(88vw,1660px)!important;
  height:clamp(260px,32vw,520px)!important;
  transform:none!important;
  text-align:right!important;
  overflow:visible!important;
  opacity:1!important;
  pointer-events:none!important;
  -webkit-mask-image:linear-gradient(90deg,transparent 0%,#000 8%,#000 90%,transparent 100%)!important;
  mask-image:linear-gradient(90deg,transparent 0%,#000 8%,#000 90%,transparent 100%)!important;
}
.zh--knowledge .zh__kin::after{
  content:""!important;
  position:absolute!important;
  left:-5%!important;
  right:-5%!important;
  top:48%!important;
  bottom:-6%!important;
  height:auto!important;
  pointer-events:none!important;
  background:linear-gradient(180deg,rgba(1,3,9,0) 0%,rgba(1,3,9,.10) 30%,rgba(1,3,9,.46) 72%,#010309 100%)!important;
}
.zh--knowledge .zh__kinWord{
  display:block!important;
  margin:0!important;
  padding:0!important;
  font-size:clamp(210px,27vw,520px)!important;
  font-weight:760!important;
  line-height:.70!important;
  letter-spacing:-.094em!important;
  text-transform:none!important;
  white-space:nowrap!important;
  color:transparent!important;
  background:linear-gradient(104deg,rgba(42,85,143,.21) 0%,rgba(121,166,218,.22) 35%,rgba(255,255,255,.115) 58%,rgba(59,110,168,.18) 82%,rgba(22,54,95,.15) 100%)!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  opacity:1!important;
  animation:none!important;
  transform:none!important;
  filter:none!important;
  text-shadow:0 26px 86px rgba(0,0,0,.15)!important;
}

@media(max-width:1450px) and (min-width:1101px){
  .zh--knowledge .zh__kin{
    right:-10vw!important;
    top:clamp(74px,9vh,104px)!important;
    width:92vw!important;
    height:clamp(230px,31vw,430px)!important;
  }
  .zh--knowledge .zh__kinWord{
    font-size:clamp(180px,26.5vw,410px)!important;
  }
}

@media(max-width:1100px){
  .zh--knowledge .zh__kin{
    right:-18vw!important;
    top:82px!important;
    bottom:auto!important;
    width:126vw!important;
    height:clamp(190px,38vw,340px)!important;
    opacity:.72!important;
  }
  .zh--knowledge .zh__kinWord{
    font-size:clamp(118px,30vw,250px)!important;
    line-height:.72!important;
    transform:none!important;
  }
}

@media(max-width:600px){
  .zh--knowledge .zh__kin{
    right:-28vw!important;
    top:72px!important;
    width:148vw!important;
    height:190px!important;
    opacity:.52!important;
  }
  .zh--knowledge .zh__kinWord{
    font-size:clamp(96px,33vw,180px)!important;
    letter-spacing:-.086em!important;
  }
}

</style>
<section aria-label="Hero Wiedza — Zaprojektowani" class="zh zh--knowledge zpUnifiedServiceHero" id="zhHero" data-zp-unified-service-hero>
<div aria-hidden="true" class="zh__bg"></div>
<div aria-hidden="true" class="zh__aurora" data-px="0.5"></div>
<div aria-hidden="true" class="zh__grid"></div>
<div aria-hidden="true" class="zh__dust">
<i style="left:62%;animation-duration:15s"></i><i style="left:71%;animation-duration:19s;animation-delay:3s"></i>
<i style="left:80%;animation-duration:13s;animation-delay:6s"></i><i style="left:55%;animation-duration:21s;animation-delay:2s"></i>
<i style="left:88%;animation-duration:17s;animation-delay:8s"></i><i style="left:67%;animation-duration:23s;animation-delay:5s"></i>
</div>
<div aria-hidden="true" class="zh__hair"></div>
<div aria-hidden="true" class="zh__kin" data-px="1.2">
<span class="zh__kinWord">wiedza</span>
</div>
<div aria-hidden="true" class="zh__stage zh__stage--knowledge" id="zhStage">
<div class="zh__conic"></div><div class="zh__halo"></div><div class="zh__ring zh__ring--b"></div><div class="zh__ring zh__ring--a"></div>
<div class="zh__brandMockWrap"><figure aria-hidden="true" class="zkPerson"><img alt="" class="zkPerson__img skip-lazy no-lazy no-litespeed-lazyload" decoding="async" fetchpriority="high" height="1400" loading="eager" src="https://zaprojektowani.com/wp-content/uploads/2026/06/wiedza_ekipa.webp" width="1200"/></figure></div>
<div class="zh__brandMini"><span>Baza wiedzy · aktualizacja <?php echo esc_html($zp_kb_latest_label); ?></span><strong>Najpierw zrozum. <em>Potem wybierz rozwiązanie.</em></strong><p><?php echo (int)$zp_kb_total_articles; ?> poradników o stronach, WooCommerce, SEO, brandingu i kampaniach — zebranych tak, żeby łatwiej porównać zakres, koszty i kolejne kroki.</p></div>
<div aria-label="Statystyki bazy wiedzy" class="zkStats"><span><strong><?php echo (int)$zp_kb_total_articles; ?></strong><em>poradników</em></span><span><strong><?php echo (int)$zp_kb_topic_count; ?></strong><em>tematów</em></span><span><strong><?php echo (int)$zp_kb_avg_read_minutes; ?> min</strong><em>średnio</em></span></div>
<div aria-label="Kategorie wiedzy" class="zh__photoChips"><a class="zh__photoChip zh__photoChip--a" href="/wiedza/"><i data-lucide="book-open"></i><span>poradniki SEO</span></a><a class="zh__photoChip zh__photoChip--b" href="/strony-internetowe-katowice/"><i data-lucide="layout-template"></i><span>strony internetowe</span></a><a class="zh__photoChip zh__photoChip--c" href="/sklepy-internetowe-katowice/"><i data-lucide="shopping-bag"></i><span>sklepy WooCommerce</span></a><a class="zh__photoChip zh__photoChip--d" href="/logo-branding-katowice/"><i data-lucide="sparkles"></i><span>logo i branding</span></a><a class="zh__photoChip zh__photoChip--e" href="/studio-wyceny/"><i data-lucide="send"></i><span>wycena projektu</span></a></div>
</div>
<div aria-hidden="true" class="zh__fade"></div>
<div aria-hidden="true" class="zh__grain"></div>
<div aria-hidden="true" class="zh__vig"></div>
<nav aria-label="Social i postęp" class="zh__rail">
<span class="zh__railIco">
<a aria-label="Facebook" href="https://www.facebook.com/zaprojektowanicom" rel="noopener" target="_blank"><svg fill="currentColor" viewbox="0 0 24 24"><path d="M14 9h3l.4-3H14V4.6c0-.9.3-1.4 1.5-1.4H17V.6C16.6.5 15.6.4 14.6.4 12.2.4 10.7 1.9 10.7 4.4V6H8v3h2.7v11H14z"></path></svg></a>
<a aria-label="Instagram" href="https://www.instagram.com/zaprojektowani" rel="noopener" target="_blank"><svg fill="none" stroke="currentColor" stroke-width="1.7" viewbox="0 0 24 24"><rect height="17" rx="5" width="17" x="3.5" y="3.5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.2" cy="6.8" fill="currentColor" r="1.1" stroke="none"></circle></svg></a>
<a aria-label="LinkedIn" href="https://www.linkedin.com/company/zaprojektowani" rel="noopener" target="_blank"><svg fill="currentColor" viewbox="0 0 24 24"><path d="M6.5 8.2A1.6 1.6 0 1 0 6.5 5a1.6 1.6 0 0 0 0 3.2zM5.1 9.6h2.8V20H5.1zM10.2 9.6H13v1.5c.4-.8 1.5-1.8 3.2-1.8 2.4 0 3.6 1.5 3.6 4.4V20h-2.8v-5.4c0-1.3-.5-2.2-1.7-2.2-1 0-1.5.7-1.8 1.4-.1.2-.1.5-.1.8V20h-2.8z"></path></svg></a>
</span>
<span class="zh__railProg"><span class="zh__railProgFill" id="zhProg"></span></span>
</nav>
<div class="zh__inner">
<div class="zh__copy">
<p class="zh__eb"><span class="dot"></span>Baza wiedzy Zaprojektowani</p>
<h1 class="zh__title">Poradniki o <span class="zh__grad">stronach, sklepach</span> i brandingu.</h1>
<p class="zh__lead">Praktyczna baza wiedzy dla firm z Katowic i całej Polski: <strong>strony internetowe, WooCommerce, identyfikacja wizualna, SEO i kampanie reklamowe</strong>. Konkretne decyzje, koszty, UX i kolejne kroki przed wdrożeniem.</p>
<div class="zh__actions">
<a class="zh__btn zh__btn--p" href="/wiedza/#zpKBIndex" id="zhMagnet"><span>Przeglądaj wpisy</span><svg viewbox="0 0 24 24"><path d="M7 17L17 7"></path><path d="M8 7h9v9"></path></svg></a>
<a class="zh__btn zh__btn--g" href="/studio-wyceny/"><span>Otrzymaj wycenę</span><svg viewbox="0 0 24 24"><path d="M7 17L17 7"></path><path d="M8 7h9v9"></path></svg></a>
</div>
<div aria-hidden="true" class="zh__mobileBrandMock"><figure class="zkPerson zkPerson--mobile"><img alt="" class="zkPerson__img skip-lazy no-lazy no-litespeed-lazyload" decoding="async" fetchpriority="high" height="1400" loading="eager" src="https://zaprojektowani.com/wp-content/uploads/2026/06/wiedza_ekipa.webp" width="1200"/></figure></div>
<div aria-label="Statystyki bazy wiedzy" class="zkStats zkStats--mobile"><span><strong><?php echo (int)$zp_kb_total_articles; ?></strong><em>poradników</em></span><span><strong><?php echo (int)$zp_kb_topic_count; ?></strong><em>tematów</em></span><span><strong><?php echo (int)$zp_kb_avg_read_minutes; ?> min</strong><em>średnio</em></span></div>

</div>
</div>

<div aria-hidden="true" class="zh__mobileScroll"><span class="zh__mouse"></span><span>Przewiń</span></div>
<div aria-hidden="true" class="zh__scroll"><span class="zh__mouse"></span>Przewiń</div>
<div class="zh__strip">
<div class="zh__stripIn">
<div class="zh__stats">
<div class="zh__stat"><b data-count="<?php echo (int)$zp_kb_total_articles; ?>">0</b><span>opublikowanych poradników</span></div>
<div class="zh__stat"><b data-count="<?php echo (int)$zp_kb_total_reads; ?>">0</b><span>łącznych czytań</span></div>
<div class="zh__stat"><b data-count="<?php echo (int)$zp_kb_topic_count; ?>">0</b><span>głównych tematów</span></div>
</div>
<div class="zh__sep"></div>
<nav class="zh__quickTopics" aria-label="Najważniejsze obszary wiedzy">
  <span class="zh__quickTopicsLabel"><i data-lucide="compass"></i> Szybkie ścieżki</span>
  <div class="zh__quickTopicsList">
    <?php if (!empty($zp_kb_topics)) : ?>
      <?php foreach (array_slice($zp_kb_topics, 0, 5, true) as $zp_quick_topic) : ?>
        <a href="<?php echo esc_url($zp_quick_topic['url']); ?>">
          <span><?php echo esc_html($zp_quick_topic['label']); ?></span>
          <em><?php echo (int)$zp_quick_topic['count']; ?></em>
          <i data-lucide="arrow-up-right"></i>
        </a>
      <?php endforeach; ?>
    <?php else : ?>
      <a href="<?php echo esc_url(home_url('/strony-internetowe-katowice/')); ?>"><span>Strony internetowe</span><i data-lucide="arrow-up-right"></i></a>
      <a href="<?php echo esc_url(home_url('/sklepy-internetowe-katowice/')); ?>"><span>Sklepy WooCommerce</span><i data-lucide="arrow-up-right"></i></a>
      <a href="<?php echo esc_url(home_url('/logo-branding-katowice/')); ?>"><span>Logo i branding</span><i data-lucide="arrow-up-right"></i></a>
      <a href="<?php echo esc_url(home_url('/kampanie-reklamowe/')); ?>"><span>Kampanie reklamowe</span><i data-lucide="arrow-up-right"></i></a>
    <?php endif; ?>
  </div>
</nav>
</div>
</div>
</section>
<script>window.addEventListener("DOMContentLoaded",function(){if(window.lucide){window.lucide.createIcons({attrs:{"aria-hidden":"true"}});}});</script>
<script>(function(){var hero=document.getElementById('zhHero');var sp=document.getElementById('zhSpline');var stage=document.getElementById('zhStage');var reduce=window.matchMedia&&window.matchMedia('(prefers-reduced-motion:reduce)').matches;var SA=false;try{SA=false /* v711: identical production behavior in browser audits */||false;}catch(e){}if(SA){try{document.documentElement.classList.add('zp-synthetic-audit');}catch(e){}}var fine=!window.matchMedia||window.matchMedia('(pointer:fine)').matches;if(!hero)return;var wm=hero.querySelector('.zh__wm');if(wm){wm.setAttribute('aria-label',(wm.textContent||'zaprojektowani').trim());}if(sp){try{sp.setAttribute('events-target','global');sp.setAttribute('loading-anim-type','none');}catch(e){}var done=false,canvasPoll=null;var reveal=function(){if(done)return;done=true;hero.classList.add('is-spline-loaded');hideBrand();if(canvasPoll)clearInterval(canvasPoll);};sp.addEventListener('load',reveal,{once:true});sp.addEventListener('load-complete',reveal,{once:true});canvasPoll=setInterval(function(){try{if(sp.shadowRoot && sp.shadowRoot.querySelector('canvas'))reveal();}catch(e){}},120);setTimeout(function(){if(canvasPoll)clearInterval(canvasPoll);},9000);}function hideBrand(){var n=0,t2=setInterval(function(){try{var sr=sp&&sp.shadowRoot;if(sr){['#logo','a#logo','#hints','[id="logo"]','[id="hints"]'].forEach(function(sel){sr.querySelectorAll(sel).forEach(function(l){l.style.setProperty('display','none','important');l.style.setProperty('visibility','hidden','important');l.style.setProperty('opacity','0','important');l.style.setProperty('pointer-events','none','important');});});}}catch(e){}if(++n>36)clearInterval(t2);},250);}function countUp(el){var target=parseFloat(el.getAttribute('data-count'))||0;var dec=parseInt(el.getAttribute('data-dec')||'0',10);var suf=el.getAttribute('data-suffix')||'';var nf;try{nf=new Intl.NumberFormat('en-US',{maximumFractionDigits:dec,minimumFractionDigits:dec});}catch(e){nf=null;}function fmt(v){if(nf)return nf.format(dec?Number(v.toFixed(dec)):Math.round(v));return(dec?v.toFixed(dec):String(Math.round(v)));}if(reduce||SA){el.textContent=fmt(target)+suf;return;}var dur=1500,start=null;function step(ts){if(!start)start=ts;var p=Math.min((ts-start)/dur,1);var e=1-Math.pow(1-p,3);var v=target*e;el.textContent=fmt(v)+suf;if(p<1)requestAnimationFrame(step);}requestAnimationFrame(step);}[].forEach.call(document.querySelectorAll('[data-count]'),countUp);var prog=document.getElementById('zhProg');function onScroll(){if(!prog)return;var d=document.documentElement;var max=(d.scrollHeight-d.clientHeight)||1;prog.style.height=Math.max(0,Math.min(100,(d.scrollTop/max)*100))+'%';}if(!SA){window.addEventListener('scroll',onScroll,{passive:true});onScroll();}if(reduce||SA)return;var mag=document.getElementById('zhMagnet');if(mag&&fine){mag.addEventListener('mousemove',function(e){var r=mag.getBoundingClientRect();var mx=(e.clientX-r.left-r.width/2)/(r.width/2),my=(e.clientY-r.top-r.height/2)/(r.height/2);mag.style.transform='translate('+(mx*7).toFixed(1)+'px,'+((my*5)-2).toFixed(1)+'px)';});mag.addEventListener('mouseleave',function(){mag.style.transform='';});}if(sp){try{sp.setAttribute('events-target','global');sp.setAttribute('mouse-events','global');}catch(e){}}var layers=[].slice.call(hero.querySelectorAll('[data-px]'));var isBrand=hero.classList&&hero.classList.contains('zh--logoBranding');var isStaticHero=hero.classList&&(hero.classList.contains('zh--knowledge')||hero.classList.contains('zh--contact')||hero.classList.contains('zh--websites')||hero.classList.contains('zh--shops'));var tx=0,ty=0,cx=0,cy=0,raf=null;function isMob(){return window.matchMedia&&window.matchMedia('(max-width:1100px)').matches;}function clamp(v,min,max){return Math.max(min,Math.min(max,v));}function applyRobotTransform(){layers.forEach(function(el){var d=parseFloat(el.getAttribute('data-px'))||1;var prefix=(el.className.indexOf('__kin')>-1)?'translateY(-52%)':'';el.style.transform=prefix+'translate3d('+(cx*d).toFixed(2)+'px,'+(cy*d).toFixed(2)+'px,0)';});if(stage){if(isBrand||isStaticHero){stage.style.transform='none';return;}var mob=isMob();var base=mob?'scaleX(-1) ':'';var depth=mob?0.65:1;stage.style.transform=base+'perspective(1400px) rotateY('+((mob?cx:-cx)*.30*depth).toFixed(2)+'deg) rotateX('+(cy*.22*depth).toFixed(2)+'deg) translate3d('+((mob?-cx:cx)*.55*depth).toFixed(1)+'px,'+(cy*.35*depth).toFixed(1)+'px,0)';}}function loop(){cx+=(tx-cx)*.12;cy+=(ty-cy)*.12;applyRobotTransform();if(Math.abs(tx-cx)>.025||Math.abs(ty-cy)>.025){raf=requestAnimationFrame(loop);}else{raf=null;}}function trackHeroPointer(e){var x,y;if(e&&e.touches&&e.touches[0]){x=e.touches[0].clientX;y=e.touches[0].clientY;}else if(e&&e.changedTouches&&e.changedTouches[0]){x=e.changedTouches[0].clientX;y=e.changedTouches[0].clientY;}else if(e&&typeof e.clientX==='number'){x=e.clientX;y=e.clientY;}else{return;}var r=hero.getBoundingClientRect();if(x<r.left||x>r.right||y<r.top||y>r.bottom)return;var px=clamp((x-r.left)/Math.max(r.width,1),0,1);var py=clamp((y-r.top)/Math.max(r.height,1),0,1);tx=(px-.5)*(isMob()?16:-16);ty=(py-.5)*-10;if(!raf)raf=requestAnimationFrame(loop);}function resetPointer(){tx=0;ty=0;if(!raf)raf=requestAnimationFrame(loop);}hero.addEventListener('mousemove',trackHeroPointer,{passive:true});hero.addEventListener('pointermove',trackHeroPointer,{passive:true});hero.addEventListener('touchmove',trackHeroPointer,{passive:true});hero.addEventListener('mouseleave',resetPointer,{passive:true});window.addEventListener('blur',resetPointer,true);applyRobotTransform();})();</script>
<!-- ZP v2.2.503 knowledge hero isolated above blog wrapper -->
<section class="zpKnowledgePro" id="zpKnowledgePro" data-zp-knowledge-pro data-ready="0" data-video-ready="0" style="--zp-wiedza-person-scale-d:<?php echo $zp_wiedza_person_scale_d; ?>;--zp-wiedza-person-y-d:<?php echo $zp_wiedza_person_y_d; ?>px;--zp-wiedza-person-x-d:<?php echo $zp_wiedza_person_x_d; ?>px;--zp-wiedza-person-scale-m:<?php echo $zp_wiedza_person_scale_m; ?>;--zp-wiedza-person-y-m:<?php echo $zp_wiedza_person_y_m; ?>px;--zp-wiedza-person-x-m:<?php echo $zp_wiedza_person_x_m; ?>px;--zp-wiedza-video-opacity:<?php echo esc_attr($zp_wiedza_video_opacity / 100); ?>;--zp-wiedza-video-brightness:<?php echo esc_attr($zp_wiedza_video_brightness / 100); ?>;--zp-wiedza-video-navy:<?php echo esc_attr($zp_wiedza_video_navy / 100); ?>;--zp-wiedza-text-shadow:<?php echo esc_attr($zp_wiedza_text_shadow / 100); ?>" data-ajax="<?php echo esc_url($zp_kb_ajax); ?>" data-nonce="<?php echo esc_attr($zp_kb_nonce); ?>" aria-labelledby="zpKnowledgeProTitle">
  <!-- v2.2.248: Lucide is already enqueued globally with defer; removed parser-blocking duplicate script. -->
  <style>@font-face{font-family:"Plus Jakarta Sans Local";src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-regular.woff2") format("woff2");font-weight:400;font-style:normal;font-display:swap}@font-face{font-family:"Plus Jakarta Sans Local";src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-500.woff2") format("woff2");font-weight:500;font-style:normal;font-display:swap}@font-face{font-family:"Plus Jakarta Sans Local";src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-600.woff2") format("woff2");font-weight:600;font-style:normal;font-display:swap}@font-face{font-family:"Plus Jakarta Sans Local";src:url("/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-700.woff2") format("woff2");font-weight:700;font-style:normal;font-display:swap}#zpKnowledgePro,#zpKnowledgePro *,#zpKnowledgePro *::before,#zpKnowledgePro *::after{box-sizing:border-box;font-family:var(--font)!important;font-style:normal!important}@media(max-width:1080px){html body #zpKnowledgePro{margin-top:0!important}}#zpKnowledgePro{--font:"Plus Jakarta Sans Local","Plus Jakarta Sans","Outfit",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;--ink:#07111f;--muted:#5d6879;--muted2:#7b8494;--line:rgba(7,17,31,.105);--navy:#05070b;--navy2:#071426;--navy3:#102a4f;--blue:#1c477a;--grad:linear-gradient(100deg,#020407 0%,#071426 38%,#102a4f 74%,#1c477a 100%);--ease:cubic-bezier(.22,1,.36,1);position:relative;width:100%;overflow:hidden;color:var(--ink);background:#fff;font-family:var(--font);-webkit-font-smoothing:antialiased;text-rendering:geometricPrecision;font-synthesis:none}html body #zpKnowledgePro .zpKBFeatured,html body #zpKnowledgePro .zpKBIndex,html body #zpKnowledgePro .zpKBInner,html body #zpKnowledgePro .zpKBIndex__layout,html body #zpKnowledgePro .zpKBPostsWrap{background:#fff!important;background-color:#fff!important}html body #zpKnowledgePro .zpKBHero,html body #zpKnowledgePro .zpKBHero .zpKBInner,html body #zpKnowledgePro .zpKBHero__inner{background:transparent!important}#zpKnowledgePro a{text-decoration:none;color:inherit}#zpKnowledgePro svg{display:block}#zpKnowledgePro img{max-width:100%}.zpKBInner{width:min(1840px,100%);margin:0 auto;padding:0 clamp(22px,6vw,110px)}.zpKBHero{position:relative;isolation:isolate;min-height:min(900px,calc(100vh - 22px));padding:clamp(124px,11vw,174px) 0 clamp(66px,7vw,110px);background:#05070b;color:#fff;overflow:hidden}.zpKBHero::before{content:"";position:absolute;inset:0;z-index:-7;background:radial-gradient(circle at 18% 14%,rgba(28,71,122,.34),transparent 36%),radial-gradient(circle at 84% 10%,rgba(130,154,210,.15),transparent 34%),linear-gradient(135deg,#020407 0%,#06101e 48%,#05070b 100%)}.zpKBHero::after{content:"";position:absolute;inset:0;z-index:-3;pointer-events:none;background:linear-gradient(90deg,rgba(3,4,7,.82),rgba(5,7,11,.52) 48%,rgba(3,4,7,.90)),linear-gradient(180deg,rgba(5,7,11,.74),rgba(5,7,11,.24) 42%,rgba(0,0,0,.86))}.zpKBHero__video{position:absolute;inset:0;z-index:-6;overflow:hidden;background:#05070b}.zpKBHero__video video{display:block;width:100%;height:100%;object-fit:cover;object-position:center;opacity:.44;filter:saturate(.72) brightness(.78) contrast(1.08);transform:scale(1.04)}.zpKBHero__noise{display:none!important;position:absolute;inset:0;z-index:-2;opacity:.072;background-image:linear-gradient(rgba(255,255,255,.12) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.12) 1px,transparent 1px);background-size:56px 56px;-webkit-mask-image:linear-gradient(180deg,transparent,#000 16%,#000 84%,transparent);mask-image:linear-gradient(180deg,transparent,#000 16%,#000 84%,transparent)}.zpKBHero__inner{width:min(1840px,100%);margin:0 auto;padding:0 clamp(22px,6vw,110px)}.zpKBCrumbs{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin:0 0 32px;color:rgba(255,255,255,.62);font-size:13px;line-height:1.2;font-weight:650;letter-spacing:-.01em;text-transform:none}.zpKBCrumbs a{color:#fff;opacity:.90;text-decoration:none}.zpKBCrumbs a:hover{opacity:1}.zpKBCrumbs i,.zpKBCrumbs svg{width:13px;height:13px;opacity:.42;stroke-width:2;font-style:normal;flex:0 0 auto}.zpKBCrumbs span{color:rgba(255,255,255,.70)}.zpKBHero__grid{display:grid;grid-template-columns:minmax(0,1.05fr) minmax(360px,.82fr);gap:clamp(34px,5vw,92px);align-items:end}.zpKBKicker,.zpKBLabel{display:inline-flex;align-items:center;gap:10px;margin:0 0 16px;color:#738096;font-size:10px;font-weight:850;letter-spacing:.15em;text-transform:uppercase}.zpKBKicker{color:#b5bdcb;font-size:11px;letter-spacing:.16em;margin-bottom:18px}.zpKBKicker::before,.zpKBLabel::before{content:"";width:30px;height:1px;background:linear-gradient(90deg,currentColor,rgba(115,128,150,.18))}.zpKBHero h1{max-width:930px;margin:0;color:#fff;font-size:clamp(36px,5.05vw,76px);line-height:1;letter-spacing:-.044em;font-weight:550;text-wrap:balance}.zpKBHero__lead{max-width:750px;margin:24px 0 0;color:rgba(255,255,255,.74);font-size:clamp(15px,1.05vw,19px);line-height:1.72;letter-spacing:-.01em}.zpKBHero__actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:34px}.zpKBBtn{--zpBtnFill:linear-gradient(90deg,#05070b 0%,#0b1830 42%,#102a4f 72%,#1c477a 100%);position:relative;isolation:isolate;overflow:hidden;display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:54px;padding:0 24px;border-radius:999px;border:1px solid rgba(255,255,255,.22);font-size:14px;line-height:1;font-weight:720;letter-spacing:-.01em;text-transform:none;background:transparent;color:#fff;box-shadow:none;transition:color .22s ease,border-color .22s ease}.zpKBBtn::before{content:"";position:absolute;inset:-1px;z-index:-1;border-radius:inherit;background:var(--zpBtnFill);transform:scaleX(0);transform-origin:left center;transition:transform .34s cubic-bezier(.16,1,.3,1)}.zpKBBtn i{width:16px;height:16px;transition:transform .22s cubic-bezier(.16,1,.3,1)}.zpKBBtn:hover::before{transform:scaleX(1)}.zpKBBtn:hover{color:#fff;border-color:rgba(28,71,122,.66)}.zpKBBtn:hover i{transform:translate(3px,-3px)}.zpKBBtn--white{background:#fff;color:#071426;border-color:#fff}.zpKBBtn--ghost{background:rgba(255,255,255,.035);color:#fff;border-color:rgba(255,255,255,.24)}.zpKBHero__visual{position:relative;align-self:stretch;min-height:500px;display:flex;align-items:flex-end;justify-content:center;pointer-events:none;overflow:visible;isolation:isolate}.zpKBHero__person{position:absolute;z-index:1;right:clamp(-60px,-2vw,-18px);bottom:clamp(-112px,-7vw,-60px);width:clamp(420px,37vw,720px);max-width:none;height:clamp(520px,48vw,820px);margin:0;overflow:visible;pointer-events:none}.zpKBHero__person::before{content:"";position:absolute;left:50%;bottom:2%;width:74%;height:24%;transform:translateX(-50%);background:radial-gradient(ellipse at center,rgba(0,0,0,.36),rgba(0,0,0,.12) 46%,transparent 74%);filter: none;opacity:.50;z-index:0}.zpKBHero__person img{position:relative;z-index:2;display:block;width:100%;height:100%;object-fit:contain;object-position:center bottom;filter:drop-shadow(0 34px 54px rgba(0,0,0,.25));-webkit-mask-image:linear-gradient(to bottom,#000 0%,#000 52%,rgba(0,0,0,.92) 64%,rgba(0,0,0,.64) 76%,rgba(0,0,0,.26) 88%,transparent 98%);mask-image:linear-gradient(to bottom,#000 0%,#000 52%,rgba(0,0,0,.92) 64%,rgba(0,0,0,.64) 76%,rgba(0,0,0,.26) 88%,transparent 98%)}.zpKBHero__statPills{position:absolute;z-index:4;left:clamp(10px,2vw,44px);right:clamp(8px,2vw,34px);bottom:clamp(22px,3.5vw,58px);display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:flex-end}.zpKBHero__statPills span{display:inline-flex;align-items:center;gap:9px;min-height:42px;padding:10px 14px;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.065);color:#fff;backdrop-filter: none;-webkit-backdrop-filter: none;box-shadow:0 16px 42px rgba(0,0,0,.14)}.zpKBHero__statPills strong{color:#fff;font-size:18px;line-height:1;font-weight:720;letter-spacing:-.04em}.zpKBHero__statPills em{color:rgba(255,255,255,.66);font-size:9px;line-height:1;font-weight:850;letter-spacing:.13em;text-transform:uppercase;font-style:normal!important}.zpKBFeatured{position:relative;padding:clamp(46px,6vw,96px) 0;background:#fff}.zpKBFeatured__grid{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(360px,.65fr);gap:clamp(22px,3vw,42px);align-items:stretch}.zpKBMonth{position:relative;display:grid;grid-template-columns:minmax(360px,.72fr) minmax(0,1fr);min-height:520px;border:1px solid var(--line);border-radius:38px;background:#fff;overflow:hidden;box-shadow:0 30px 110px rgba(7,17,31,.08)}.zpKBMonth__media{position:relative;display:block;min-height:520px;height:100%;overflow:hidden;background:#eef1f5}.zpKBMonth__media img{position:absolute;inset:0;width:100%;height:100%;max-width:none;object-fit:cover;object-position:center center;display:block;filter:saturate(.98) contrast(1.02);transition:transform .55s var(--ease)}.zpKBMonth:hover .zpKBMonth__media img{transform:scale(1.045)}.zpKBMonth__media::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 44%,rgba(0,0,0,.22));pointer-events:none}.zpKBMonth__body{padding:clamp(26px,3vw,48px);display:flex;flex-direction:column;justify-content:center}.zpKBMonth h2{margin:0;color:#07111f;font-size:clamp(34px,3.25vw,58px);line-height:1.02;letter-spacing:-.048em;font-weight:650;text-wrap:balance}.zpKBMonth p{margin:20px 0 28px;color:#5d6879;font-size:15px;line-height:1.72}.zpKBMonth__meta{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:18px}.zpKBMonth__meta span{padding:9px 11px;border:1px solid rgba(7,17,31,.10);border-radius:999px;background:#f7f8fa;color:#5d6879;font-size:10px;font-weight:850;letter-spacing:.10em;text-transform:uppercase}.zpKBMonth__link,.zpKBPost__link,.zpKBPick a{display:inline-flex;align-items:center;gap:10px;color:#071426;font-size:12px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}.zpKBMonth__link svg,.zpKBPick a svg,.zpKBPost__link svg{width:16px;height:16px;transition:transform .25s}.zpKBMonth__link:hover svg,.zpKBPick a:hover svg,.zpKBPost__link:hover svg{transform:translate(3px,-3px)}.zpKBPickBox{display:grid;gap:14px}.zpKBPickBox__head{border:1px solid var(--line);border-radius:30px;padding:24px;background:#f7f8fa}.zpKBPickBox__head h2{margin:0;color:#07111f;font-size:clamp(26px,2vw,34px);letter-spacing:-.04em;line-height:1.06}.zpKBPickBox__head p{margin:10px 0 0;color:#687384;font-size:14px;line-height:1.55}.zpKBPick{display:grid;grid-template-columns:112px 1fr;gap:16px;align-items:center;border:1px solid var(--line);border-radius:26px;background:#fff;padding:14px;min-height:142px}.zpKBPick img{width:112px;height:102px;object-fit:cover;object-position:center;border-radius:18px}.zpKBPick small{display:block;margin-bottom:8px;color:#738096;font-size:9px;font-weight:850;letter-spacing:.12em;text-transform:uppercase}.zpKBPick h3{margin:0 0 10px;color:#07111f;font-size:16px;line-height:1.12;letter-spacing:-.035em}.zpKBPick a{font-size:10px}.zpKBIndex{position:relative;padding:clamp(52px,7vw,112px) 0;background:linear-gradient(180deg,#f7f8fa 0%,#fff 54%,#fff 100%)}.zpKBIndex__layout{display:grid;grid-template-columns:minmax(0,1fr) minmax(320px,410px);gap:clamp(24px,4vw,58px);align-items:start}.zpKBIndex__main{min-width:0}.zpKBIndex__side{position:sticky;top:112px;display:grid;gap:16px}.zpKBIndex__head{display:grid;grid-template-columns:minmax(0,1fr) minmax(320px,520px);gap:28px;align-items:end;margin:0 0 28px}.zpKBIndex__head h2{margin:0;color:#07111f;font-size:clamp(36px,4.2vw,70px);line-height:1;letter-spacing:-.052em;font-weight:650;text-wrap:balance}.zpKBIndex__lead{margin:0;color:#687384;font-size:16px;line-height:1.75;letter-spacing:-.01em}.zpKBToolbar{display:grid;grid-template-columns:minmax(0,1fr) 145px auto;gap:12px;align-items:center;margin:0 0 18px}.zpKBSearch{position:relative;display:flex;align-items:center;min-height:58px;border:1px solid var(--line);border-radius:999px;background:#fff;box-shadow:0 18px 54px rgba(7,17,31,.04)}.zpKBSearch svg{position:absolute;left:20px;width:19px;height:19px;color:#102a4f}.zpKBSearch input{width:100%;height:58px;border:0;outline:0;background:transparent;padding:0 22px 0 56px;color:#07111f;font:600 15px/1 var(--font)!important}.zpKBSort{height:58px;border:1px solid var(--line);border-radius:999px;background:#fff;color:#07111f;padding:0 18px;font-size:12px;font-weight:850;letter-spacing:.08em;text-transform:uppercase;outline:0}.zpKBView{display:flex;gap:6px;align-items:center;justify-content:center;min-height:58px;padding:5px;border:1px solid var(--line);border-radius:999px;background:#fff}.zpKBView button{width:46px;height:46px;border:0;border-radius:999px;background:transparent;color:#536075;display:grid;place-items:center;cursor:pointer;transition:.22s var(--ease)}.zpKBView button svg{width:18px;height:18px}.zpKBView button:hover,.zpKBView button.is-active{background:#071426!important;color:#fff!important;transform:none!important}.zpKBCats{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 18px}.zpKBCat{min-height:38px;padding:0 13px;border:1px solid rgba(7,17,31,.10);border-radius:999px;background:#fff;color:#536075;font-size:12px;font-weight:760;cursor:pointer;transition:.22s var(--ease)}.zpKBCat span{margin-left:6px;color:#8190a3}.zpKBCat:hover,.zpKBCat.is-active{background:#071426!important;border-color:#071426!important;color:#fff!important;transform:none!important}.zpKBCat.is-active span{color:rgba(255,255,255,.74)}.zpKBStatus{display:flex;justify-content:space-between;gap:18px;margin:6px 0 18px;color:#7b8494;font-size:12px;font-weight:750}.zpKBLoader{display:none}.is-loading .zpKBLoader{display:inline-flex;gap:5px;align-items:center}.zpKBLoader i{width:5px;height:5px;border-radius:50%;background:#102a4f;animation:zpKBDot .8s infinite alternate}.zpKBLoader i:nth-child(2){animation-delay:.12s}.zpKBLoader i:nth-child(3){animation-delay:.24s}@keyframes zpKBDot{to{opacity:.25;transform:translateY(-3px)}}.zpKBEmpty{display:none;border:1px dashed rgba(7,17,31,.18);border-radius:28px;padding:28px;background:#fff;color:#687384}.is-empty .zpKBEmpty{display:block}.is-empty .zpKBPosts{display:none}.zpKBPosts{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}.zpKBPost{position:relative;border:1px solid var(--line);border-radius:30px;background:#fff;overflow:hidden;box-shadow:0 22px 74px rgba(7,17,31,.055);transition:transform .25s var(--ease),box-shadow .25s var(--ease)}.zpKBPost:hover{transform:translateY(-4px);box-shadow:0 30px 90px rgba(7,17,31,.09)}.zpKBPost__media{position:relative;display:block;aspect-ratio:16/10;overflow:hidden;background:#edf0f4}.zpKBPost__media img{width:100%;height:100%;object-fit:cover;object-position:center;display:block;transition:transform .5s var(--ease)}.zpKBPost:hover .zpKBPost__media img{transform:scale(1.045)}.zpKBPost__media::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.04) 0%,rgba(0,0,0,.40) 100%);pointer-events:none}.zpKBPost__views{position:absolute;z-index:3;left:12px;top:12px;display:inline-flex;align-items:center;gap:6px;min-height:28px;padding:7px 10px;border-radius:999px;background:rgba(255,255,255,.86);backdrop-filter: none;-webkit-backdrop-filter: none;color:#07111f;font-size:10px;font-weight:850;letter-spacing:.04em}.zpKBPost__views svg{width:13px;height:13px}.zpKBPost__tagStack{position:absolute;z-index:3;left:12px;right:12px;bottom:12px;display:flex;flex-wrap:wrap;gap:6px}.zpKBPost__tagStack em{display:inline-flex;min-height:26px;align-items:center;padding:7px 9px;border-radius:999px;background:rgba(255,255,255,.88);color:#07111f;font-size:9px;font-weight:850;letter-spacing:.08em;text-transform:uppercase;font-style:normal!important}.zpKBPost__body{padding:20px}.zpKBPost__meta{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;color:#7b8494;font-size:9px;font-weight:850;letter-spacing:.10em;text-transform:uppercase}.zpKBPost h3{margin:0;color:#07111f;font-size:clamp(20px,1.5vw,27px);line-height:1.05;letter-spacing:-.045em;font-weight:650}.zpKBPost p{margin:14px 0 18px;color:#687384;font-size:14px;line-height:1.62}.zpKBPost__link{font-size:10px}.zpKBPosts.is-list{grid-template-columns:1fr}.zpKBPosts.is-list .zpKBPost{display:grid;grid-template-columns:320px 1fr}.zpKBPosts.is-list .zpKBPost__media{aspect-ratio:auto;min-height:238px;height:100%}.zpKBPosts.is-compact{grid-template-columns:repeat(4,minmax(0,1fr))}.zpKBPosts.is-compact .zpKBPost__media{aspect-ratio:16/9}.zpKBPosts.is-compact .zpKBPost p{display:none}.zpKBPosts.is-compact .zpKBPost h3{font-size:20px}.zpKBPosts.is-compact .zpKBPost__meta span:nth-child(3){display:none}.zpKBSideCard{border:1px solid var(--line);border-radius:30px;background:#fff;padding:22px;box-shadow:0 22px 74px rgba(7,17,31,.045)}.zpKBSideCard h3{margin:0 0 10px;color:#07111f;font-size:24px;line-height:1.06;letter-spacing:-.045em}.zpKBSideCard p{margin:0 0 16px;color:#687384;font-size:13px;line-height:1.6}.zpKBPathsMini{display:grid;gap:8px}.zpKBPath{position:relative;display:grid;grid-template-columns:1fr auto;gap:8px;align-items:center;min-height:56px;width:100%;padding:14px 16px;border:1px solid rgba(7,17,31,.10);border-radius:18px;background:#fff;color:#07111f;text-align:left;cursor:pointer;overflow:hidden;transition:.22s var(--ease)}.zpKBPath::before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,#05070b,#0b1830,#102a4f,#1c477a);transform:scaleX(0);transform-origin:left;transition:transform .28s var(--ease);z-index:0}.zpKBPath span,.zpKBPath strong,.zpKBPath i{position:relative;z-index:1}.zpKBPath span{font-size:15px;font-weight:720;letter-spacing:-.02em}.zpKBPath strong{font-size:10px;color:#7b8494;font-weight:850;letter-spacing:.1em;text-transform:uppercase}.zpKBPath i{width:15px;height:15px;grid-column:2;grid-row:1 / span 2;color:#07111f}.zpKBPath:hover{border-color:#071426;color:#fff}.zpKBPath:hover::before{transform:scaleX(1)}.zpKBPath:hover strong,.zpKBPath:hover i{color:rgba(255,255,255,.76)}.zpKBFooterCTA{position:relative;isolation:isolate;margin-top:clamp(54px,7vw,112px);padding:clamp(54px,7vw,92px);border-radius:34px;overflow:hidden;display:grid;grid-template-columns:minmax(0,1fr) minmax(340px,.52fr);gap:clamp(28px,5vw,72px);align-items:center;background:#05070b;color:#fff}.zpKBFooterCTA__bleed{position:absolute;inset:0;z-index:0;background:radial-gradient(circle at 14% 0%,rgba(28,71,122,.26),transparent 36%),radial-gradient(circle at 90% 12%,rgba(59,110,168,.15),transparent 34%),linear-gradient(135deg,#030407 0%,#05070b 44%,#07101d 100%)}.zpKBFooterCTA__bleed::before{content:"";position:absolute;inset:0;opacity:.046;background-image:linear-gradient(rgba(255,255,255,.13) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.13) 1px,transparent 1px);background-size:44px 44px}.zpKBFooterCTA__bleed::after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(2,3,6,.96),rgba(5,9,17,.76) 44%,rgba(2,3,6,.96)),linear-gradient(180deg,rgba(2,3,6,.82),rgba(6,13,25,.50) 48%,rgba(2,3,6,.96))}.zpKBFooterCTA::before{content:"";position:absolute;z-index:1;left:50%;top:0;width:100vw;height:120%;transform:translateX(-50%);opacity:.10;background:radial-gradient(circle at 50% 22%,rgba(59,110,168,.30),transparent 42%),radial-gradient(circle at 18% 70%,rgba(16,42,79,.30),transparent 36%);filter:none}.zpKBFooterCTA__mark{position:absolute;z-index:1;left:50%;bottom:-.16em;transform:translateX(-50%);width:max-content;color:#fff;opacity:.055;font-size:clamp(82px,18vw,260px);line-height:.78;letter-spacing:-.105em;font-weight:760;pointer-events:none;user-select:none;white-space:nowrap;mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent),linear-gradient(180deg,#000,#000 50%,rgba(0,0,0,.62) 70%,transparent);-webkit-mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent),linear-gradient(180deg,#000,#000 50%,rgba(0,0,0,.62) 70%,transparent);-webkit-mask-composite:source-in;mask-composite:intersect}.zpKBFooterCTA__copy,.zpKBFooterCTA__panel{position:relative;z-index:5}.zpKBFooterCTA__eyebrow{display:inline-flex;align-items:center;gap:10px;margin:0 0 14px;color:rgba(255,255,255,.72);text-transform:uppercase;letter-spacing:.13em;font-size:10px;line-height:1;font-weight:800}.zpKBFooterCTA__eyebrow::before{content:"";display:block;width:24px;height:1px;background:linear-gradient(90deg,#102a4f,#1c477a,#3b6ea8)}.zpKBFooterCTA h2{max-width:900px;margin:0!important;color:#fff!important;font-size:clamp(36px,4.65vw,72px)!important;line-height:.98!important;letter-spacing:-.056em!important;font-weight:560!important;text-wrap:balance}.zpKBFooterCTA p{max-width:760px;margin:20px 0 0!important;color:rgba(255,255,255,.68)!important;font-size:clamp(14px,1vw,16px)!important;line-height:1.62!important}.zpKBFooterCTA__panel{display:grid;gap:14px}.zpKBFooterCTA__frame{position:relative;isolation:isolate;min-height:300px;border-radius:28px;display:grid;place-items:center;text-align:center;padding:24px;background:rgba(255,255,255,.015);overflow:hidden;border:1px solid rgba(255,255,255,.30)}.zpKBFooterCTA__frame::before{content:"";position:absolute;inset:-1px;z-index:-1;border-radius:inherit;padding:1px;background:linear-gradient(90deg,transparent 0%,rgba(255,255,255,0) 35%,rgba(59,110,168,.98) 50%,rgba(255,255,255,.80) 56%,rgba(59,110,168,.98) 62%,rgba(255,255,255,0) 78%,transparent 100%);background-size:260px 100%;background-repeat:no-repeat;animation:zpKBFooterBorderShine 4.8s linear infinite;-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:.9}.zpKBFooterCTA__frame::after{content:"";position:absolute;inset:auto 22px 22px;height:3px;border-radius:999px;background:linear-gradient(90deg,#102a4f,#1c477a,#3b6ea8);background-size:220% 100%;animation:zpKBFooterLineRun 2.8s linear infinite;opacity:.84}@keyframes zpKBFooterBorderShine{0%{background-position:-260px 0}100%{background-position:calc(100% + 260px) 0}}@keyframes zpKBFooterLineRun{0%{background-position:0% 50%}100%{background-position:220% 50%}}.zpKBFooterCTA__frame span{position:relative;z-index:3;display:block;color:#fff!important;font-size:clamp(28px,3.2vw,48px);line-height:1.04;letter-spacing:-.045em;font-weight:560;text-shadow:0 18px 44px rgba(0,0,0,.44)}.zpKBFooterCTA__actions{display:grid;gap:10px}.zpKBFooterCTA__btn{--zpBtnFill:linear-gradient(90deg,#05070b 0%,#0b1830 42%,#102a4f 72%,#1c477a 100%);width:100%;min-height:54px;display:inline-flex!important;align-items:center;justify-content:center;gap:10px;padding:0 22px;border-radius:999px;background:var(--zpBtnFill) left center / 0% 100% no-repeat,linear-gradient(#fff,#fff) right center / 100% 100% no-repeat!important;color:#111!important;font-size:14px;line-height:1;font-weight:720;letter-spacing:-.01em;border:1px solid #fff!important;position:relative;overflow:hidden;text-decoration:none!important;transition:background-size .34s cubic-bezier(.16,1,.3,1),color .22s ease,border-color .22s ease!important}.zpKBFooterCTA__btn i{width:16px;height:16px;transition:transform .22s cubic-bezier(.16,1,.3,1)}.zpKBFooterCTA__btn:hover{background-size:100% 100%,0% 100%!important;color:#fff!important;border-color:rgba(28,71,122,.58)!important}.zpKBFooterCTA__btn:hover i{transform:translate(3px,-3px)}.zpKBFooterCTA__btn--ghost{background:var(--zpBtnFill) left center / 0% 100% no-repeat,linear-gradient(rgba(255,255,255,.10),rgba(255,255,255,.10)) right center / 100% 100% no-repeat!important;color:#fff!important;border-color:rgba(255,255,255,.28)!important;backdrop-filter: none;-webkit-backdrop-filter:none}@media(max-width:1380px){.zpKBPosts{grid-template-columns:repeat(2,minmax(0,1fr))}.zpKBPosts.is-compact{grid-template-columns:repeat(3,minmax(0,1fr))}.zpKBIndex__layout{grid-template-columns:minmax(0,1fr) 340px}.zpKBMonth h2{font-size:clamp(32px,3vw,50px)}}@media(max-width:1080px){.zpKBHero{min-height:auto}.zpKBHero__grid{grid-template-columns:1fr}.zpKBHero__visual{min-height:360px}.zpKBFeatured__grid,.zpKBIndex__layout,.zpKBIndex__head,.zpKBFooterCTA{grid-template-columns:1fr}.zpKBIndex__side{position:relative;top:auto;order:-1}.zpKBPosts.is-compact{grid-template-columns:repeat(2,minmax(0,1fr))}.zpKBToolbar{grid-template-columns:minmax(0,1fr) 142px auto}.zpKBMonth{grid-template-columns:1fr}.zpKBMonth__media{min-height:320px;aspect-ratio:16/10}.zpKBFeatured{order:3}.zpKBIndex{order:1}.zpKBFooterCTA{padding:48px 22px 62px!important;border-radius:28px!important}.zpKBFooterCTA__frame{min-height:250px}.zpKBFooterCTA h2{font-size:clamp(34px,10vw,48px)!important}.zpKBFooterCTA p{font-size:13px!important}.zpKBFooterCTA__frame span{font-size:clamp(24px,8vw,34px)}}@media(max-width:760px){#zpKnowledgePro{display:flex;flex-direction:column}.zpKBHero{padding:116px 0 54px}.zpKBHero__inner,.zpKBInner{padding-left:18px!important;padding-right:18px!important}.zpKBCrumbs{font-size:9px;margin-bottom:22px}.zpKBKicker{font-size:9px;letter-spacing:.13em}.zpKBHero h1{font-size:clamp(28px,8.6vw,42px)!important;line-height:1.02!important;letter-spacing:-.038em!important}.zpKBHero__lead{font-size:14px;line-height:1.65}.zpKBHero__actions{gap:10px}.zpKBBtn{width:100%;min-height:52px}.zpKBHero__visual{min-height:300px}.zpKBHero__person{right:-70px;bottom:-74px;width:min(118vw,520px);height:380px}.zpKBHero__statPills{left:0;right:0;bottom:12px;justify-content:flex-start}.zpKBHero__statPills span{min-height:36px;padding:9px 11px}.zpKBFeatured{padding-top:36px}.zpKBFeatured .zpKBInner{padding-left:16px!important;padding-right:16px!important}.zpKBMonth{border-radius:28px;min-height:0}.zpKBMonth__media{min-height:245px;aspect-ratio:16/10}.zpKBMonth__body{padding:24px 20px}.zpKBMonth h2{font-size:clamp(29px,9vw,42px)}.zpKBPickBox__head{border-radius:24px}.zpKBPick{grid-template-columns:96px 1fr;gap:12px}.zpKBPick img{width:96px;height:86px}.zpKBIndex{padding-top:44px}.zpKBIndex__head h2{font-size:clamp(31px,9.4vw,44px);letter-spacing:-.044em}.zpKBIndex__lead{font-size:14px}.zpKBToolbar{grid-template-columns:1fr;gap:10px}.zpKBSort,.zpKBView{grid-row:2}.zpKBSort{grid-column:1;width:calc(50% - 5px);min-width:0}.zpKBView{grid-column:1;justify-self:end;width:calc(50% - 5px)}.zpKBView button{width:36px;height:36px}.zpKBView{min-height:48px}.zpKBSort{height:48px}.zpKBSearch{min-height:54px}.zpKBSearch input{height:54px;font-size:13px}.zpKBCats{overflow-x:auto;flex-wrap:nowrap;padding-bottom:2px;scrollbar-width:none}.zpKBCats::-webkit-scrollbar{display:none}.zpKBCat{white-space:nowrap}.zpKBStatus{font-size:11px}.zpKBPosts,.zpKBPosts.is-compact{grid-template-columns:1fr}.zpKBPosts.is-list .zpKBPost{display:block}.zpKBPosts.is-list .zpKBPost__media{aspect-ratio:16/10;min-height:0}.zpKBPost{border-radius:26px}.zpKBPost__body{padding:18px}.zpKBPost h3{font-size:25px}.zpKBPost__tagStack em{font-size:8px}.zpKBPost__views{font-size:9px}.zpKBSideCard{border-radius:24px;padding:18px}.zpKBPathsMini{grid-template-columns:1fr}.zpKBPath{min-height:52px}.zpKBFooterCTA{width:100vw!important;margin-left:calc(50% - 50vw)!important;margin-right:calc(50% - 50vw)!important;border-radius:0!important;padding:58px 18px 72px!important}.zpKBFooterCTA__mark{font-size:clamp(82px,25vw,156px)}.zpKBFooterCTA__frame{min-height:250px}}#zpKnowledgePro .zpKBBtn--white,#zpKnowledgePro .zpKBBtn--white span,#zpKnowledgePro .zpKBBtn--white i{color:#071426!important;stroke:#071426!important}#zpKnowledgePro .zpKBBtn--white:hover,#zpKnowledgePro .zpKBBtn--white:hover span,#zpKnowledgePro .zpKBBtn--white:hover i{color:#fff!important;stroke:#fff!important}#zpKnowledgePro .zpKBFooterCTA__btn:not(.zpKBFooterCTA__btn--ghost),#zpKnowledgePro .zpKBFooterCTA__btn:not(.zpKBFooterCTA__btn--ghost) span,#zpKnowledgePro .zpKBFooterCTA__btn:not(.zpKBFooterCTA__btn--ghost) i{color:#071426!important;stroke:#071426!important}#zpKnowledgePro .zpKBFooterCTA__btn:not(.zpKBFooterCTA__btn--ghost):hover,#zpKnowledgePro .zpKBFooterCTA__btn:not(.zpKBFooterCTA__btn--ghost):hover span,#zpKnowledgePro .zpKBFooterCTA__btn:not(.zpKBFooterCTA__btn--ghost):hover i{color:#fff!important;stroke:#fff!important}#zpKnowledgePro .zpKBView{display:grid!important;grid-template-columns:repeat(3,1fr)!important;align-items:center!important;justify-items:center!important;gap:4px!important;padding:4px!important}#zpKnowledgePro .zpKBView button{display:grid!important;place-items:center!important;margin:0!important;padding:0!important;line-height:0!important}#zpKnowledgePro .zpKBView button i,#zpKnowledgePro .zpKBView button svg{display:block!important;margin:0!important;width:18px!important;height:18px!important;stroke:currentColor!important}#zpKnowledgePro .zpKBView button:hover,#zpKnowledgePro .zpKBView button.is-active,#zpKnowledgePro .zpKBCat:hover,#zpKnowledgePro .zpKBCat.is-active,#zpKnowledgePro .zpKBPath:hover{background:linear-gradient(100deg,#020407 0%,#071426 42%,#102a4f 74%,#1c477a 100%)!important;border-color:rgba(16,42,79,.88)!important;color:#fff!important}#zpKnowledgePro .zpKBCat:hover span,#zpKnowledgePro .zpKBCat.is-active span{color:rgba(255,255,255,.74)!important}#zpKnowledgePro .zpKBFeatured{padding-top:clamp(26px,4vw,58px)!important}#zpKnowledgePro .zpKBSideHint{display:none;align-items:center;gap:8px;margin:12px 0 0;color:#7b8494;font-size:10px;font-weight:850;letter-spacing:.12em;text-transform:uppercase}#zpKnowledgePro .zpKBSideHint i{width:15px;height:15px;animation:zpKBHintMove 1.25s ease-in-out infinite}@keyframes zpKBHintMove{0%,100%{transform:translateX(0);opacity:.55}50%{transform:translateX(6px);opacity:1}}@media(max-width:760px){#zpKnowledgePro .zpKBHero__video video{opacity:.56!important;transform:scale(1.02)!important}#zpKnowledgePro .zpKBFeatured{padding-top:20px!important}#zpKnowledgePro .zpKBIndex__side{order:-1!important;width:100%;overflow:hidden}#zpKnowledgePro .zpKBSideCard{padding:18px 0 16px!important;border-radius:0!important}#zpKnowledgePro .zpKBSideCard>.zpKBLabel,#zpKnowledgePro .zpKBSideCard>h3,#zpKnowledgePro .zpKBSideCard>p,#zpKnowledgePro .zpKBSideHint{margin-left:0;margin-right:0}#zpKnowledgePro .zpKBSideCard>h3{font-size:clamp(26px,7.8vw,34px)!important;line-height:1.02!important;letter-spacing:-.044em!important;max-width:92%}#zpKnowledgePro .zpKBSideCard>p{font-size:13px!important;line-height:1.55!important;max-width:94%;margin-bottom:10px!important}#zpKnowledgePro .zpKBPathsMini{padding:14px 18px 4px 0!important;margin-right:-18px!important}#zpKnowledgePro .zpKBPath{min-width:235px!important;width:235px!important;min-height:112px!important;padding:18px!important;background:#fff!important;grid-template-columns:1fr auto!important}#zpKnowledgePro .zpKBPath span{line-height:1.08!important}#zpKnowledgePro .zpKBPath i{align-self:end!important}#zpKnowledgePro .zpKBToolbar{grid-template-columns:minmax(0,1fr) minmax(136px,40%)!important}#zpKnowledgePro .zpKBSort{grid-row:2!important}#zpKnowledgePro .zpKBView{grid-row:2!important;justify-self:stretch!important}#zpKnowledgePro .zpKBView button{width:38px!important;height:38px!important}}@media(prefers-reduced-motion:reduce){#zpKnowledgePro *{animation:none!important;transition:none!important}.zpKBHero__video video{transform:none!important}}#zpKnowledgePro .zpKBHero + .zpKBFeatured{margin-top:30px!important}@media(max-width:760px){#zpKnowledgePro .zpKBHero + .zpKBFeatured{margin-top:30px!important}}#zpKnowledgePro .zpKBHero__video video{opacity:.50!important}#zpKnowledgePro .zpKBHero__inner{z-index:2!important}#zpKnowledgePro .zpKBHero__visual{position:relative!important;z-index:2!important}#zpKnowledgePro .zpKBPlanBox{display:grid;gap:14px;align-content:start}#zpKnowledgePro .zpKBPlanBox__head{border:1px solid var(--line);border-radius:30px;padding:24px;background:#fff;box-shadow:0 22px 74px rgba(7,17,31,.045)}#zpKnowledgePro .zpKBPlanBox__head h2{margin:0;color:#07111f;font-size:clamp(26px,2vw,34px);letter-spacing:-.04em;line-height:1.06}#zpKnowledgePro .zpKBPlanBox__head p{margin:10px 0 0;color:#687384;font-size:14px;line-height:1.6}#zpKnowledgePro .zpKBPlanSteps{display:grid;gap:10px}#zpKnowledgePro .zpKBPlanSteps article{position:relative;display:grid;grid-template-columns:44px 1fr;gap:0 14px;align-items:start;min-height:104px;padding:18px;border:1px solid var(--line);border-radius:26px;background:#fff;box-shadow:0 18px 62px rgba(7,17,31,.045);overflow:hidden}#zpKnowledgePro .zpKBPlanSteps article::before{content:"";position:absolute;inset:0;background:linear-gradient(105deg,#020407,#071426,#102a4f,#1c477a);opacity:0;transition:opacity .25s var(--ease);z-index:0}#zpKnowledgePro .zpKBPlanSteps article>*{position:relative;z-index:1}#zpKnowledgePro .zpKBPlanSteps article:hover::before{opacity:1}#zpKnowledgePro .zpKBPlanSteps span{grid-row:1 / span 2;display:grid;place-items:center;width:44px;height:44px;border-radius:16px;background:#f2f5f8;color:#102a4f;font-size:11px;font-weight:900;letter-spacing:.08em}#zpKnowledgePro .zpKBPlanSteps strong{color:#07111f;font-size:18px;line-height:1.1;letter-spacing:-.03em}#zpKnowledgePro .zpKBPlanSteps p{margin:7px 0 0;color:#687384;font-size:13px;line-height:1.55}#zpKnowledgePro .zpKBPlanSteps article:hover span{background:rgba(255,255,255,.12);color:#fff}#zpKnowledgePro .zpKBPlanSteps article:hover strong,#zpKnowledgePro .zpKBPlanSteps article:hover p{color:#fff}#zpKnowledgePro .zpKBPlanBox__cta{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:54px;padding:0 22px;border-radius:999px;background:#071426;color:#fff;font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}#zpKnowledgePro .zpKBPlanBox__cta svg{width:16px;height:16px}#zpKnowledgePro .zpKBSideCard{overflow:visible!important}#zpKnowledgePro .zpKBSideCard h3{font-size:clamp(24px,1.7vw,31px)!important;letter-spacing:-.04em!important}#zpKnowledgePro .zpKBPath{min-height:72px!important;border-radius:22px!important;grid-template-columns:auto 1fr auto!important}#zpKnowledgePro .zpKBPath::after{content:"";position:relative;z-index:1;grid-column:1;grid-row:1 / span 2;width:34px;height:34px;border-radius:13px;background:#f2f5f8;box-shadow:inset 0 0 0 1px rgba(7,17,31,.06)}#zpKnowledgePro .zpKBPath span{grid-column:2!important;font-size:16px!important;line-height:1.05!important;font-weight:720!important}#zpKnowledgePro .zpKBPath strong{grid-column:2!important;font-size:10px!important}#zpKnowledgePro .zpKBPath i{grid-column:3!important;align-self:center!important;grid-row:1 / span 2!important;width:16px!important;height:16px!important}@media(max-width:760px){#zpKnowledgePro .zpKBHero__video video{opacity:.58!important}#zpKnowledgePro .zpKBFeatured__grid{grid-template-columns:1fr!important}#zpKnowledgePro .zpKBPlanBox{margin-top:8px}#zpKnowledgePro .zpKBIndex__layout{display:flex!important;flex-direction:column!important;gap:24px!important}#zpKnowledgePro .zpKBIndex__main{width:100%!important;min-width:0!important;order:2!important}#zpKnowledgePro .zpKBIndex__side{position:relative!important;top:auto!important;order:1!important}#zpKnowledgePro .zpKBSideCard{padding:8px 0 14px!important;border:0!important;background:transparent!important;box-shadow:none!important}#zpKnowledgePro .zpKBSideCard>.zpKBLabel,#zpKnowledgePro .zpKBSideCard>h3,#zpKnowledgePro .zpKBSideCard>p,#zpKnowledgePro .zpKBSideHint{padding-left:0!important;padding-right:0!important}#zpKnowledgePro .zpKBSideHint{align-items:center!important;gap:8px!important;margin:12px 0 2px!important;color:#6f7b8e!important}#zpKnowledgePro .zpKBSideHint i{animation:zpKBHintMoveFinal 1.05s ease-in-out infinite!important}@keyframes zpKBHintMoveFinal{0%,100%{transform:translateX(0);opacity:.45}50%{transform:translateX(9px);opacity:1}}#zpKnowledgePro .zpKBPathsMini{width:calc(100vw - 22px)!important;margin-left:0!important;margin-right:calc(50% - 50vw)!important;padding:14px 18px 8px 0!important;gap:12px!important;scrollbar-width:none!important}#zpKnowledgePro .zpKBPath{flex:0 0 245px!important;width:245px!important;min-width:245px!important;min-height:104px!important;border-radius:28px!important;padding:17px!important;grid-template-columns:40px 1fr 18px!important;box-shadow:0 16px 54px rgba(7,17,31,.055)!important}#zpKnowledgePro .zpKBPath::after{width:36px!important;height:36px!important;border-radius:14px!important}#zpKnowledgePro .zpKBPath span{font-size:18px!important;line-height:1.06!important}#zpKnowledgePro .zpKBPath strong{font-size:10px!important}#zpKnowledgePro .zpKBToolbar{grid-template-columns:1fr!important}#zpKnowledgePro .zpKBSearch input{width:100%!important;min-width:0!important}#zpKnowledgePro .zpKBSort{grid-column:1/-1!important}#zpKnowledgePro .zpKBView{grid-column:1/-1!important;width:100%!important;justify-content:space-between!important}#zpKnowledgePro .zpKBCats{display:flex!important;flex-wrap:nowrap!important;overflow-x:auto!important;overflow-y:hidden!important;-webkit-overflow-scrolling:touch!important;scrollbar-width:none!important;padding:0 18px 8px 0!important}#zpKnowledgePro .zpKBCats::-webkit-scrollbar{display:none!important}#zpKnowledgePro .zpKBCat{flex:0 0 auto!important}}#zpKnowledgePro .zpKBHero__person{transform:translate3d(var(--zp-wiedza-person-x-d,0px),var(--zp-wiedza-person-y-d,0px),0) scale(var(--zp-wiedza-person-scale-d,1))!important;transform-origin:center bottom!important;will-change:transform}#zpKnowledgePro .zpKBHero__video::after{content:"";position:absolute;inset:0;z-index:2;pointer-events:none;background:linear-gradient(90deg,rgba(2,4,7,.34),rgba(7,20,38,.28) 42%,rgba(16,42,79,.44)),linear-gradient(180deg,rgba(2,4,7,.24),rgba(7,20,38,.42));mix-blend-mode:multiply}#zpKnowledgePro .zpKBMobileFilterLabel{display:none}@media(max-width:760px){#zpKnowledgePro .zpKBHero__video{z-index:-6!important;background:#05070b!important}#zpKnowledgePro .zpKBHero__video video{opacity:.76!important;filter:saturate(.92) brightness(.72) contrast(1.14)!important;transform:scale(1.035)!important;object-fit:cover!important;object-position:center!important}#zpKnowledgePro .zpKBHero__video::after{background:linear-gradient(90deg,rgba(2,4,7,.48),rgba(7,20,38,.34) 46%,rgba(16,42,79,.54)),linear-gradient(180deg,rgba(2,4,7,.20),rgba(7,20,38,.48) 66%,rgba(2,4,7,.70))!important;mix-blend-mode:normal!important}#zpKnowledgePro .zpKBHero::after{background:linear-gradient(90deg,rgba(3,4,7,.70),rgba(5,7,11,.38) 48%,rgba(3,4,7,.82)),linear-gradient(180deg,rgba(5,7,11,.52),rgba(5,7,11,.20) 42%,rgba(0,0,0,.78))!important}#zpKnowledgePro .zpKBHero__person{transform:translate3d(var(--zp-wiedza-person-x-m,0px),var(--zp-wiedza-person-y-m,0px),0) scale(var(--zp-wiedza-person-scale-m,1))!important;transform-origin:center bottom!important}#zpKnowledgePro .zpKBToolbar{display:grid!important;grid-template-columns:minmax(0,1fr) auto!important;gap:10px!important;align-items:center!important}#zpKnowledgePro .zpKBMobileFilterLabel{grid-column:1/-1;display:inline-flex!important;align-items:center;gap:8px;width:max-content;margin:2px 0 0;color:#738096;font-size:10px;font-weight:850;letter-spacing:.16em;text-transform:uppercase;line-height:1}#zpKnowledgePro .zpKBMobileFilterLabel i{width:14px;height:14px;color:#102a4f}#zpKnowledgePro .zpKBSearch{grid-column:1/-1!important;justify-self:stretch!important}#zpKnowledgePro .zpKBSort{grid-column:1!important;grid-row:auto!important;width:100%!important;min-width:0!important;height:46px!important}#zpKnowledgePro .zpKBView{grid-column:2!important;grid-row:auto!important;width:auto!important;min-width:max-content!important;justify-self:end!important;height:46px!important;min-height:46px!important;padding:4px!important}#zpKnowledgePro .zpKBView button{width:34px!important;height:34px!important}}#zpKnowledgePro .zpKBPath::after{display:none!important;content:none!important}#zpKnowledgePro .zpKBPath{grid-template-columns:minmax(0,1fr) auto!important;align-items:center!important;min-height:66px!important;padding:16px 18px!important}#zpKnowledgePro .zpKBPath span{grid-column:1!important;grid-row:1!important;min-width:0!important}#zpKnowledgePro .zpKBPath strong{grid-column:1!important;grid-row:2!important;min-width:0!important}#zpKnowledgePro .zpKBPath i,#zpKnowledgePro .zpKBPath svg{grid-column:2!important;grid-row:1 / span 2!important;align-self:center!important;justify-self:end!important;width:18px!important;height:18px!important}#zpKnowledgePro .zpKBHero::before{z-index:-1!important}#zpKnowledgePro .zpKBHero::after{z-index:1!important}#zpKnowledgePro .zpKBHero__video::after{background: linear-gradient(90deg,rgba(2,4,7,.62),rgba(7,20,38,.40) 46%,rgba(16,42,79,.60)),linear-gradient(180deg,rgba(2,4,7,.20),rgba(7,20,38,.44) 58%,rgba(2,4,7,.78))!important}#zpKnowledgePro .zpKBHero__video video{position:absolute!important;inset:0!important;z-index:1!important;display:block!important;width:100%!important;height:100%!important;opacity:.82!important;visibility:visible!important;object-fit:cover!important;object-position:center!important;filter:saturate(.92) brightness(.76) contrast(1.13)!important;transform:scale(1.035)!important;pointer-events:none!important}#zpKnowledgePro .zpKBHero__inner,#zpKnowledgePro .zpKBHero__visual{position:relative!important;z-index:3!important}@media(max-width:760px){#zpKnowledgePro .zpKBHero__video{z-index:0!important;display:block!important;opacity:1!important;visibility:visible!important}#zpKnowledgePro .zpKBHero__video video{display:block!important;opacity:.88!important;visibility:visible!important;object-position:center top!important;transform:scale(1.055)!important}#zpKnowledgePro .zpKBHero__video::after{background: linear-gradient(90deg,rgba(2,4,7,.68),rgba(7,20,38,.44) 48%,rgba(16,42,79,.66)),linear-gradient(180deg,rgba(2,4,7,.18),rgba(7,20,38,.46) 56%,rgba(2,4,7,.82))!important}#zpKnowledgePro .zpKBIndex__side{width:100%!important;max-width:none!important;overflow:visible!important}#zpKnowledgePro .zpKBSideCard{width:100%!important;max-width:none!important;overflow:visible!important}#zpKnowledgePro .zpKBSideHint{display:inline-flex!important;margin:12px 0 14px!important}#zpKnowledgePro .zpKBSideHint i,#zpKnowledgePro .zpKBSideHint svg{animation:zpKBHintMove2157 1.1s ease-in-out infinite!important}@keyframes zpKBHintMove2157{0%,100%{transform:translateX(0);opacity:.50}50%{transform:translateX(9px);opacity:1}}#zpKnowledgePro .zpKBPathsMini{display:flex!important;grid-template-columns:none!important;gap:10px!important;width:calc(100vw - 20px)!important;max-width:none!important;margin-left:calc(50% - 50vw + 10px)!important;margin-right:calc(50% - 50vw + 10px)!important;padding:0 18px 12px!important;overflow-x:auto!important;overflow-y:hidden!important;-webkit-overflow-scrolling:touch!important;scroll-snap-type:x mandatory!important;overscroll-behavior-x:contain!important;touch-action:pan-x!important}#zpKnowledgePro .zpKBPathsMini::-webkit-scrollbar{display:none!important}#zpKnowledgePro .zpKBPath{flex:0 0 min(78vw,330px)!important;width:min(78vw,330px)!important;min-width:min(78vw,330px)!important;max-width:min(78vw,330px)!important;scroll-snap-align:start!important}#zpKnowledgePro .zpKBSearch{width:100%!important;max-width:none!important;min-width:0!important;grid-column:1 / -1!important}#zpKnowledgePro .zpKBToolbar{width:100%!important}}#zpKnowledgePro .zpKBHero::before{z-index:-6!important}#zpKnowledgePro .zpKBHero::after{z-index:-3!important;background: linear-gradient(90deg,rgba(3,4,7,.74) 0%,rgba(5,7,11,.48) 46%,rgba(3,4,7,.70) 100%),linear-gradient(180deg,rgba(5,7,11,.54) 0%,rgba(5,7,11,.14) 44%,rgba(5,7,11,.74) 100%),radial-gradient(circle at 68% 44%,rgba(16,42,79,.24),transparent 58%)!important}#zpKnowledgePro .zpKBHero__video{z-index:-7!important}#zpKnowledgePro .zpKBHero__video::after{background: radial-gradient(circle at 68% 42%,rgba(5,7,11,0),rgba(5,7,11,.52) 76%),linear-gradient(180deg,rgba(5,7,11,.28),rgba(5,7,11,.06) 38%,rgba(5,7,11,.70))!important}#zpKnowledgePro .zpKBHero__video video,#zpKnowledgePro .zpKBHero__videoEl{opacity:.70!important;filter:brightness(.92) saturate(.86) contrast(1.08)!important}#zpKnowledgePro[data-ready="0"] .zpKBHero__video video,#zpKnowledgePro[data-ready="0"] .zpKBHero__videoEl{opacity:.01!important}#zpKnowledgePro[data-ready="1"] .zpKBHero__video video,#zpKnowledgePro[data-ready="1"] .zpKBHero__videoEl{opacity:.70!important;transition:opacity .72s cubic-bezier(.16,1,.3,1),filter .72s ease!important}@media(max-width:760px){#zpKnowledgePro .zpKBHero::after{background: linear-gradient(90deg,rgba(3,4,7,.60) 0%,rgba(5,7,11,.32) 55%,rgba(3,4,7,.62) 100%),linear-gradient(180deg,rgba(5,7,11,.34) 0%,rgba(5,7,11,.08) 36%,rgba(5,7,11,.68) 100%),radial-gradient(circle at 58% 54%,rgba(16,42,79,.20),transparent 60%)!important}#zpKnowledgePro .zpKBHero__video video,#zpKnowledgePro .zpKBHero__videoEl{opacity:.82!important;filter:brightness(.98) saturate(.94) contrast(1.08)!important}#zpKnowledgePro[data-ready="1"] .zpKBHero__video video,#zpKnowledgePro[data-ready="1"] .zpKBHero__videoEl{opacity:.82!important}#zpKnowledgePro .zpKBHero__video::after{background: radial-gradient(circle at 58% 44%,rgba(5,7,11,0),rgba(5,7,11,.38) 78%),linear-gradient(180deg,rgba(5,7,11,.18),rgba(5,7,11,.02) 38%,rgba(5,7,11,.64))!important}}#zpKnowledgePro .zpKBHero{position:relative!important;isolation:isolate!important;overflow:hidden!important}#zpKnowledgePro .zpKBHero::before{z-index:1!important;pointer-events:none!important;background: linear-gradient(rgba(255,255,255,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.07) 1px,transparent 1px)!important;background-size:72px 72px!important;opacity:.10!important;transform:skewY(-4deg) scale(1.1)!important;-webkit-mask-image:radial-gradient(circle at 72% 42%,#000 0%,transparent 68%)!important;mask-image:radial-gradient(circle at 72% 42%,#000 0%,transparent 68%)!important}#zpKnowledgePro .zpKBHero::after{pointer-events:none!important;background: linear-gradient(90deg,rgba(3,4,7,.74) 0%,rgba(5,7,11,.46) 38%,rgba(5,7,11,.22) 64%,rgba(3,4,7,.58) 100%),linear-gradient(180deg,rgba(3,4,7,.58) 0%,rgba(5,7,11,.14) 44%,rgba(5,7,11,.70) 100%),radial-gradient(circle at 72% 44%,rgba(28,71,122,.20),transparent 56%)!important}#zpKnowledgePro .zpKBHero__video{position:absolute!important;inset:0!important;display:block!important;width:100%!important;height:100%!important;overflow:hidden!important;opacity:1!important;visibility:visible!important;pointer-events:none!important}#zpKnowledgePro .zpKBHero__video::after{content:""!important;position:absolute!important;inset:0!important;z-index:2!important;pointer-events:none!important;background: radial-gradient(circle at 70% 42%,rgba(5,7,11,0),rgba(5,7,11,.48) 76%),linear-gradient(180deg,rgba(5,7,11,.28),rgba(5,7,11,.06) 38%,rgba(5,7,11,.68))!important}#zpKnowledgePro .zpKBHero__video video,#zpKnowledgePro .zpKBHero__videoEl{position:relative!important;z-index:1!important;display:block!important;width:100%!important;height:100%!important;max-width:none!important;min-width:100%!important;min-height:100%!important;object-fit:cover!important;object-position:center center!important;opacity:.82!important;visibility:visible!important;filter:brightness(1.04) saturate(.98) contrast(1.03)!important;transform:scale(1.06)!important;background:#05070b!important}#zpKnowledgePro[data-ready="0"] .zpKBHero__video video,#zpKnowledgePro[data-ready="0"] .zpKBHero__videoEl,#zpKnowledgePro[data-ready="1"] .zpKBHero__video video,#zpKnowledgePro[data-ready="1"] .zpKBHero__videoEl{opacity:.82!important}#zpKnowledgePro .zpKBHero__noise{z-index:3!important;pointer-events:none!important}#zpKnowledgePro .zpKBHero__inner{position:relative!important;z-index:5!important}@media(max-width:760px){#zpKnowledgePro .zpKBHero::after{background: linear-gradient(90deg,rgba(3,4,7,.60) 0%,rgba(5,7,11,.28) 55%,rgba(3,4,7,.58) 100%),linear-gradient(180deg,rgba(3,4,7,.36) 0%,rgba(5,7,11,.06) 32%,rgba(5,7,11,.66) 100%),radial-gradient(circle at 58% 54%,rgba(28,71,122,.16),transparent 58%)!important}#zpKnowledgePro .zpKBHero__video video,#zpKnowledgePro .zpKBHero__videoEl{object-position:center 20%!important;opacity:.90!important;filter:brightness(1.08) saturate(1) contrast(1.03)!important;transform:translateY(-8%) scale(1.16)!important}#zpKnowledgePro[data-ready="0"] .zpKBHero__video video,#zpKnowledgePro[data-ready="0"] .zpKBHero__videoEl,#zpKnowledgePro[data-ready="1"] .zpKBHero__video video,#zpKnowledgePro[data-ready="1"] .zpKBHero__videoEl{opacity:.90!important}#zpKnowledgePro .zpKBHero__video::after{background: radial-gradient(circle at 58% 44%,rgba(5,7,11,0),rgba(5,7,11,.36) 78%),linear-gradient(180deg,rgba(5,7,11,.18),rgba(5,7,11,.02) 38%,rgba(5,7,11,.62))!important}}#zpKnowledgePro .zpKBHero{--zpKBVideoOpacity:var(--zp-wiedza-video-opacity,.74);--zpKBVideoBrightness:var(--zp-wiedza-video-brightness,.74);--zpKBVideoNavy:var(--zp-wiedza-video-navy,.82);--zpKBTextShadow:var(--zp-wiedza-text-shadow,.72)}#zpKnowledgePro .zpKBHero__video{z-index:0!important;background:#05070b!important}#zpKnowledgePro .zpKBHero__video video,#zpKnowledgePro .zpKBHero__videoEl{opacity:var(--zpKBVideoOpacity)!important;filter: grayscale(.08) sepia(.12) hue-rotate(178deg) saturate(.42) brightness(var(--zpKBVideoBrightness)) contrast(1.14)!important;mix-blend-mode:luminosity!important}#zpKnowledgePro[data-ready="0"] .zpKBHero__video video,#zpKnowledgePro[data-ready="0"] .zpKBHero__videoEl,#zpKnowledgePro[data-ready="1"] .zpKBHero__video video,#zpKnowledgePro[data-ready="1"] .zpKBHero__videoEl{opacity:var(--zpKBVideoOpacity)!important}#zpKnowledgePro .zpKBHero__video::before{content:""!important;position:absolute!important;inset:0!important;z-index:2!important;pointer-events:none!important;background: linear-gradient(135deg,rgba(2,4,7,calc(var(--zpKBVideoNavy) * .72)) 0%,rgba(7,20,38,calc(var(--zpKBVideoNavy) * .84)) 42%,rgba(16,42,79,calc(var(--zpKBVideoNavy) * .74)) 72%,rgba(3,4,7,calc(var(--zpKBVideoNavy) * .88)) 100%),radial-gradient(circle at 72% 42%,rgba(28,71,122,calc(var(--zpKBVideoNavy) * .34)),transparent 58%)!important;mix-blend-mode:color!important}#zpKnowledgePro .zpKBHero__video::after{z-index:3!important;background: linear-gradient(90deg,rgba(3,4,7,calc(.52 + (var(--zpKBTextShadow) * .28))) 0%,rgba(5,7,11,calc(.30 + (var(--zpKBTextShadow) * .24))) 36%,rgba(5,7,11,calc(.18 + (var(--zpKBTextShadow) * .16))) 62%,rgba(3,4,7,calc(.52 + (var(--zpKBTextShadow) * .22))) 100%),linear-gradient(180deg,rgba(3,4,7,calc(.36 + (var(--zpKBTextShadow) * .20))) 0%,rgba(5,7,11,.12) 42%,rgba(3,4,7,calc(.56 + (var(--zpKBTextShadow) * .22))) 100%),radial-gradient(ellipse at 28% 45%,rgba(2,4,7,calc(var(--zpKBTextShadow) * .74)),transparent 58%),radial-gradient(ellipse at 36% 67%,rgba(2,4,7,calc(var(--zpKBTextShadow) * .58)),transparent 54%)!important;mix-blend-mode:normal!important}#zpKnowledgePro .zpKBHero::after{z-index:2!important;background: linear-gradient(90deg,rgba(3,4,7,calc(.54 + (var(--zpKBTextShadow) * .20))) 0%,rgba(5,7,11,calc(.30 + (var(--zpKBTextShadow) * .12))) 45%,rgba(5,7,11,.18) 68%,rgba(3,4,7,calc(.44 + (var(--zpKBTextShadow) * .16))) 100%),radial-gradient(ellipse at 24% 46%,rgba(2,4,7,calc(var(--zpKBTextShadow) * .50)),transparent 62%),radial-gradient(circle at 72% 44%,rgba(16,42,79,.22),transparent 56%)!important}#zpKnowledgePro .zpKBHero__copy,#zpKnowledgePro .zpKBHero__text,#zpKnowledgePro .zpKBHero__content,#zpKnowledgePro .zpKBHero h1,#zpKnowledgePro .zpKBHero__lead,#zpKnowledgePro .zpKBHero__actions,#zpKnowledgePro .zpKBCrumbs{position:relative!important;z-index:6!important}#zpKnowledgePro .zpKBHero__grid > div:first-child{position:relative!important;z-index:6!important}#zpKnowledgePro .zpKBHero__grid > div:first-child::before{content:""!important;position:absolute!important;z-index:-1!important;left:-9%!important;top:-16%!important;width:min(960px,122%)!important;height:138%!important;border-radius:46px!important;pointer-events:none!important;background: radial-gradient(ellipse at 24% 42%,rgba(2,4,7,calc(var(--zpKBTextShadow) * .76)),rgba(5,7,11,calc(var(--zpKBTextShadow) * .46)) 42%,rgba(5,7,11,calc(var(--zpKBTextShadow) * .18)) 70%,transparent 100%)!important;filter: none;opacity:1!important}#zpKnowledgePro .zpKBHero h1,#zpKnowledgePro .zpKBHero__lead,#zpKnowledgePro .zpKBHero__actions,#zpKnowledgePro .zpKBCrumbs{text-shadow:0 16px 42px rgba(0,0,0,calc(var(--zpKBTextShadow) * .52))!important}@media(max-width:760px){#zpKnowledgePro .zpKBHero__video video,#zpKnowledgePro .zpKBHero__videoEl{opacity:calc(var(--zpKBVideoOpacity) + .08)!important;filter: grayscale(.08) sepia(.12) hue-rotate(178deg) saturate(.40) brightness(calc(var(--zpKBVideoBrightness) + .08)) contrast(1.14)!important}#zpKnowledgePro .zpKBHero__video::before{background: linear-gradient(135deg,rgba(2,4,7,calc(var(--zpKBVideoNavy) * .76)) 0%,rgba(7,20,38,calc(var(--zpKBVideoNavy) * .88)) 45%,rgba(16,42,79,calc(var(--zpKBVideoNavy) * .78)) 74%,rgba(3,4,7,calc(var(--zpKBVideoNavy) * .90)) 100%),radial-gradient(circle at 58% 44%,rgba(28,71,122,calc(var(--zpKBVideoNavy) * .28)),transparent 62%)!important}#zpKnowledgePro .zpKBHero__grid > div:first-child::before{left:-12%!important;top:-12%!important;width:128%!important;height:122%!important;border-radius:34px!important;filter: none}}#zpKnowledgePro .zpKBIndex__head h2{max-width:720px!important;font-size:clamp(44px,4.65vw,76px)!important;line-height:.96!important;letter-spacing:-.058em!important;text-wrap:balance!important}#zpKnowledgePro .zpKBIndex__head{align-items:end!important;margin-bottom:24px!important}#zpKnowledgePro .zpKBCats{position:relative!important;display:flex!important;flex-wrap:nowrap!important;align-items:center!important;gap:8px!important;width:100%!important;max-width:100%!important;overflow-x:auto!important;overflow-y:hidden!important;-webkit-overflow-scrolling:touch!important;scrollbar-width:none!important;padding:0 58px 10px 0!important;margin:0 0 16px!important;scroll-snap-type:x proximity!important}#zpKnowledgePro .zpKBCats::-webkit-scrollbar{display:none!important}#zpKnowledgePro .zpKBCat{flex:0 0 auto!important;white-space:nowrap!important;scroll-snap-align:start!important}#zpKnowledgePro .zpKBCats::after{content:"→";position:sticky;right:0;z-index:5;flex:0 0 42px;width:42px;height:38px;margin-left:-42px;display:grid;place-items:center;border-radius:999px;color:#071426;font-size:20px;line-height:1;font-weight:800;background:linear-gradient(90deg,rgba(255,255,255,0),#fff 34%,#fff 100%);pointer-events:none;animation:zpKBCatArrowMove 1.25s ease-in-out infinite}@keyframes zpKBCatArrowMove{0%,100%{transform:translateX(0);opacity:.46}50%{transform:translateX(7px);opacity:.9}}#zpKnowledgePro .zpKBPathsMini{display:grid!important;grid-template-columns:1fr!important;gap:10px!important}#zpKnowledgePro .zpKBPath{width:100%!important}@media(max-width:1080px){#zpKnowledgePro .zpKBIndex__head h2{font-size:clamp(36px,8.4vw,58px)!important}#zpKnowledgePro .zpKBCats{width:100%!important;margin-right:0!important;padding-right:58px!important}}@media(max-width:760px){#zpKnowledgePro .zpKBIndex__head h2{font-size:clamp(33px,10vw,46px)!important;line-height:.98!important}#zpKnowledgePro .zpKBCats{width:calc(100vw - 22px)!important;margin-right:calc(50% - 50vw)!important;padding:0 58px 9px 0!important}}#zpKnowledgePro .zpKBCatsHint{display:flex!important;align-items:center!important;justify-content:flex-end!important;gap:7px!important;margin:-2px 6px 9px auto!important;color:#6e7a8c!important;font-size:12px!important;line-height:1!important;font-weight:650!important;letter-spacing:.08em!important;text-transform:uppercase!important;opacity:.72!important;pointer-events:none!important;user-select:none!important}#zpKnowledgePro .zpKBCatsHint svg{width:15px!important;height:15px!important;stroke-width:2!important;animation:zpKBCatsHintArrow 1.45s ease-in-out infinite!important}@keyframes zpKBCatsHintArrow{0%,100%{transform:translateX(0);opacity:.45}50%{transform:translateX(5px);opacity:1}}#zpKnowledgePro .zpKBCats::after{content:none!important;display:none!important}#zpKnowledgePro .zpKBPosts .zpKBPost{opacity:0;transform:translate3d(0,28px,0);transition:opacity .58s cubic-bezier(.2,.8,.2,1),transform .58s cubic-bezier(.2,.8,.2,1);transition-delay:min(calc(var(--i,0) * 28ms),260ms);will-change:opacity,transform}#zpKnowledgePro .zpKBPosts .zpKBPost.is-visible,#zpKnowledgePro .zpKBPosts .zpKBPost:nth-child(-n+6){opacity:1;transform:translate3d(0,0,0)}@media (prefers-reduced-motion: reduce){#zpKnowledgePro .zpKBPosts .zpKBPost{opacity:1!important;transform:none!important;transition:none!important}#zpKnowledgePro .zpKBCatsHint svg{animation:none!important}}@media(max-width:760px){#zpKnowledgePro .zpKBIndex__layout{display:block!important}#zpKnowledgePro .zpKBIndex__side{display:none!important}#zpKnowledgePro .zpKBIndex__head{margin-bottom:18px!important;display:block!important}#zpKnowledgePro .zpKBIndex__head .zpKBIndex__lead{display:none!important}#zpKnowledgePro .zpKBIndex__head h2{max-width:100%!important;margin-bottom:0!important}#zpKnowledgePro .zpKBToolbar{margin-bottom:10px!important}#zpKnowledgePro .zpKBCatsHint{justify-content:flex-start!important;margin:2px 0 8px!important;font-size:10.5px!important;letter-spacing:.12em!important;opacity:.62!important}#zpKnowledgePro .zpKBCats{width:100%!important;max-width:100%!important;margin:0 0 18px!important;padding:0 12px 10px 0!important;gap:8px!important}#zpKnowledgePro .zpKBCat{min-height:38px!important;padding:10px 14px!important;font-size:12px!important}}html body #zpKnowledgePro .zpKBFeatured,html body #zpKnowledgePro .zpKBIndex,html body #zpKnowledgePro .zpKBFooterCTA,html body #zpKnowledgePro .zpKBFeatured .zpKBInner,html body #zpKnowledgePro .zpKBIndex .zpKBInner{background:#fff!important;background-color:#fff!important}#zpKnowledgePro .zpKBHero{margin-top:0!important;border-top:0!important;background:#05070b!important}#zpKnowledgePro .zpKBHero__inner{background:transparent!important}#zpKnowledgePro .zpKBCrumbs--underCta{display:flex!important;flex-wrap:wrap!important;align-items:center!important;gap:10px!important;width:min(560px,100%)!important;margin:26px 0 0!important;padding-top:18px!important;border-top:1px solid rgba(255,255,255,.16)!important;color:rgba(255,255,255,.66)!important;font-size:14px!important;line-height:1.2!important;font-weight:700!important;letter-spacing:-.012em!important;text-transform:none!important}#zpKnowledgePro .zpKBCrumbs--underCta a{color:rgba(255,255,255,.82)!important;opacity:1!important;transition:color .22s ease,opacity .22s ease!important}#zpKnowledgePro .zpKBCrumbs--underCta a:hover{color:#fff!important}#zpKnowledgePro .zpKBCrumbs--underCta svg,#zpKnowledgePro .zpKBCrumbs--underCta i{width:14px!important;height:14px!important;color:rgba(255,255,255,.34)!important;opacity:1!important;stroke-width:2.1!important;flex:0 0 auto!important}#zpKnowledgePro .zpKBCrumbs--underCta span{color:rgba(255,255,255,.74)!important}@media(max-width:760px){#zpKnowledgePro .zpKBCrumbs--underCta{width:100%!important;margin-top:22px!important;padding-top:16px!important;font-size:13px!important}}#zpKnowledgePro .zpKBHero,#zpKnowledgePro .zpKBHero__video{background:#04080f!important;background-color:#04080f!important}#zpKnowledgePro:not([data-video-ready="1"]) .zpKBHero__video video,#zpKnowledgePro:not([data-video-ready="1"]) .zpKBHero__videoEl{opacity:0!important;visibility:hidden!important}#zpKnowledgePro[data-video-ready="1"] .zpKBHero__video video,#zpKnowledgePro[data-video-ready="1"] .zpKBHero__videoEl{visibility:visible!important;transition:opacity .55s cubic-bezier(.22,1,.36,1)!important}html body.page-id #zpKnowledgePro,html body #zpKnowledgePro{background:#fff!important;background-color:#fff!important}html body #zpKnowledgePro::before,html body #zpKnowledgePro::after{background:transparent!important}html body #zpKnowledgePro .zpKBHero,html body #zpKnowledgePro .zpKBHero::before,html body #zpKnowledgePro .zpKBHero::after,html body #zpKnowledgePro .zpKBHero__video{background-color:#04080f!important}html body #zpKnowledgePro .zpKBFeatured,html body #zpKnowledgePro .zpKBIndex,html body #zpKnowledgePro .zpKBFooterCTA,html body #zpKnowledgePro .zpKBIndex__main,html body #zpKnowledgePro .zpKBPostsWrap,html body #zpKnowledgePro .zpKBIndex__layout,html body #zpKnowledgePro .zpKBIndex__side,html body #zpKnowledgePro .zpKBFeatured .zpKBInner,html body #zpKnowledgePro .zpKBIndex .zpKBInner,html body #zpKnowledgePro .zpKBPosts,html body #zpKnowledgePro .zpKBStatus,html body #zpKnowledgePro .zpKBToolbar,html body #zpKnowledgePro .zpKBCats,html body #zpKnowledgePro .zpKBCatsHint{background:#fff!important;background-color:#fff!important}html body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBFeatured,html body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBIndex,html body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBFooterCTA,html body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBIndex__main,html body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBPostsWrap,html body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBIndex__layout,html body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBIndex__side,html body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBFeatured .zpKBInner,html body.zp-wiedza-bg-boot #zpKnowledgePro .zpKBIndex .zpKBInner{background:#fff!important;background-color:#fff!important}html body #zpKnowledgePro{background:#fff!important;background-color:#fff!important}html body #zpKnowledgePro .zpKBHero{contain:paint;transform:translateZ(0)}html body #zpKnowledgePro .zpKBHero__inner,html body #zpKnowledgePro .zpKBHero__grid,html body #zpKnowledgePro .zpKBHero__visual{background:transparent!important;background-color:transparent!important}html body #zpKnowledgePro .zpKBHero__video{background: radial-gradient(circle at 18% 12%,rgba(28,71,122,.22),transparent 36%),linear-gradient(135deg,#020407 0%,#06101e 48%,#04080f 100%)!important;overflow:hidden!important}html body #zpKnowledgePro .zpKBHero__video::before{z-index:1!important;background: linear-gradient(90deg,rgba(2,4,7,.64),rgba(7,20,38,.28) 46%,rgba(2,4,7,.74)),linear-gradient(180deg,rgba(2,4,7,.28),rgba(2,4,7,.56))!important}html body #zpKnowledgePro .zpKBHero__video video,html body #zpKnowledgePro .zpKBHero__videoEl{transition:opacity .62s cubic-bezier(.22,1,.36,1),visibility 0s linear .62s!important;transform:scale(1.035)!important}html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__video video,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__videoEl{opacity:var(--zpKBVideoOpacity,var(--zp-wiedza-video-opacity,.58))!important;transition:opacity .62s cubic-bezier(.22,1,.36,1),visibility 0s!important}html body #zpKnowledgePro .zpKBFeatured,html body #zpKnowledgePro .zpKBIndex,html body #zpKnowledgePro .zpKBFooterCTA,html body #zpKnowledgePro .zpKBIndex .zpKBInner,html body #zpKnowledgePro .zpKBFeatured .zpKBInner,html body #zpKnowledgePro .zpKBIndex__layout,html body #zpKnowledgePro .zpKBIndex__main,html body #zpKnowledgePro .zpKBIndex__side,html body #zpKnowledgePro .zpKBPostsWrap{background:#fff!important;background-color:#fff!important}html body #zpKnowledgePro{background:#04080f!important;background-color:#04080f!important;color-scheme:normal!important}html body #zpKnowledgePro .zpKBHero{margin-top:0!important;border-top:1px solid transparent!important;background-color:#04080f!important;--zpKBVideoOpacity:.84;--zpKBVideoBrightness:.90;--zpKBVideoNavy:.62;--zpKBTextShadow:.62;contain:layout paint}html body #zpKnowledgePro .zpKBHero__video{background: radial-gradient(circle at 20% 12%,rgba(28,71,122,.20),transparent 34%),linear-gradient(135deg,#020407 0%,#06101e 52%,#04080f 100%)!important;background-color:#04080f!important}html body #zpKnowledgePro .zpKBHero__video::before{background: linear-gradient(90deg,rgba(2,4,7,.52),rgba(7,20,38,.22) 48%,rgba(2,4,7,.58)),linear-gradient(180deg,rgba(2,4,7,.20),rgba(2,4,7,.46))!important;mix-blend-mode:normal!important}html body #zpKnowledgePro .zpKBHero__video::after{background: radial-gradient(circle at 70% 42%,rgba(5,7,11,0),rgba(5,7,11,.34) 78%),linear-gradient(180deg,rgba(5,7,11,.12),rgba(5,7,11,.02) 42%,rgba(5,7,11,.52))!important}html body #zpKnowledgePro .zpKBHero::after{background: linear-gradient(90deg,rgba(3,4,7,.66) 0%,rgba(5,7,11,.34) 42%,rgba(5,7,11,.18) 66%,rgba(3,4,7,.52) 100%),linear-gradient(180deg,rgba(3,4,7,.40) 0%,rgba(5,7,11,.08) 44%,rgba(3,4,7,.58) 100%),radial-gradient(circle at 72% 44%,rgba(16,42,79,.16),transparent 58%)!important}html body #zpKnowledgePro .zpKBHero__video video,html body #zpKnowledgePro .zpKBHero__videoEl{opacity:0!important;visibility:hidden!important;filter:grayscale(.04) sepia(.06) hue-rotate(178deg) saturate(.66) brightness(.90) contrast(1.10)!important;mix-blend-mode:normal!important;transform:scale(1.045)!important;transition:opacity .55s cubic-bezier(.22,1,.36,1),visibility 0s linear .55s!important;will-change:opacity!important}html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__video video,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__videoEl{opacity:.84!important;visibility:visible!important;transition:opacity .55s cubic-bezier(.22,1,.36,1),visibility 0s!important}html body #zpKnowledgePro .zpKBFeatured,html body #zpKnowledgePro .zpKBIndex,html body #zpKnowledgePro .zpKBFooterCTA,html body #zpKnowledgePro .zpKBFeatured .zpKBInner,html body #zpKnowledgePro .zpKBIndex .zpKBInner,html body #zpKnowledgePro .zpKBIndex__layout,html body #zpKnowledgePro .zpKBIndex__main,html body #zpKnowledgePro .zpKBIndex__side,html body #zpKnowledgePro .zpKBPostsWrap,html body #zpKnowledgePro .zpKBPosts,html body #zpKnowledgePro .zpKBToolbar,html body #zpKnowledgePro .zpKBCats,html body #zpKnowledgePro .zpKBCatsHint,html body #zpKnowledgePro .zpKBStatus{background:#fff!important;background-color:#fff!important}@media(max-width:760px){html body #zpKnowledgePro .zpKBHero{--zpKBVideoOpacity:.88;--zpKBVideoBrightness:.94;--zpKBVideoNavy:.58}html body #zpKnowledgePro .zpKBHero__video video,html body #zpKnowledgePro .zpKBHero__videoEl{object-position:center 22%!important;filter:grayscale(.03) sepia(.04) hue-rotate(178deg) saturate(.74) brightness(.96) contrast(1.08)!important;transform:translateY(-6%) scale(1.13)!important}html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__video video,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__videoEl{opacity:.88!important}html body #zpKnowledgePro .zpKBHero::after{background: linear-gradient(90deg,rgba(3,4,7,.54) 0%,rgba(5,7,11,.24) 56%,rgba(3,4,7,.52) 100%),linear-gradient(180deg,rgba(3,4,7,.28) 0%,rgba(5,7,11,.04) 34%,rgba(5,7,11,.56) 100%)!important}}html body #zpKnowledgePro .zpKBHero{--zpKBVideoOpacity:.46!important;--zpKBVideoBrightness:.58!important;--zpKBVideoNavy:.96!important;--zpKBTextShadow:.86!important;background:#030711!important}html body #zpKnowledgePro .zpKBHero__video{background:linear-gradient(135deg,#020407 0%,#06101e 52%,#030711 100%)!important}html body #zpKnowledgePro .zpKBHero__video video,html body #zpKnowledgePro .zpKBHero__videoEl{opacity:.46!important;filter:grayscale(.18) sepia(.16) hue-rotate(176deg) saturate(.24) brightness(.58) contrast(1.18)!important;mix-blend-mode:luminosity!important}html body #zpKnowledgePro[data-ready="0"] .zpKBHero__video video,html body #zpKnowledgePro[data-ready="0"] .zpKBHero__videoEl,html body #zpKnowledgePro[data-ready="1"] .zpKBHero__video video,html body #zpKnowledgePro[data-ready="1"] .zpKBHero__videoEl,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__video video,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__videoEl{opacity:.46!important;visibility:visible!important}html body #zpKnowledgePro .zpKBHero__video::before{background: linear-gradient(135deg,rgba(2,4,7,.92) 0%,rgba(7,20,38,.94) 42%,rgba(16,42,79,.82) 72%,rgba(2,4,7,.96) 100%),radial-gradient(circle at 74% 42%,rgba(28,71,122,.24),transparent 58%)!important;mix-blend-mode:color!important}html body #zpKnowledgePro .zpKBHero__video::after{background: linear-gradient(90deg,rgba(2,4,7,.88) 0%,rgba(5,7,11,.62) 38%,rgba(5,7,11,.40) 66%,rgba(2,4,7,.84) 100%),linear-gradient(180deg,rgba(2,4,7,.72) 0%,rgba(5,7,11,.28) 42%,rgba(2,4,7,.92) 100%),radial-gradient(ellipse at 28% 48%,rgba(2,4,7,.76),transparent 62%)!important}html body #zpKnowledgePro .zpKBHero::after{background: linear-gradient(90deg,rgba(2,4,7,.86) 0%,rgba(5,7,11,.54) 45%,rgba(5,7,11,.34) 68%,rgba(2,4,7,.82) 100%),radial-gradient(ellipse at 26% 48%,rgba(2,4,7,.72),transparent 62%),radial-gradient(circle at 72% 44%,rgba(16,42,79,.18),transparent 58%)!important}@media(max-width:760px){html body #zpKnowledgePro .zpKBHero{--zpKBVideoOpacity:.50!important;--zpKBVideoBrightness:.60!important;--zpKBVideoNavy:.96!important}html body #zpKnowledgePro .zpKBHero__video video,html body #zpKnowledgePro .zpKBHero__videoEl,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__video video,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__videoEl{opacity:.50!important;filter:grayscale(.16) sepia(.16) hue-rotate(176deg) saturate(.28) brightness(.60) contrast(1.16)!important}html body #zpKnowledgePro .zpKBHero__video::after{background: linear-gradient(90deg,rgba(2,4,7,.78) 0%,rgba(5,7,11,.52) 54%,rgba(2,4,7,.82) 100%),linear-gradient(180deg,rgba(2,4,7,.58) 0%,rgba(5,7,11,.22) 36%,rgba(2,4,7,.90) 100%)!important}}html body #zpKnowledgePro .zpKBFeatured,html body #zpKnowledgePro .zpKBIndex,html body #zpKnowledgePro .zpKBFooterCTA{content-visibility:auto!important;contain-intrinsic-size:1px 980px}html body #zpKnowledgePro .zpKBHero{content-visibility:visible!important;contain:layout paint!important}html body #zpKnowledgePro .zpKBHero__lead{max-width:650px!important;margin:clamp(22px,2.2vw,32px) 0 0!important;font-size:clamp(15px,1.18vw,18px)!important;line-height:1.66!important;letter-spacing:-.012em!important;color:rgba(255,255,255,.72)!important}html body #zpKnowledgePro .zpKBHero{--zpKBVideoOpacity:.78!important;--zpKBVideoBrightness:.94!important;--zpKBVideoNavy:.58!important;--zpKBTextShadow:.64!important;background:#04080f!important}html body #zpKnowledgePro .zpKBHero__video{background:#04080f!important}html body #zpKnowledgePro .zpKBHero__video video,html body #zpKnowledgePro .zpKBHero__videoEl,html body #zpKnowledgePro[data-ready="0"] .zpKBHero__video video,html body #zpKnowledgePro[data-ready="0"] .zpKBHero__videoEl,html body #zpKnowledgePro[data-ready="1"] .zpKBHero__video video,html body #zpKnowledgePro[data-ready="1"] .zpKBHero__videoEl,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__video video,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__videoEl{opacity:.78!important;visibility:visible!important;mix-blend-mode:normal!important;filter:grayscale(.04) sepia(.04) hue-rotate(178deg) saturate(.82) brightness(.94) contrast(1.08)!important;transform:scale(1.045)!important}html body #zpKnowledgePro .zpKBHero__video::before{content:""!important;position:absolute!important;inset:0!important;z-index:2!important;pointer-events:none!important;background: linear-gradient(135deg,rgba(2,4,7,.42) 0%,rgba(7,20,38,.36) 44%,rgba(16,42,79,.22) 72%,rgba(2,4,7,.48) 100%)!important;mix-blend-mode:multiply!important}html body #zpKnowledgePro .zpKBHero__video::after{content:""!important;position:absolute!important;inset:0!important;z-index:3!important;pointer-events:none!important;background: linear-gradient(90deg,rgba(2,4,7,.70) 0%,rgba(5,7,11,.42) 42%,rgba(5,7,11,.22) 68%,rgba(2,4,7,.60) 100%),linear-gradient(180deg,rgba(2,4,7,.48) 0%,rgba(5,7,11,.12) 44%,rgba(2,4,7,.72) 100%)!important;mix-blend-mode:normal!important}html body #zpKnowledgePro .zpKBHero::after{background: linear-gradient(90deg,rgba(2,4,7,.72) 0%,rgba(5,7,11,.40) 45%,rgba(5,7,11,.18) 68%,rgba(2,4,7,.58) 100%),radial-gradient(ellipse at 26% 48%,rgba(2,4,7,.48),transparent 62%),radial-gradient(circle at 72% 44%,rgba(16,42,79,.14),transparent 58%)!important}@media(max-width:760px){html body #zpKnowledgePro .zpKBHero__video video,html body #zpKnowledgePro .zpKBHero__videoEl,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__video video,html body #zpKnowledgePro[data-video-ready="1"] .zpKBHero__videoEl{opacity:.84!important;filter:grayscale(.03) sepia(.03) hue-rotate(178deg) saturate(.88) brightness(.98) contrast(1.06)!important;transform:translateY(-5%) scale(1.12)!important}html body #zpKnowledgePro .zpKBHero__video::after{background: linear-gradient(90deg,rgba(2,4,7,.66) 0%,rgba(5,7,11,.34) 54%,rgba(2,4,7,.66) 100%),linear-gradient(180deg,rgba(2,4,7,.36) 0%,rgba(5,7,11,.08) 36%,rgba(2,4,7,.70) 100%)!important}}html body #zpKnowledgePro .zpKBFeatured{padding:clamp(34px,4.6vw,76px) 0 clamp(42px,5vw,88px)!important;background: radial-gradient(circle at 8% 0%,rgba(16,42,79,.045),transparent 34%),linear-gradient(180deg,#fff 0%,#f7f8fb 100%)!important}html body #zpKnowledgePro .zpKBFeatured__grid{display:grid!important;grid-template-columns:minmax(0,1.22fr) minmax(360px,.58fr)!important;gap:clamp(16px,2vw,28px)!important;align-items:stretch!important}html body #zpKnowledgePro .zpKBMonth{position:relative!important;display:grid!important;grid-template-rows:minmax(320px,42vw) auto!important;border-radius:34px!important;border:1px solid rgba(7,17,31,.095)!important;background:#fff!important;box-shadow:0 28px 90px rgba(7,17,31,.075)!important;isolation:isolate!important}html body #zpKnowledgePro .zpKBMonth::before{content:"wyróżniony wpis"!important;position:absolute!important;left:22px!important;top:22px!important;z-index:4!important;display:inline-flex!important;align-items:center!important;min-height:34px!important;padding:0 13px!important;border-radius:999px!important;background:rgba(255,255,255,.88)!important;backdrop-filter:blur(12px)!important;-webkit-backdrop-filter:blur(12px)!important;color:#071426!important;font-size:10px!important;line-height:1!important;font-weight:900!important;letter-spacing:.12em!important;text-transform:uppercase!important;box-shadow:0 12px 34px rgba(7,17,31,.12)!important}html body #zpKnowledgePro .zpKBMonth__media{position:relative!important;aspect-ratio:16/8.3!important;overflow:hidden!important;background:#eef1f5!important}html body #zpKnowledgePro .zpKBMonth__media img{position:absolute!important;object-position:center center!important}html body #zpKnowledgePro .zpKBMonth__media::after{content:""!important;position:absolute!important;inset:0!important;background: linear-gradient(180deg,rgba(2,4,7,.02) 0%,rgba(2,4,7,.10) 54%,rgba(2,4,7,.52) 100%),radial-gradient(circle at 18% 18%,rgba(255,255,255,.18),transparent 40%)!important;pointer-events:none!important}html body #zpKnowledgePro .zpKBMonth__body{padding:clamp(24px,2.6vw,42px)!important;display:grid!important}html body #zpKnowledgePro .zpKBMonth .zpKBLabel{margin-bottom:13px!important;color:#6f7b8e!important}html body #zpKnowledgePro .zpKBMonth h2{max-width:820px!important;margin:0!important;color:#07111f!important;font-size:clamp(32px,3.85vw,62px)!important;line-height:.98!important;letter-spacing:-.056em!important;font-weight:560!important;text-wrap:balance!important}html body #zpKnowledgePro .zpKBMonth p{max-width:780px!important;margin:18px 0 26px!important;color:#5d6879!important;font-size:15px!important;line-height:1.7!important}html body #zpKnowledgePro .zpKBMonth__meta{margin:0 0 16px!important}html body #zpKnowledgePro .zpKBMonth__meta span{background:#f3f5f8!important;border-color:rgba(7,17,31,.075)!important}html body #zpKnowledgePro .zpKBTopPicks{display:grid!important;gap:14px!important;min-width:0!important}html body #zpKnowledgePro .zpKBTopPicks__head{position:relative!important;overflow:hidden!important;min-height:176px!important;display:flex!important;flex-direction:column!important;justify-content:flex-end!important;border-radius:30px!important;border:1px solid rgba(255,255,255,.10)!important;padding:24px!important;background:linear-gradient(135deg,#020407 0%,#071426 44%,#102a4f 76%,#1c477a 100%)!important;color:#fff!important;box-shadow:0 24px 78px rgba(7,17,31,.14)!important;isolation:isolate!important}html body #zpKnowledgePro .zpKBTopPicks__head::before{content:"wiedza"!important;position:absolute!important;right:-18px!important;bottom:-18px!important;z-index:-1!important;color:#fff!important;opacity:.075!important;font-size:94px!important;line-height:.78!important;letter-spacing:-.105em!important;font-weight:760!important;white-space:nowrap!important;pointer-events:none!important}html body #zpKnowledgePro .zpKBTopPicks__head .zpKBLabel{color:rgba(255,255,255,.66)!important;margin-bottom:13px!important}html body #zpKnowledgePro .zpKBTopPicks__head h2{max-width:390px!important;margin:0!important;color:#fff!important;font-size:clamp(24px,1.9vw,34px)!important;line-height:1.02!important;letter-spacing:-.045em!important;font-weight:560!important;text-wrap:balance!important}html body #zpKnowledgePro .zpKBTopPicks__head p{max-width:390px!important;margin:12px 0 0!important;color:rgba(255,255,255,.68)!important;font-size:13px!important;line-height:1.58!important}html body #zpKnowledgePro .zpKBTopPick{position:relative!important;display:grid!important;gap:16px!important;align-items:center!important;min-height:154px!important;padding:14px!important;border:1px solid rgba(7,17,31,.095)!important;box-shadow:0 18px 62px rgba(7,17,31,.055)!important;overflow:hidden!important;transition:transform .25s var(--ease),box-shadow .25s var(--ease),border-color .25s var(--ease)!important}html body #zpKnowledgePro .zpKBTopPick:hover{transform:translateY(-3px)!important;border-color:rgba(16,42,79,.20)!important;box-shadow:0 24px 78px rgba(7,17,31,.085)!important}html body #zpKnowledgePro .zpKBTopPick__media{position:relative!important;display:block!important;width:132px!important;height:126px!important;overflow:hidden!important;background:#eef1f5!important}html body #zpKnowledgePro .zpKBTopPick__media img{width:100%!important;height:100%!important;object-fit:cover!important;object-position:center!important;display:block!important;transition:transform .45s var(--ease)!important}html body #zpKnowledgePro .zpKBTopPick:hover .zpKBTopPick__media img{transform:scale(1.045)!important}html body #zpKnowledgePro .zpKBTopPick small{display:block!important;margin-bottom:9px!important;color:#738096!important;font-size:9px!important;font-weight:900!important;letter-spacing:.12em!important;text-transform:uppercase!important}html body #zpKnowledgePro .zpKBTopPick h3{margin:0 0 12px!important;color:#07111f!important;font-size:18px!important;letter-spacing:-.04em!important;font-weight:650!important;text-wrap:balance!important}html body #zpKnowledgePro .zpKBTopPick a{display:inline-flex!important;align-items:center!important;gap:8px!important;color:#071426!important;font-size:10px!important;font-weight:900!important;letter-spacing:.12em!important;text-transform:uppercase!important}html body #zpKnowledgePro .zpKBTopPick a svg{width:14px!important;height:14px!important;transition:transform .22s var(--ease)!important}html body #zpKnowledgePro .zpKBTopPick a:hover svg{transform:translate(3px,-3px)!important}@media(max-width:1080px){html body #zpKnowledgePro .zpKBMonth{grid-template-rows:auto auto!important}html body #zpKnowledgePro .zpKBMonth__media{aspect-ratio:16/9!important;min-height:0!important}html body #zpKnowledgePro .zpKBTopPicks{grid-template-columns:repeat(2,minmax(0,1fr))!important}html body #zpKnowledgePro .zpKBTopPick__media{height:112px!important}}@media(max-width:760px){html body #zpKnowledgePro .zpKBFeatured{padding-top:30px!important;padding-bottom:44px!important}html body #zpKnowledgePro .zpKBFeatured .zpKBInner{padding-left:16px!important;padding-right:16px!important}html body #zpKnowledgePro .zpKBMonth{border-radius:28px!important;grid-template-rows:auto auto!important}html body #zpKnowledgePro .zpKBMonth::before{left:16px!important;top:16px!important;min-height:31px!important;font-size:9px!important}html body #zpKnowledgePro .zpKBMonth__body{padding:23px 20px 24px!important}html body #zpKnowledgePro .zpKBMonth h2{font-size:clamp(30px,9vw,42px)!important;line-height:1.02!important;letter-spacing:-.048em!important}html body #zpKnowledgePro .zpKBMonth p{font-size:14px!important;line-height:1.62!important;margin-bottom:22px!important}html body #zpKnowledgePro .zpKBTopPicks{gap:12px!important}html body #zpKnowledgePro .zpKBTopPicks__head{border-radius:26px!important;min-height:166px!important;padding:22px!important}html body #zpKnowledgePro .zpKBTopPicks__head h2{font-size:28px!important}html body #zpKnowledgePro .zpKBTopPick{gap:13px!important}html body #zpKnowledgePro .zpKBTopPick__media{height:98px!important}html body #zpKnowledgePro .zpKBTopPick h3{font-size:16px!important;line-height:1.14!important;margin-bottom:10px!important}}html body #zpKnowledgePro{--zpNavyFlagSoft:linear-gradient(135deg,#020407 0%,#05070b 34%,#0b1830 64%,#102a4f 100%)}html body #zpKnowledgePro .zpKBFeatured__grid{grid-template-columns:minmax(0,1.08fr) minmax(390px,.54fr)!important;align-items:start!important}html body #zpKnowledgePro .zpKBMonth{align-self:start!important;min-height:0!important}html body #zpKnowledgePro .zpKBMonth__media{aspect-ratio:16/7.15!important;height:auto!important;min-height:0!important;max-height:460px!important}html body #zpKnowledgePro .zpKBMonth__body{min-height:0!important;padding:clamp(26px,2.35vw,40px)!important}html body #zpKnowledgePro .zpKBTopPicks{align-self:start!important;gap:16px!important}html body #zpKnowledgePro .zpKBTopPicks__head{min-height:190px!important;background:var(--zpNavyFlagSoft)!important;box-shadow:0 26px 82px rgba(5,7,11,.20)!important}html body #zpKnowledgePro .zpKBTopPicks__head::after{content:""!important;position:absolute!important;inset:0!important;z-index:-1!important;pointer-events:none!important;background: radial-gradient(circle at 15% 5%,rgba(28,71,122,.30),transparent 38%),linear-gradient(180deg,rgba(255,255,255,.055),rgba(255,255,255,0) 42%)!important}html body #zpKnowledgePro .zpKBTopPick{grid-template-columns:132px minmax(0,1fr)!important;align-items:stretch!important;min-height:172px!important;padding:13px!important;border-radius:30px!important;background:linear-gradient(180deg,#fff 0%,#fbfcfe 100%)!important}html body #zpKnowledgePro .zpKBTopPick__media{min-height:146px!important;border-radius:22px!important}html body #zpKnowledgePro .zpKBTopPick__body{display:flex!important;flex-direction:column!important;min-width:0!important;padding:4px 2px 4px 0!important}html body #zpKnowledgePro .zpKBTopPick small{margin-bottom:8px!important;color:#7a8494!important}html body #zpKnowledgePro .zpKBTopPick h3{margin:0 0 8px!important;font-size:17px!important;letter-spacing:-.042em!important}html body #zpKnowledgePro .zpKBTopPick p{display:-webkit-box!important;-webkit-box-orient:vertical!important;overflow:hidden!important;margin:0 0 13px!important;color:#657184!important;font-size:12px!important;line-height:1.48!important;letter-spacing:-.006em!important}html body #zpKnowledgePro .zpKBTopPick .zpKBTopPick__btn{width:max-content!important;min-height:38px!important;padding:0 14px!important;border-radius:999px!important;background:var(--zpNavyFlag)!important;color:#fff!important;box-shadow:0 12px 30px rgba(7,20,38,.16)!important}html body #zpKnowledgePro .zpKBTopPick .zpKBTopPick__btn svg{color:#fff!important;stroke:currentColor!important}html body #zpKnowledgePro .zpKBMonth__link{width:max-content!important;min-height:44px!important;padding:0 17px!important;border-radius:999px!important;background:var(--zpNavyFlag)!important;color:#fff!important;box-shadow:0 14px 34px rgba(7,20,38,.17)!important}html body #zpKnowledgePro .zpKBMonth__link svg{color:#fff!important;stroke:currentColor!important}@media(min-width:1440px){html body #zpKnowledgePro .zpKBMonth__media{max-height:430px!important;aspect-ratio:16/6.9!important}html body #zpKnowledgePro .zpKBMonth h2{font-size:clamp(38px,3vw,56px)!important}}@media(max-width:1080px){html body #zpKnowledgePro .zpKBFeatured__grid{grid-template-columns:1fr!important}html body #zpKnowledgePro .zpKBTopPicks__head{min-height:170px!important}html body #zpKnowledgePro .zpKBTopPick{grid-template-columns:124px minmax(0,1fr)!important;min-height:166px!important}html body #zpKnowledgePro .zpKBTopPick__media{width:124px!important;min-height:140px!important}}@media(max-width:760px){html body #zpKnowledgePro .zpKBMonth__media{aspect-ratio:16/10!important;max-height:none!important}html body #zpKnowledgePro .zpKBTopPick{grid-template-columns:104px minmax(0,1fr)!important;min-height:150px!important;padding:12px!important}html body #zpKnowledgePro .zpKBTopPick__media{width:104px!important;height:100%!important;min-height:126px!important;border-radius:20px!important}html body #zpKnowledgePro .zpKBTopPick p{font-size:11.5px!important;line-height:1.42!important;margin-bottom:11px!important}html body #zpKnowledgePro .zpKBTopPick .zpKBTopPick__btn{min-height:36px!important;padding:0 13px!important;font-size:9px!important}}html body #zpKnowledgePro{--zpNavyFlag:linear-gradient(110deg,#05070b 0%,#0b1830 42%,#102a4f 74%,#1c477a 100%);--zpNavyFlagReverse:linear-gradient(110deg,#1c477a 0%,#102a4f 28%,#0b1830 62%,#05070b 100%);--zpNavyBlack:linear-gradient(135deg,#020307 0%,#05070b 38%,#07111f 66%,#0b1830 100%)}html body #zpKnowledgePro .zpKBMonth{grid-template-columns:1fr!important;grid-template-rows:auto auto!important;overflow:hidden!important}html body #zpKnowledgePro .zpKBMonth__media{grid-column:1/-1!important;width:100%!important;margin:0!important;border-radius:0!important;aspect-ratio:16/7.35!important;max-height:410px!important}html body #zpKnowledgePro .zpKBMonth__media img{inset:0!important;width:100%!important;height:100%!important;max-width:none!important;object-fit:cover!important;display:block!important}html body #zpKnowledgePro .zpKBMonth__pills{position:absolute!important;left:18px!important;top:18px!important;z-index:4!important;display:flex!important;flex-wrap:wrap!important;gap:8px!important;pointer-events:none!important}html body #zpKnowledgePro .zpKBMonth__pills em{display:inline-flex!important;align-items:center!important;min-height:30px!important;padding:0 12px!important;border:1px solid rgba(255,255,255,.62)!important;border-radius:999px!important;background:rgba(255,255,255,.90)!important;color:#07111f!important;backdrop-filter:blur(10px)!important;-webkit-backdrop-filter:blur(10px)!important;font-style:normal!important;font-size:10px!important;line-height:1!important;font-weight:760!important;letter-spacing:-.006em!important;text-transform:none!important}html body #zpKnowledgePro .zpKBTopPicks{gap:12px!important}html body #zpKnowledgePro .zpKBTopPicks__head{min-height:174px!important;padding:24px 24px 22px!important;background:var(--zpNavyBlack)!important;border-color:rgba(255,255,255,.10)!important}html body #zpKnowledgePro .zpKBTopPicks__head::after{background: radial-gradient(circle at 92% 12%,rgba(28,71,122,.22),transparent 34%),linear-gradient(180deg,rgba(255,255,255,.045),rgba(255,255,255,0) 46%)!important}html body #zpKnowledgePro .zpKBTopPicks__head h2{font-size:clamp(25px,1.85vw,34px)!important;line-height:.98!important}html body #zpKnowledgePro .zpKBTopPicks__head p{max-width:92%!important;color:rgba(255,255,255,.66)!important}html body #zpKnowledgePro .zpKBTopPick{grid-template-columns:116px minmax(0,1fr)!important;min-height:142px!important;padding:12px!important;border-radius:28px!important;background:#fff!important;border-color:rgba(7,17,31,.10)!important}html body #zpKnowledgePro .zpKBTopPick__media{width:116px!important;min-height:118px!important;border-radius:20px!important}html body #zpKnowledgePro .zpKBTopPick__body{justify-content:center!important;padding:2px 0!important}html body #zpKnowledgePro .zpKBTopPick h3{margin-bottom:7px!important;font-size:16px!important}html body #zpKnowledgePro .zpKBTopPick p{margin-bottom:10px!important;font-size:11.5px!important;line-height:1.42!important}html body #zpKnowledgePro .zpKBMonth__link,html body #zpKnowledgePro .zpKBTopPick__btn{background:var(--zpNavyFlag)!important;color:#fff!important;text-transform:none!important;letter-spacing:-.004em!important;font-weight:760!important;transform:none!important;transition:background .28s ease,color .28s ease,border-color .28s ease!important}html body #zpKnowledgePro .zpKBMonth__link:hover,html body #zpKnowledgePro .zpKBTopPick__btn:hover{color:#fff!important}html body #zpKnowledgePro .zpKBMonth__link:hover svg,html body #zpKnowledgePro .zpKBTopPick__btn:hover svg,html body #zpKnowledgePro .zpKBTopPick a:hover svg{transform:none!important}html body #zpKnowledgePro .zpKBMonth__link span,html body #zpKnowledgePro .zpKBTopPick__btn span{text-transform:none!important}@media(min-width:1440px){html body #zpKnowledgePro .zpKBMonth__media{max-height:400px!important;aspect-ratio:16/7.25!important}html body #zpKnowledgePro .zpKBFeatured__grid{grid-template-columns:minmax(0,1.08fr) minmax(378px,.52fr)!important}}@media(max-width:1080px){html body #zpKnowledgePro .zpKBMonth__media{aspect-ratio:16/8.5!important;max-height:none!important}html body #zpKnowledgePro .zpKBTopPicks__head{min-height:158px!important}html body #zpKnowledgePro .zpKBTopPick{grid-template-columns:118px minmax(0,1fr)!important;min-height:146px!important}html body #zpKnowledgePro .zpKBTopPick__media{width:118px!important;min-height:122px!important}}@media(max-width:760px){html body #zpKnowledgePro .zpKBMonth__pills{left:14px!important;top:14px!important;gap:6px!important}html body #zpKnowledgePro .zpKBMonth__pills em{min-height:28px!important;padding:0 10px!important;font-size:9px!important}html body #zpKnowledgePro .zpKBTopPicks__head{min-height:158px!important}html body #zpKnowledgePro .zpKBTopPick{grid-template-columns:102px minmax(0,1fr)!important;min-height:142px!important;border-radius:24px!important}html body #zpKnowledgePro .zpKBTopPick__media{width:102px!important;min-height:118px!important;border-radius:18px!important}html body #zpKnowledgePro .zpKBTopPick h3{font-size:15px!important}}html body #zpKnowledgePro .zpKBFeatured{padding-bottom:clamp(20px,2.4vw,38px)!important}html body #zpKnowledgePro .zpKBIndex{padding-top:clamp(24px,3vw,46px)!important}html body #zpKnowledgePro .zpKBMonth::before{display:none!important;content:none!important}html body #zpKnowledgePro .zpKBMonth__pills{left:auto!important;top:auto!important;right:18px!important;bottom:18px!important;justify-content:flex-end!important;max-width:calc(100% - 36px)!important}html body #zpKnowledgePro .zpKBMonth__body{width:100%!important;max-width:none!important;justify-content:stretch!important;align-content:start!important}html body #zpKnowledgePro .zpKBMonth h2,html body #zpKnowledgePro .zpKBMonth p{max-width:none!important;width:100%!important}html body #zpKnowledgePro .zpKBTopPicks{gap:10px!important;align-content:start!important}html body #zpKnowledgePro .zpKBTopPicks__head{min-height:158px!important;padding:22px!important;background:linear-gradient(135deg,#020307 0%,#05070b 44%,#07111f 72%,#0b1830 100%)!important;box-shadow:none!important}html body #zpKnowledgePro .zpKBTopPicks__head h2{max-width:420px!important;font-size:clamp(23px,1.72vw,31px)!important}html body #zpKnowledgePro .zpKBTopPicks__head p{max-width:96%!important;font-size:12.5px!important;line-height:1.5!important}html body #zpKnowledgePro .zpKBTopPick{grid-template-columns:104px minmax(0,1fr)!important;min-height:118px!important;padding:10px!important;gap:13px!important;border-radius:24px!important;box-shadow:none!important;transform:none!important}html body #zpKnowledgePro .zpKBTopPick:hover{transform:none!important;box-shadow:none!important}html body #zpKnowledgePro .zpKBTopPick__media{width:104px!important;min-height:98px!important;height:100%!important;border-radius:17px!important}html body #zpKnowledgePro .zpKBTopPick small{margin-bottom:5px!important;font-size:8.5px!important;letter-spacing:.10em!important}html body #zpKnowledgePro .zpKBTopPick h3{margin-bottom:5px!important;font-size:14.5px!important;line-height:1.12!important;letter-spacing:-.035em!important}html body #zpKnowledgePro .zpKBTopPick p{-webkit-line-clamp:2!important;margin-bottom:8px!important;font-size:11px!important;line-height:1.38!important}html body #zpKnowledgePro .zpKBTopPick .zpKBTopPick__btn{min-height:33px!important;padding:0 12px!important;font-size:9.5px!important;box-shadow:none!important}html body #zpKnowledgePro .zpKBMonth,html body #zpKnowledgePro .zpKBTopPicks__head,html body #zpKnowledgePro .zpKBTopPick,html body #zpKnowledgePro .zpKBMonth__link,html body #zpKnowledgePro .zpKBTopPick__btn{box-shadow:none!important}html body #zpKnowledgePro .zpKBMonth__link:hover,html body #zpKnowledgePro .zpKBTopPick__btn:hover{background:var(--zpNavyFlagReverse)!important;transform:none!important;box-shadow:none!important}@media(min-width:1440px){html body #zpKnowledgePro .zpKBFeatured__grid{grid-template-columns:minmax(0,1.08fr) minmax(392px,.52fr)!important;gap:30px!important}html body #zpKnowledgePro .zpKBMonth__body{padding:clamp(30px,2.5vw,44px)!important}html body #zpKnowledgePro .zpKBMonth h2{font-size:clamp(40px,3.15vw,58px)!important}}@media(max-width:1080px){html body #zpKnowledgePro .zpKBFeatured{padding-bottom:34px!important}html body #zpKnowledgePro .zpKBIndex{padding-top:32px!important}html body #zpKnowledgePro .zpKBTopPicks{grid-template-columns:1fr 1fr!important}html body #zpKnowledgePro .zpKBTopPicks__head{grid-column:1/-1!important;min-height:150px!important}html body #zpKnowledgePro .zpKBTopPick{grid-template-columns:108px minmax(0,1fr)!important;min-height:126px!important}html body #zpKnowledgePro .zpKBTopPick__media{width:108px!important;min-height:106px!important}}@media(max-width:760px){html body #zpKnowledgePro .zpKBFeatured{padding-bottom:28px!important}html body #zpKnowledgePro .zpKBIndex{padding-top:28px!important}html body #zpKnowledgePro .zpKBMonth__pills{left:auto!important;top:auto!important;right:14px!important;bottom:14px!important;max-width:calc(100% - 28px)!important}html body #zpKnowledgePro .zpKBTopPicks{grid-template-columns:1fr!important}html body #zpKnowledgePro .zpKBTopPick{grid-template-columns:100px minmax(0,1fr)!important;min-height:132px!important}html body #zpKnowledgePro .zpKBTopPick__media{width:100px!important;min-height:112px!important}}</style>


<style id="zp-suite-wiedza-index-reveal-705">
  #zpKnowledgePro .zpKBPosts[data-zp-reveal-ready="1"] .zpKBPost {
    transition: opacity .52s cubic-bezier(.16,1,.3,1), transform .62s cubic-bezier(.16,1,.3,1), box-shadow .25s var(--ease);
  }
  #zpKnowledgePro .zpKBPosts[data-zp-reveal-ready="1"] .zpKBPost:not(.is-visible) {
    opacity: 0;
    transform: translate3d(0, 22px, 0);
  }
  #zpKnowledgePro .zpKBPosts[data-zp-reveal-ready="1"] .zpKBPost.is-visible {
    opacity: 1;
    transform: translate3d(0, 0, 0);
  }
  @media (prefers-reduced-motion: reduce) {
    #zpKnowledgePro .zpKBPosts .zpKBPost {
      opacity: 1 !important;
      transform: none !important;
      transition: none !important;
    }
  }
</style>


<style id="zp-kb-seo-hub">
#zpKnowledgePro .zpKBSeoHub{position:relative;background:linear-gradient(180deg,#f7faff 0%,#fff 72%);padding:clamp(62px,7vw,108px) 0 48px;border-bottom:1px solid rgba(7,17,31,.08);overflow:hidden}
#zpKnowledgePro .zpKBSeoHub:before{content:"";position:absolute;width:680px;height:680px;right:-300px;top:-330px;border-radius:50%;background:radial-gradient(circle,rgba(49,93,145,.11),transparent 68%);pointer-events:none}
#zpKnowledgePro .zpKBSeoHub__crumbs{position:relative;z-index:1;display:flex;align-items:center;gap:9px;flex-wrap:wrap;margin:0 0 34px;color:#7b8494;font-size:12px}
#zpKnowledgePro .zpKBSeoHub__crumbs a{color:#445166;text-decoration:none;font-weight:650}
#zpKnowledgePro .zpKBSeoHub__crumbs a:hover{color:#1c477a}
#zpKnowledgePro .zpKBTopicIntro{position:relative;z-index:1;display:grid;grid-template-columns:minmax(0,1fr) minmax(300px,520px);gap:36px;align-items:end;margin-bottom:28px}
#zpKnowledgePro .zpKBTopicIntro h2{margin:0;font-size:clamp(36px,4.2vw,64px);line-height:1.03;letter-spacing:-.052em;color:#07111f;font-weight:650;text-wrap:balance}
#zpKnowledgePro .zpKBTopicIntro p{margin:0;color:#647085;font-size:15px;line-height:1.8}
#zpKnowledgePro .zpKBSeoHub__eyebrow{display:block;margin-bottom:13px;font-size:11px;line-height:1.4;letter-spacing:.1em;text-transform:uppercase;color:#496b93;font-weight:800}
#zpKnowledgePro .zpKBTopicGrid{position:relative;z-index:1;display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin:0}
#zpKnowledgePro .zpKBTopicCard{position:relative;isolation:isolate;display:flex;min-height:208px;flex-direction:column;justify-content:space-between;gap:20px;padding:22px;border:1px solid rgba(7,17,31,.105);border-radius:24px;background:rgba(255,255,255,.94);color:#07111f;text-decoration:none;overflow:hidden;transition:border-color .22s ease,transform .22s ease,background .22s ease}
#zpKnowledgePro .zpKBTopicCard:before{content:"";position:absolute;left:0;right:0;top:0;height:3px;background:linear-gradient(90deg,#315d91,#8ec8f7);opacity:.78}
#zpKnowledgePro .zpKBTopicCard:after{content:"";position:absolute;width:160px;height:160px;right:-95px;bottom:-105px;border-radius:50%;background:rgba(49,93,145,.08);z-index:-1;transition:transform .22s ease}
#zpKnowledgePro .zpKBTopicCard:hover{border-color:rgba(28,71,122,.38);background:#fff;transform:translateY(-3px)}
#zpKnowledgePro .zpKBTopicCard:hover:after{transform:scale(1.25)}
#zpKnowledgePro .zpKBTopicCard__top{display:flex;align-items:center;justify-content:space-between;gap:12px}
#zpKnowledgePro .zpKBTopicIcon{display:grid;place-items:center;width:42px;height:42px;border-radius:13px;background:#edf3f9;color:#214e7d}
#zpKnowledgePro .zpKBTopicIcon svg{width:19px;height:19px}
#zpKnowledgePro .zpKBTopicCount{display:flex;align-items:baseline;gap:5px;padding:7px 10px;border-radius:999px;background:#f2f5f8;color:#607087;font-size:9px;font-weight:700;white-space:nowrap}
#zpKnowledgePro .zpKBTopicCount strong{font-size:13px;color:#173f6b}
#zpKnowledgePro .zpKBTopicCard__body h3{margin:0 0 8px;font-size:19px;line-height:1.24;letter-spacing:-.03em;color:#07111f}
#zpKnowledgePro .zpKBTopicCard__body p{margin:0;color:#687386;font-size:12.5px;line-height:1.62}
#zpKnowledgePro .zpKBTopicCard__link{display:flex;align-items:center;justify-content:space-between;gap:10px;font-size:12px;font-weight:800;color:#244f7e}
#zpKnowledgePro .zpKBTopicCard__link i{display:grid;place-items:center;width:30px;height:30px;border-radius:50%;background:#071426;color:#fff;font-style:normal}
#zpKnowledgePro .zpKBQuickStart{position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:26px;margin-top:18px;padding:19px 22px;border-radius:22px;background:#0b1729;color:#fff}
#zpKnowledgePro .zpKBQuickStart__copy{display:flex;align-items:center;gap:14px;min-width:0}
#zpKnowledgePro .zpKBQuickStart__icon{display:grid;place-items:center;flex:0 0 auto;width:40px;height:40px;border-radius:13px;background:#ffffff12;color:#9ed0ff}
#zpKnowledgePro .zpKBQuickStart__icon svg{width:18px;height:18px}
#zpKnowledgePro .zpKBQuickStart strong{display:block;color:#fff;font-size:14px;line-height:1.35}
#zpKnowledgePro .zpKBQuickStart span{display:block;margin-top:3px;color:#bfc9d7;font-size:11.5px;line-height:1.5}
#zpKnowledgePro .zpKBQuickStart__actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}
#zpKnowledgePro .zpKBQuickStart__actions a{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 13px;border:1px solid #ffffff24;border-radius:999px;color:#fff;font-size:10.5px;font-weight:800;white-space:nowrap}
#zpKnowledgePro .zpKBQuickStart__actions a:first-child{background:#fff;color:#0b1729;border-color:#fff}
#zpKnowledgePro .zpKBSeoHub__head{position:relative;z-index:1;display:grid;margin-top:32px;padding:clamp(28px,4vw,44px);grid-template-columns:minmax(0,1.08fr) minmax(300px,.58fr);gap:clamp(28px,5vw,70px);align-items:start;border:1px solid rgba(7,17,31,.085);border-radius:28px;background:#fff}
#zpKnowledgePro .zpKBSeoHub__copy{max-width:1040px}
#zpKnowledgePro .zpKBSeoHub__head h2{margin:0 0 20px;font-size:clamp(30px,3.2vw,48px);line-height:1.1;letter-spacing:-.042em;color:#07111f;font-weight:650}
#zpKnowledgePro .zpKBSeoHub__copy p{margin:0 0 15px;max-width:980px;color:#5d6879;font-size:16px;line-height:1.82}
#zpKnowledgePro .zpKBSeoHub__copy a{color:#07111f;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:4px;font-weight:700}
#zpKnowledgePro .zpKBSeoHub__note{padding:22px 24px;border-radius:22px;background:#f3f7fb;border:1px solid rgba(28,71,122,.10)}
#zpKnowledgePro .zpKBSeoHub__note strong{display:block;margin-bottom:8px;font-size:14px;color:#07111f}
#zpKnowledgePro .zpKBSeoHub__note p{margin:0;color:#647085;font-size:13px;line-height:1.7}
/* index usability polish */
#zpKnowledgePro .zpKBIndex__head{padding:26px 28px;border:1px solid rgba(7,17,31,.08);border-radius:26px;background:#f8fafc}
#zpKnowledgePro .zpKBToolbar{padding:8px;border:1px solid rgba(7,17,31,.08);border-radius:22px;background:#f7f9fc}
#zpKnowledgePro .zpKBSearch,#zpKnowledgePro .zpKBSort,#zpKnowledgePro .zpKBView{box-shadow:none!important}
#zpKnowledgePro .zpKBPost{box-shadow:0 12px 36px rgba(7,17,31,.04)!important}
#zpKnowledgePro .zpKBPost:hover{box-shadow:0 18px 46px rgba(7,17,31,.065)!important}
@media(max-width:1280px){#zpKnowledgePro .zpKBTopicGrid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:900px){#zpKnowledgePro .zpKBTopicIntro,#zpKnowledgePro .zpKBSeoHub__head{grid-template-columns:1fr}#zpKnowledgePro .zpKBTopicGrid{grid-template-columns:repeat(2,minmax(0,1fr))}#zpKnowledgePro .zpKBQuickStart{align-items:flex-start;flex-direction:column}#zpKnowledgePro .zpKBQuickStart__actions{justify-content:flex-start}}
@media(max-width:620px){#zpKnowledgePro .zpKBSeoHub{padding-top:46px}#zpKnowledgePro .zpKBTopicIntro{margin-bottom:22px}#zpKnowledgePro .zpKBTopicGrid{grid-template-columns:1fr}#zpKnowledgePro .zpKBTopicCard{min-height:0}#zpKnowledgePro .zpKBSeoHub__copy p{font-size:15.5px}#zpKnowledgePro .zpKBQuickStart__actions{display:grid;width:100%;grid-template-columns:1fr}#zpKnowledgePro .zpKBQuickStart__actions a{text-align:center}#zpKnowledgePro .zpKBIndex__head{padding:22px}#zpKnowledgePro .zpKBToolbar{padding:0;border:0;background:transparent}}
</style>
<section class="zpKBSeoHub" aria-labelledby="zpKBSeoHubTitle">
  <div class="zpKBInner">
    <nav class="zpKBSeoHub__crumbs" aria-label="Okruszki">
      <a href="<?php echo esc_url(home_url('/')); ?>">Strona główna</a><span aria-hidden="true">/</span><span>Wiedza</span>
    </nav>
    <header class="zpKBTopicIntro">
      <div>
        <span class="zpKBSeoHub__eyebrow">Znajdź właściwy temat</span>
        <h2 id="zpKBSeoHubTitle">Wybierz obszar i przejdź od razu do konkretnych poradników.</h2>
      </div>
      <p>Nie musisz przeglądać całego bloga. Wiedza jest podzielona na kilka głównych klastrów — wybierz ten, który odpowiada decyzji, przed którą teraz stoisz.</p>
    </header>
    <?php if (!empty($zp_kb_topics)): $zp_kb_topic_icons=['strony-internetowe'=>'layout-template','sklepy-internetowe'=>'shopping-bag','logo-branding'=>'sparkles','seo-konwersja'=>'search','kampanie-reklamowe'=>'megaphone','case-study'=>'briefcase-business']; ?>
      <nav class="zpKBTopicGrid" aria-label="Główne obszary wiedzy">
        <?php foreach ($zp_kb_topics as $topic_key => $topic): $icon=$zp_kb_topic_icons[$topic_key] ?? 'book-open'; ?>
          <a class="zpKBTopicCard" href="<?php echo esc_url($topic['url']); ?>">
            <span class="zpKBTopicCard__top"><span class="zpKBTopicIcon"><i data-lucide="<?php echo esc_attr($icon); ?>"></i></span><span class="zpKBTopicCount"><strong><?php echo (int)$topic['count']; ?></strong> poradników</span></span>
            <span class="zpKBTopicCard__body"><h3><?php echo esc_html($topic['label']); ?></h3><p><?php echo esc_html($topic['description']); ?></p></span>
            <span class="zpKBTopicCard__link">Otwórz temat <i aria-hidden="true">↗</i></span>
          </a>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>
    <div class="zpKBQuickStart" aria-label="Szybkie skróty">
      <div class="zpKBQuickStart__copy"><span class="zpKBQuickStart__icon"><i data-lucide="compass"></i></span><div><strong>Nie wiesz, od którego artykułu zacząć?</strong><span>Przejdź do wyszukiwarki, zobacz realizacje albo od razu opisz swój projekt.</span></div></div>
      <div class="zpKBQuickStart__actions"><a href="#zpKBIndex">Przeszukaj całą bazę</a><a href="<?php echo esc_url(home_url('/realizacje/')); ?>">Zobacz realizacje</a><a href="<?php echo esc_url(home_url('/studio-wyceny/')); ?>">Wyceń projekt</a></div>
    </div>
    <div class="zpKBSeoHub__head">
      <div class="zpKBSeoHub__copy">
        <span class="zpKBSeoHub__eyebrow">Baza wiedzy Zaprojektowani</span>
        <h2>Poradniki o stronach internetowych, sklepach, SEO, brandingu i kampaniach.</h2>
        <p>Ta baza wiedzy porządkuje tematy, które najczęściej pojawiają się przed rozpoczęciem projektu i podczas rozwoju strony. Znajdziesz tu praktyczne materiały o <a href="<?php echo esc_url(home_url('/strony-internetowe-katowice/')); ?>">projektowaniu stron internetowych</a>, <a href="<?php echo esc_url(home_url('/sklepy-internetowe-katowice/')); ?>">sklepach WooCommerce</a>, <a href="<?php echo esc_url(home_url('/logo-branding-katowice/')); ?>">logo i identyfikacji wizualnej</a>, SEO, UX oraz kampaniach reklamowych.</p>
        <p>Artykuły są ułożone wokół konkretnych decyzji: jaki zakres projektu wybrać, ile może kosztować wdrożenie, kiedy landing page wystarczy, jak przygotować sklep do sprzedaży, co powinien zawierać brandbook i jak połączyć stronę z reklamami oraz analityką. Jeśli chcesz najpierw zobaczyć efekt końcowy, przejdź też do naszych <a href="<?php echo esc_url(home_url('/realizacje/')); ?>">realizacji stron, sklepów i brandingu</a>.</p>
      </div>
      <aside class="zpKBSeoHub__note">
        <strong>Jak korzystać z Wiedzy?</strong>
        <p>Wybierz obszar powyżej albo przejdź do indeksu wszystkich artykułów. Każdy wpis jest dostępny przez zwykły link HTML, więc poradniki pozostają czytelne dla użytkowników i robotów wyszukiwarek również bez JavaScriptu.</p>
      </aside>
    </div>
  </div>
</section>

<div class="zpKBFeatured">
    <div class="zpKBInner"><div class="zpKBFeatured__grid">
      <?php if ($zp_kb_featured->have_posts()): $zp_kb_featured->the_post(); $fid=get_the_ID(); ?>
        <article class="zpKBMonth">
          <a class="zpKBMonth__media" href="<?php the_permalink(); ?>">
            <?php echo zp_suite_wiedza_image_markup($fid, 'large', 'zpKBMonth__img', get_the_title($fid), '(max-width: 1080px) calc(100vw - 32px), 60vw'); ?>
            <span class="zpKBMonth__pills" aria-hidden="true">
              <?php $zp_kb_fcats = get_the_category($fid); if (!empty($zp_kb_fcats)) : ?>
                <em><?php echo esc_html($zp_kb_fcats[0]->name); ?></em>
              <?php endif; ?>
              <em>Wyróżniony wpis</em>
            </span>
          </a>
          <div class="zpKBMonth__body">
            <span class="zpKBLabel">Post miesiąca</span>
            <div class="zpKBMonth__meta"><span><?php echo esc_html(get_the_date('d.m.Y')); ?></span><span><?php echo esc_html(zp_suite_wiedza_read_time($fid)); ?></span></div>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p><?php echo esc_html(zp_suite_wiedza_fast_excerpt($fid, 42)); ?></p>
            <a class="zpKBMonth__link" href="<?php the_permalink(); ?>"><span>Czytaj wyróżniony wpis</span><i data-lucide="arrow-up-right"></i></a>
          </div>
        </article>
      <?php endif; wp_reset_postdata(); ?>
      <aside class="zpKBTopPicks" aria-label="Polecane wpisy z bazy wiedzy">
        <div class="zpKBTopPicks__head">
          <span class="zpKBLabel">Warto przeczytać</span>
          <h2>Cztery szybkie tematy, które pomagają lepiej zaplanować projekt.</h2>
          <p>Wybraliśmy dodatkowe wpisy z bazy wiedzy — idealne, jeśli chcesz uporządkować stronę, sklep, SEO albo branding przed wyceną.</p>
        </div>
        <?php $zp_kb_pick_i = 0; if ($zp_kb_selected->have_posts()): while($zp_kb_selected->have_posts()): $zp_kb_selected->the_post(); $sid=get_the_ID(); if ($sid === $zp_kb_featured_id) { continue; } if ($zp_kb_pick_i >= 4) { break; } $zp_kb_pick_i++; ?>
          <article class="zpKBTopPick">
            <a class="zpKBTopPick__media" href="<?php the_permalink(); ?>">
              <?php echo zp_suite_wiedza_image_markup($sid, 'medium', 'zpKBTopPick__img', get_the_title($sid), '(max-width: 760px) 100px, (max-width: 1080px) 108px, 132px'); ?>
            </a>
            <div class="zpKBTopPick__body">
              <small><?php echo esc_html(get_the_date('d.m.Y')); ?> · <?php echo esc_html(zp_suite_wiedza_read_time($sid)); ?></small>
              <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <p><?php echo esc_html(zp_suite_wiedza_fast_excerpt($sid, 20)); ?></p>
              <a class="zpKBTopPick__btn" href="<?php the_permalink(); ?>"><span>Czytaj wpis</span><i data-lucide="arrow-up-right"></i></a>
            </div>
          </article>
        <?php endwhile; endif; wp_reset_postdata(); ?>
      </aside>
    </div></div>
  </div>

  <div class="zpKBIndex" id="zpKBIndex">
    <div class="zpKBInner"><div class="zpKBIndex__layout">
      <main class="zpKBIndex__main">
        <header class="zpKBIndex__head"><div><span class="zpKBLabel">Indeks wiedzy</span><h2>Centrum wiedzy</h2></div><p class="zpKBIndex__lead">Znajdź poradnik, który pomoże Ci lepiej zaplanować stronę internetową, sklep WooCommerce, branding, SEO albo kampanię reklamową — zanim podejmiesz decyzję o wdrożeniu.</p></header>
        <div class="zpKBToolbar"><span class="zpKBMobileFilterLabel"><i data-lucide="sliders-horizontal"></i>Filtruj</span><label class="zpKBSearch"><i data-lucide="search"></i><input type="search" data-zp-kb-search placeholder="Szukaj: WooCommerce, SEO, branding, strona internetowa..."></label><select class="zpKBSort" data-zp-kb-sort aria-label="Sortowanie wpisów"><option value="newest">Najnowsze</option><option value="oldest">Najstarsze</option><option value="title">A–Z</option></select><div class="zpKBView" aria-label="Zmień układ wpisów"><button type="button" class="is-active" data-zp-view="grid" aria-label="Widok kart"><i data-lucide="layout-grid"></i></button><button type="button" data-zp-view="list" aria-label="Widok listy"><i data-lucide="rows-3"></i></button><button type="button" data-zp-view="compact" aria-label="Widok kompaktowy"><i data-lucide="panel-top"></i></button></div></div>
        <div class="zpKBCatsHint" aria-hidden="true"><span>Przesuń w prawo</span><i data-lucide="arrow-right"></i></div><nav class="zpKBCats" aria-label="Kategorie wpisów"><a class="zpKBCat is-active" href="<?php echo esc_url(home_url('/wiedza/')); ?>" data-zp-cat="all">Wszystkie</a><?php if(!empty($zp_kb_topics)): foreach($zp_kb_topics as $topic_key=>$topic): ?><a class="zpKBCat" href="<?php echo esc_url($topic['url']); ?>" data-zp-cat="<?php echo esc_attr($topic_key); ?>"><?php echo esc_html($topic['label']); ?> <span><?php echo (int)$topic['count']; ?></span></a><?php endforeach; else: foreach($zp_kb_cats as $cat): ?><a class="zpKBCat" href="<?php echo esc_url(get_category_link($cat)); ?>" data-zp-cat="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?> <span><?php echo (int)$cat->count; ?></span></a><?php endforeach; endif; ?></nav>
        <div class="zpKBStatus"><span><b data-zp-kb-count><?php echo (int) $zp_kb_posts_count; ?></b> wpisów w aktualnym widoku</span><span class="zpKBLoader" aria-hidden="true"><i></i><i></i><i></i> szukam</span></div><div class="zpKBEmpty">Nie znaleziono wpisów dla tej frazy albo kategorii. Zmień filtr lub wpisz szersze zapytanie.</div><div class="zpKBPosts" data-zp-kb-results><?php echo $zp_kb_posts_html; ?></div>
      </main>
      <aside class="zpKBIndex__side" aria-label="Ścieżki tematyczne"><div class="zpKBSideCard"><span class="zpKBLabel">Ścieżki tematyczne</span><h3>Wybierz obszar i przejdź do powiązanych poradników.</h3><p>Najważniejsze tematy są zgrupowane w kilka czytelnych klastrów. Linki prowadzą również do archiwów kategorii, więc działają bez JavaScriptu.</p><div class="zpKBSideHint" aria-hidden="true"><span>Przesuń w prawo</span><i data-lucide="arrow-right"></i></div><div class="zpKBPathsMini"><?php if(!empty($zp_kb_topics)): foreach($zp_kb_topics as $topic_key=>$topic): ?><a class="zpKBPath" href="<?php echo esc_url($topic['url']); ?>" data-zp-path-cat="<?php echo esc_attr($topic_key); ?>"><span><?php echo esc_html($topic['label']); ?></span><strong><?php echo (int)$topic['count']; ?> wpisów</strong><i data-lucide="arrow-down-right"></i></a><?php endforeach; else: foreach($zp_kb_cats as $path_cat): ?><a class="zpKBPath" href="<?php echo esc_url(get_category_link($path_cat)); ?>" data-zp-path-cat="<?php echo esc_attr($path_cat->slug); ?>"><span><?php echo esc_html($path_cat->name); ?></span><strong><?php echo (int)$path_cat->count; ?> wpisów</strong><i data-lucide="arrow-down-right"></i></a><?php endforeach; endif; ?></div></div></aside>
    </div>

      <section class="zpKBFooterCTA" aria-label="Studio wyceny Zaprojektowani"><div class="zpKBFooterCTA__bleed" aria-hidden="true"></div><div class="zpKBFooterCTA__mark" aria-hidden="true">zaprojektowani</div><div class="zpKBFooterCTA__copy"><span class="zpKBFooterCTA__eyebrow">Twój projekt może być następny</span><h2>Zbudujmy stronę internetową, sklep WooCommerce albo markę, która od pierwszego kontaktu wygląda profesjonalnie.</h2><p>Opisz nam, co chcesz stworzyć. Dobierzemy zakres, kierunek wizualny, funkcje, SEO, analitykę, branding i plan wdrożenia — bez przypadkowych rozwiązań.</p></div><div class="zpKBFooterCTA__panel"><div class="zpKBFooterCTA__frame"><span>Tu może być Twój projekt!</span></div><div class="zpKBFooterCTA__actions"><a class="zpKBFooterCTA__btn" href="/studio-wyceny/"><span>Otrzymaj darmową wycenę</span><i data-lucide="arrow-up-right"></i></a><a class="zpKBFooterCTA__btn zpKBFooterCTA__btn--ghost" href="/strony-internetowe-katowice/"><span>Zobacz ofertę</span><i data-lucide="arrow-up-right"></i></a></div></div>
</section>
    </div>
  </div>

  <script>
  (function(){var zpWiedzaVideo2154=true;var root=document.querySelector('[data-zp-knowledge-pro]');if(!root)return;try{root.setAttribute('data-ready','1')}catch(e){}var heroVideo=root.querySelector('.zpKBHero__video video');if(heroVideo){try{heroVideo.muted=true;heroVideo.defaultMuted=true;heroVideo.loop=true;heroVideo.playsInline=true;heroVideo.setAttribute('muted','');heroVideo.setAttribute('loop','');heroVideo.setAttribute('playsinline','');heroVideo.setAttribute('webkit-playsinline','');heroVideo.setAttribute('preload','none');heroVideo.removeAttribute('src');}catch(e){}}function icons(){if(window.lucide&&window.lucide.createIcons){try{window.lucide.createIcons({attrs:{'stroke-width':1.8,'stroke-linecap':'round','stroke-linejoin':'round'}})}catch(e){}}}icons();var vids=root.querySelectorAll('video');vids.forEach(function(v){try{v.muted=true;v.playsInline=true;v.setAttribute('preload','none')}catch(e){}});['touchstart','click'].forEach(function(ev){root.addEventListener(ev,function(){vids.forEach(function(v){try{v.play()}catch(e){}})},{once:true,passive:true})});var ajax=root.getAttribute('data-ajax'),nonce=root.getAttribute('data-nonce'),results=root.querySelector('[data-zp-kb-results]'),input=root.querySelector('[data-zp-kb-search]'),sort=root.querySelector('[data-zp-kb-sort]'),count=root.querySelector('[data-zp-kb-count]'),cats=[].slice.call(root.querySelectorAll('[data-zp-cat]')),views=[].slice.call(root.querySelectorAll('[data-zp-view]'));var activeCat='all',activeView='grid',timer=null,controller=null;function setView(v){activeView=v;views.forEach(function(b){b.classList.toggle('is-active',b.getAttribute('data-zp-view')===v)});if(!results)return;results.classList.toggle('is-list',v==='list');results.classList.toggle('is-compact',v==='compact')}views.forEach(function(btn){btn.addEventListener('click',function(){setView(btn.getAttribute('data-zp-view')||'grid')})});function run(){if(!ajax||!results)return;root.classList.add('is-loading');root.classList.remove('is-empty');if(controller)controller.abort();controller=window.AbortController?new AbortController():null;var fd=new FormData();fd.append('action','zp_wiedza_search');fd.append('nonce',nonce||'');fd.append('search',input&&input.value?input.value:'');fd.append('cat',activeCat);fd.append('sort',sort&&sort.value?sort.value:'newest');fetch(ajax,{method:'POST',credentials:'same-origin',body:fd,signal:controller?controller.signal:null}).then(function(r){return r.json()}).then(function(res){if(!res||!res.success)return;results.innerHTML=res.data.html||'';if(count)count.textContent=res.data.count||0;root.classList.toggle('is-empty',!(res.data.count>0));setView(activeView);icons()}).catch(function(e){if(e.name!=='AbortError')console.warn('ZP wiedza AJAX',e)}).finally(function(){root.classList.remove('is-loading')})}function debounce(){clearTimeout(timer);timer=setTimeout(run,220)}if(input)input.addEventListener('input',debounce);if(sort)sort.addEventListener('change',run);cats.forEach(function(btn){btn.addEventListener('click',function(e){if(e&&e.preventDefault)e.preventDefault();cats.forEach(function(b){b.classList.remove('is-active')});btn.classList.add('is-active');activeCat=btn.getAttribute('data-zp-cat')||'all';run()})});[].slice.call(root.querySelectorAll('[data-zp-path-cat]')).forEach(function(btn){btn.addEventListener('click',function(e){if(e&&e.preventDefault)e.preventDefault();var slug=btn.getAttribute('data-zp-path-cat')||'all';var target=cats.filter(function(b){return(b.getAttribute('data-zp-cat')||'')===slug})[0];if(target)target.click();var index=root.querySelector('#zpKBIndex');if(index)index.scrollIntoView({behavior:'smooth',block:'start'})})});setView('grid');
      function zpRevealPosts(){
        if(!results) return;
        var posts=[].slice.call(results.querySelectorAll('[data-zp-post]'));
        if(!posts.length) return;
        if(root._zpKBRevealObserver){try{root._zpKBRevealObserver.disconnect()}catch(e){}}
        var reduced=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var audit=document.documentElement.classList.contains('zp-synthetic-audit');
        if(reduced||audit||!('IntersectionObserver' in window)){
          results.removeAttribute('data-zp-reveal-ready');
          posts.forEach(function(p){p.classList.add('is-visible')});
          return;
        }
        results.setAttribute('data-zp-reveal-ready','1');
        var observer=new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          });
        },{rootMargin:'240px 0px',threshold:.01});
        root._zpKBRevealObserver=observer;
        posts.forEach(function(p){p.classList.remove('is-visible');observer.observe(p)});
      }
      zpRevealPosts();
      if(results&&window.MutationObserver){
        root._zpKBResultsObserver=new MutationObserver(function(mutations){
          if(mutations.some(function(mutation){return mutation.type==='childList'})){
            zpRevealPosts();
          }
        });
        root._zpKBResultsObserver.observe(results,{childList:true});
      }else if(results){
        results.removeAttribute('data-zp-reveal-ready');
      }
    })();
  </script>

  <script>
  (function(){
    var root=document.querySelector('[data-zp-knowledge-pro]');
    if(!root) return;

    var pathRail=root.querySelector('.zpKBPathsMini');
    if(pathRail){
      pathRail.setAttribute('tabindex','0');
      pathRail.setAttribute('aria-label','Przesuwane ścieżki tematyczne');
    }

    var video=root.querySelector('.zpKBHero__video video');
    if(!video) return;

    var src=video.getAttribute('data-zp-video-src') || 'https://zaprojektowani.com/wp-content/uploads/videos/zaprojektowani_video.mp4';
    var started=false;
    var revealed=false;

    function revealVideo(){
      if(revealed) return;
      revealed=true;
      root.setAttribute('data-video-ready','1');
      try{
        var p=video.play && video.play();
        if(p && p.catch) p.catch(function(){});
      }catch(e){}
    }

    function startVideo(){
      if(started) return;
      started=true;
      try{
        video.muted=true;
        video.defaultMuted=true;
        video.loop=true;
        video.autoplay=true;
        video.playsInline=true;
        video.setAttribute('muted','');
        video.setAttribute('loop','');
        video.setAttribute('autoplay','');
        video.setAttribute('playsinline','');
        video.setAttribute('webkit-playsinline','');
        video.setAttribute('preload','metadata');
        if(!video.getAttribute('src')) video.setAttribute('src',src);
        video.load && video.load();
      }catch(e){}

      if(video.readyState >= 2){ revealVideo(); return; }
      video.addEventListener('loadeddata', revealVideo, {once:true, passive:true});
      video.addEventListener('canplay', revealVideo, {once:true, passive:true});
      setTimeout(revealVideo, 1700);
    }

    if('requestIdleCallback' in window){
      requestIdleCallback(startVideo,{timeout:2800});
    }else{
      setTimeout(startVideo,1800);
    }

    ['pointerdown','touchstart','click','scroll','keydown'].forEach(function(ev){
      window.addEventListener(ev,startVideo,{once:true,passive:true});
    });
    document.addEventListener('visibilitychange',function(){ if(!document.hidden) startVideo(); });
    // v2.2.248: do not auto-start video on pageshow; it was forcing MP4 metadata on the first entry.
  })();
  </script>
</section>
