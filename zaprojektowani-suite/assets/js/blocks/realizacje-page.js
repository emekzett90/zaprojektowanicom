(function(){
'use strict';
const root=document.getElementById('zpRealizacjePage');
if(!root||root.dataset.zpInit==='1')return;
root.dataset.zpInit='1';
const toList=value=>Array.isArray(value)?value.filter(Boolean):String(value||'').split(/[|,]/).map(v=>v.trim()).filter(Boolean);
const rawProjects=Array.isArray(window.zpRealizacjeProjects)?window.zpRealizacjeProjects:[];
const PROJECTS=rawProjects.map(p=>Object.assign({},p,{
  cat:String(p.cat||'web'), services:toList(p.services), meta:toList(p.meta), scope:toList(p.scope),
  results:Array.isArray(p.results)&&p.results.length?p.results:[[String((toList(p.scope)).length)+' elementów','Kompletny zakres dopasowany do potrzeb projektu.'],['Indywidualnie','Projekt przygotowany od podstaw dla konkretnej marki.'],['Gotowe do użycia','Materiały i wdrożenie przygotowane do dalszego rozwoju.']],
  challenge:String(p.challenge||'Uporządkowanie komunikacji i stworzenie rozwiązania dopasowanego do celów marki.'),
  solution:String(p.solution||'Indywidualny kierunek wizualny, czytelna struktura oraz dopracowane doświadczenie użytkownika.'),
  effect:String(p.effect||'Spójny projekt gotowy do sprzedaży, marketingu i dalszego rozwoju marki.')
}));
if(!PROJECTS.length){root.hidden=true;return;}
const catLabel=c=>c.includes('shop')?'Sklep internetowy':(c.includes('branding')&&c.includes('web'))?'Strona + branding':c.includes('branding')?'Logo & branding':'Strona internetowa';
const catIcon=c=>c.includes('shop')?'shopping-bag':(c.includes('branding')&&!c.includes('web'))?'pen-tool':c.includes('branding')?'layers':'monitor';
const icons=()=>{try{window.lucide&&window.lucide.createIcons({attrs:{'stroke-width':2}})}catch(e){}};
const esc=s=>String(s||'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
const slugify=s=>String(s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'').slice(0,90);



const FEATURED_INDEXES=PROJECTS.map((p,i)=>p.featured?i:-1).filter(i=>i>-1).concat(PROJECTS.map((p,i)=>i)).filter((v,i,a)=>a.indexOf(v)===i).slice(0,Math.min(3,PROJECTS.length));
const FEATURED=FEATURED_INDEXES.map(i=>PROJECTS[i]).filter(Boolean);
const spotSlider=root.querySelector('[data-spot-slider]');
const spotSlides=root.querySelector('[data-spot-slides]');
const spotDots=root.querySelector('[data-spot-dots]');
let spotCurrent=0,spotTimer=null,spotPaused=false,spotAutoLock=0;
/* label printed by the template in the page language (the EN dictionary of this page has no entry for it) */
const LIVE_LABEL=esc((spotSlider&&spotSlider.dataset.labelLive)||'Zobacz live');
const SPOT_DELAY=6000;
const REDUCED_MOTION=window.matchMedia('(prefers-reduced-motion: reduce)');
function spotlightMarkup(p,projectIndex,slideIndex){
  const points=(p.scope||[]).slice(0,3);
  return `<article class="spotSlide${slideIndex===0?' is-active':''}" data-spot-slide data-project-index="${projectIndex}" data-open="${projectIndex}" tabindex="0" role="button" aria-label="${esc(p.brand)} — otwórz szczegóły projektu" aria-hidden="${slideIndex===0?'false':'true'}">
    <div class="spotMedia"><img class="spotImg" src="${esc(p.img)}" alt="${esc(p.brand)} — ${esc(p.sub)}" ${slideIndex===0?'fetchpriority="high"':'loading="eager"'} decoding="async"></div>
    <div class="spotCopy">
      <div class="spotBadges">
        <span class="spotBadge"><i data-lucide="star"></i> Wyróżniona realizacja</span>
        <span class="spotBadge"><i data-lucide="${catIcon(p.cat)}"></i> ${catLabel(p.cat)}</span>
      </div>
      <h2>${esc(p.brand)}</h2>
      <div class="sub">${esc(p.sub)}</div>
      <p class="desc">${esc(p.desc)}</p>
      <ul class="spotList">${points.map(s=>`<li><i data-lucide="check"></i>${esc(s)}</li>`).join('')}</ul>
      <div class="spotActions">
        <button class="btn btn--light" data-open="${projectIndex}" type="button"><span>Zobacz case study</span><i data-lucide="arrow-up-right"></i></button>
        ${p.live?`<a class="btn btn--ghostD" href="${esc(p.live)}" target="_blank" rel="noopener"><span>${LIVE_LABEL}</span><i data-lucide="external-link"></i></a>`:''}
      </div>
    </div>
  </article>`;
}
function renderSpotlight(){
  spotSlides.innerHTML=FEATURED.map((p,i)=>spotlightMarkup(p,FEATURED_INDEXES[i],i)).join('');
  spotDots.innerHTML=FEATURED.map((p,i)=>`<button class="spotDot${i===0?' is-on is-running':''}" data-spot-dot="${i}" type="button" aria-label="Pokaż projekt ${esc(p.brand)}" aria-current="${i===0?'true':'false'}"></button>`).join('');
  icons();
}
function restartSpotProgress(){
  spotDots.querySelectorAll('.spotDot').forEach((d,i)=>{
    d.classList.remove('is-running');
    void d.offsetWidth;
    if(i===spotCurrent&&!spotPaused)d.classList.add('is-running');
  });
}
function showSpot(next,direction=1,userAction=false){
  const slides=[...spotSlides.querySelectorAll('[data-spot-slide]')];
  const dots=[...spotDots.querySelectorAll('[data-spot-dot]')];
  if(slides.length<2)return;
  next=(next+slides.length)%slides.length;
  if(next===spotCurrent&&slides[spotCurrent]&&slides[spotCurrent].classList.contains('is-active')){
    restartSpotProgress();
    if(userAction)startSpotAuto();
    return;
  }
  spotSlider.classList.toggle('is-reverse',direction<0);
  slides.forEach((slide,i)=>{slide.classList.toggle('is-active',i===next);slide.setAttribute('aria-hidden',i===next?'false':'true')});
  dots.forEach((dot,i)=>{dot.classList.toggle('is-on',i===next);dot.setAttribute('aria-current',i===next?'true':'false')});
  spotCurrent=next;
  restartSpotProgress();
  if(userAction)startSpotAuto();
}
function stopSpotAuto(){
  if(spotTimer){window.clearTimeout(spotTimer);spotTimer=null;}
}
function advanceSpotAuto(){
  if(spotPaused||document.hidden||REDUCED_MOTION.matches||FEATURED.length<2)return;
  const now=Date.now();
  if(now-spotAutoLock<700)return;
  spotAutoLock=now;
  stopSpotAuto();
  showSpot(spotCurrent+1,1,false);
  startSpotAuto();
}
function startSpotAuto(){
  stopSpotAuto();
  if(spotPaused||document.hidden||REDUCED_MOTION.matches||FEATURED.length<2){
    restartSpotProgress();
    return;
  }
  restartSpotProgress();
  /* The CSS progress animation is the visual clock. This timeout is a
     slightly delayed fallback in case an animationend event is suppressed. */
  spotTimer=window.setTimeout(advanceSpotAuto,SPOT_DELAY+120);
}
renderSpotlight();
spotSlider.querySelector('[data-spot-prev]').addEventListener('click',e=>{e.stopPropagation();showSpot(spotCurrent-1,-1,true)});
spotSlider.querySelector('[data-spot-next]').addEventListener('click',e=>{e.stopPropagation();showSpot(spotCurrent+1,1,true)});
spotDots.addEventListener('click',e=>{const d=e.target.closest('[data-spot-dot]');if(!d)return;e.stopPropagation();const n=+d.dataset.spotDot;showSpot(n,n<spotCurrent?-1:1,true)});
spotDots.addEventListener('animationend',e=>{
  if(e.animationName!=='spotProgress'||e.pseudoElement!=='::after')return;
  const dot=e.target.closest('[data-spot-dot]');
  if(!dot||!dot.classList.contains('is-running')||+dot.dataset.spotDot!==spotCurrent)return;
  advanceSpotAuto();
});
spotSlider.addEventListener('mouseenter',()=>{spotPaused=true;stopSpotAuto();restartSpotProgress()});
spotSlider.addEventListener('mouseleave',()=>{spotPaused=false;startSpotAuto()});
spotSlider.addEventListener('focusin',()=>{spotPaused=true;stopSpotAuto();restartSpotProgress()});
spotSlider.addEventListener('focusout',e=>{if(!spotSlider.contains(e.relatedTarget)){spotPaused=false;startSpotAuto()}});
document.addEventListener('visibilitychange',()=>{if(document.hidden)stopSpotAuto();else startSpotAuto()});
FEATURED.forEach(p=>{const preload=new Image();preload.src=p.img});
startSpotAuto();

const groups=[...root.querySelectorAll('[data-group]')].map(el=>({
  el,
  grid:el.querySelector('[data-grid]'),
  cards:[...el.querySelectorAll('.card')],
  more:el.querySelector('[data-more]'),
  countEl:el.querySelector('[data-group-count]'),
  limit:parseInt(el.dataset.limit,10)||0,
  open:false
}));
const allCards=groups.reduce((list,g)=>list.concat(g.cards),[]);
icons();

/* reveal wejścia (IO, stagger, reduced-motion aware) */
(function(){
  const reduce=window.matchMedia&&window.matchMedia('(prefers-reduced-motion:reduce)').matches;
  const cards=allCards;
  const els=[root.querySelector('.spotIn'),root.querySelector('.head'),...cards].filter(Boolean);
  els.forEach(el=>el.classList.add('rv'));
  if(reduce||!('IntersectionObserver' in window)){els.forEach(el=>el.classList.add('is-in'));return;}
  const io=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(!entry.isIntersecting)return;
    const el=entry.target;
    if(el.classList.contains('card')){
      const g=el.parentElement;
      const cols=Math.max(1,(getComputedStyle(g).gridTemplateColumns||'').split(' ').filter(Boolean).length);
      const idx=[...g.children].filter(c=>c.style.display!=='none').indexOf(el);
      el.style.transitionDelay=((Math.max(0,idx)%cols)*70)+'ms';
    }
    requestAnimationFrame(()=>el.classList.add('is-in'));
    io.unobserve(el);
  })},{rootMargin:'0px 0px -6% 0px',threshold:.08});
  els.forEach(el=>io.observe(el));
})();

/* filtr + szukajka + pusty stan + „pokaż wszystkie” w grupach na telefonie */
let activeF='all';
const searchInput=root.querySelector('[data-search]');
const applyFilter=()=>{
  const q=(searchInput.value||'').trim().toLowerCase();
  const filtering=activeF!=='all'||q.length>0;
  let total=0;
  groups.forEach(g=>{
    let n=0;
    g.cards.forEach(c=>{
      const p=PROJECTS[+c.dataset.i]||{};
      const okF=activeF==='all'||String(c.dataset.cat||'').split(/\s+/).includes(activeF);
      const okQ=!q||[p.brand,p.sub,p.type,p.tag,p.desc,(p.services||[]).join(' ')].join(' ').toLowerCase().includes(q);
      const ok=okF&&okQ;
      c.style.display=ok?'':'none';
      if(ok)n++;
      c.classList.toggle('is-extra',ok&&!filtering&&!g.open&&g.limit>0&&n>g.limit);
    });
    g.el.style.display=n?'':'none';
    if(g.countEl)g.countEl.textContent=n;
    if(g.more)g.more.hidden=filtering||g.open||!(g.limit>0&&n>g.limit);
    total+=n;
  });
  root.querySelector('[data-count]').textContent=total;
  root.querySelector('[data-empty]').classList.toggle('is-on',total===0);
};
groups.forEach(g=>{
  if(!g.more)return;
  g.more.addEventListener('click',()=>{g.open=true;applyFilter();});
});
root.querySelector('[data-reset]').addEventListener('click',()=>{
  searchInput.value='';
  root.querySelectorAll('.filter').forEach(x=>{x.classList.toggle('is-on',x.dataset.f==='all');x.setAttribute('aria-selected',x.dataset.f==='all'?'true':'false')});
  activeF='all';applyFilter();
});
root.querySelectorAll('.filter').forEach(b=>b.addEventListener('click',()=>{
  root.querySelectorAll('.filter').forEach(x=>{x.classList.remove('is-on');x.setAttribute('aria-selected','false')});
  b.classList.add('is-on');b.setAttribute('aria-selected','true');activeF=b.dataset.f;applyFilter();
}));
searchInput.addEventListener('input',applyFilter);
applyFilter();

/* modal */
const modal=root.querySelector('[data-modal]');
const sheet=root.querySelector('[data-sheet]');
let cur=0,lastFocus=null;
function modalOrder(){
  const shown=allCards.filter(c=>c.style.display!=='none').map(c=>+c.dataset.i);
  return shown.length?shown:PROJECTS.map((p,i)=>i);
}
function stepModal(dir){
  const order=modalOrder();
  const at=order.indexOf(cur);
  openModal(order[((at<0?0:at)+dir+order.length)%order.length]);
}
function processSteps(p){
  if(String(p.cat).includes('branding')&&!String(p.cat).includes('web')){
    return [
      ['messages-square','Brief i kierunek','Poznajemy markę, odbiorców, charakter i miejsca wykorzystania identyfikacji.'],
      ['compass','Koncepcje kreatywne','Budujemy autorskie kierunki znaku, typografii i kolorystyki.'],
      ['pen-tool','System identyfikacji','Dopracowujemy logo, warianty i najważniejsze materiały marki.'],
      ['package-check','Pliki i wdrożenie','Przekazujemy komplet formatów oraz zasady poprawnego stosowania.']
    ];
  }
  if(String(p.cat).includes('shop')){
    return [
      ['messages-square','Brief i architektura','Porządkujemy ofertę, grupy klientów i najważniejsze ścieżki zakupowe.'],
      ['panels-top-left','Projekt UX/UI','Tworzymy indywidualny interfejs sklepu i kluczowych kart produktowych.'],
      ['shopping-bag','Wdrożenie e-commerce','Konfigurujemy WooCommerce, płatności, warianty i funkcje customowe.'],
      ['rocket','Testy i publikacja','Sprawdzamy mobile, zakup, analitykę i przygotowujemy sklep do sprzedaży.']
    ];
  }
  return [
    ['messages-square','Brief i architektura','Ustalamy cele, odbiorców, strukturę strony i priorytety komunikacji.'],
    ['panels-top-left','Projekt UX/UI','Projektujemy indywidualny wygląd oraz czytelne ścieżki użytkownika.'],
    ['code-2','Wdrożenie','Budujemy serwis, funkcje customowe, responsywność i podstawy SEO.'],
    ['rocket','Testy i start','Weryfikujemy działanie, konfigurujemy analitykę i publikujemy projekt.']
  ];
}
function openModal(i){
  cur=(i+PROJECTS.length)%PROJECTS.length;
  const p=PROJECTS[cur];
  const order=modalOrder();
  const pos=Math.max(0,order.indexOf(cur));
  const isBrandingCase=String(p.cat||'').split(/\s+/).includes('branding')&&!String(p.cat||'').split(/\s+/).includes('web')&&!String(p.cat||'').split(/\s+/).includes('shop');
  sheet.classList.toggle('is-branding-case',isBrandingCase);
  const projectSlug=slugify((p.brand||'projekt')+'-'+(p.type||'case-study'));
  if(history && typeof history.replaceState==='function'){
    history.replaceState(null,'','#case-'+projectSlug);
  }
  sheet.innerHTML=`
  <div class="sheetTop">
    <div class="sheetMedia"><img src="${esc(p.img)}" alt="${esc(p.brand)}"></div>
    <div class="sheetIntro" data-idx="${String(cur+1).padStart(2,'0')}">
      <div class="sheetLabels"><span class="label label--fill">${catLabel(p.cat)}</span><span class="label">${esc(p.year)}</span><span class="label">${esc(p.tag)}</span></div>
      <h3 id="sheetTitle">${esc(p.brand)}</h3>
      <div class="sub">${esc(p.sub)}</div>
      <p class="desc">${esc(p.desc)}</p>
      <div class="introActions">
        <a class="btn btn--ink" href="/studio-wyceny/"><span>Wyceń podobny projekt</span><i data-lucide="arrow-up-right"></i></a>
        ${p.live?`<a class="btn btn--ghost" href="${esc(p.live)}" target="_blank" rel="noopener"><span>${LIVE_LABEL}</span><i data-lucide="external-link"></i></a>`:''}
      </div>
    </div>
  </div>
  <div class="sheetBody">
    <div class="facts">
      <div class="fact"><span class="factIco"><i data-lucide="building-2"></i></span><div><span>Klient</span><b>${esc(p.client)}</b></div></div>
      <div class="fact"><span class="factIco"><i data-lucide="tags"></i></span><div><span>Branża</span><b>${esc(p.tag)}</b></div></div>
      <div class="fact"><span class="factIco"><i data-lucide="calendar"></i></span><div><span>Rok</span><b>${esc(p.year)}</b></div></div>
      <div class="fact"><span class="factIco"><i data-lucide="badge-check"></i></span><div><span>Status</span><b>${p.live?'Online — projekt działa live':'Projekt portfolio — realizacja zakończona'}</b></div></div>
    </div>
    <div class="caseStory">
      <article class="storyCard"><span class="storyIcon"><i data-lucide="target"></i></span><div><small>Cel projektu</small><p>${esc(p.challenge)}</p></div></article>
      <article class="storyCard storyCard--navy"><span class="storyIcon"><i data-lucide="wand-sparkles"></i></span><div><small>Kierunek rozwiązania</small><p>${esc(p.solution)}</p></div></article>
      <article class="storyCard"><span class="storyIcon"><i data-lucide="trending-up"></i></span><div><small>Efekt dla marki</small><p>${esc(p.effect)}</p></div></article>
    </div>
    <div class="secTitle">Zakres współpracy <em>co wykonaliśmy</em></div>
    <ul class="scopeGrid">${p.scope.map(s=>`<li><i data-lucide="check"></i>${esc(s)}</li>`).join('')}</ul>
    <div class="secTitle">Najważniejsze efekty <em>wartość projektu</em></div>
    <div class="nums">${p.results.map(r=>`<div class="num"><b>${esc(r[0])}</b><span>${esc(r[1])}</span></div>`).join('')}</div>
    <div class="secTitle">Technologie i usługi <em>wykorzystany zakres</em></div>
    <div class="stack">${p.services.map(s=>`<span>${esc(s)}</span>`).join('')}</div>
    <div class="secTitle">Jak pracowaliśmy <em>proces realizacji</em></div>
    <div class="proc">${processSteps(p).map(step=>`<div class="step"><span class="stepIco"><i data-lucide="${step[0]}"></i></span><b>${esc(step[1])}</b><span>${esc(step[2])}</span></div>`).join('')}</div>
    <div class="sheetFoot">
      <div class="pn">
        <button data-prev aria-label="Poprzedni projekt"><i data-lucide="arrow-left"></i></button>
        <button data-next aria-label="Następny projekt"><i data-lucide="arrow-right"></i></button>
      </div>
      <span class="pnLabel">${String(pos+1).padStart(2,'0')} / ${String(order.length).padStart(2,'0')} — ${esc((PROJECTS[order[(pos+1)%order.length]]||p).brand)} dalej</span>
      <div class="footCtas">
        <a class="btn btn--ghost" href="/kontakt/"><span>Porozmawiajmy o projekcie</span><i data-lucide="phone"></i></a>
      </div>
    </div>
  </div>
  <nav class="mobileModalBar" aria-label="Sterowanie szczegółami projektu">
    <button class="mobileModalBar__nav" type="button" data-mobile-prev aria-label="Poprzedni projekt"><i data-lucide="arrow-left"></i></button>
    <button class="mobileModalBar__close" type="button" data-mobile-close><i data-lucide="x"></i><span>Zamknij</span></button>
    <button class="mobileModalBar__nav" type="button" data-mobile-next aria-label="Następny projekt"><i data-lucide="arrow-right"></i></button>
    ${p.live?`<a class="mobileModalBar__cta" href="${esc(p.live)}" target="_blank" rel="noopener"><span>${LIVE_LABEL}</span><i data-lucide="external-link"></i></a>`:`<a class="mobileModalBar__cta" href="/studio-wyceny/"><span>Wyceń</span><i data-lucide="arrow-up-right"></i></a>`}
  </nav>`;
  icons();
  sheet.querySelector('[data-prev]').addEventListener('click',()=>stepModal(-1));
  sheet.querySelector('[data-next]').addEventListener('click',()=>stepModal(1));
  const mobilePrev=sheet.querySelector('[data-mobile-prev]');
  const mobileNext=sheet.querySelector('[data-mobile-next]');
  const mobileClose=sheet.querySelector('[data-mobile-close]');
  if(mobilePrev)mobilePrev.addEventListener('click',()=>stepModal(-1));
  if(mobileNext)mobileNext.addEventListener('click',()=>stepModal(1));
  if(mobileClose)mobileClose.addEventListener('click',closeModal);
  modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');
  document.body.style.overflow='hidden';
  modal.scrollTop=0;
  const c=modal.querySelector('.sheetClose'); if(document.activeElement!==c){lastFocus=document.activeElement;c.focus();}
}
function closeModal(){modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');document.body.style.overflow='';if(location.hash.indexOf('#case-')===0&&history&&typeof history.replaceState==='function'){history.replaceState(null,'',location.pathname+location.search);}if(lastFocus)lastFocus.focus();}
root.addEventListener('click',e=>{
  const link=e.target.closest('a[href]');
  if(link&&!link.hasAttribute('data-open'))return; /* live site, Studio wyceny, Kontakt: real links */
  const t=e.target.closest('[data-open]');
  if(t){
    e.preventDefault();
    openModal(+t.dataset.open);
    return;
  }
  const card=e.target.closest('.card');
  if(card&&!e.target.closest('a')){openModal(+card.dataset.i);return;}
  if(e.target.closest('[data-close]')||e.target===modal){closeModal();}
});
document.addEventListener('keydown',e=>{
  if(!modal.classList.contains('is-open'))return;
  if(e.key==='Escape')closeModal();
  if(e.key==='ArrowRight')stepModal(1);
  if(e.key==='ArrowLeft')stepModal(-1);
});

spotSlider.addEventListener('keydown',e=>{if(e.key==='ArrowRight'){e.preventDefault();showSpot(spotCurrent+1,1,true);return;}if(e.key==='ArrowLeft'){e.preventDefault();showSpot(spotCurrent-1,-1,true);return;}if((e.key==='Enter'||e.key===' ')&&e.target.closest('[data-spot-slide]')){e.preventDefault();openModal(FEATURED_INDEXES[spotCurrent]);}});
/* deep link do case study: #case-nazwa-projektu + podgląd ?modal=N */
const hashSlug=(location.hash||'').replace(/^#case-/,'');
if(hashSlug){
  const hashIndex=PROJECTS.findIndex(p=>slugify((p.brand||'projekt')+'-'+(p.type||'case-study'))===hashSlug);
  if(hashIndex>-1){window.setTimeout(()=>openModal(hashIndex),80);}
}else{
  const mp=new URLSearchParams(location.search).get('modal');
  if(mp!==null){openModal(parseInt(mp,10)||0);}
}


/* Wyraźna podpowiedź poziomego swipe filtrów na mobile */
(function(){
  const rail=root.querySelector('.filters');
  const hint=root.querySelector('.filterSwipeHint');
  if(!rail)return;
  let cancelled=false,played=false;
  const cancel=()=>{cancelled=true;hint&&hint.classList.add('is-hidden')};
  ['pointerdown','touchstart','wheel'].forEach(ev=>rail.addEventListener(ev,cancel,{passive:true,once:true}));
  rail.addEventListener('scroll',()=>{if(cancelled&&rail.scrollLeft>8)hint&&hint.classList.add('is-hidden')},{passive:true});
  const play=()=>{
    if(played||cancelled||window.innerWidth>700||window.matchMedia('(prefers-reduced-motion: reduce)').matches)return;
    const max=rail.scrollWidth-rail.clientWidth;
    if(max<40){hint&&hint.classList.add('is-hidden');return;}
    played=true;
    const start=rail.scrollLeft;
    setTimeout(()=>{
      if(cancelled)return;
      rail.scrollTo({left:Math.min(start+126,max),behavior:'smooth'});
      setTimeout(()=>{
        if(cancelled)return;
        rail.scrollTo({left:start,behavior:'smooth'});
        setTimeout(()=>{
          if(cancelled)return;
          rail.scrollTo({left:Math.min(start+58,max),behavior:'smooth'});
          setTimeout(()=>{if(!cancelled)rail.scrollTo({left:start,behavior:'smooth'})},430);
        },520);
      },720);
    },620);
  };
  if(document.readyState==='complete')play();else window.addEventListener('load',play,{once:true});
})();


})();
