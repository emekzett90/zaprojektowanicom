(function(w,d){
  'use strict';
  var syntheticAudit=false;
  var homeIconManaged=false;
  try{syntheticAudit=w.zpSyntheticAudit===true||w.__zpSyntheticAudit===true||d.documentElement.classList.contains('zp-synthetic-audit')||false /* v711: identical production behavior in browser audits */||false;}catch(e){}
  try{homeIconManaged=((w.location&&w.location.pathname||'/').replace(/\/+$/,'')||'/')==='/';}catch(e){}
  w.__zpHeaderSyntheticAudit=syntheticAudit; if(syntheticAudit){try{d.documentElement.classList.add('zp-synthetic-audit');}catch(e){}}
  function qs(sel,ctx){return (ctx||d).querySelector(sel)}
  function qsa(sel,ctx){return Array.prototype.slice.call((ctx||d).querySelectorAll(sel))}

  function initZpNewNav(){
    var nav=qs('#zpNewNav');
    if(!nav || nav.dataset.zpReady==='1') return;
    nav.dataset.zpReady='1';

    /* v2.2.523: service pages must render the final transparent hero header immediately,
       without waiting for scroll/resize measurements. */
    try{
      var servicePath=/(?:^|\/)(strony-internetowe-katowice|sklepy-internetowe-katowice|logo-branding-katowice)(?:\/|$)/.test((w.location&&w.location.pathname)||'');
      var serviceBody=d.body&&d.body.classList&&(d.body.classList.contains('zp-service-hero-page')||d.body.classList.contains('zp-strony-katowice-page')||d.body.classList.contains('zp-sklepy-katowice-page')||d.body.classList.contains('zp-logo-branding-page'));
      if(servicePath||serviceBody){
        nav.classList.add('zpNewNav--heroOverlay');
        nav.classList.remove('is-scrolled');
      }
    }catch(e){}

    var shell=qs('.zpNewNav__shell',nav);
    /* v2.2.420: do not visually gate the mobile header on Lucide.
       CSS reserves icon boxes, so controls are stable from first paint. */
    nav.classList.add('zp-icons-ready');
    var desktopMq=w.matchMedia('(min-width:1101px)');
    var items=qsa('.zpNewNav__item[data-mega],.zpNewNav__item[data-drop]',nav);
    var closeTimer=null;
    var raf=null;
    var resizeRaf=null;
    var currentItem=null;
    var spacer=null;

    /* v2.2.95: no white-sticky flash on service hero pages.
       Body class is available before JS, so add hero overlay immediately. */
    if(d.body && d.body.classList && (d.body.classList.contains('zp-logo-branding-page') || d.body.classList.contains('zp-logo-branding-shortcode'))){
      nav.classList.add('zpNewNav--heroOverlay');
      nav.classList.remove('is-scrolled');
    }

    function isDesktop(){return desktopMq.matches}

    function shouldUseHeroOverlay(){
      if(nav.classList.contains('zpNewNav--heroOverlay')) return true;
      var path=(w.location&&w.location.pathname||'').replace(/\/+$/,'/');
      if(/\/(logo-branding-katowice|logo-branding|wiedza|strony-internetowe-katowice|sklepy-internetowe-katowice|kontakt)\//.test(path)) return true;
      if(d.body && d.body.classList && (d.body.classList.contains('zp-logo-branding-page') || d.body.classList.contains('zp-logo-branding-shortcode') || d.body.classList.contains('zp-wiedza-bg-boot'))) return true;
      if(d.body && d.body.classList && d.body.classList.contains('single-post')) return true;
      var hero=d.querySelector('.zpNewHero,.zpWebHeroKat,.zpShopCockpit,#zpShopHeroKat,.zpBrandHeroSafe,#zpBrandHeroKatSafe,.zpKBHero,.zpKnowledgePro .zpKBHero');
      if(!hero) return false;
      var rect=hero.getBoundingClientRect();
      return rect.top < 180 && rect.bottom > 160;
    }

    function syncHeroOverlay(){
      nav.classList.toggle('zpNewNav--heroOverlay', shouldUseHeroOverlay());
    }

    /* v2.2.588 — hydratacja ikon w DWÓCH etapach zamiast jednego długiego taska.
       Wcześniej createIcons() przetwarzało CAŁY dokument (setki ikon) na raz — na
       telefonie blokowało to main thread tuż po pierwszym otwarciu drawera i tap
       w "zamknij" czekał w kolejce (lag zamykania menu przy pierwszym załadowaniu).
       Teraz: header+drawer hydratują się natychmiast (kilkadziesiąt ikon, tanio),
       reszta strony partiami po ~60 w requestIdleCallback — między partiami main
       thread jest wolny i interakcje reagują od ręki. */
    var zpIconAttrs={'stroke-width':1.9,'stroke-linecap':'round','stroke-linejoin':'round'};
    var homeIconsPrepared=false;
    var homeIconObserver=null;
    function zpCreateIcons(){
      if(!(w.lucide && typeof w.lucide.createIcons==='function')) return false;
      try{
        w.__zpAllowLucideScan=true;
        w.lucide.createIcons({attrs:zpIconAttrs});
        return true;
      }catch(e){
        return false;
      }finally{
        w.__zpAllowLucideScan=false;
      }
    }
    function hydrateIconScope(scope){
      scope=scope||d;
      try{
        qsa('[data-lucide-defer]',scope).forEach(function(el){
          var name=el.getAttribute('data-lucide-defer');
          if(name) el.setAttribute('data-lucide',name);
          el.removeAttribute('data-lucide-defer');
        });
      }catch(e){}
      return zpCreateIcons();
    }
    function navIsBusy(){
      try{
        return Date.now()<(w.__zpNavUiBusyUntil||0) || nav.classList.contains('zpNewNav--drawer-open') || !!qs('#zpNewNavDrawer.is-open',nav);
      }catch(e){return false;}
    }
    function scheduleHomeScope(scope){
      var run=function(){
        if(navIsBusy()){
          w.setTimeout(run,180);
          return;
        }
        hydrateIconScope(scope);
      };
      if('requestIdleCallback' in w){w.requestIdleCallback(run,{timeout:1200});}
      else{w.setTimeout(run,120);}
    }
    function prepareHomeIcons(){
      if(homeIconsPrepared) return;
      homeIconsPrepared=true;
      var compact=false;
      try{compact=w.matchMedia&&w.matchMedia('(max-width:1100px)').matches;}catch(e){}
      try{
        qsa('[data-lucide]:not(svg)',d).forEach(function(el){
          var critical=false;
          if(nav.contains(el)){
            critical=compact ? !!el.closest('.zpNewNav__mobileBar') : !!el.closest('.zpNewNav__brand,.zpNewNav__desktop,.zpNewNav__actions');
          }else{
            critical=!!el.closest('#zhHero');
          }
          if(!critical){
            el.setAttribute('data-lucide-defer',el.getAttribute('data-lucide')||'');
            el.removeAttribute('data-lucide');
          }
        });
      }catch(e){}
      zpCreateIcons();
      nav.classList.add('zp-icons-ready');

      /* Hydrate a section only shortly before it can be seen. */
      if('IntersectionObserver' in w){
        homeIconObserver=new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if(!entry.isIntersecting) return;
            homeIconObserver.unobserve(entry.target);
            scheduleHomeScope(entry.target);
          });
        },{rootMargin:'180px 0px',threshold:0.01});
        var scopes=[];
        qsa('[data-lucide-defer]',d).forEach(function(el){
          if(nav.contains(el)) return;
          var scope=el.closest('section,footer,[data-zp-home-contact-form],#zpShowcaseWhite') || el.parentElement;
          if(scope && scopes.indexOf(scope)===-1){scopes.push(scope);homeIconObserver.observe(scope);}
        });
      }

      /* Drawer icons are cheap, but only run in idle and never over a tap. */
      var drawer=qs('#zpNewNavDrawer',nav);
      if(drawer){scheduleHomeScope(drawer);}
    }
    w.zpSuiteHydrateIcons=function(scope){
      if(!homeIconsPrepared && homeIconManaged) prepareHomeIcons();
      return hydrateIconScope(scope||d);
    };
    function renderIcons(scope){
      if(syntheticAudit){
        nav.classList.add('zp-icons-ready');
        return;
      }
      if(homeIconManaged){
        if(!(w.lucide && typeof w.lucide.createIcons==='function')){
          w.setTimeout(function(){renderIcons(scope);},120);
          return;
        }
        prepareHomeIcons();
        if(scope) hydrateIconScope(scope);
        return;
      }
      if(nav.__zpIconsChunking){return;}
      if(!(w.lucide && typeof w.lucide.createIcons==='function')){
        w.setTimeout(renderIcons,120);
        return;
      }
      nav.__zpIconsChunking=true;
      /* Etap 1: odrocz wszystko poza headerem/drawerem, hydratuj tylko nawigację. */
      var outside=[];
      try{
        var all=d.querySelectorAll('i[data-lucide]');
        for(var k=0;k<all.length;k++){
          var el=all[k];
          if(!nav.contains(el)){
            el.setAttribute('data-lucide-defer',el.getAttribute('data-lucide')||'');
            el.removeAttribute('data-lucide');
            outside.push(el);
          }
        }
      }catch(e){outside=[];}
      zpCreateIcons();
      nav.classList.add('zp-icons-ready');
      /* Etap 2: przywracaj i hydratuj resztę partiami w idle. */
      var i=0,CHUNK=60;
      var idle=w.requestIdleCallback||function(fn){return w.setTimeout(fn,140);};
      function step(){
        var n=0;
        while(i<outside.length && n<CHUNK){
          var el=outside[i++];
          try{
            el.setAttribute('data-lucide',el.getAttribute('data-lucide-defer')||'');
            el.removeAttribute('data-lucide-defer');
          }catch(e){}
          n++;
        }
        zpCreateIcons();
        if(i<outside.length){idle(step,{timeout:700});}
        else{nav.__zpIconsChunking=false;}
      }
      if(outside.length){idle(step,{timeout:900});}
      else{nav.__zpIconsChunking=false;}
    }

    function getAdminTop(){
      if(!d.body.classList.contains('admin-bar')) return 0;
      var bar=d.getElementById('wpadminbar');
      if(bar){
        var cs=w.getComputedStyle? w.getComputedStyle(bar) : null;
        var r=bar.getBoundingClientRect();
        if(cs && (cs.display==='none' || cs.visibility==='hidden')) return 0;
        if(r.height>0 && r.bottom>0) return Math.round(r.height);
      }
      return 0;
    }

    function syncAdminOffset(){
      var top=getAdminTop();
      nav.style.setProperty('--zp-admin-top',top+'px');
      nav.style.setProperty('top',top+'px','important');
    }

    function ensureSpacer(){
      if(spacer && spacer.parentNode) return spacer;
      spacer=d.createElement('div');
      spacer.className='zpNewNav__fixedSpacer';
      spacer.setAttribute('aria-hidden','true');
      nav.parentNode.insertBefore(spacer,nav.nextSibling);
      return spacer;
    }

    function updateSpacer(){
      var s=ensureSpacer();
      syncHeroOverlay();

      /* v2.2.42: Realizacje/FAQ use a fixed, white header without JS spacer.
         The page itself has stable CSS top padding, so no delayed jump/drop after load. */
      if(d.body && d.body.classList && (d.body.classList.contains('zp-realizacje-page') || d.body.classList.contains('zp-realizacje-shortcode') || d.body.classList.contains('zp-faq-page') || d.body.classList.contains('zp-faq-shortcode'))){
        s.style.height='0px';
        return;
      }

      if(nav.classList.contains('zpNewNav--heroOverlay')){
        s.style.height='0px';
        return;
      }
      var h=shell ? Math.ceil(shell.getBoundingClientRect().height) : Math.ceil(nav.getBoundingClientRect().height);
      s.style.height=h+'px';
    }

    function applyMobileGlass(y){
      if(!shell) return;
      var mobile = w.matchMedia('(max-width:1100px)').matches;
      var sticky = y > 18 && mobile && !nav.classList.contains('is-mega-open');

      if(sticky){
        /* v1.8.82: hard runtime glass. CSS had many historical !important layers,
           so we set the final sticky-mobile glass directly on the shell with priority. */
        /* v1.8.83: actual transparent glass. Previous .50-.58 white looked like a flat grey bar. */
        /* v1.8.84: true see-through mobile glass.
           Background intentionally low-alpha; previous .30+ looked like a flat grey bar. */
        var mobileBar = qs('.zpNewNav__mobileBar', nav);
        nav.style.setProperty('background','transparent','important');
        nav.style.setProperty('background-color','transparent','important');
        shell.style.setProperty('background','rgba(255,255,255,.88)','important');
        shell.style.setProperty('background-color','rgba(255,255,255,.88)','important');
        shell.style.setProperty('background-image','linear-gradient(180deg,rgba(255,255,255,.96),rgba(255,255,255,.84))','important');
        shell.style.setProperty('border-bottom','0','important');
        shell.style.setProperty('box-shadow','none','important');
        shell.style.setProperty('backdrop-filter','none saturate(178%)','important');
        shell.style.setProperty('-webkit-backdrop-filter','none saturate(178%)','important');
        if(mobileBar){
          mobileBar.style.setProperty('background','transparent','important');
          mobileBar.style.setProperty('background-color','transparent','important');
          mobileBar.style.setProperty('background-image','none','important');
          mobileBar.style.setProperty('box-shadow','none','important');
        }
      }else{
        var mobileBar = qs('.zpNewNav__mobileBar', nav);
        nav.style.removeProperty('background');
        nav.style.removeProperty('background-color');
        shell.style.removeProperty('background');
        shell.style.removeProperty('background-color');
        shell.style.removeProperty('background-image');
        shell.style.removeProperty('border-bottom');
        shell.style.setProperty('box-shadow','none','important');
        shell.style.removeProperty('backdrop-filter');
        shell.style.removeProperty('-webkit-backdrop-filter');
        if(mobileBar){
          mobileBar.style.removeProperty('background');
          mobileBar.style.removeProperty('background-color');
          mobileBar.style.removeProperty('background-image');
          mobileBar.style.removeProperty('box-shadow');
        }
      }
    }

    function setScrolledState(){
      var y=w.scrollY || d.documentElement.scrollTop || 0;
      syncHeroOverlay();
      nav.classList.toggle('is-scrolled', y>18);
      d.body.classList.toggle('zp-nav-scrolled', y>18);
      applyMobileGlass(y);
      updateSpacer();
    }

    function updateMegaTop(){
      if(!shell) return;
      syncAdminOffset();
      /* v2.2.511: do not measure a transitional/hovering shell. Desktop header is locked to 88px,
         so mega menu always starts exactly below it and never creates a blank gap or header drop. */
      var desktop=isDesktop();
      /* v2.2.809: desktop header heights are fixed in CSS. Reading the shell rect here
         after class/style writes forced a full-page reflow during startup. */
      var h=88, top=0;
      if(desktop){
        if(nav.classList.contains('is-mega-open')) h=85;
        else if(nav.classList.contains('zpNewNav--heroOverlay') && !nav.classList.contains('is-scrolled')) h=104;
        top=getAdminTop()+h;
      }else{
        top=Math.max(getAdminTop(),Math.round(shell.getBoundingClientRect().bottom));
      }
      d.documentElement.style.setProperty('--zp-mega-top',top+'px');
      nav.style.setProperty('--zp-mega-top',top+'px');
      qsa('.zpNewNav__mega--portal',d.body).forEach(function(panel){
        panel.style.top=top+'px';
        panel.style.marginTop='0px';
      });
    }

    function prepareMegaPortals(){
      qsa('.zpNewNav__item[data-mega]',nav).forEach(function(item,idx){
        var mega=qs(':scope > .zpNewNav__mega',item);
        if(!mega || mega.dataset.portalReady==='1') return;
        var id='zpNewNavMegaPortal-'+idx+'-'+Math.random().toString(36).slice(2,7);
        item.dataset.megaPortalId=id;
        mega.dataset.portalOwner=id;
        mega.dataset.portalReady='1';
        mega.classList.add('zpNewNav__mega--portal');
        mega.setAttribute('hidden','hidden');
        d.body.appendChild(mega);
        item.__zpMegaPanel=mega;
        mega.addEventListener('mouseenter',function(){if(!isDesktop()) return;w.clearTimeout(closeTimer)});
        mega.addEventListener('mouseleave',function(){if(!isDesktop()) return;w.clearTimeout(closeTimer);closeTimer=w.setTimeout(function(){closeItem(item)},120)});
      });
    }

    function getPanel(item){
      if(!item) return null;
      if(item.__zpMegaPanel) return item.__zpMegaPanel;
      var id=item.dataset.megaPortalId;
      if(id){
        var portal=qs('.zpNewNav__mega--portal[data-portal-owner="'+id+'"]',d.body);
        if(portal){item.__zpMegaPanel=portal;return portal}
      }
      return qs(':scope > .zpNewNav__drop',item) || qs(':scope > .zpNewNav__mega',item);
    }

    function setMegaState(){
      var isOpen=!!qs('.zpNewNav__item.is-open',nav);
      nav.classList.toggle('is-mega-open',isOpen);
      if(isOpen) updateMegaTop();
    }

    function closeItem(item){
      if(!item) return;
      item.classList.remove('is-open');
      if(currentItem===item) currentItem=null;
      var link=qs(':scope > .zpNewNav__link',item);
      var panel=getPanel(item);
      if(link) link.setAttribute('aria-expanded','false');
      if(panel){
        panel.classList.remove('is-portal-open');
        w.setTimeout(function(){if(!panel.classList.contains('is-portal-open')) panel.setAttribute('hidden','hidden')},180);
      }
      setMegaState();
    }

    function closeAll(except){
      items.forEach(function(item){if(except && item===except) return;closeItem(item)});
      setMegaState();
    }

    function openItem(item){
      if(!item || !isDesktop()) return;
      w.clearTimeout(closeTimer);
      prepareMegaPortals();
      updateSpacer();
      updateMegaTop();
      closeAll(item);
      item.classList.add('is-open');
      currentItem=item;
      var link=qs(':scope > .zpNewNav__link',item);
      var panel=getPanel(item);
      if(link) link.setAttribute('aria-expanded','true');
      if(panel){
        panel.removeAttribute('hidden');
        if(panel.classList.contains('zpNewNav__mega--portal')){
          /* v2.2.509: sticky / white header must open the light mega menu;
             top hero overlay keeps the dark premium dropdown. */
          var lightMega = nav.classList.contains('is-scrolled') || !nav.classList.contains('zpNewNav--heroOverlay');
          panel.classList.toggle('is-light-menu', !!lightMega);
          panel.classList.toggle('is-dark-menu', !lightMega);
          panel.getBoundingClientRect();
          panel.classList.add('is-portal-open');
        }
      }
      setMegaState();
      renderIcons(panel);
    }

    /* Mega panels are prepared on first desktop open. This removes non-visible DOM work from first paint. */

    items.forEach(function(item){
      var link=qs(':scope > .zpNewNav__link',item);
      item.addEventListener('mouseenter',function(){if(!isDesktop()) return;w.clearTimeout(closeTimer);openItem(item)});
      item.addEventListener('mouseleave',function(){if(!isDesktop()) return;w.clearTimeout(closeTimer);closeTimer=w.setTimeout(function(){closeItem(item)},120)});
      item.addEventListener('focusin',function(){if(!isDesktop()) return;w.clearTimeout(closeTimer);openItem(item)});
      item.addEventListener('focusout',function(e){if(!isDesktop()) return;var panel=getPanel(item);if(item.contains(e.relatedTarget)||(panel&&panel.contains(e.relatedTarget))) return;w.clearTimeout(closeTimer);closeTimer=w.setTimeout(function(){closeItem(item)},100)});
      if(link){
        link.addEventListener('click',function(e){
          if(!isDesktop()) return;
          var panel=getPanel(item);if(!panel) return;
          e.preventDefault();
          if(item.classList.contains('is-open')) closeItem(item); else openItem(item);
        });
      }
    });

    function lock(){d.documentElement.classList.add('zpNewNav-lock');d.body.classList.add('zpNewNav-lock')}
    function unlock(){d.documentElement.classList.remove('zpNewNav-lock');d.body.classList.remove('zpNewNav-lock')}
    /* v2.2.519 — single source of truth for the mobile drawer:
       one state flag, one open/close/toggle, ONE event type (click).
       Fixes: re-open after X (X sat over the burger), can't-reopen, lag. */
    var isDrawerOpen=false;
    var suppressDrawerClickUntil=0;
    function isDrawerDomOpen(){
      var drawer=qs('#zpNewNavDrawer',nav);
      return !!(drawer && !drawer.classList.contains('is-closing') && (drawer.classList.contains('is-open') || drawer.getAttribute('aria-hidden')==='false'));
    }
    function openDrawer(){
      try{w.__zpNavUiBusyUntil=Date.now()+(homeIconManaged?420:1600);}catch(e){}
      var drawer=qs('#zpNewNavDrawer',nav), burger=qs('.zpNewNav__burger',nav);
      if(!drawer) return;
      if(drawer.__zpCloseTimer){clearTimeout(drawer.__zpCloseTimer);drawer.__zpCloseTimer=0;}
      nav.classList.remove('zpNewNav--drawer-closing');
      drawer.classList.remove('is-closing');
      if(isDrawerDomOpen()){
        isDrawerOpen=true;
        if(burger) burger.setAttribute('aria-expanded','true');
        lock();
        return;
      }
      isDrawerOpen=true;
      drawer.hidden=false; drawer.removeAttribute('hidden');
      drawer.setAttribute('aria-hidden','false');
      drawer.classList.add('is-open');
      if(burger) burger.setAttribute('aria-expanded','true');
      lock();
      /* Icons must never hydrate on the hamburger tap. They are rendered at init/idle. */
      if(!nav.classList.contains('zp-icons-ready')){w.setTimeout(renderIcons,220);}
    }
    function closeDrawer(){
      try{w.__zpNavUiBusyUntil=Date.now()+(homeIconManaged?320:1600);}catch(e){}
      isDrawerOpen=false;
      suppressDrawerClickUntil=Date.now()+(homeIconManaged?260:1200);
      var drawer=qs('#zpNewNavDrawer',nav), burger=qs('.zpNewNav__burger',nav);
      if(drawer){
        if(drawer.__zpCloseTimer){clearTimeout(drawer.__zpCloseTimer);}
        drawer.classList.remove('is-open');
        drawer.classList.add('is-closing');
        drawer.setAttribute('aria-hidden','true');
        if(homeIconManaged){
          drawer.classList.remove('is-closing');
          drawer.hidden=true; drawer.setAttribute('hidden','hidden');
        }else{
          drawer.hidden=false; drawer.removeAttribute('hidden');
          drawer.__zpCloseTimer=w.setTimeout(function(){
            if(!drawer.classList.contains('is-open')){
              drawer.classList.remove('is-closing');
              drawer.hidden=true; drawer.setAttribute('hidden','hidden');
            }
          },420);
        }
      }
      if(burger) burger.setAttribute('aria-expanded','false');
      unlock();
      nav.classList.remove('zpNewNav--drawer-open');
      nav.classList.add('zpNewNav--drawer-closing');
      w.setTimeout(function(){nav.classList.remove('zpNewNav--drawer-closing');},homeIconManaged?280:1250);
    }
    function toggleDrawer(){ if(isDrawerDomOpen() || isDrawerOpen) closeDrawer(); else openDrawer(); }

    /* Mobile drawer + accordion handlers — ONE click event per element, central state.
       Search is intentionally removed on mobile, so no search handlers here. */
    var burger=qs('.zpNewNav__burger',nav);
    function bindTap(el,fn,opts){
      if(!el) return;
      opts=opts||{};
      var lastFastTap=0;
      function run(e){
        if(e){
          if(e.type==='click' && opts.isBurger && Date.now()<suppressDrawerClickUntil){
            e.preventDefault();e.stopPropagation();if(e.stopImmediatePropagation)e.stopImmediatePropagation();return;
          }
          e.preventDefault();e.stopPropagation();if(e.stopImmediatePropagation)e.stopImmediatePropagation();
        }
        try{w.__zpNavUiBusyUntil=Date.now()+(homeIconManaged?420:1600);}catch(_e){}
        lastFastTap=Date.now();
        fn(e);
      }
      if(w.PointerEvent){
        el.addEventListener('pointerdown',run,{passive:false});
        el.addEventListener('click',function(e){
          if(Date.now()-lastFastTap<760 || (opts.isBurger && Date.now()<suppressDrawerClickUntil)){
            e.preventDefault();e.stopPropagation();if(e.stopImmediatePropagation)e.stopImmediatePropagation();return;
          }
          run(e);
        },false);
      }else{
        el.addEventListener('touchstart',run,{passive:false});
        el.addEventListener('click',function(e){
          if(Date.now()-lastFastTap<760 || (opts.isBurger && Date.now()<suppressDrawerClickUntil)){
            e.preventDefault();e.stopPropagation();if(e.stopImmediatePropagation)e.stopImmediatePropagation();return;
          }
          run(e);
        },false);
      }
    }
    var earlyDrawerApi=w.__zpEarlyDrawerApi;
    if(!earlyDrawerApi){
      bindTap(burger,toggleDrawer,{isBurger:true});
      qsa('[data-zpnn-close]',nav).forEach(function(btn){ bindTap(btn,closeDrawer); });
    }
    /* tapping a real menu link closes the drawer (link still navigates) */
    qsa('#zpNewNavDrawer .zpNewNav__mLink, #zpNewNavDrawer .zpNewNav__drawerContact',nav).forEach(function(a){ a.addEventListener('click',function(){ if(earlyDrawerApi) earlyDrawerApi.close(); else closeDrawer(); }); });
    qsa('.zpNewNav__mSummary',nav).forEach(function(btn){btn.addEventListener('click',function(e){e.preventDefault();var item=btn.closest('.zpNewNav__mItem');if(!item) return;var isOpen=item.classList.contains('is-open');qsa('.zpNewNav__mItem',nav).forEach(function(other){if(other!==item){other.classList.remove('is-open');var b=qs('.zpNewNav__mSummary',other);if(b) b.setAttribute('aria-expanded','false')}});item.classList.toggle('is-open',!isOpen);btn.setAttribute('aria-expanded',!isOpen?'true':'false')})});

    d.addEventListener('click',function(e){var target=e.target;var insideNav=nav.contains(target);var insidePortal=!!(target.closest&&target.closest('.zpNewNav__mega--portal'));if(!insideNav&&!insidePortal) closeAll()});
    d.addEventListener('keydown',function(e){if(e.key==='Escape'){closeAll();if(earlyDrawerApi) earlyDrawerApi.close(); else closeDrawer();}});
    w.addEventListener('scroll',function(){if(raf) return;raf=w.requestAnimationFrame(function(){setScrolledState();if(nav.classList.contains('is-mega-open')) updateMegaTop();raf=null})},{passive:true});
    w.addEventListener('resize',function(){
      if(resizeRaf) return;
      resizeRaf=w.requestAnimationFrame(function(){
        resizeRaf=null;
        updateSpacer();
        updateMegaTop();
        if(!isDesktop()) closeAll();
      });
    },{passive:true});

    function normalizePath(href){
      if(!href) return '';
      try{href=new URL(href,w.location.origin).pathname;}catch(e){}
      href=String(href||'').split('#')[0].split('?')[0];
      if(href.length>1) href=href.replace(/\/+$/,'');
      return href || '/';
    }
    function setActive(){
      var path=normalizePath(w.location.pathname||'/');
      var servicePaths=['/strony-internetowe-katowice','/sklepy-internetowe-katowice','/logo-branding-katowice','/kampanie-reklamowe'];
      var isService=servicePaths.indexOf(path)!==-1;
      qsa('.zpNewNav__link,.zpNewNav__mLink,#zpNewNavDrawer .zpNewNav__mSub a,.zpNewNav__megaLink,.zpNewNav__promoCard',nav).forEach(function(a){
        var href=normalizePath(a.getAttribute('href'));
        if(!href) return;
        if((href==='/'&&path==='/') || (href!=='/' && (path===href || path.indexOf(href+'/')===0))){
          a.classList.add('is-active');
          var item=a.closest&&a.closest('.zpNewNav__item'); if(item) item.classList.add('is-active');
        }
      });
      if(isService){
        qsa('.zpNewNav__item[data-mega],.zpNewNav__item[data-drop]',nav).forEach(function(item){
          var hasService=!!qs('a[href*="strony-internetowe-katowice"],a[href*="sklepy-internetowe-katowice"],a[href*="logo-branding-katowice"],a[href*="kampanie-reklamowe"]',item);
          if(hasService){
            item.classList.add('is-active');
            var link=qs(':scope > .zpNewNav__link',item); if(link) link.classList.add('is-active');
          }
        });
      }
    }

    setActive();
    syncHeroOverlay();
    syncAdminOffset();
    ensureSpacer();
    setScrolledState();
    updateSpacer();
    updateMegaTop();
    renderIcons();
    try{nav.classList.add('zpNewNav--tap-ready'); var dr=qs('#zpNewNavDrawer',nav); if(dr){dr.style.setProperty('will-change','opacity, transform');}}catch(e){}
    if(!syntheticAudit && ((!isDesktop()) || (d.body&&d.body.classList.contains('admin-bar')))){
      w.setTimeout(function(){updateSpacer();updateMegaTop()},120);
      w.setTimeout(function(){updateSpacer();updateMegaTop()},600);
    }
  }

  /* Footer scripts execute after the header markup already exists.  Initialising
     immediately lets us defer home-only work before legacy DOMContentLoaded jobs. */
  if(d.getElementById('zpNewNav')) initZpNewNav();
  else if(d.readyState==='loading') d.addEventListener('DOMContentLoaded',initZpNewNav);
  else initZpNewNav();
  w.addEventListener('load',initZpNewNav,{passive:true});
})(window,document);

/* ZP Suite v2.2.41 — responsive desktop header: switch to mobile UI when links no longer fit */
(function(w,d){
  'use strict';
  function qs(s,c){return (c||d).querySelector(s)}
  function qsa(s,c){return Array.prototype.slice.call((c||d).querySelectorAll(s))}
  function icons(){if(w.lucide && typeof w.lucide.createIcons==='function'){try{w.lucide.createIcons({attrs:{'stroke-width':1.9,'stroke-linecap':'round','stroke-linejoin':'round'}})}catch(e){}}}
  function init(){
    if(w.__zpHeaderSyntheticAudit) return;
    var nav=qs('#zpNewNav'); if(!nav || nav.__zpFitNav240) return; nav.__zpFitNav240=true;
    var raf=0;
    function closeDesktopDropdowns(){
      qsa('.zpNewNav__item.is-open',nav).forEach(function(item){item.classList.remove('is-open'); var a=qs(':scope > .zpNewNav__link',item); if(a) a.setAttribute('aria-expanded','false');});
      qsa('.zpNewNav__mega--portal.is-portal-open,.zpNewNav__mega.is-portal-open,.zpNewNav__drop.is-portal-open').forEach(function(p){p.classList.remove('is-portal-open');p.setAttribute('hidden','hidden');});
      nav.classList.remove('is-mega-open');
    }
    function mustUseMobile(){
      var vw=w.innerWidth || d.documentElement.clientWidth || 0;
      if(vw<=1100) return true; // CSS tablet/mobile header handles the whole 981-1100 transition band.
      /* v2.2.809: on normal desktop widths the menu is guaranteed to fit by CSS.
         Skip 8 synchronous geometry reads that Lighthouse attributed to ~190 ms forced reflow. */
      if(vw>=1240) return false;
      var inner=qs('.zpNewNav__inner',nav), brand=qs('.zpNewNav__brand',nav), desktop=qs('.zpNewNav__desktop',nav), menu=qs('.zpNewNav__menu',nav), actions=qs('.zpNewNav__actions',nav);
      if(!inner||!desktop||!menu||!actions||!brand) return false;
      // Temporarily remove force class for a truthful desktop measurement.
      var had=nav.classList.contains('zpNewNav--forceMobile');
      var hadBody=d.body.classList.contains('zpNewNav-force-mobile');
      if(had) nav.classList.remove('zpNewNav--forceMobile');
      if(hadBody) d.body.classList.remove('zpNewNav-force-mobile');
      var innerW=inner.clientWidth || 0;
      var brandW=Math.ceil(brand.getBoundingClientRect().width||0);
      var desktopW=Math.ceil(desktop.scrollWidth||desktop.getBoundingClientRect().width||0);
      var actionsW=Math.ceil(actions.scrollWidth||actions.getBoundingClientRect().width||0);
      var menuW=Math.ceil(menu.scrollWidth||menu.getBoundingClientRect().width||0);
      var required=brandW+desktopW+actionsW+64;
      var overlap=false;
      try{
        var m=menu.getBoundingClientRect(), a=actions.getBoundingClientRect();
        overlap = m.right > (a.left-14);
      }catch(e){}
      if(had) nav.classList.add('zpNewNav--forceMobile');
      if(hadBody) d.body.classList.add('zpNewNav-force-mobile');
      return required>innerW || overlap || (menuW+actionsW+brandW+64)>innerW;
    }
    function sync(){
      raf=0;
      var force=mustUseMobile();
      nav.classList.toggle('zpNewNav--forceMobile',force);
      d.body.classList.toggle('zpNewNav-force-mobile',force);
      if(force) closeDesktopDropdowns();
      /* Icon hydration is owned by the primary header init; rescanning the whole document here
         caused duplicate DOM mutations and style recalculation on load/resize. */
    }
    function schedule(){if(raf) return; raf=w.requestAnimationFrame(sync)}
    function scheduleInitial(){
      var vw=w.innerWidth || d.documentElement.clientWidth || 0;
      if(vw<=1100){schedule();return;}
      if('requestIdleCallback' in w){w.requestIdleCallback(schedule,{timeout:900});}
      else{w.setTimeout(schedule,180);}
    }
    scheduleInitial();
    w.addEventListener('resize',schedule,{passive:true});
    w.addEventListener('orientationchange',schedule,{passive:true});
    w.addEventListener('load',function(){schedule();w.setTimeout(schedule,160);w.setTimeout(schedule,700);},{passive:true});
    if('ResizeObserver' in w){try{new ResizeObserver(schedule).observe(nav)}catch(e){}}
  }
  if(d.readyState==='loading') d.addEventListener('DOMContentLoaded',init,{once:true}); else init();
})(window,document);

/* ZP Suite v2.2.286 — guard: drawer logo only, no centered logo inline styles */
(function(){
  var logoGuardRaf=0;
  function zp286DrawerLogoGuard(){
    var nav=document.getElementById('zpNewNav');
    if(!nav) return;
    var force=document.body.classList.contains('zpNewNav-force-mobile') || nav.classList.contains('zpNewNav--forceMobile');
    var wide=window.matchMedia && window.matchMedia('(min-width:641px)').matches;
    var useLight=!!(force && wide);
    var state=(useLight?'1':'0')+'|'+(force?'1':'0')+'|'+(wide?'1':'0');
    if(nav.__zp286DrawerLogoState===state) return;
    nav.__zp286DrawerLogoState=state;
    Array.prototype.forEach.call(nav.querySelectorAll('.zpNewNav__drawerLogo--light'),function(el){
      el.style.setProperty('display',useLight?'block':'none','important');
      el.style.setProperty('opacity',useLight?'1':'0','important');
      el.style.setProperty('visibility',useLight?'visible':'hidden','important');
      el.style.setProperty('height',useLight?'46px':'0','important');
      el.style.setProperty('max-height',useLight?'46px':'0','important');
    });
    Array.prototype.forEach.call(nav.querySelectorAll('.zpNewNav__drawerLogo--dark'),function(el){
      el.style.setProperty('display',useLight?'none':'block','important');
      el.style.setProperty('opacity',useLight?'0':'1','important');
      el.style.setProperty('visibility',useLight?'hidden':'visible','important');
      el.style.setProperty('height',useLight?'0':'46px','important');
      el.style.setProperty('max-height',useLight?'0':'46px','important');
    });
  }
  function scheduleLogoGuard(){
    if(logoGuardRaf) return;
    logoGuardRaf=window.requestAnimationFrame(function(){
      logoGuardRaf=0;
      zp286DrawerLogoGuard();
    });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',zp286DrawerLogoGuard,{once:true}); else zp286DrawerLogoGuard();
  document.addEventListener('click',function(){setTimeout(scheduleLogoGuard,0);},true);
  window.addEventListener('resize',scheduleLogoGuard,{passive:true});
  window.addEventListener('scroll',scheduleLogoGuard,{passive:true});
})();


/* ZP v2.2.304 — remove legacy header shadow/line nodes if cached markup/scripts inject them. */
(function(w,d){
  var cleanRaf=0;
  var cleanRuns=0;
  function cleanHeaderArtifacts(){
    cleanRuns++;
    var ids=['zpHeaderLine2147'];
    ids.forEach(function(id){var el=d.getElementById(id); if(el && el.parentNode){el.parentNode.removeChild(el);}});
    d.querySelectorAll('.zpHeaderLine2147,.zpNewNav__line,.zpNewNav__electricLine,.zpNewNav__shadow,.zpNewNav__fade,.zpNewNav__bottomFade').forEach(function(el){
      if(el && el.parentNode){el.parentNode.removeChild(el);}
    });
    var nav=d.getElementById('zpNewNav');
    if(nav){
      nav.style.setProperty('box-shadow','none','important');
      nav.style.setProperty('filter','none','important');
      var shell=nav.querySelector('.zpNewNav__shell');
      if(shell){shell.style.setProperty('box-shadow','none','important'); shell.style.setProperty('filter','none','important');}
    }
  }
  function scheduleClean(){
    if(cleanRuns > 8 || cleanRaf) return;
    cleanRaf=w.requestAnimationFrame(function(){
      cleanRaf=0;
      cleanHeaderArtifacts();
    });
  }
  if(d.readyState==='loading') d.addEventListener('DOMContentLoaded',cleanHeaderArtifacts,{once:true}); else cleanHeaderArtifacts();
  w.addEventListener('load',cleanHeaderArtifacts,{once:true});
  w.addEventListener('scroll',scheduleClean,{passive:true});
})(window,document);
