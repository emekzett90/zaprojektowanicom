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
    /* 3.2.80: public PL home at every breakpoint reads the initial scroll before
       our class/style writes, including a restored mobile scroll position.
       The existing hero-overlay spacer has zero height; regular scroll reads
       and every other header context keep their original behavior. */
    var initialScrollY=null;
    try{
      var initialBody=d.body;
      if(homeIconManaged && initialBody && initialBody.classList
        && (initialBody.classList.contains('home') || initialBody.classList.contains('front-page'))
        && !initialBody.classList.contains('admin-bar')
        && nav.classList.contains('zpNewNav--heroOverlay')
        && d.getElementById('zhHero') && /^pl(?:-|$)/i.test(d.documentElement.lang||'')
        && w.matchMedia){
        initialScrollY=w.scrollY || d.documentElement.scrollTop || 0;
      }
    }catch(e){}
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
    var caseHeader=nav.getAttribute('data-zp-case-header')==='1';
    var caseHero=caseHeader ? qs('.zpCS18 > .zpCS18__hero') : null;
    /* v2.2.420: do not visually gate the mobile header on Lucide.
       CSS reserves icon boxes, so controls are stable from first paint. */
    nav.classList.add('zp-icons-ready');
    var desktopMq=w.matchMedia('(min-width:1361px)');
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

    /* 3.1.1: a class is set only when it changes. Setting it again on every scroll frame, even unchanged,
       woke every script that watches the header for changes (the language switch queued three updates
       and two timers per frame). */
    function setCls(el,name,on){ if(el && el.classList.contains(name)!==!!on) el.classList.toggle(name,!!on); }

    function syncHeroOverlay(){
      setCls(nav,'zpNewNav--heroOverlay', shouldUseHeroOverlay());
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
      try{compact=w.matchMedia&&w.matchMedia('(max-width:1360px)').matches;}catch(e){}
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
      /* 3.2.34: mobile home has its final CSS clearance from first paint.
         Do not inject a new in-flow element when the deferred controller starts. */
      if(!isDesktop() && d.body && d.body.classList.contains('zp-mobile-shell-3234')){
        syncHeroOverlay();
        if(spacer && spacer.parentNode) spacer.style.height='0px';
        return;
      }
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

    function applyMobileGlass(scrolled){
      if(!shell) return;
      var mobile = w.matchMedia('(max-width:1360px)').matches;
      var sticky = scrolled && mobile && !nav.classList.contains('is-mega-open');

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

    function setScrolledState(initialY){
      var y=typeof initialY==='number' ? initialY : (w.scrollY || d.documentElement.scrollTop || 0);
      // Only the verified Case Studies shell waits for its first light section.
      // Read both boundaries before the existing state/style writes below.
      var scrolled=y>18;
      if(caseHeader){
        caseHero=caseHero || qs('.zpCS18 > .zpCS18__hero');
        if(caseHero){scrolled=caseHero.getBoundingClientRect().bottom <= (shell || nav).getBoundingClientRect().bottom;}
      }
      syncHeroOverlay();
      setCls(nav,'is-scrolled', scrolled);
      setCls(d.body,'zp-nav-scrolled', scrolled);
      applyMobileGlass(scrolled);
      updateSpacer();
    }

    function updateMegaTop(){
      if(!shell) return;
      syncAdminOffset();
      /* v2.2.511: do not measure a transitional/hovering shell. Desktop header is locked to 88px,
         so mega menu always starts exactly below it and never creates a blank gap or header drop. */
      var desktop=isDesktop();
      /* 3.2.80: this offset positions desktop mega portals only. The mobile
         drawer has its own fixed viewport layout. Keep admin offset syncing,
         and calculate the portal offset normally when resizing to desktop. */
      if(!desktop) return;
      /* v2.2.809: desktop header heights are fixed in CSS. Reading the shell rect here
         after class/style writes forced a full-page reflow during startup. */
      var h=88, top=0;
      if(desktop){
        if(nav.classList.contains('is-mega-open')) h=85;
        else if(nav.classList.contains('zpNewNav--heroOverlay') && !nav.classList.contains('is-scrolled')) h=104;
        top=getAdminTop()+h;
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

    /* The inline controller is ready as soon as the header is parsed. Keep a
       small fallback for installations that strip inline scripts. */
    var earlyDrawerApi=w.__zpEarlyDrawerApi;
    var drawer=qs('#zpNewNavDrawer',nav), burger=qs('.zpNewNav__burger',nav);
    var drawerReturnFocus=null;
    function focusDrawerControl(el){
      if(!el || !el.focus) return;
      try{el.focus({preventScroll:true});}catch(e){el.focus();}
    }
    function isDrawerDomOpen(){return !!(drawer && drawer.classList.contains('is-open'));}
    function closeDrawer(restoreFocus){
      if(earlyDrawerApi){earlyDrawerApi.close(restoreFocus);return;}
      var wasOpen=isDrawerDomOpen();
      if(wasOpen && restoreFocus!==false) focusDrawerControl(drawerReturnFocus || burger);
      else if(wasOpen && isDesktop() && drawer.contains(d.activeElement)) focusDrawerControl(qs('a.zpNewNav__brand',nav) || qs('.zpNewNav__brand a[href]',nav));
      drawerReturnFocus=null;
      if(drawer){drawer.classList.remove('is-open','is-closing');drawer.hidden=true;drawer.setAttribute('aria-hidden','true');drawer.setAttribute('inert','');}
      if(burger) burger.setAttribute('aria-expanded','false');
      nav.classList.remove('zpNewNav--drawer-open','zpNewNav--drawer-closing');
      d.documentElement.classList.remove('zpNewNav-lock');
      if(d.body) d.body.classList.remove('zpNewNav-lock');
    }
    function openDrawer(){
      if(earlyDrawerApi){earlyDrawerApi.open();return;}
      if(!drawer || isDesktop() || isDrawerDomOpen()) return;
      drawerReturnFocus=d.activeElement && d.activeElement!==d.body ? d.activeElement : burger;
      w.__zpNavUiBusyUntil=Date.now()+320;
      drawer.hidden=false;drawer.removeAttribute('hidden');drawer.removeAttribute('inert');
      drawer.setAttribute('aria-hidden','false');drawer.classList.add('is-open');
      nav.classList.add('zpNewNav--drawer-open');
      if(burger) burger.setAttribute('aria-expanded','true');
      d.documentElement.classList.add('zpNewNav-lock');
      if(d.body) d.body.classList.add('zpNewNav-lock');
      var scrollPanel=qs('.zpNewNav__drawerScroll',drawer) || qs('.zpNewNav__drawerPanel',drawer);if(scrollPanel)scrollPanel.scrollTop=0;
      focusDrawerControl(qs('.zpNewNav__drawerPanel button[data-zpnn-close]',drawer) || qs('.zpNewNav__drawerPanel',drawer));
    }
    if(!earlyDrawerApi){
      if(burger) burger.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();isDrawerDomOpen()?closeDrawer():openDrawer();});
      qsa('[data-zpnn-close]',nav).forEach(function(btn){btn.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();closeDrawer();});});
      qsa('#zpNewNavDrawer a[href]',nav).forEach(function(a){a.addEventListener('click',function(){closeDrawer(false);});});
      d.addEventListener('keydown',function(e){
        if(!isDrawerDomOpen()) return;
        if(e.key==='Escape'){e.preventDefault();e.stopImmediatePropagation();closeDrawer();return;}
        if(e.key!=='Tab') return;
        var panel=qs('.zpNewNav__drawerPanel',drawer);
        var controls=qsa('a[href],button:not([disabled]),[tabindex]:not([tabindex="-1"])',panel).filter(function(el){return !el.closest('[hidden],[inert]') && el.getClientRects().length>0;});
        var first=controls[0],last=controls[controls.length-1];
        if(!first){e.preventDefault();focusDrawerControl(panel);return;}
        if(!panel.contains(d.activeElement) || (e.shiftKey && d.activeElement===first)){e.preventDefault();focusDrawerControl(e.shiftKey?last:first);}
        else if(!e.shiftKey && d.activeElement===last){e.preventDefault();focusDrawerControl(first);}
      },true);
    }
    qsa('.zpNewNav__mSummary',nav).forEach(function(btn){btn.addEventListener('click',function(e){e.preventDefault();var item=btn.closest('.zpNewNav__mItem');if(!item) return;var isOpen=item.classList.contains('is-open');qsa('.zpNewNav__mItem',nav).forEach(function(other){if(other!==item){other.classList.remove('is-open');var b=qs('.zpNewNav__mSummary',other);if(b) b.setAttribute('aria-expanded','false')}});item.classList.toggle('is-open',!isOpen);btn.setAttribute('aria-expanded',!isOpen?'true':'false')})});

    d.addEventListener('click',function(e){var target=e.target;var insideNav=nav.contains(target);var insidePortal=!!(target.closest&&target.closest('.zpNewNav__mega--portal'));if(!insideNav&&!insidePortal) closeAll()});
    d.addEventListener('keydown',function(e){if(e.key==='Escape') closeAll();});
    w.addEventListener('scroll',function(){if(raf) return;raf=w.requestAnimationFrame(function(){setScrolledState();if(nav.classList.contains('is-mega-open')) updateMegaTop();raf=null})},{passive:true});
    w.addEventListener('resize',function(){
      if(resizeRaf) return;
      resizeRaf=w.requestAnimationFrame(function(){
        resizeRaf=null;
        setScrolledState();
        if(isDesktop()) closeDrawer(false);
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
    if(isDesktop() || !d.body || !d.body.classList.contains('zp-mobile-shell-3234')) ensureSpacer();
    setScrolledState(initialScrollY);
    if(caseHeader){
      w.addEventListener('load',function(){setScrolledState();},{once:true,passive:true});
      w.addEventListener('pageshow',function(){setScrolledState();},{passive:true});
    }
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

/* 3.2.68: the same 1360/1361 media boundary controls CSS, the early drawer
   and this controller. No geometry fit scan or width-based inline logo override. */

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
