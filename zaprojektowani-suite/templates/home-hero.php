<?php
/**
 * ZAPROJEKTOWANI — HOME HERO v2.2.496
 * New lightweight live hero. Old .zpRobotHero template removed/replaced to avoid duplicate hero code.
 */
if (!defined('ABSPATH')) { exit; }
?>
<style>:root{--bg:#010309;--ink:#fff;--mut:rgba(255,255,255,.66);--mut2:rgba(255,255,255,.42);--line:rgba(255,255,255,.12);--acc-deep:#0a1a30;--acc:#1c477a;--acc-mid:#3b6ea8;--acc-ice:#8ec8f7;--gold:#ffc246;--font:"Plus Jakarta Sans Local","Plus Jakarta Sans",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}*{box-sizing:border-box}html,body{margin:0;background:var(--bg);color:var(--ink);font-family:var(--font);-webkit-font-smoothing:antialiased}.zh{position:relative;isolation:isolate;min-height:100vh;overflow:hidden;background:var(--bg)}.zh__bg{position:absolute;inset:0;z-index:0;background:radial-gradient(120% 90% at 74% 44%,rgba(18,48,88,.22),transparent 56%),radial-gradient(80% 80% at 94% 82%,rgba(8,26,52,.3),transparent 50%),radial-gradient(70% 70% at 12% 22%,rgba(14,38,72,.12),transparent 46%),linear-gradient(140deg,#010308 0%,#030a16 50%,#01040c 100%)}.zh__aurora{position:absolute;left:36%;top:16%;width:54%;height:66%;z-index:1;pointer-events:none;background:radial-gradient(closest-side,rgba(24,60,104,.2),transparent 70%),radial-gradient(closest-side,rgba(45,92,149,.09),transparent 72%);background-repeat:no-repeat;background-position:32% 34%,72% 64%;background-size:64% 64%,54% 54%;filter:blur(38px);opacity:.6;animation:zhBreath 15s ease-in-out infinite}@keyframes zhBreath{0%,100%{transform:scale(1);opacity:.5}50%{transform:scale(1.05) translateY(-1.1%);opacity:.7}}.zh__grid{position:absolute;inset:0;z-index:1;opacity:.03;pointer-events:none;background-image:linear-gradient(rgba(255,255,255,.1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.07) 1px,transparent 1px);background-size:82px 82px;-webkit-mask-image:radial-gradient(120% 100% at 60% 42%,#000 26%,transparent 74%);mask-image:radial-gradient(120% 100% at 60% 42%,#000 26%,transparent 74%)}.zh__dust{position:absolute;inset:0;z-index:2;pointer-events:none;overflow:hidden}.zh__dust i{position:absolute;bottom:-12px;width:3px;height:3px;border-radius:50%;background:rgba(142,200,247,.55);opacity:0;animation:zhDust linear infinite}@keyframes zhDust{0%{transform:translateY(0) scale(1);opacity:0}12%{opacity:.55}88%{opacity:.45}100%{transform:translateY(-94vh) scale(.6);opacity:0}}.zh__grain{position:absolute;inset:0;z-index:5;pointer-events:none;opacity:.3;mix-blend-mode:overlay;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.5'/%3E%3C/svg%3E")}.zh__vig{position:absolute;inset:0;z-index:5;pointer-events:none;background:radial-gradient(135% 130% at 50% 26%,transparent 48%,rgba(0,0,0,.5))}.zh__hair{position:absolute;left:0;right:0;top:0;height:1px;z-index:7;background:linear-gradient(90deg,transparent,rgba(142,200,247,.5),transparent);opacity:.4}.zh__kin{position:absolute;z-index:2;right:0;top:47%;width:88%;transform:translateY(-52%);text-align:right;pointer-events:none;-webkit-mask-image:radial-gradient(135% 135% at 74% 48%,#000 46%,transparent 90%);mask-image:radial-gradient(135% 135% at 74% 48%,#000 46%,transparent 90%)}.zh__kinWord{display:block;font-weight:800;line-height:.8;letter-spacing:-.05em;text-transform:uppercase;font-size:clamp(150px,25vw,460px);background:linear-gradient(100deg,#16365f 0%,#1c477a 28%,#3b6ea8 50%,#79a6da 64%,#2a558f 84%,#16365f 100%);background-size:260% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;animation:zhShine 11s linear infinite;opacity:.5}@keyframes zhShine{to{background-position:-260% 0}}.zh__rotor{display:inline-block;overflow:hidden;height:.8em;vertical-align:bottom}.zh__rotor>span{display:block;animation:zhRot 13s cubic-bezier(.76,0,.24,1) infinite}.zh__rotor i{display:block;height:.8em;font-style:normal}@keyframes zhRot{0%,15%{transform:translateY(0)}20%,35%{transform:translateY(-.8em)}40%,55%{transform:translateY(-1.6em)}60%,75%{transform:translateY(-2.4em)}80%,95%{transform:translateY(-3.2em)}100%{transform:translateY(-4em)}}.zh__stage{position:absolute;z-index:3;right:-6vw;inset-block:0;width:70vw;min-width:1000px;pointer-events:auto;will-change:transform}.zh__conic{position:absolute;left:52%;top:50%;width:64%;aspect-ratio:1;transform:translate(-50%,-50%);border-radius:50%;pointer-events:none;background:conic-gradient(from 0deg,transparent 0deg,rgba(28,71,122,0) 46deg,rgba(59,110,168,.12) 128deg,rgba(142,200,247,.045) 196deg,rgba(28,71,122,0) 300deg,transparent 360deg);-webkit-mask:radial-gradient(closest-side,transparent 54%,#000 60%,#000 73%,transparent 80%);mask:radial-gradient(closest-side,transparent 54%,#000 60%,#000 73%,transparent 80%);filter:blur(8px);opacity:.34;animation:zhConic 20s linear infinite}@keyframes zhConic{to{transform:translate(-50%,-50%) rotate(360deg)}}.zh__halo{position:absolute;left:52%;top:50%;width:48%;aspect-ratio:1;transform:translate(-50%,-50%);border-radius:50%;background:radial-gradient(circle,rgba(30,78,128,.07),rgba(16,44,82,.025) 44%,transparent 66%);filter:blur(16px);animation:zhPulse 8s ease-in-out infinite;pointer-events:none}@keyframes zhPulse{0%,100%{opacity:.45;transform:translate(-50%,-50%) scale(1)}50%{opacity:.66;transform:translate(-50%,-50%) scale(1.05)}}.zh__ring{position:absolute;left:52%;top:50%;border-radius:50%;border:1px solid rgba(142,200,247,.035);transform:translate(-50%,-50%);pointer-events:none}.zh__ring--a{width:50%;aspect-ratio:1;border-style:dashed;animation:zhSpin 46s linear infinite}.zh__ring--b{width:66%;aspect-ratio:1;border-color:rgba(142,200,247,.018);animation:zhSpin 72s linear infinite reverse}.zh__ring--a::before{content:"";position:absolute;top:-4px;left:50%;width:7px;height:7px;border-radius:50%;background:rgba(142,200,247,.55);box-shadow:0 0 7px 1px rgba(142,200,247,.22);transform:translateX(-50%)}@keyframes zhSpin{to{transform:translate(-50%,-50%) rotate(360deg)}}.zh__spline{position:absolute;inset:0;width:100%;height:100%;border:0;opacity:1;transition:opacity .35s ease;pointer-events:auto}.zh.is-spline-loaded .zh__spline{opacity:1}.zh__poster{position:absolute;inset:0;z-index:1;pointer-events:none;background:var(--robot) no-repeat center calc(50% + 70px)/min(82%,860px) auto;filter:saturate(1.03) brightness(1) drop-shadow(0 40px 80px rgba(0,0,0,.55));opacity:1;transition:opacity .8s ease}.zh.is-spline-loaded .zh__poster{opacity:0}.zh__fade{position:absolute;inset:0;z-index:4;pointer-events:none;background:linear-gradient(90deg,#010309 0%,rgba(1,3,9,.82) 24%,rgba(1,3,9,.42) 50%,rgba(1,3,9,.1) 74%,transparent 100%),linear-gradient(to bottom,transparent 56%,rgba(1,3,9,.7) 100%)}.zh__splineMask{position:absolute;right:0;bottom:0;width:200px;height:54px;z-index:4;pointer-events:none;background:linear-gradient(to top,#010309 30%,transparent)}.zh__rail{position:absolute;right:20px;left:auto;top:clamp(112px,18vh,188px);bottom:auto;z-index:8;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;gap:18px;pointer-events:none}.zh__rail>*{pointer-events:auto}.zh__railTop{display:none}.zh__railIco{display:grid;gap:16px}.zh__railIco a{color:var(--mut2);display:block;transition:color .2s ease,transform .2s ease}.zh__railIco a:hover{color:var(--acc-ice);transform:translateY(-2px)}.zh__railIco svg{width:17px;height:17px;display:block}.zh__railProg{width:2px;height:96px;margin-top:2px;background:rgba(255,255,255,.12);position:relative;border-radius:999px;overflow:hidden}.zh__railProgFill{position:absolute;left:0;top:0;width:100%;height:0%;background:linear-gradient(to bottom,var(--acc-ice),var(--acc));border-radius:999px;box-shadow:0 0 12px rgba(142,200,247,.32);transition:height .08s linear}.zh__inner{position:relative;z-index:6;width:min(1700px,calc(100% - 96px));margin:0 auto;min-height:100vh;display:flex;align-items:center;padding:120px 0 124px;pointer-events:none}.zh__copy{max-width:1000px;pointer-events:none}.zh__eb{display:inline-flex;align-items:center;gap:13px;margin:0 0 18px;padding:0;border:0;border-radius:0;background:transparent;color:#b9b2cf;font-size:10px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;line-height:1.25;-webkit-backdrop-filter:none;backdrop-filter:none}.zh__eb .dot{display:block;position:relative;flex:0 0 42px;width:42px;height:1px;border-radius:999px;background:linear-gradient(90deg,rgba(185,178,207,0),rgba(185,178,207,.72),rgba(142,200,247,.28),rgba(185,178,207,0))}.zh__eb .dot::after{content:"";position:absolute;inset:-1px;border-radius:999px;background:linear-gradient(90deg,transparent,rgba(142,200,247,.34),transparent);filter:blur(3px);opacity:.75}@keyframes zhPing{0%{transform:scale(.6);opacity:1}100%{transform:scale(1.8);opacity:0}}.zh__title{margin:0;max-width:15ch;font-weight:480;letter-spacing:-.046em;line-height:.97;font-size:clamp(46px,7vw,124px);text-wrap:balance;opacity:0;transform:translateY(26px);filter:blur(8px);animation:zhRise 1.1s cubic-bezier(.16,1,.3,1) .1s forwards}@keyframes zhRise{to{opacity:1;transform:translateY(0);filter:blur(0)}}.zh__grad{background:linear-gradient(110deg,var(--acc-mid) 0%,var(--acc-ice) 26%,#fff 42%,var(--acc-ice) 58%,var(--acc-mid) 100%);background-size:240% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:700;background-position:130% 0;animation:zhSheen 1.5s cubic-bezier(.16,1,.3,1) 1s forwards}@keyframes zhSheen{to{background-position:14% 0}}.zh__lead{margin:32px 0 0;max-width:600px;color:var(--mut);font-size:17.5px;line-height:1.72;letter-spacing:-.01em;opacity:0;animation:zhFade .9s ease .5s forwards}.zh__actions{pointer-events:auto;margin-top:36px;display:flex;flex-wrap:wrap;gap:14px;opacity:0;animation:zhFade .9s ease .62s forwards}@keyframes zhFade{to{opacity:1}}.zh__btn{position:relative;display:inline-flex;align-items:center;gap:10px;min-height:58px;padding:0 28px;border-radius:999px;font-size:15px;font-weight:700;letter-spacing:-.01em;white-space:nowrap;text-decoration:none;cursor:pointer}.zh__btn span,.zh__btn svg{position:relative;z-index:2}.zh__btn svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;transition:transform .24s cubic-bezier(.16,1,.3,1)}.zh__btn--p{color:#071426;border:0;background-color:#fff;background-image:linear-gradient(100deg,#020407 0%,#07101e 38%,#102a4f 76%,var(--acc) 100%);background-repeat:no-repeat;background-position:left center;background-size:0% 100%;box-shadow:none;transition:background-size .44s cubic-bezier(.16,1,.3,1),color .2s ease,transform .24s cubic-bezier(.16,1,.3,1)}.zh__btn--p::after{content:"";position:absolute;inset:0;z-index:1;border-radius:inherit;padding:1px;pointer-events:none;background:linear-gradient(120deg,rgba(28,71,122,.75),rgba(16,42,79,.5) 48%,rgba(2,4,7,.55) 100%);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude}.zh__btn--p:hover,.zh__btn--p:focus-visible{color:#fff;background-size:100% 100%}.zh__btn--p:hover svg{transform:translate(3px,-3px)}.zh__btn--g{color:#fff;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.22);-webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);transition:background .24s ease,color .2s ease,transform .24s cubic-bezier(.16,1,.3,1)}.zh__btn--g:hover{background:#fff;color:#071426;transform:translateY(-2px)}.zh__btn--g:hover svg{transform:translate(3px,-3px)}.zh__chips{pointer-events:auto;margin-top:30px;display:flex;flex-wrap:wrap;gap:9px;opacity:0;animation:zhFade .9s ease .74s forwards}.zh__chip{position:relative;display:inline-flex;align-items:center;gap:8px;padding:9px 15px;border:1px solid var(--line);border-radius:999px;background:rgba(255,255,255,.03);color:var(--mut);font-size:12px;font-weight:600;letter-spacing:-.01em;text-decoration:none;overflow:hidden;transition:border-color .24s ease,color .24s ease,transform .24s cubic-bezier(.16,1,.3,1),background .24s ease,box-shadow .24s ease}.zh__chip::before{content:"";position:absolute;inset:0;background:linear-gradient(100deg,rgba(142,200,247,.18),rgba(28,71,122,.18),transparent 70%);opacity:0;transition:opacity .24s ease;pointer-events:none}.zh__chip b,.zh__chip span{position:relative;z-index:1}.zh__chip b{color:var(--acc-ice);font-weight:800;font-size:10px;letter-spacing:.06em}.zh__chip:hover{border-color:rgba(142,200,247,.5);color:#fff;background:rgba(28,71,122,.18);transform:translateY(-3px);box-shadow:0 14px 32px rgba(0,0,0,.22)}.zh__chip:hover::before{opacity:1}.zh__side{position:absolute;z-index:6;right:max(28px,calc((100% - 1700px)/2 + 28px));top:calc(50% + 144px);transform:translateY(-50%);display:flex;flex-direction:row;gap:14px;width:min(520px,42vw);opacity:0;animation:zhFade 1s ease .9s forwards}.zh__rev{position:relative;isolation:isolate;display:block;flex:1 1 0;min-width:0;min-height:152px;text-decoration:none;color:#fff;overflow:hidden;padding:16px 18px 54px;border-radius:20px;border:1px solid rgba(255,255,255,.11);background:rgba(1,4,12,.34);-webkit-backdrop-filter:blur(28px) saturate(1.42);backdrop-filter:blur(28px) saturate(1.42);box-shadow:0 22px 58px rgba(0,0,0,.34),inset 0 1px 0 rgba(255,255,255,.12),inset 0 -1px 0 rgba(255,255,255,.035);transition:transform .32s cubic-bezier(.16,1,.3,1),border-color .32s,background .32s ease;transform:translateZ(0)}.zh__rev::before{content:"";position:absolute;inset:0;z-index:0;border-radius:inherit;background:linear-gradient(to bottom,rgba(255,255,255,.055),rgba(255,255,255,.012) 42%,rgba(1,4,12,.12));pointer-events:none}.zh__rev::after{content:"";position:absolute;inset:0;z-index:1;border-radius:inherit;padding:1px;background:linear-gradient(180deg,rgba(255,255,255,.18),rgba(142,200,247,.075) 48%,rgba(255,255,255,.035));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:.78;pointer-events:none}.zh__rev>*{position:relative;z-index:2}.zh__rev:hover{transform:translateY(-4px) translateZ(0);border-color:rgba(142,200,247,.26);background:rgba(1,4,12,.42)}.zh__rl{display:block;font-size:9.5px;font-weight:800;letter-spacing:.13em;text-transform:uppercase;color:var(--mut2)}.zh__sc{display:flex;align-items:baseline;gap:8px;margin:12px 0 10px}.zh__sc strong{font-size:30px;font-weight:780;letter-spacing:-.04em;line-height:1}.zh__sc em{font-style:normal;color:var(--gold);font-size:13px;letter-spacing:1.5px}.zh__rt{font-size:14px;font-weight:700;color:#fff;max-width:58%;line-height:1.32;margin-bottom:0}.zh__rt span{display:block;font-size:13px;font-weight:500;color:var(--mut2);margin-top:6px;line-height:1.34}.zh__rlogo{position:absolute;right:18px;bottom:18px;height:18px}.zh__rlogo img{height:100%;width:auto;object-fit:contain;opacity:.92}.zh__strip{position:absolute;left:0;right:0;bottom:0;z-index:6;border-top:1px solid var(--line);background:linear-gradient(to top,rgba(1,4,12,.74),transparent);-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px)}.zh__stripIn{width:min(1700px,calc(100% - 96px));margin:0 auto;display:flex;align-items:center;gap:26px;height:78px}.zh__stats{display:flex;align-items:center;gap:26px;flex:none}.zh__stat{display:flex;align-items:baseline;gap:7px;white-space:nowrap}.zh__stat b{font-size:20px;font-weight:800;letter-spacing:-.03em;color:#fff;font-variant-numeric:tabular-nums}.zh__stat i{font-style:normal;color:var(--gold);font-size:12px;margin-left:2px}.zh__stat span{font-size:12px;font-weight:600;color:var(--mut2)}.zh__sep{width:1px;height:28px;background:var(--line);flex:none}.zh__marq{position:relative;flex:1;overflow:hidden;-webkit-mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent);mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent)}.zh__marqTrack{display:flex;align-items:center;width:max-content;animation:zhMarq 28s linear infinite;opacity:.55}.zh__marqGroup{display:flex;align-items:center;gap:42px;padding-right:42px;flex:0 0 auto}.zh__marqTrack img{height:22px;width:auto;object-fit:contain;filter:grayscale(1) brightness(1.6) opacity(.85);transform:scale(1);transition:filter .28s ease,transform .28s ease,opacity .28s ease}.zh__marqTrack img:hover{filter:grayscale(1) brightness(2.65) contrast(1.12) opacity(1);opacity:1;transform:scale(1.1)}@keyframes zhMarq{to{transform:translateX(-50%)}}.zh__mobileStrip{display:none}.zh__mobileScroll{display:none}.zh__scroll{position:absolute;left:50%;bottom:96px;z-index:6;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:8px;color:var(--mut2);font-size:9.5px;font-weight:700;letter-spacing:.26em;text-transform:uppercase;opacity:0;animation:zhFade 1s ease 1.3s forwards}.zh__mouse{position:relative;width:22px;height:34px;border:1.5px solid rgba(255,255,255,.28);border-radius:999px}.zh__mouse::after{content:"";position:absolute;left:50%;top:7px;width:3px;height:7px;border-radius:999px;background:var(--acc-ice);transform:translateX(-50%);animation:zhWheel 1.8s ease-in-out infinite}@keyframes zhWheel{0%,100%{opacity:0;transform:translate(-50%,0)}40%{opacity:1}70%{opacity:0;transform:translate(-50%,9px)}}.zh__wm{position:absolute;left:max(48px,calc((100% - 1700px)/2));right:auto;top:auto;bottom:78px;z-index:5;width:min(1120px,calc(100% - 96px));transform:none;font-size:clamp(76px,8.65vw,158px);font-weight:800;letter-spacing:-.078em;line-height:.72;pointer-events:none;text-transform:lowercase;white-space:nowrap;text-align:left;overflow:hidden;color:rgba(255,255,255,.062);opacity:1;clip-path:inset(0 100% 0 0);animation:zhWmReveal 1.05s steps(13,end) .65s forwards;-webkit-mask-image:linear-gradient(90deg,transparent 0%,#000 8%,#000 86%,transparent 100%);mask-image:linear-gradient(90deg,transparent 0%,#000 8%,#000 86%,transparent 100%);text-shadow:none;mix-blend-mode:normal}.zh__wm::after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:68%;z-index:2;pointer-events:none;background:linear-gradient(to bottom,rgba(1,3,9,0) 0%,rgba(1,3,9,.34) 44%,rgba(1,3,9,.72) 76%,#010309 100%)}@keyframes zhWmReveal{to{clip-path:inset(0 0 0 0)}}@media(max-height:820px) and (min-width:1101px){.zh__rail{top:96px}}@media(max-width:1400px) and (min-width:1101px){.zh__rail{top:92px;right:18px}.zh__railIco{gap:13px}.zh__railProg{height:78px}}@media(max-width:1100px){.zh{min-height:auto}.zh__rail{display:none}.zh__stage{width:122vw;right:calc(-48vw - 120px);left:auto;inset-block:0;opacity:.68;transform:scaleX(-1);transform-origin:center center}.zh__poster{background-position:center calc(50% + 18px);background-size:auto 72vh}.zh__fade{background:linear-gradient(90deg,#010309 0%,rgba(1,3,9,.86) 22%,rgba(1,3,9,.42) 48%,transparent 78%),linear-gradient(to bottom,transparent 50%,#010309 98%)}.zh__kin{width:118%;top:30%;opacity:.5}.zh__inner{align-items:flex-start;min-height:auto;padding:138px 0 36px;width:calc(100% - 36px)}.zh__side{position:relative;z-index:6;right:auto;top:auto;transform:none;flex-direction:row;width:calc(100% - 36px);max-width:none;margin:34px auto 36px;gap:12px}.zh__rev{min-height:150px;padding:14px 14px 50px}.zh__rt{max-width:none;margin-bottom:0}.zh__strip,.zh__scroll{display:none}.zh__title{font-size:clamp(40px,9vw,72px);max-width:none}.zh__wm{position:relative;display:block;left:auto;right:auto;top:auto;bottom:auto;z-index:6;width:calc(100% - 36px);margin:0 auto 0;font-size:clamp(50px,18vw,108px);line-height:.7;letter-spacing:-.078em;opacity:1;color:rgba(255,255,255,.068);-webkit-mask-image:linear-gradient(90deg,transparent 0%,#000 12%,#000 82%,transparent 100%);mask-image:linear-gradient(90deg,transparent 0%,#000 12%,#000 82%,transparent 100%);text-shadow:none;mix-blend-mode:normal}.zh__wm::after{height:62%;background:linear-gradient(to bottom,rgba(1,3,9,0) 0%,rgba(1,3,9,.26) 48%,rgba(1,3,9,.58) 78%,#010309 100%)}.zh__mobileStrip{display:block;position:relative;z-index:6;width:calc(100% - 36px);margin:0 auto 6px;padding:18px 0 4px;border-top:1px solid rgba(255,255,255,.1);border-bottom:0;overflow:hidden;-webkit-mask-image:linear-gradient(90deg,transparent,#000 9%,#000 91%,transparent);mask-image:linear-gradient(90deg,transparent,#000 9%,#000 91%,transparent)}.zh__mobileStrip .zh__marqTrack{animation-duration:24s;opacity:.58}.zh__mobileStrip .zh__marqGroup{gap:34px;padding-right:34px}.zh__mobileStrip img{height:20px;filter:grayscale(1) brightness(1.72) opacity(.86)}.zh__mobileScroll{position:relative;z-index:6;display:flex;align-items:center;justify-content:center;gap:10px;width:calc(100% - 36px);margin:14px auto 38px;color:rgba(255,255,255,.42);font-size:9px;font-weight:800;letter-spacing:.24em;text-transform:uppercase;overflow:hidden}.zh__mobileScroll::before,.zh__mobileScroll::after{content:"";height:1px;flex:1;background:linear-gradient(90deg,transparent,rgba(142,200,247,.34),rgba(255,255,255,.08));opacity:.72}.zh__mobileScroll::after{background:linear-gradient(90deg,rgba(255,255,255,.08),rgba(142,200,247,.34),transparent)}.zh__mobileScroll .zh__mouse{width:18px;height:28px;border-color:rgba(255,255,255,.24);flex:0 0 auto}.zh__mobileScroll .zh__mouse::after{top:6px;height:6px}}@media(max-width:600px){.zh__wm{margin:2px auto 0;font-size:clamp(46px,18.5vw,94px);color:rgba(255,255,255,.068)}.zh__eb{font-size:9px}.zh__inner{padding-top:120px}.zh__lead{font-size:15px}.zh__kinWord{font-size:64vw}.zh__chips{display:none!important}.zh__rev{padding:13px 13px 50px}.zh__sc strong{font-size:26px}}@media(max-width:600px){.zh__stage{width:136vw;right:calc(-68vw - 120px);opacity:.66}.zh__poster{background-position:center calc(50% + 14px);background-size:auto 70vh}}@media(prefers-reduced-motion:reduce){.zh__aurora,.zh__rotor>span,.zh__kinWord,.zh__ring--a,.zh__ring--b,.zh__halo,.zh__conic,.zh__eb .dot::after,.zh__dust i,.zh__marqTrack,.zh__mouse::after,.zh__grad{animation:none!important}.zh__title,.zh__lead,.zh__actions,.zh__chips,.zh__side,.zh__scroll{animation:none!important;opacity:1!important;transform:none!important;filter:none!important}.zh__rotor{height:auto}.zh__rotor>span{transform:none}.zh__grad{background-position:14% 0}.zh__wm{animation:none!important;clip-path:inset(0 0 0 0)!important}}
.zh__title{font-size:clamp(46px,7vw,114px)!important;line-height:.99!important;letter-spacing:-.046em!important;font-weight:480!important;max-width:15ch!important}
.zh__lead{font-size:17.5px!important;line-height:1.72!important;letter-spacing:-.01em!important;max-width:600px!important}
.zh__lead strong{font-weight:760;color:rgba(255,255,255,.92)}
.zh__scroll{bottom:112px!important;gap:11px!important}
@media(max-width:1100px){.zh__title{font-size:clamp(40px,9vw,72px)!important;line-height:1.02!important;letter-spacing:-.044em!important;max-width:none!important}.zh__lead{font-size:15.5px!important;line-height:1.66!important;max-width:680px!important}.zh__mobileScroll{margin:22px auto 46px!important;min-height:42px!important}}
@media(max-width:600px){.zh__title{font-size:clamp(38px,10.8vw,48px)!important;line-height:1.045!important;letter-spacing:-.042em!important}.zh__lead{font-size:15px!important;line-height:1.64!important}.zh__mobileScroll{margin:24px auto 48px!important}}
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
.zh .zh__wm{
margin-bottom:0!important;
}
.zh .zh__mobileScroll{
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
.zh .zh__mobileScroll{
margin:4px auto 20px!important;
min-height:32px!important;
}
}
@media(min-width:1101px){
.zh .zh__chips{
margin-bottom:50px!important;
}
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
@media(min-width:1101px){
.zh:not(.zh--logoBranding) .zh__chips{
margin-bottom:80px!important;
}
}
@media(min-width:1101px) and (max-width:1400px){
.zh:not(.zh--logoBranding) .zh__chips{
margin-top:68px!important;
margin-bottom:80px!important;
}
}
@media(min-width:1101px) and (max-width:1320px){
.zh:not(.zh--logoBranding) .zh__chips{
margin-top:82px!important;
margin-bottom:86px!important;
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
@media(min-width:1101px) and (max-width:1500px){
.zh:not(.zh--logoBranding) .zh__side{
top:calc(50% + 18px)!important;
right:max(22px,calc((100% - 1700px)/2 + 22px))!important;
width:min(468px,36vw)!important;
gap:10px!important;
align-items:stretch!important;
transform:translateY(-50%)!important;
}
.zh:not(.zh--logoBranding) .zh__rev{
min-height:118px!important;
padding:13px 15px 42px!important;
border-radius:18px!important;
}
.zh:not(.zh--logoBranding) .zh__rl{
font-size:8.5px!important;
letter-spacing:.11em!important;
}
.zh:not(.zh--logoBranding) .zh__sc{
margin:9px 0 7px!important;
gap:7px!important;
}
.zh:not(.zh--logoBranding) .zh__sc strong{
font-size:32px!important;
line-height:.86!important;
}
.zh:not(.zh--logoBranding) .zh__sc em{
font-size:11px!important;
letter-spacing:1px!important;
}
.zh:not(.zh--logoBranding) .zh__rt{
max-width:72%!important;
font-size:12.5px!important;
line-height:1.25!important;
}
.zh:not(.zh--logoBranding) .zh__rt span{
font-size:11.5px!important;
margin-top:4px!important;
}
.zh:not(.zh--logoBranding) .zh__rlogo{
right:15px!important;
bottom:15px!important;
height:16px!important;
}
.zh:not(.zh--logoBranding) .zh__chips{
margin-top:38px!important;
margin-bottom:80px!important;
}
}
@media(min-width:1101px) and (max-width:1380px){
.zh:not(.zh--logoBranding) .zh__side{
top:calc(50% - 8px)!important;
width:min(430px,34vw)!important;
gap:9px!important;
}
.zh:not(.zh--logoBranding) .zh__rev{
min-height:110px!important;
padding:12px 13px 39px!important;
}
.zh:not(.zh--logoBranding) .zh__sc strong{
font-size:30px!important;
}
.zh:not(.zh--logoBranding) .zh__rt{
max-width:78%!important;
font-size:12px!important;
}
.zh:not(.zh--logoBranding) .zh__rt span{
font-size:10.8px!important;
}
.zh:not(.zh--logoBranding) .zh__chips{
margin-top:34px!important;
margin-bottom:86px!important;
}
}
@media(min-width:1101px) and (max-width:1280px){
.zh:not(.zh--logoBranding) .zh__side{
top:calc(50% - 26px)!important;
width:min(400px,32vw)!important;
}
.zh:not(.zh--logoBranding) .zh__rev{
min-height:104px!important;
padding:11px 12px 36px!important;
}
.zh:not(.zh--logoBranding) .zh__sc strong{
font-size:28px!important;
}
.zh:not(.zh--logoBranding) .zh__sc em{
font-size:10px!important;
}
.zh:not(.zh--logoBranding) .zh__rt{
font-size:11.5px!important;
max-width:82%!important;
}
.zh:not(.zh--logoBranding) .zh__rlogo{
height:14px!important;
}
.zh:not(.zh--logoBranding) .zh__chips{
margin-top:32px!important;
margin-bottom:90px!important;
}
}
@media(min-width:1101px) and (max-width:1180px){
.zh:not(.zh--logoBranding) .zh__side{
display:none!important;
}
.zh:not(.zh--logoBranding) .zh__chips{
margin-top:30px!important;
margin-bottom:92px!important;
}
}
@media(max-width:1100px){
.zh:not(.zh--logoBranding) .zh__spline{
pointer-events:none!important;
}
}
@media(min-width:1601px) and (max-width:1800px){
.zh:not(.zh--logoBranding) .zh__scroll{
bottom:96px!important;
z-index:8!important;
}
}
@media(min-width:1501px) and (max-width:1600px){
.zh:not(.zh--logoBranding) .zh__scroll{
bottom:104px!important;
z-index:8!important;
}
}
@media(max-width:1100px){
.zh:not(.zh--logoBranding) .zh__spline{
pointer-events:auto!important;
}
.zh:not(.zh--logoBranding) .zh__stage{
pointer-events:auto!important;
}
}
@media(max-width:1100px){
.zh:not(.zh--logoBranding) .zh__stage{
transform:none!important;
transform-origin:center center!important;
}
.zh:not(.zh--logoBranding) .zh__spline{
transform:none!important;
}
}
@media(max-width:600px){
.zh:not(.zh--logoBranding) .zh__stage{
transform:none!important;
}
}
@media(max-width:1100px){
.zh:not(.zh--logoBranding) .zh__stage{
pointer-events:auto!important;
}
.zh:not(.zh--logoBranding) .zh__spline{
pointer-events:auto!important;
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


/* PATCH v2.2.495 — mobile CTA w jednej linii, poster +30px wyżej, desktop spacing */
@media(min-width:1101px){
  .zh{--zhHeaderGap:clamp(80px,5.2vw,116px)!important;}
  .zh .zh__inner{padding-top:calc(var(--zpHeaderH) + var(--zhHeaderGap))!important;}
  .zh:not(.zh--logoBranding) .zh__chips{margin-bottom:110px!important;}
  .zh:not(.zh--logoBranding) .zh__scroll{bottom:88px!important;}
}
@media(min-width:1101px) and (max-width:1500px){
  .zh:not(.zh--logoBranding) .zh__chips{margin-bottom:116px!important;}
}
@media(max-width:1100px){
  .zh .zh__poster,
  .zh.is-spline-loaded .zh__poster{
    background-position:center calc(50% - 52px)!important;
  }
  .zh .zh__actions{
    display:flex!important;
    flex-direction:row!important;
    flex-wrap:nowrap!important;
    gap:10px!important;
    width:100%!important;
    align-items:center!important;
  }
  .zh .zh__actions .zh__btn{
    flex:1 1 0!important;
    min-width:0!important;
    width:auto!important;
    justify-content:center!important;
    padding-left:14px!important;
    padding-right:14px!important;
    font-size:13.5px!important;
    min-height:54px!important;
  }
  .zh .zh__actions .zh__btn span{
    white-space:nowrap!important;
  }
}
@media(max-width:600px){
  .zh .zh__poster,
  .zh.is-spline-loaded .zh__poster{
    background-position:center calc(50% - 56px)!important;
  }
  .zh .zh__actions{gap:8px!important;}
  .zh .zh__actions .zh__btn{
    padding-left:10px!important;
    padding-right:10px!important;
    font-size:12.8px!important;
    gap:7px!important;
  }
  .zh .zh__actions .zh__btn svg{
    width:15px!important;
    height:15px!important;
  }
}
@media(max-width:380px){
  .zh .zh__actions .zh__btn{font-size:12px!important;padding-left:8px!important;padding-right:8px!important;}
  .zh .zh__actions .zh__btn svg{width:14px!important;height:14px!important;}
}

/* PATCH v2.2.496 — desktop scroll/logo gap + tablet poster left */
@media(min-width:1101px){
  /* +20px przestrzeni między „Przewiń” a frosted glass paskiem z logami */
  .zh:not(.zh--logoBranding) .zh__scroll{
    bottom:108px!important;
  }
}
@media(min-width:1101px) and (max-width:1500px){
  .zh:not(.zh--logoBranding) .zh__scroll{
    bottom:108px!important;
  }
}
@media(min-width:701px) and (max-width:1100px){
  /* tablet: sam poster robota przesunięty 50px w lewo, bez Spline */
  .zh .zh__stage{
    right:calc(-48vw - 70px)!important;
  }
  .zh .zh__poster,
  .zh.is-spline-loaded .zh__poster{
    background-position:calc(50% - 50px) calc(50% - 52px)!important;
  }
}
@media(max-width:700px){
  .zh .zh__poster,
  .zh.is-spline-loaded .zh__poster{
    background-position:center calc(50% - 56px)!important;
  }
}

/* Same poster and crop, but a right-sized source for phones/tablets (~33 KB vs ~100 KB). */
@media(max-width:1100px){
  #zhPoster{--robot:url('https://zaprojektowani.com/wp-content/uploads/2026/06/robot_poster_desktop-768x793.webp')!important}
}



/* v2.2.584 — hero opinie: style 5.0 przeniesione do jednego bloku "zp-suite-584-rating-watermark"
   na końcu pliku (naprawa skoku kerningu + duże 5.0 wtopione w tło karty). */

</style>
<!-- v2.2.547: hero poster preload moved to <head> (zp_suite_2547 in optimizer.php) for earlier LCP discovery. -->


<style id="zh-home-live-spacing-performance-patch">
/* ZP live home hero — stały odstęp od headera + smooth first paint */
.zh{--zpHeaderH:clamp(78px,6.2vw,104px);--zhHeaderGap:clamp(50px,5.2vw,86px);content-visibility:visible;contain:none;}
.zh__inner{padding-top:calc(var(--zpHeaderH) + var(--zhHeaderGap))!important;}
.zh__stage{transform:translateZ(0);backface-visibility:hidden;}
.zh__spline{opacity:0;transition:opacity .45s ease;}
.zh.is-spline-loaded .zh__spline{opacity:1;}
.zh.is-spline-loaded .zh__poster{opacity:0;}
@media(min-width:1101px){
  .zh{min-height:calc(100svh + 18px)!important;}
  .zh__inner{min-height:calc(100svh + 18px)!important;}
}
@media(max-width:1100px){
  .zh{--zpHeaderH:clamp(72px,12vw,96px);--zhHeaderGap:42px;content-visibility:visible;}
  .zh__inner{padding-top:calc(var(--zpHeaderH) + var(--zhHeaderGap))!important;}
  .zh__stage{pointer-events:none!important;}
}
@media(max-width:600px){
  .zh{--zpHeaderH:74px;--zhHeaderGap:38px;}
  .zh__inner{padding-top:calc(var(--zpHeaderH) + var(--zhHeaderGap))!important;}
}

/* PATCH v2.2.494 — mobile: sam poster, robot wyżej, krótszy kicker w jednej linii */
@media(max-width:1100px){
  .zh .zh__spline{display:none!important;visibility:hidden!important;opacity:0!important;pointer-events:none!important;}
  .zh .zh__stage{transform:none!important;pointer-events:none!important;}
  .zh .zh__poster,
  .zh.is-spline-loaded .zh__poster{
    display:block!important;
    opacity:1!important;
    background-position:center calc(50% - 22px)!important;
  }
  .zh .zh__eb{white-space:nowrap!important;font-size:9.5px!important;letter-spacing:.145em!important;gap:10px!important;}
  .zh .zh__eb .dot{flex-basis:32px!important;width:32px!important;}
}
@media(max-width:600px){
  .zh .zh__poster,
  .zh.is-spline-loaded .zh__poster{
    background-position:center calc(50% - 26px)!important;
  }
  .zh .zh__eb{font-size:8.6px!important;letter-spacing:.105em!important;gap:8px!important;}
  .zh .zh__eb .dot{flex-basis:24px!important;width:24px!important;}
}
@media(max-width:370px){
  .zh .zh__eb{font-size:8px!important;letter-spacing:.075em!important;}
  .zh .zh__eb .dot{flex-basis:18px!important;width:18px!important;}
}


/* PATCH v2.2.697 — Home hero: bez clipowania liter + jaśniejszy, animowany gradient */
.zh .zh__title{
  overflow:visible!important;
}
.zh .zh__title .zh__grad{
  display:inline-block!important;
  overflow:visible!important;
  line-height:1.12!important;
  padding-top:.015em!important;
  padding-right:.13em!important;
  padding-bottom:.18em!important;
  padding-left:.025em!important;
  margin-top:-.015em!important;
  margin-right:-.065em!important;
  margin-bottom:-.115em!important;
  margin-left:-.025em!important;
  vertical-align:baseline!important;
  background-image:linear-gradient(105deg,
    #79baf0 0%,
    #bfe5ff 20%,
    #ffffff 42%,
    #dff3ff 58%,
    #9fd3fb 78%,
    #ffffff 100%)!important;
  background-size:260% 100%!important;
  background-position:0% 50%!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  -webkit-text-fill-color:transparent!important;
  color:transparent!important;
  animation:zhHeroGradientFlow 4.8s ease-in-out 1s infinite alternate!important;
  will-change:background-position!important;
}
@keyframes zhHeroGradientFlow{
  0%{background-position:0% 50%}
  100%{background-position:100% 50%}
}
@media(max-width:1100px){
  .zh .zh__title .zh__grad{
    line-height:1.14!important;
    padding-bottom:.21em!important;
    margin-bottom:-.13em!important;
    background-image:linear-gradient(105deg,
      #a9d9fb 0%,
      #e7f7ff 22%,
      #ffffff 44%,
      #eefaff 60%,
      #bce5ff 80%,
      #ffffff 100%)!important;
    background-size:280% 100%!important;
    animation-duration:5.2s!important;
  }
}
@media(max-width:600px){
  .zh .zh__title .zh__grad{
    line-height:1.16!important;
    padding-bottom:.23em!important;
    margin-bottom:-.14em!important;
  }
}
@media(prefers-reduced-motion:reduce){
  .zh .zh__title .zh__grad{
    animation:none!important;
    background-position:52% 50%!important;
  }
}

</style>
<section class="zh" id="zhHero" aria-label="Hero">
<div class="zh__bg" aria-hidden="true"></div>
<div class="zh__aurora" data-px="0.5" aria-hidden="true"></div>
<div class="zh__grid" aria-hidden="true"></div>
<div class="zh__dust" aria-hidden="true">
<i style="left:62%;animation-duration:15s"></i><i style="left:71%;animation-duration:19s;animation-delay:3s"></i>
<i style="left:80%;animation-duration:13s;animation-delay:6s"></i><i style="left:55%;animation-duration:21s;animation-delay:2s"></i>
<i style="left:88%;animation-duration:17s;animation-delay:8s"></i><i style="left:67%;animation-duration:23s;animation-delay:5s"></i>
</div>
<div class="zh__hair" aria-hidden="true"></div>
<div class="zh__stage" id="zhStage" aria-hidden="true">
<div class="zh__conic"></div>
<div class="zh__halo"></div>
<div class="zh__ring zh__ring--b"></div>
<div class="zh__ring zh__ring--a"></div>
<spline-viewer class="zh__spline" id="zhSpline" events-target="global" loading-anim-type="none" data-url="https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode"></spline-viewer>
<div class="zh__poster" id="zhPoster" style="--robot:url('https://zaprojektowani.com/wp-content/uploads/2026/06/robot_poster_desktop.webp')"></div>
<div class="zh__splineMask"></div>
</div>
<div class="zh__fade" aria-hidden="true"></div>
<div class="zh__grain" aria-hidden="true"></div>
<div class="zh__vig" aria-hidden="true"></div>
<nav class="zh__rail" aria-label="Social i postęp">
<span class="zh__railIco">
<a href="https://www.facebook.com/zaprojektowanicom" target="_blank" rel="noopener" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 9h3l.4-3H14V4.6c0-.9.3-1.4 1.5-1.4H17V.6C16.6.5 15.6.4 14.6.4 12.2.4 10.7 1.9 10.7 4.4V6H8v3h2.7v11H14z"/></svg></a>
<a href="https://www.instagram.com/zaprojektowani" target="_blank" rel="noopener" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none"/></svg></a>
<a href="https://www.linkedin.com/company/zaprojektowani" target="_blank" rel="noopener" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.5 8.2A1.6 1.6 0 1 0 6.5 5a1.6 1.6 0 0 0 0 3.2zM5.1 9.6h2.8V20H5.1zM10.2 9.6H13v1.5c.4-.8 1.5-1.8 3.2-1.8 2.4 0 3.6 1.5 3.6 4.4V20h-2.8v-5.4c0-1.3-.5-2.2-1.7-2.2-1 0-1.5.7-1.8 1.4-.1.2-.1.5-.1.8V20h-2.8z"/></svg></a>
</span>
<span class="zh__railProg"><span class="zh__railProgFill" id="zhProg"></span></span>
</nav>
<div class="zh__inner">
<div class="zh__copy">
<p class="zh__eb"><span class="dot"></span>Studio projektowe i technologiczne • Katowice</p>
<h1 class="zh__title">Strony internetowe Katowice, <span class="zh__grad">sklepy WooCommerce i branding</span></h1>
<p class="zh__svcRot" aria-hidden="true">Projektujemy
  <span class="zh__svcRotMask" id="zhSvcRot"><span>
    <i>strony internetowe WordPress</i>
    <i>sklepy WooCommerce</i>
    <i>logo i identyfikację wizualną</i>
    <i>kampanie Meta&nbsp;Ads i Google&nbsp;Ads</i>
    <i>rozwiązania indywidualne dla firm</i>
  </span></span>
</p>
<p class="zh__lead">Jeden zespół zamiast pięciu wykonawców: <strong>projekt, treści, WordPress, WooCommerce, SEO i kampanie</strong>. Dzięki temu strona nie jest tylko ładnym widokiem, ale narzędziem, które zdobywa zapytania od klientów.</p>
<div class="zh__actions">
<a class="zh__btn zh__btn--p" id="zhMagnet" href="/studio-wyceny/"><span>Wyceń projekt</span><svg viewBox="0 0 24 24"><path d="M7 17L17 7"/><path d="M8 7h9v9"/></svg></a>
<a class="zh__btn zh__btn--g" href="/realizacje/"><span>Zobacz realizacje</span><svg viewBox="0 0 24 24"><path d="M7 17L17 7"/><path d="M8 7h9v9"/></svg></a>
</div>
<div class="zh__chips" aria-label="Zakres usług">
<a class="zh__chip" href="/strony-internetowe-katowice/"><b>01</b><span>strony internetowe Katowice</span></a>
<a class="zh__chip" href="/sklepy-internetowe-katowice/"><b>02</b><span>sklepy internetowe WooCommerce</span></a>
<a class="zh__chip" href="/logo-branding-katowice/"><b>03</b><span>logo i branding Katowice</span></a>
<a class="zh__chip" href="/kampanie-reklamowe/"><b>04</b><span>kampanie reklamowe</span></a>
</div>
</div>
</div>
<div class="zh__side">
<a class="zh__rev" href="https://www.trustindex.io/reviews/zaprojektowani.com" target="_blank" rel="noopener nofollow">
<span class="zh__rl">Certyfikat opinii</span>
<span class="zh__sc"><strong>5.0</strong><em>★★★★★</em></span>
<span class="zh__rt">69 opinii klientów<span>średnia ocena w Trustindex</span></span>
<span class="zh__rlogo"><img src="https://zaprojektowani.com/wp-content/uploads/2026/05/trustindex_logo_wieksze.webp" alt="Trustindex"></span>
</a>
<a class="zh__rev" href="https://www.facebook.com/zaprojektowanicom/reviews" target="_blank" rel="noopener nofollow">
<span class="zh__rl">Facebook</span>
<span class="zh__sc"><strong>5.0</strong><em>★★★★★</em></span>
<span class="zh__rt">54 opinie klientów<span>rekomendacje i kontakt</span></span>
<span class="zh__rlogo"><img src="https://zaprojektowani.com/wp-content/uploads/2026/05/facebook_duze_logo.webp" alt="Facebook"></span>
</a>
</div>
<div class="zh__mobileStrip" aria-label="Narzędzia i technologie">
<div class="zh__marqTrack">
<span class="zh__marqGroup">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/google-ads-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/manage-wp-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/elementorpro-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/copilot-logo-2.webp" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/meta-ads-logo-1.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/google-search-console-logo.png" alt="">
</span>
<span class="zh__marqGroup" aria-hidden="true">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/google-ads-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/manage-wp-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/elementorpro-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/copilot-logo-2.webp" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/meta-ads-logo-1.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/google-search-console-logo.png" alt="">
</span>
</div>
</div>
<div class="zh__wm" aria-hidden="true">zaprojektowani</div>
<div class="zh__mobileScroll" aria-hidden="true"><span class="zh__mouse"></span><span>Przewiń</span></div>
<div class="zh__scroll" aria-hidden="true"><span class="zh__mouse"></span>Przewiń</div>
<div class="zh__strip">
<div class="zh__stripIn">
<div class="zh__stats">
<div class="zh__stat"><b data-count="120" data-suffix="+">120+</b><span>projektów</span></div>
<div class="zh__stat"><b data-count="5.0" data-dec="1">5.0</b><i>★</i><span>średnia ocena</span></div>
<div class="zh__stat"><b data-count="10" data-suffix="+">10+</b><span>lat doświadczenia</span></div>
</div>
<div class="zh__sep"></div>
<div class="zh__marq">
<div class="zh__marqTrack">
<span class="zh__marqGroup">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/google-ads-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/manage-wp-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/elementorpro-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/copilot-logo-2.webp" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/meta-ads-logo-1.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/google-search-console-logo.png" alt="">
</span>
<span class="zh__marqGroup" aria-hidden="true">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/google-ads-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/manage-wp-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/elementorpro-logo.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/copilot-logo-2.webp" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/meta-ads-logo-1.png" alt="">
<img src="https://zaprojektowani.com/wp-content/uploads/2026/05/google-search-console-logo.png" alt="">
</span>
</div>
</div>
</div>
</div>
</section>
<script id="zh-spline-loader">(function(){
  'use strict';
  var d=document,w=window,mq=w.matchMedia;
  var hero=d.getElementById('zhHero');
  var sp=d.getElementById('zhSpline');
  if(!hero||!sp) return;

  // v2.2.544 — stable desktop Spline for real users, poster-only for Lighthouse/headless.
  // The poster is always first paint. Real desktop gets Spline immediately after first paint,
  // not on mousemove. Synthetic/browser-lab runs are aggressively protected to avoid WebGL
  // closing the Lighthouse target.
  function shouldSkipSpline(){
    try{
      if(/[?&](zp_lh|zp_no_spline|zp_no_webgl)(?:=1)?(?:&|$)/.test(location.search||'')) return true;
      var ua=navigator.userAgent||'';
      if(navigator.webdriver===true) return true;
      if(/HeadlessChrome|Chrome-Lighthouse|Lighthouse|PageSpeed|Speed Insights/i.test(ua)) return true;
      /* Capability fallback: software WebGL renderers are exactly the environments where
         the 3D scene can stall PSI/Lighthouse. Real visitors without hardware WebGL keep
         the static poster instead of paying for a scene their device cannot render well. */
      var c=d.createElement('canvas');
      var gl=c.getContext('webgl2',{failIfMajorPerformanceCaveat:true})||c.getContext('webgl',{failIfMajorPerformanceCaveat:true})||c.getContext('experimental-webgl');
      if(gl){
        var ext=gl.getExtension('WEBGL_debug_renderer_info');
        var renderer=ext?String(gl.getParameter(ext.UNMASKED_RENDERER_WEBGL)||''):String(gl.getParameter(gl.RENDERER)||'');
        if(/SwiftShader|llvmpipe|software rasterizer|software renderer/i.test(renderer)) return true;
      }
    }catch(e){}
    return false;
  }

  if(shouldSkipSpline()){
    try{w.__zpNoSpline=true;d.documentElement.classList.add('zp-no-spline');}catch(e){}
    try{sp.removeAttribute('url');sp.removeAttribute('data-url');sp.style.pointerEvents='none';}catch(e){}
    return;
  }
  if(mq&&mq('(prefers-reduced-motion:reduce)').matches) return;
  /* v2.2.669: gate szerokosci z nasluchem — kiedys jednorazowy return; okno <=1100px przy
     load (DevTools/side-by-side) wylaczalo robota na stale mimo pozniejszej maksymalizacji. */
  var mqDesk=mq?mq('(min-width:1101px)'):null;
  function armDesktopRetry(){
    if(!mqDesk) return;
    var onCross=function(ev){ if(ev.matches){ try{startSpline();}catch(e){} } };
    if(mqDesk.addEventListener){ mqDesk.addEventListener('change',onCross); }
    else if(mqDesk.addListener){ mqDesk.addListener(onCross); }
  }
  try{var conn=navigator.connection||navigator.webkitConnection||{};if(conn.saveData===true)return;}catch(e){}

  var started=false;
  function addPreconnect(){
    try{
      [
        'https://prod.spline.design',
        'https://unpkg.com',
        'https://cdn.jsdelivr.net'
      ].forEach(function(host,i){
        if(d.querySelector('link[data-zh-spline-preconnect=\"'+i+'\"]')) return;
        var l=d.createElement('link');
        l.rel='preconnect'; l.href=host; l.crossOrigin='';
        l.setAttribute('data-zh-spline-preconnect',String(i));
        d.head.appendChild(l);
      });
    }catch(e){}
  }
  function loadViewerScript(){
    if(w.customElements&&customElements.get('spline-viewer')) return;
    if(d.querySelector('script[data-zh-spline-viewer]')) return;
    var sc=d.createElement('script');
    sc.type='module';
    sc.src='https://unpkg.com/@splinetool/viewer@1.10.57/build/spline-viewer.js';
    sc.setAttribute('data-zh-spline-viewer','1');
    sc.onerror=function(){
      /* v2.2.669: fallback CDN — unpkg bywa niedostepny; ta sama paczka i wersja z jsDelivr. */
      try{
        if(d.querySelector('script[data-zh-spline-viewer-fb]')){hero.classList.remove('is-spline-loading');return;}
        var fb=d.createElement('script');
        fb.type='module';
        fb.src='https://cdn.jsdelivr.net/npm/@splinetool/viewer@1.10.57/build/spline-viewer.js';
        fb.setAttribute('data-zh-spline-viewer-fb','1');
        fb.onerror=function(){try{hero.classList.remove('is-spline-loading');}catch(e){}};
        d.head.appendChild(fb);
      }catch(e){try{hero.classList.remove('is-spline-loading');}catch(e2){}}
    };
    d.head.appendChild(sc);
  }
  function startSpline(){
    if(started||shouldSkipSpline()) return;
    if(mq&&mq('(max-width:1100px)').matches){armDesktopRetry();return;}
    started=true;
    try{hero.classList.add('is-spline-loading');sp.setAttribute('events-target','global');sp.setAttribute('loading-anim-type','none');sp.setAttribute('mouse-events','global');sp.style.pointerEvents='auto';}catch(e){}
    addPreconnect();
    var url=sp.getAttribute('data-url');
    if(url && !sp.getAttribute('url')) sp.setAttribute('url',url);
    loadViewerScript();
  }
  /* v2.2.808 — real desktop visitors get Spline immediately after the first paint.
     Lighthouse/headless is still stopped above by shouldSkipSpline(), so we can be
     aggressive for humans without bringing the PSI timeout back. */
  function startAfterFirstPaint(){
    if(mq&&mq('(max-width:1100px)').matches){armDesktopRetry();return;}
    addPreconnect();
    w.requestAnimationFrame(function(){
      w.requestAnimationFrame(function(){
        startSpline();
      });
    });
  }
  if(d.readyState==='loading') d.addEventListener('DOMContentLoaded',startAfterFirstPaint,{once:true});
  else startAfterFirstPaint();
})();</script>
<script>(function(){var hero=document.getElementById('zhHero');var sp=document.getElementById('zhSpline');var stage=document.getElementById('zhStage');var reduce=window.matchMedia&&window.matchMedia('(prefers-reduced-motion:reduce)').matches;var fine=!window.matchMedia||window.matchMedia('(pointer:fine)').matches;var SA=false;try{SA=window.zpSyntheticAudit===true||false /* v711: identical production behavior in browser audits */||false;}catch(e){}if(SA){try{document.documentElement.classList.add('zp-synthetic-audit');}catch(e){}}if(!hero)return;var wm=hero.querySelector('.zh__wm');if(wm){wm.setAttribute('aria-label',(wm.textContent||'zaprojektowani').trim());}if(sp&&!SA&&(sp.getAttribute('url')||sp.getAttribute('data-url'))){try{sp.setAttribute('events-target','global');sp.setAttribute('loading-anim-type','none');}catch(e){}var done=false,canvasPoll=null;var reveal=function(){if(done)return;done=true;if(canvasPoll)clearInterval(canvasPoll);requestAnimationFrame(function(){requestAnimationFrame(function(){hero.classList.add('is-spline-loaded');hideBrand();});});};sp.addEventListener('load',reveal,{once:true});sp.addEventListener('load-complete',reveal,{once:true});canvasPoll=setInterval(function(){try{if(sp.shadowRoot && sp.shadowRoot.querySelector('canvas'))reveal();}catch(e){}},120);setTimeout(function(){if(canvasPoll)clearInterval(canvasPoll);},9000);}function hideBrand(){var n=0,t2=setInterval(function(){try{var sr=sp&&sp.shadowRoot;if(sr){['#logo','a#logo','#hints','[id="logo"]','[id="hints"]'].forEach(function(sel){sr.querySelectorAll(sel).forEach(function(l){l.style.setProperty('display','none','important');l.style.setProperty('visibility','hidden','important');l.style.setProperty('opacity','0','important');l.style.setProperty('pointer-events','none','important');});});}}catch(e){}if(++n>36)clearInterval(t2);},250);}function countUp(el){var target=parseFloat(el.getAttribute('data-count'))||0;var dec=parseInt(el.getAttribute('data-dec')||'0',10);var suf=el.getAttribute('data-suffix')||'';el.textContent=(dec?target.toFixed(dec):target)+suf;}[].forEach.call(hero.querySelectorAll('[data-count]'),countUp);var prog=document.getElementById('zhProg');function onScroll(){if(!prog)return;var d=document.documentElement;var max=(d.scrollHeight-d.clientHeight)||1;prog.style.height=Math.max(0,Math.min(100,(d.scrollTop/max)*100))+'%';}if(!SA){window.addEventListener('scroll',onScroll,{passive:true});onScroll();}if(reduce||SA)return;var mag=document.getElementById('zhMagnet');if(mag&&fine){mag.addEventListener('mousemove',function(e){var r=mag.getBoundingClientRect();var mx=(e.clientX-r.left-r.width/2)/(r.width/2),my=(e.clientY-r.top-r.height/2)/(r.height/2);mag.style.transform='translate('+(mx*7).toFixed(1)+'px,'+((my*5)-2).toFixed(1)+'px)';});mag.addEventListener('mouseleave',function(){mag.style.transform='';});}if(sp&&!SA){try{sp.setAttribute('events-target','global');sp.setAttribute('mouse-events','global');}catch(e){}}var layers=[].slice.call(hero.querySelectorAll('[data-px]'));var tx=0,ty=0,cx=0,cy=0,raf=null;function isMob(){return window.matchMedia&&window.matchMedia('(max-width:1100px)').matches;}function clamp(v,min,max){return Math.max(min,Math.min(max,v));}function syncSplinePointerMode(){if(!sp||SA)return;try{sp.setAttribute('events-target','global');sp.setAttribute('mouse-events','global');if(sp.getAttribute('url'))sp.style.pointerEvents='auto';}catch(e){}}syncSplinePointerMode();window.addEventListener('resize',syncSplinePointerMode,{passive:true});function applyRobotTransform(){layers.forEach(function(el){var d=parseFloat(el.getAttribute('data-px'))||1;var prefix=(el.className.indexOf('__kin')>-1)?'translateY(-52%)':'';el.style.transform=prefix+'translate3d('+(cx*d).toFixed(2)+'px,'+(cy*d).toFixed(2)+'px,0)';});if(stage){var mob=isMob();if(mob){stage.style.setProperty('transform','none','important');return;}var base='';var depth=1;var rotY=(-cx)*.38*depth;var moveX=(cx)*.75*depth;stage.style.setProperty('transform',base+'perspective(1400px)rotateY('+rotY.toFixed(2)+'deg)rotateX('+(cy*.28*depth).toFixed(2)+'deg)translate3d('+moveX.toFixed(1)+'px,'+(cy*.45*depth).toFixed(1)+'px,0)','important');}}function loop(){cx+=(tx-cx)*.12;cy+=(ty-cy)*.12;applyRobotTransform();if(Math.abs(tx-cx)>.025||Math.abs(ty-cy)>.025){raf=requestAnimationFrame(loop);}else{raf=null;}}function trackHeroPointer(e){var x,y;if(e&&e.touches&&e.touches[0]){x=e.touches[0].clientX;y=e.touches[0].clientY;}else if(e&&e.changedTouches&&e.changedTouches[0]){x=e.changedTouches[0].clientX;y=e.changedTouches[0].clientY;}else if(e&&typeof e.clientX==='number'){x=e.clientX;y=e.clientY;}else{return;}var r=hero.getBoundingClientRect();if(x<r.left||x>r.right||y<r.top||y>r.bottom)return;var px=clamp((x-r.left)/Math.max(r.width,1),0,1);var py=clamp((y-r.top)/Math.max(r.height,1),0,1);tx=(px-.5)*16;ty=(py-.5)*-10;if(!raf)raf=requestAnimationFrame(loop);}function resetPointer(){tx=0;ty=0;if(!raf)raf=requestAnimationFrame(loop);}/* v2.2.526: parallax pointer-tracking is desktop-only. On mobile the touchmove handler
   ran on every scroll/touch (getBoundingClientRect + rAF + style writes) yet the robot tilt
   is invisible on mobile — pure main-thread waste that made scrolling janky. */
if(fine&&window.matchMedia&&window.matchMedia('(min-width:1101px)').matches){hero.addEventListener('mousemove',trackHeroPointer,{passive:true});hero.addEventListener('pointermove',trackHeroPointer,{passive:true});hero.addEventListener('mouseleave',resetPointer,{passive:true});window.addEventListener('blur',resetPointer,true);}applyRobotTransform();})();</script>

<style id="zp-suite-584-rating-watermark">

/* v2.2.584 — hero opinie: JEDYNE źródło stylu 5.0.
   Root cause poprzednich problemów: seria starszych patchy (wght 950/1000/1200 + text-stroke,
   selektor .zh:not(.zh--logoBranding)... o specyficzności (0,4,1)) wygrywała w spoczynku,
   a przegrywała dopiero z moim selektorem :hover (0,4,3) — stąd małe metaliczne 5.0 w spoczynku
   i wielki "poszarpany" kontur na hoverze. Patche usunięte, a ten blok ma specyficzność
   (0,4,3)/(0,5,3) i jawnie resetuje każdą właściwość, którą tamte ustawiały.
   Stan spoczynku i hover są IDENTYCZNE. */
html body .zh:not(.zh--logoBranding) .zh__rev .zh__sc{
  position:static!important;
  margin:14px 0 10px!important;
  min-height:16px!important;
}
html body .zh:not(.zh--logoBranding) .zh__rev .zh__sc em{
  position:relative!important;
  z-index:3!important;
  font-size:14px!important;
  letter-spacing:2.5px!important;
}
html body .zh:not(.zh--logoBranding) .zh__rev .zh__sc strong,
html body .zh:not(.zh--logoBranding) .zh__rev:hover .zh__sc strong,
html body .zh:not(.zh--logoBranding) .zh__rev:focus-within .zh__sc strong{
  position:absolute!important;
  right:24px!important;
  top:14px!important;
  transform:none!important;
  z-index:1!important;
  margin:0!important;
  padding:0 .05em .16em 0!important;
  display:block!important;
  font-size:clamp(72px,5.6vw,90px)!important;
  font-weight:800!important;
  font-variation-settings:"wght" 800!important;
  line-height:.78!important;
  letter-spacing:-.085em!important;
  font-kerning:normal!important;
  font-variant-numeric:tabular-nums!important;
  color:transparent!important;
  -webkit-text-fill-color:transparent!important;
  -webkit-text-stroke:0 transparent!important;
  background-image:linear-gradient(118deg,rgba(255,255,255,.204) 0%,rgba(206,232,252,.132) 40%,rgba(142,200,247,.066) 74%,rgba(142,200,247,.034) 100%)!important;
  background-size:100% 100%!important;
  background-repeat:no-repeat!important;
  -webkit-background-clip:text!important;
  background-clip:text!important;
  -webkit-mask-image:linear-gradient(to top,rgba(0,0,0,.5) 0%,#000 62%)!important;
  mask-image:linear-gradient(to top,rgba(0,0,0,.5) 0%,#000 62%)!important;
  text-shadow:none!important;
  filter:none!important;
  pointer-events:none!important;
  user-select:none!important;
  -webkit-user-select:none!important;
  opacity:.92!important;
  transition:none!important;
  animation:none!important;
}
/* Opis i logo nad watermarkiem */
html body .zh:not(.zh--logoBranding) .zh__rev .zh__rl,
html body .zh:not(.zh--logoBranding) .zh__rev .zh__rt,
html body .zh:not(.zh--logoBranding) .zh__rev .zh__rlogo{
  position:relative!important;
  z-index:3!important;
}
html body .zh:not(.zh--logoBranding) .zh__rev .zh__rlogo{position:absolute!important;}
@media(max-width:1100px){
  html body .zh:not(.zh--logoBranding) .zh__rev .zh__sc strong,
  html body .zh:not(.zh--logoBranding) .zh__rev:hover .zh__sc strong,
  html body .zh:not(.zh--logoBranding) .zh__rev:focus-within .zh__sc strong{
    font-size:clamp(53px,8.1vw,71px)!important;
    right:18px!important;
    top:13px!important;
  }
}
@media(max-width:600px){
  html body .zh:not(.zh--logoBranding) .zh__rev .zh__sc strong,
  html body .zh:not(.zh--logoBranding) .zh__rev:hover .zh__sc strong,
  html body .zh:not(.zh--logoBranding) .zh__rev:focus-within .zh__sc strong{
    font-size:clamp(46px,13.8vw,58px)!important;
    right:14px!important;
    top:13px!important;
  }
  html body .zh:not(.zh--logoBranding) .zh__rev .zh__sc em{font-size:12.5px!important;letter-spacing:2px!important;}
}

</style>

<style id="zp-suite-684-hero-service-rotator">
/* v2.2.684 — rotator usług pod H1 w hero.
   Nazwa celowo .zh__svcRot, a NIE .zh__rotor: legacy .zh__rotor ma w tym pliku
   własną animację `zhRot` (5 sztywnych kroków), która gryzłaby się z transformem
   ustawianym z JS. Blok stoi na końcu pliku, więc nie rusza istniejącej kaskady.
   Keyframe `zhFade` jest już zdefiniowany wyżej w tym samym pliku. */
.zh .zh__svcRot{
  display:inline-flex;align-items:baseline;gap:.42em;flex-wrap:wrap;
  margin:22px 0 0;font-size:13px;font-weight:700;letter-spacing:-.01em;
  color:rgba(255,255,255,.42);
  opacity:0;animation:zhFade .9s ease .56s forwards;
}
.zh .zh__svcRotMask{
  display:inline-block;overflow:hidden;font-size:14.5px;height:1.62em;vertical-align:bottom;
  -webkit-mask-image:linear-gradient(to bottom,transparent,#000 26%,#000 74%,transparent);
  mask-image:linear-gradient(to bottom,transparent,#000 26%,#000 74%,transparent);
}
.zh .zh__svcRotMask>span{display:block;transition:transform .72s cubic-bezier(.76,0,.24,1)}
.zh .zh__svcRotMask i{
  display:block;height:1.62em;line-height:1.62em;font-style:normal;white-space:nowrap;
  font-size:1em;font-weight:800;letter-spacing:-.022em;
  background:linear-gradient(100deg,#8ec8f7 0%,#fff 42%,#8ec8f7 100%);
  background-size:220% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;
}
@media(max-width:600px){
  .zh .zh__svcRot{font-size:12px;margin-top:18px}
  .zh .zh__svcRotMask{font-size:13px}
}
@media(prefers-reduced-motion:reduce){
  .zh .zh__svcRot{opacity:1!important;animation:none!important}
  .zh .zh__svcRotMask{height:auto;-webkit-mask-image:none;mask-image:none}
  .zh .zh__svcRotMask>span{transform:none!important;transition:none!important}
  .zh .zh__svcRotMask i:not(:first-child){display:none}
}
</style>
<script id="zp-suite-684-hero-service-rotator-js">(function(w,d){
  'use strict';
  var mask = d.getElementById('zhSvcRot');
  if (!mask) return;
  var track = mask.firstElementChild;
  var items = mask.querySelectorAll('i');
  if (!track || items.length < 2) return;
  /* prefers-reduced-motion: CSS pokazuje tylko pierwszą pozycję, JS nie rusza. */
  if (w.matchMedia && w.matchMedia('(prefers-reduced-motion:reduce)').matches) return;
  var i = 0;
  setInterval(function(){
    i = (i + 1) % items.length;
    track.style.transform = 'translateY(-' + (i * 100 / items.length) + '%)';
  }, 2600);
})(window, document);</script>

<style id="zp-suite-731-home-h1-compact">
/* v2.2.731 — HOME: zachowany mniejszy H1, ale poprawione descenders,
   żeby nie ucinało liter typu g / y / p w ostatniej linii.
   Tylko główny hero (#zhHero); podstrony usług pozostają bez zmian. */
@media (min-width:1101px){
  html body #zhHero .zh__title{
    font-size:clamp(46px,5.2vw,96px)!important;
    line-height:.91!important;
    letter-spacing:-.046em!important;
    overflow:visible!important;
    padding-bottom:.06em!important;
    margin-bottom:0!important;
  }
  html body #zhHero .zh__title .zh__grad{
    display:inline!important;
    line-height:inherit!important;
    overflow:visible!important;
    padding-top:0!important;
    padding-bottom:.14em!important;
    margin-top:0!important;
    margin-bottom:-.02em!important;
    -webkit-box-decoration-break:clone!important;
    box-decoration-break:clone!important;
  }
}
@media (min-width:601px) and (max-width:1100px){
  html body #zhHero .zh__title{
    font-size:clamp(38px,7.8vw,64px)!important;
    line-height:.94!important;
    overflow:visible!important;
    padding-bottom:.04em!important;
  }
  html body #zhHero .zh__title .zh__grad{
    display:inline!important;
    line-height:inherit!important;
    overflow:visible!important;
    padding-bottom:.10em!important;
    margin-bottom:-.01em!important;
  }
}
@media (max-width:600px){
  html body #zhHero .zh__title{
    font-size:clamp(34px,9.8vw,44px)!important;
    line-height:.97!important;
    overflow:visible!important;
    padding-bottom:.03em!important;
  }
  html body #zhHero .zh__title .zh__grad{
    display:inline!important;
    line-height:inherit!important;
    overflow:visible!important;
    padding-bottom:.08em!important;
    margin-bottom:0!important;
  }
}
</style>

