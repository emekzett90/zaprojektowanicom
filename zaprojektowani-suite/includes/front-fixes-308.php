<?php
/**
 * v2.2.790 — Preserve desktop letter choreography; mobile uses native text in fix310.
 * Release the visual Cookiebot gate after the H1 entrance. No consent state changes.
 * Keep inline JS free of less-than characters for legacy HTML buffer compatibility.
 */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('zp_suite_308_is_home_like')) {
  function zp_suite_308_is_home_like() {
    if (is_admin()) { return false; }
    if (function_exists('zp_suite_2492_is_home_like')) { return zp_suite_2492_is_home_like(); }
    return is_front_page() || is_home();
  }
}

if (!function_exists('zp_suite_308_hero_letters_css')) {
  function zp_suite_308_hero_letters_css() {
    if (!zp_suite_308_is_home_like()) { return; }
    ?>
<style id="zp-suite-308-hero-letters">
/* v2.2.788 — Home hero H1: kaskada liter na warstwach GPU (tylko opacity). :not(#zpx) podbija specyficzność ponad reguły hero. */
html body #zhHero .zh__title.zh__title--wait:not(#zpx){opacity:.01!important;animation:none!important;transition:none!important;transform:none!important;filter:none!important}
html body #zhHero .zh__title.zh__title--letters:not(#zpx){opacity:1!important;transform:none!important;filter:none!important;animation:none!important;transition:none!important;visibility:visible!important;white-space:nowrap!important;text-wrap:nowrap!important}
html body #zhHero .zh__title.zh__title--letters.zh__title--wait:not(#zpx){opacity:.01!important}
/* Po kaskadzie tytuł zostaje w stanie końcowym — bez ponownego odtworzenia wejścia zhRise z CSS hero. */
html body #zhHero .zh__title.zh__title--lettersdone:not(#zpx){animation:none!important;opacity:1!important;transform:none!important;filter:none!important}
html body #zhHero .zh__title--letters .zw:not(#zpx){display:inline!important;white-space:nowrap!important;transition:none!important}
html body #zhHero .zh__title--letters .zl:not(#zpx){display:inline-block!important;position:relative!important;vertical-align:baseline!important;padding:.25em .1em .3em!important;margin:-.25em -.1em -.3em!important;-webkit-text-fill-color:transparent!important;text-shadow:none!important;background:none!important;opacity:1!important;transform:none!important;filter:none!important;animation:none!important;transition:none!important}
html body #zhHero .zh__title--letters .zl>zp-i:not(#zpx){position:absolute!important;left:0!important;top:0!important;right:0!important;bottom:0!important;display:block!important;margin:0!important;padding:0!important;opacity:0;transform:none!important;transition:none!important;pointer-events:none!important;white-space:nowrap!important}
html body #zhHero .zh__title--letters .zl__t:not(#zpx){display:block!important;padding:.25em .1em .3em!important;margin:0!important;text-align:left!important;white-space:nowrap!important;-webkit-text-fill-color:currentColor!important;transition:none!important;animation:none!important}
html body #zhHero .zh__title--letters .zl--g .zl__t:not(#zpx){background-image:var(--zg-i)!important;background-size:var(--zg-s)!important;background-position:var(--zg-p)!important;background-repeat:no-repeat!important;-webkit-background-clip:text!important;background-clip:text!important;-webkit-text-fill-color:transparent!important;color:transparent!important}
html body #zhHero .zh__title--letters .zl>.zl__b:not(#zpx){filter:blur(6px)!important}
html body #zhHero .zh__title--letters.is-in .zl>zp-i:not(#zpx){will-change:opacity;animation:zpHLs .8s linear both;animation-delay:calc(var(--i,0) * 17ms + 100ms)}
html body #zhHero .zh__title--letters.is-in .zl>.zl__b:not(#zpx){animation-name:zpHLb}
/* Krzywe = oryginalne wejście (opacity .01→1 + blur 6px→0, cubic-bezier(.2,.7,.2,1)) rozłożone na warstwę ostrą i rozmytą. */
@keyframes zpHLs{0%{opacity:0}4%{opacity:.021}8%{opacity:.083}12%{opacity:.181}17%{opacity:.324}23%{opacity:.483}30%{opacity:.628}40%{opacity:.772}52%{opacity:.878}66%{opacity:.949}82%{opacity:.988}100%{opacity:1}}
@keyframes zpHLb{0%{opacity:0}4%{opacity:.153}8%{opacity:.257}12%{opacity:.306}17%{opacity:.306}23%{opacity:.265}30%{opacity:.205}40%{opacity:.133}52%{opacity:.074}66%{opacity:.031}82%{opacity:.007}100%{opacity:0}}
html body #zhHero .zh__title .zh__grad.zh__grad--pieces:not(#zpx),
html body .zpHomeContent .zh .zh__title .zh__grad.zh__grad--pieces:not(#zpx){background-image:none!important;animation:none!important;will-change:auto!important}
@media (prefers-reduced-motion:reduce){
  html body #zhHero .zh__title.zh__title--wait:not(#zpx){opacity:1!important}
}
</style>
    <?php
  }
}
add_action('wp_head', 'zp_suite_308_hero_letters_css', PHP_INT_MAX);

if (!function_exists('zp_suite_308_preload_title_font')) {
  /**
   * v2.2.788 — H1 hero ma font-weight 480 → przeglądarka bierze krój 500, którego strona nie preloadowała
   * (preload był tylko dla 400/600/700). Bez preloadu font tytułu przychodzi późno (szczególnie mobile),
   * co opóźnia start kaskady. Preload tylko na stronie głównej, tylko gdy plik istnieje w tym samym miejscu co pozostałe.
   */
  function zp_suite_308_preload_title_font() {
    if (!zp_suite_308_is_home_like()) { return; }
    $rel = '/wp-content/web-font/jakarta/plus-jakarta-sans-v12-latin_latin-ext-500.woff2';
    if (!is_file(untrailingslashit(ABSPATH) . $rel)) { return; }
    echo '<link rel="preload" href="' . esc_url(home_url($rel)) . '" as="font" type="font/woff2" crossorigin>' . "\n";
  }
}
add_action('wp_head', 'zp_suite_308_preload_title_font', 2);

if (!function_exists('zp_suite_308_hero_letters_script')) {
  function zp_suite_308_hero_letters_script() {
    return <<<'HTML'
<script id="zp-suite-308-hero-letters-js">(function(d,w){'use strict';var SW='data-zpl-'+'switch';
var t=d.querySelector('#zhHero .zh__title');
if(!t||t.getAttribute('data-zp-letters'))return;
if(w.matchMedia&&w.matchMedia('(max-width:1100px)').matches)return;
if(w.ZPHeroIntroGate&&!w.ZPHeroIntroGate.active)return;
t.setAttribute('data-zp-letters','1');
try{if(w.matchMedia&&w.matchMedia('(prefers-reduced-motion:reduce)').matches)return;if(w.top!==w.self)return;}catch(e){return;}
var STEP=17,DUR=800,LEAD=100,st=0,rendered=false,inView=false,fontsAsked=false,fontsOk=false,
  orig='',originalText='',originalLabel=t.getAttribute('aria-label'),originalSkip=t.getAttribute('data-zpl-skip'),n=0,els=[],lastS=null,expX0=0,expXN=0,io=null,mo=null,poll=0,endT=0,endTries=0,t0=Date.now(),builtAt=0,width0=0,
  CB=['CookiebotOnDialogDisplay','CookiebotOnAccept','CookiebotOnDecline','CookiebotOnLoad'];
t.classList.add('zh__title--wait');
function shown(){var e=t.parentElement,o=1,c;while(e&&e.nodeType===1&&e!==d.body){c=w.getComputedStyle(e);if(c.display==='none'||c.visibility==='hidden')return false;o*=parseFloat(c.opacity||'1');e=e.parentElement;}return o>0.6;}
function why(r){try{w.__zpLettersWhy=r;}catch(e){}return false;}
function layer(cls,c){var a=d.createElement('zp-i'),b=d.createElement('zp-t');a.className=cls;a.setAttribute('aria-hidden','true');b.className='zl__t';b.appendChild(d.createTextNode(c));a.appendChild(b);return a;}
function dim(v,ref){v=String(v||'auto').trim();if(v==='auto'||v==='cover'||v==='contain')return ref;if(v.slice(-1)==='%')return ref*parseFloat(v)/100;return parseFloat(v)||0;}
function off(v,area,img){v=String(v||'0%').trim();if(v==='left'||v==='top')return 0;if(v==='center')return (area-img)/2;if(v==='right'||v==='bottom')return area-img;if(v.slice(-1)==='%')return (area-img)*parseFloat(v)/100;return parseFloat(v)||0;}
function build(){
  var g=t.querySelector('.zh__grad'),gi=null,gc,texts=[],chars=[],frags=[],W=0,tw,node,s,i,j,k,r,rg,line=-1,pTop=null,pH=0;
  orig=t.innerHTML;originalText=t.textContent;var label=(t.textContent||'').replace(/\s+/g,' ').trim();
  if(g){gc=w.getComputedStyle(g);gi={img:gc.backgroundImage,size:gc.backgroundSize,pos:gc.backgroundPosition,clone:(gc.webkitBoxDecorationBreak||gc.boxDecorationBreak)==='clone',inline:gc.display==='inline'};if(!gi.img||gi.img==='none')return why('no-gradient');}
  rg=d.createRange();tw=d.createTreeWalker(t,4,null);
  while((node=tw.nextNode()))texts.push(node);
  for(k=0;texts.length>k;k++){node=texts[k];s=node.nodeValue;
    for(i=0;s.length>i;i++){if(/\s/.test(s.charAt(i)))continue;
      rg.setStart(node,i);rg.setEnd(node,i+1);r=rg.getBoundingClientRect();
      if(pTop===null||r.top-pTop>Math.max(r.height,pH)*0.5)line++;
      pTop=r.top;pH=r.height;
      chars.push({x:r.left,y:r.top,line:line,g:!!(g&&g.contains(node))});}}
  n=chars.length;if(!n)return why('no-chars');
  if(g){var rs=g.getClientRects();
    for(i=0;rs.length>i;i++){r=rs[i];if(0>=r.width||0>=r.height)continue;var f=null;
      for(j=0;frags.length>j;j++){if(Math.max(r.height,frags[j].b-frags[j].t)*0.5>Math.abs(frags[j].t-r.top)){f=frags[j];break;}}
      if(f){f.l=Math.min(f.l,r.left);f.r=Math.max(f.r,r.right);f.t=Math.min(f.t,r.top);f.b=Math.max(f.b,r.bottom);}else frags.push({l:r.left,r:r.right,t:r.top,b:r.bottom});}
    frags.sort(function(a,b){return a.t-b.t;});for(i=0;frags.length>i;i++){frags[i].x0=W;W+=frags[i].r-frags[i].l;}
    if(!frags.length)return why('no-frags');}
  var ci=0,lastLine=chars[0].line;
  for(k=0;texts.length>k;k++){node=texts[k];s=node.nodeValue;var fr=d.createDocumentFragment(),word=null,ws='';
    for(i=0;s.length>i;i++){var c=s.charAt(i);
      if(/\s/.test(c)){ws+=c;word=null;continue;}
      if(ws){fr.appendChild(d.createTextNode(ws));ws='';}
      if(chars[ci].line!==lastLine){if(g&&gi&&!gi.inline&&node.parentNode===g&&chars[ci].g&&(ci===0||!chars[ci-1].g))g.parentNode.insertBefore(d.createElement('br'),g);else fr.appendChild(d.createElement('br'));lastLine=chars[ci].line;word=null;}
      if(!word){word=d.createElement('zp-w');word.className='zw';fr.appendChild(word);}
      var el=d.createElement('zp-l');el.className=chars[ci].g?'zl zl--g':'zl';el.style.setProperty('--i',String(ci));
      el.appendChild(d.createTextNode(c));el.appendChild(layer('zl__b',c));el.appendChild(layer('zl__s',c));
      word.appendChild(el);els.push(el);ci++;}
    if(ws)fr.appendChild(d.createTextNode(ws));
    node.parentNode.replaceChild(fr,node);}
  t.setAttribute('data-zpl-skip','');
  t.setAttribute('aria-label',label);
  t.classList.add('zh__title--letters');if(g)g.classList.add('zh__grad--pieces');
  var a=[];
  function tr(el){rg.setStart(el.firstChild,0);rg.setEnd(el.firstChild,1);return rg.getBoundingClientRect();}
  for(i=0;n>i;i++)a.push(tr(els[i]).left);
  for(i=0;n-1>i;i++){if(chars[i].line!==chars[i+1].line)continue;var corr=(chars[i+1].x-chars[i].x)-(a[i+1]-a[i]);
    if(Math.abs(corr)>0.01)els[i].style.setProperty('margin-right','calc('+corr.toFixed(3)+'px - .1em)','important');}
  var bx=[],by=[],bh=[],dx=0,dy=0,q,wi=0;
  for(i=0;n>i;i++){q=tr(els[i]);if(Math.abs(q.left-chars[i].x)+Math.abs(q.top-chars[i].y)>dx+dy)wi=i;dx=Math.max(dx,Math.abs(q.left-chars[i].x));dy=Math.max(dy,Math.abs(q.top-chars[i].y));
    r=els[i].getBoundingClientRect();bx.push(r.left);by.push(r.top);bh.push(r.height);}
  if(dx>1||dy>1){q=tr(els[wi]);return why('shift '+dx.toFixed(2)+'/'+dy.toFixed(2)+' at '+wi+' '+Math.round(chars[wi].x)+','+Math.round(chars[wi].y)+' vs '+Math.round(q.left)+','+Math.round(q.top)+' w'+w.innerWidth);}
  expX0=bx[0];expXN=bx[n-1];
  if(g){var sz=gi.size.split(',')[0].trim().split(/\s+/),ps=gi.pos.split(',')[0].trim().split(/\s+/);
    for(i=0;n>i;i++){if(!chars[i].g)continue;var cy=by[i]+bh[i]/2,fg=null;
      for(j=0;frags.length>j;j++){if(cy>=frags[j].t&&frags[j].b>=cy){fg=frags[j];break;}}
      if(!fg){fg=frags[0];for(j=1;frags.length>j;j++){if(Math.abs((fg.t+fg.b)/2-cy)>Math.abs((frags[j].t+frags[j].b)/2-cy))fg=frags[j];}}
      var ax,ay,aw,ah;if(gi.clone||!gi.inline){ax=fg.l;ay=fg.t;aw=fg.r-fg.l;ah=fg.b-fg.t;}else{ax=fg.l-fg.x0;ay=fg.t;aw=W;ah=fg.b-fg.t;}
      var sw=dim(sz[0],aw),sh=dim(sz[1]||'auto',ah),ox=off(ps[0],aw,sw),oy=off(ps[1]||'50%',ah,sh);
      els[i].style.setProperty('--zg-i',gi.img);
      els[i].style.setProperty('--zg-s',sw.toFixed(2)+'px '+sh.toFixed(2)+'px');
      els[i].style.setProperty('--zg-p',(ox-(bx[i]-ax)).toFixed(2)+'px '+(oy-(by[i]-ay)).toFixed(2)+'px');}}
  lastS=els[n-1].querySelector('.zl__s');
  return true;
}
function finish(){
  if(st===3)return;var was=st;st=3;
  clearInterval(poll);clearTimeout(endT);
  try{if(io)io.disconnect();if(mo)mo.disconnect();}catch(e){}
  try{d.removeEventListener('pointerdown',early,true);d.removeEventListener('keydown',early,true);d.removeEventListener('touchstart',early,true);
    d.removeEventListener('zpl:change',onZpl);d.removeEventListener('visibilitychange',tick);w.removeEventListener('resize',onResize);
    t.removeEventListener('animationend',onEnd);if(d.fonts&&d.fonts.removeEventListener)d.fonts.removeEventListener('loadingdone',onFonts);
    for(var i=0;CB.length>i;i++)w.removeEventListener(CB[i],tick);}catch(e){}
  if(was>=1&&orig){
    if(t.querySelector('.zl')||t.textContent===originalText)t.innerHTML=orig;
    // A completed language replacement belongs to the translator, not the entrance.
  }
  t.classList.add('zh__title--lettersdone');
  t.classList.remove('zh__title--wait');t.classList.remove('zh__title--letters');t.classList.remove('is-in');
  if(originalSkip===null)t.removeAttribute('data-zpl-skip');else t.setAttribute('data-zpl-skip',originalSkip);
  if(originalLabel===null)t.removeAttribute('aria-label');else t.setAttribute('aria-label',originalLabel);
  d.removeEventListener('zp:hero-intro-release',finish);
  if(w.ZPHeroIntroGate)w.ZPHeroIntroGate.release('desktop-complete');
}
function play(){st=2;t.classList.add('is-in');clearTimeout(endT);endTries=0;endT=setTimeout(endCheck,LEAD+STEP*(n-1)+DUR+500);}
function endCheck(){if(st!==2)return;var an=null;try{an=(lastS&&lastS.getAnimations)?lastS.getAnimations()[0]:null;}catch(e){}
  if(an&&an.playState==='running'&&40>endTries){endTries++;endT=setTimeout(endCheck,250);return;}finish();}
function onEnd(e){if(st===2&&e.target===lastS)finish();}
function tick(){
  if(st===3)return;
  if(d.hidden){if(st===0)t0=Date.now();else if(st===1)builtAt=Date.now();return;}
  if(st===0){
    if(Date.now()-t0>15000){finish();return;}
    if(!rendered||!fontsOk||d.readyState==='loading')return;
    st=1;builtAt=Date.now();width0=w.innerWidth;var ok=false;
    try{ok=build();}catch(e){ok=why('err '+(e&&e.message));}
    if(!ok){finish();return;}
    t.classList.remove('zh__title--wait');
    if(w.MutationObserver){mo=new MutationObserver(function(ms){for(var i=0;ms.length>i;i++){var el=ms[i].target;if(el&&el.nodeType===1&&el.hasAttribute(SW)&&(el.classList.contains('is-busy')||el.hasAttribute('data-zpl-to'))){finish();return;}}});
      d.querySelectorAll('['+SW+']').forEach(function(el){mo.observe(el,{attributes:true,attributeFilter:['class','data-zpl-to']});});}
  }
  if(st===1){if(Date.now()-builtAt>120000){finish();return;}if(!inView||!shown())return;play();return;}
}
function onRendered(){if(fontsAsked)return;fontsAsked=true;
  var done=function(){if(!fontsOk){fontsOk=true;tick();}};
  try{if(d.fonts&&d.fonts.load){var list=[t],g0=t.querySelector('.zh__grad'),ps=[],i,cs;if(g0)list.push(g0);
    for(i=0;list.length>i;i++){cs=w.getComputedStyle(list[i]);ps.push(d.fonts.load(cs.fontStyle+' '+cs.fontWeight+' '+cs.fontSize+' '+cs.fontFamily,list[i].textContent||'a'));}
    Promise.all(ps).then(done,done);setTimeout(done,2500);}else done();}catch(e){done();}}
function early(e){try{var el=(e&&e.target&&e.target.closest)?e.target.closest('['+SW+']'):null;if(el&&(st===1||st===2))finish();}catch(x){}}
function onZpl(){if(st===1||st===2){finish();try{if(w.ZPL&&w.ZPL.refresh)w.ZPL.refresh();}catch(e){}}}
function onResize(){if((st===1||st===2)&&w.innerWidth!==width0)finish();}
function onFonts(){if(st!==1&&st!==2)return;try{if(Math.abs(els[0].getBoundingClientRect().left-expX0)>1||Math.abs(els[n-1].getBoundingClientRect().left-expXN)>1)finish();}catch(e){finish();}}
try{
  d.addEventListener('zp:hero-intro-release',finish);
  d.addEventListener('pointerdown',early,true);d.addEventListener('keydown',early,true);d.addEventListener('touchstart',early,true);
  d.addEventListener('zpl:change',onZpl);d.addEventListener('visibilitychange',tick);w.addEventListener('resize',onResize);
  t.addEventListener('animationend',onEnd);if(d.fonts&&d.fonts.addEventListener)d.fonts.addEventListener('loadingdone',onFonts);
  for(var q=0;CB.length>q;q++)w.addEventListener(CB[q],tick);
  if(w.IntersectionObserver){io=new IntersectionObserver(function(es){var e=es[es.length-1];rendered=true;inView=e.isIntersecting&&e.intersectionRatio>0.2;onRendered();tick();},{threshold:[0,0.25,0.5,1]});io.observe(t);}
  else{w.requestAnimationFrame(function(){w.requestAnimationFrame(function(){rendered=true;inView=true;onRendered();});});}
  poll=setInterval(tick,250);
}catch(e){finish();}
})(document,window);</script>
HTML;
  }
}

if (!function_exists('zp_suite_308_inject_hero_letters')) {
  /**
   * Wstrzykuje skrypt tuż za </h1> hero. Skrypt inline w tym miejscu wykonuje się zanim
   * tytuł zostanie pierwszy raz namalowany, więc nie ma mignięcia pełnego H1.
   */
  function zp_suite_308_inject_hero_letters($output, $tag) {
    if (!in_array($tag, ['zp_home_hero', 'zp_home_full', 'zp_home', 'zp_zaprojektowani_home'], true)) { return $output; }
    if (!is_string($output) || strpos($output, 'id="zhHero"') === false || strpos($output, 'zp-suite-308-hero-letters-js') !== false) { return $output; }
    $script = zp_suite_308_hero_letters_script();
    $count = 0;
    $output = preg_replace_callback('/<h1\s+class="zh__title"[^>]*>.*?<\/h1>/s', function ($m) use ($script) {
      return $m[0] . $script;
    }, $output, 1, $count);
    return $output;
  }
}
add_filter('do_shortcode_tag', 'zp_suite_308_inject_hero_letters', 20, 2);
