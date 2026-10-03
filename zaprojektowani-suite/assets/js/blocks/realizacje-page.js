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

/* On mobile the search/categories belong below the portfolio heading.
   Move the existing toolbar instead of duplicating controls, so filtering state
   and accessibility attributes stay perfectly synchronized. */
const toolsBar=root.querySelector('.tools');
const portfolioHead=root.querySelector('.head');
const featuredSection=root.querySelector('.spot');
const mobileToolsMq=window.matchMedia('(max-width:700px)');
function placePortfolioTools(){
  if(!toolsBar||!portfolioHead||!featuredSection)return;
  if(mobileToolsMq.matches){
    if(portfolioHead.nextElementSibling!==toolsBar)portfolioHead.insertAdjacentElement('afterend',toolsBar);
  }else if(featuredSection.previousElementSibling!==toolsBar){
    featuredSection.insertAdjacentElement('beforebegin',toolsBar);
  }
}
placePortfolioTools();
if(typeof mobileToolsMq.addEventListener==='function')mobileToolsMq.addEventListener('change',placePortfolioTools);
else if(typeof mobileToolsMq.addListener==='function')mobileToolsMq.addListener(placePortfolioTools);

const FEATURED_INDEXES=PROJECTS.map((p,i)=>p.featured?i:-1).filter(i=>i>-1).concat(PROJECTS.map((p,i)=>i)).filter((v,i,a)=>a.indexOf(v)===i).slice(0,Math.min(3,PROJECTS.length));
const FEATURED=FEATURED_INDEXES.map(i=>PROJECTS[i]).filter(Boolean);
const spotSlider=root.querySelector('[data-spot-slider]');
const spotSlides=root.querySelector('[data-spot-slides]');
const spotDots=root.querySelector('[data-spot-dots]');
const spotSection=root.querySelector('.spot');
let spotCurrent=0,spotTimer=null,spotPaused=false,spotAutoLock=0;
const SPOT_DELAY=6000;
const REDUCED_MOTION=window.matchMedia('(prefers-reduced-motion: reduce)');
function spotlightMarkup(p,projectIndex,slideIndex){
  const points=(p.scope||[]).slice(0,3);
  return `<article class="spotSlide${slideIndex===0?' is-active':''}" data-spot-slide data-project-index="${projectIndex}" data-open="${projectIndex}" data-no="${String(slideIndex+1).padStart(2,'0')}" tabindex="0" role="button" aria-label="${esc(p.brand)} — otwórz szczegóły projektu" aria-hidden="${slideIndex===0?'false':'true'}">
    <img class="spotImg" src="${esc(p.img)}" alt="${esc(p.brand)} — ${esc(p.sub)}" ${slideIndex===0?'fetchpriority="high"':'loading="eager"'}>
    <div class="spotShade" aria-hidden="true"></div>
    <div class="spotCopy">
      <div class="spotBadges">
        <span class="spotBadge"><i data-lucide="star" style="width:13px;height:13px"></i> Wyróżniona realizacja</span>
        <span class="spotBadge"><i data-lucide="${catIcon(p.cat)}" style="width:13px;height:13px"></i> ${catLabel(p.cat)}</span>
      </div>
      <h2>${esc(p.brand)}</h2>
      <div class="sub">${esc(p.sub)}</div>
      <p class="desc">${esc(p.desc)}</p>
      <ul class="spotList">${points.map(s=>`<li><i data-lucide="check"></i>${esc(s)}</li>`).join('')}</ul>
      <div class="spotActions">
        <button class="btn btn--ghostD" data-open="${projectIndex}" type="button"><span>Zobacz case study</span><i data-lucide="arrow-up-right"></i></button>
        ${p.live?`<a class="btn btn--ghostD" href="${esc(p.live)}" target="_blank" rel="noopener"><span>Zobacz live</span><i data-lucide="external-link"></i></a>`:''}
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
  if(spotPaused||document.hidden||REDUCED_MOTION.matches||FEATURED.length<2||spotSection.classList.contains('is-filter-hidden'))return;
  const now=Date.now();
  if(now-spotAutoLock<700)return;
  spotAutoLock=now;
  stopSpotAuto();
  showSpot(spotCurrent+1,1,false);
  startSpotAuto();
}
function startSpotAuto(){
  stopSpotAuto();
  if(spotPaused||document.hidden||REDUCED_MOTION.matches||FEATURED.length<2||spotSection.classList.contains('is-filter-hidden')){
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

const grid=root.querySelector('[data-grid]');
function renderGrid(){
  grid.innerHTML=PROJECTS.map((p,i)=>{
    const slug=slugify((p.brand||'projekt')+'-'+(p.type||'case-study'));
    return `
  <article class="card" id="case-${slug}" data-cat="${esc(p.cat)}" data-i="${i}" data-project-slug="${slug}" tabindex="0" aria-label="${esc(p.brand)} — zobacz case study">
    <div class="cardMedia"><img src="${esc(p.img)}" alt="${esc(p.brand)} — ${esc(p.sub)}" loading="lazy" decoding="async"><span class="cat"><i data-lucide="${catIcon(p.cat)}"></i>${catLabel(p.cat)}</span><span class="cardGo" aria-hidden="true"><i data-lucide="arrow-up-right"></i></span></div>
    <div class="cardBody">
      <div class="microRow"><span>${esc(p.type)}</span><b>${esc(p.year)}</b></div>
      <h3>${esc(p.brand)}</h3>
      <div class="cardSub">${esc(p.sub)}</div>
      <p class="cardDesc">${esc(p.desc)}</p>
      <div class="chips">${p.services.slice(0,3).map(s=>`<span>${esc(s)}</span>`).join('')}</div>
      <div class="cardActions">
        <a class="more" href="#case-${slug}" data-open="${i}"><span>Zobacz case study</span><i data-lucide="arrow-up-right"></i></a>
        ${p.live?`<a class="livePill" href="${esc(p.live)}" target="_blank" rel="noopener" aria-label="Zobacz stronę live"><span>live</span> <i data-lucide="external-link"></i></a>`:''}
      </div>
    </div>
  </article>`;
  }).join('');
}
renderGrid();
icons();

/* reveal wejścia (IO, stagger, reduced-motion aware) */
(function(){
  const reduce=window.matchMedia&&window.matchMedia('(prefers-reduced-motion:reduce)').matches;
  const cards=[...grid.querySelectorAll('.card')];
  const els=[root.querySelector('.spotIn'),root.querySelector('.head'),...cards].filter(Boolean);
  els.forEach(el=>el.classList.add('rv'));
  if(reduce||!('IntersectionObserver' in window)){els.forEach(el=>el.classList.add('is-in'));return;}
  const io=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(!entry.isIntersecting)return;
    const el=entry.target;
    const idx=cards.indexOf(el);
    if(idx>-1){
      const cols=window.innerWidth>1240?3:(window.innerWidth>700?2:1);
      el.style.transitionDelay=((idx%cols)*85)+'ms';
    }
    requestAnimationFrame(()=>el.classList.add('is-in'));
    io.unobserve(el);
  })},{rootMargin:'0px 0px -6% 0px',threshold:.08});
  els.forEach(el=>io.observe(el));
})();

/* filtr + szukajka + pusty stan */
let activeF='all';
const applyFilter=()=>{
  const q=(root.querySelector('[data-search]').value||'').trim().toLowerCase();
  const hideFeatured=activeF!=='all'||q.length>0;
  spotSection.classList.toggle('is-filter-hidden',hideFeatured);
  spotSection.setAttribute('aria-hidden',hideFeatured?'true':'false');
  if(hideFeatured){stopSpotAuto()}else if(!spotPaused){startSpotAuto()}
  let n=0;
  grid.querySelectorAll('.card').forEach(c=>{
    const p=PROJECTS[+c.dataset.i];
    const okF=activeF==='all'||c.dataset.cat.includes(activeF);
    const okQ=!q||[p.brand,p.sub,p.type,p.tag,p.desc,p.services.join(' ')].join(' ').toLowerCase().includes(q);
    c.style.display=(okF&&okQ)?'':'none'; if(okF&&okQ)n++;
  });
  root.querySelector('[data-count]').textContent=n;
  root.querySelector('[data-empty]').classList.toggle('is-on',n===0);
};
root.querySelector('[data-reset]').addEventListener('click',()=>{
  root.querySelector('[data-search]').value='';
  root.querySelectorAll('.filter').forEach(x=>{x.classList.toggle('is-on',x.dataset.f==='all');x.setAttribute('aria-selected',x.dataset.f==='all'?'true':'false')});
  activeF='all';applyFilter();
});
root.querySelectorAll('.filter').forEach(b=>b.addEventListener('click',()=>{
  root.querySelectorAll('.filter').forEach(x=>{x.classList.remove('is-on');x.setAttribute('aria-selected','false')});
  b.classList.add('is-on');b.setAttribute('aria-selected','true');activeF=b.dataset.f;applyFilter();
}));
root.querySelector('[data-search]').addEventListener('input',applyFilter);

/* modal */
const modal=root.querySelector('[data-modal]');
const sheet=root.querySelector('[data-sheet]');
let cur=0,lastFocus=null;
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
        ${p.live?`<a class="btn btn--ghost" href="${esc(p.live)}" target="_blank" rel="noopener"><span>Zobacz live</span><i data-lucide="external-link"></i></a>`:''}
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
      <span class="pnLabel">${String(cur+1).padStart(2,'0')} / ${String(PROJECTS.length).padStart(2,'0')} — ${esc(PROJECTS[(cur+1)%PROJECTS.length].brand)} dalej</span>
      <div class="footCtas">
        <a class="btn btn--ghost" href="/kontakt/"><span>Porozmawiajmy o projekcie</span><i data-lucide="phone"></i></a>
      </div>
    </div>
  </div>
  <nav class="mobileModalBar" aria-label="Sterowanie szczegółami projektu">
    <button class="mobileModalBar__nav" type="button" data-mobile-prev aria-label="Poprzedni projekt"><i data-lucide="arrow-left"></i></button>
    <button class="mobileModalBar__close" type="button" data-mobile-close><i data-lucide="x"></i><span>Zamknij</span></button>
    <button class="mobileModalBar__nav" type="button" data-mobile-next aria-label="Następny projekt"><i data-lucide="arrow-right"></i></button>
    ${p.live?`<a class="mobileModalBar__cta" href="${esc(p.live)}" target="_blank" rel="noopener"><span>Zobacz live</span><i data-lucide="external-link"></i></a>`:`<a class="mobileModalBar__cta" href="/studio-wyceny/"><span>Wyceń</span><i data-lucide="arrow-up-right"></i></a>`}
  </nav>`;
  icons();
  sheet.querySelector('[data-prev]').addEventListener('click',()=>openModal(cur-1));
  sheet.querySelector('[data-next]').addEventListener('click',()=>openModal(cur+1));
  const mobilePrev=sheet.querySelector('[data-mobile-prev]');
  const mobileNext=sheet.querySelector('[data-mobile-next]');
  const mobileClose=sheet.querySelector('[data-mobile-close]');
  if(mobilePrev)mobilePrev.addEventListener('click',()=>openModal(cur-1));
  if(mobileNext)mobileNext.addEventListener('click',()=>openModal(cur+1));
  if(mobileClose)mobileClose.addEventListener('click',closeModal);
  modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');
  document.body.style.overflow='hidden';
  modal.scrollTop=0;
  const c=modal.querySelector('.sheetClose'); if(document.activeElement!==c){lastFocus=document.activeElement;c.focus();}
}
function closeModal(){modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');document.body.style.overflow='';if(location.hash.indexOf('#case-')===0&&history&&typeof history.replaceState==='function'){history.replaceState(null,'',location.pathname+location.search);}if(lastFocus)lastFocus.focus();}
root.addEventListener('click',e=>{
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
  if(e.key==='ArrowRight')openModal(cur+1);
  if(e.key==='ArrowLeft')openModal(cur-1);
});
grid.addEventListener('keydown',e=>{const c=e.target.closest('.card');if(c&&(e.key==='Enter'||e.key===' ')){e.preventDefault();openModal(+c.dataset.i);}});
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
