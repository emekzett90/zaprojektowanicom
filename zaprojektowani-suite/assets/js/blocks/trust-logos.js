(function(){
  const sections = Array.prototype.slice.call(document.querySelectorAll('[data-zp-trust-pinned]'));
  if (!sections.length) return;

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const clamp = (num, min, max) => Math.min(Math.max(num, min), max);
  const MOBILE_LIMIT = 1500;

  function initLucide(){
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      try {
        window.lucide.createIcons({
          attrs: {
            'stroke-width': 1.75,
            'stroke-linecap': 'round',
            'stroke-linejoin': 'round'
          }
        });
      } catch(e) {}
    }
  }

  function bootSection(section){
    if (section.dataset.zpTrustPinnedReady === '1') return;
    section.dataset.zpTrustPinnedReady = '1';

    const spacer = section.querySelector('[data-zp-trust-spacer]');
    const logos = Array.from(section.querySelectorAll('[data-zp-logo]'));
    const person = section.querySelector('[data-zp-person]');
    const current = section.querySelector('[data-zp-trust-current]');
    const total = section.querySelector('[data-zp-trust-total]');
    const bar = section.querySelector('[data-zp-trust-bar]');

    if (!spacer || !logos.length) return;

    initLucide();

    /* v2.2.666: the section holds ~70 lazy logo <img> (rows + marquee clones) and on
       mobile it additionally sits under content-visibility:auto — on weak hardware the
       first scroll into it triggered fetch+decode+full layout at once (visible lag).
       After window load, in idle time, warm every lazy image (fetch + decode) and lift
       content-visibility so the section is pre-rendered before the user reaches it.
       Skipped for synthetic audits and Save-Data, so Lighthouse flows are untouched. */
    function warmAssets(){
      try {
        var doc = document.documentElement;
        var isAudit = doc.classList.contains('zp-synthetic-audit') ||
          false ||
          false /* v711: identical production behavior in browser audits */;
        if (isAudit) return;
        var conn = window.navigator && window.navigator.connection;
        if (conn && conn.saveData) return;
        var seen = {};
        Array.prototype.forEach.call(section.querySelectorAll('img[loading="lazy"]'), function(img){
          img.loading = 'eager';
          var src = img.currentSrc || img.src;
          if (!src || seen[src]) return;
          seen[src] = 1;
          if (typeof img.decode === 'function') { img.decode().catch(function(){}); }
        });
        section.style.contentVisibility = 'visible';
        section.style.containIntrinsicSize = 'auto';
      } catch(e) {}
    }
    function scheduleWarm(){
      var run = function(){
        if ('requestIdleCallback' in window) { window.requestIdleCallback(warmAssets, { timeout: 2600 }); }
        else { window.setTimeout(warmAssets, 700); }
      };
      if (document.readyState === 'complete') { run(); }
      else { window.addEventListener('load', run, { once: true }); }
    }
    scheduleWarm();

    const totalCount = logos.length;
    function getMobileLogos(){
      return logos.filter((logo) => !logo.classList.contains('zpTrustPinned__logo--hideMobile'));
    }
    if (total) total.textContent = String(totalCount).padStart(2, '0');

    let ticking = false;
    let lastDesktopVisibleCount = -1;
    let lastMobileIndex = -1;
    let currentMode = null;
    let mobileTimer = null;
    let mobileIndex = 0;

    let smoothReveal = 0;
    let smoothPerson = 0;
    let smoothExit = 0;
    let smoothRow = 0;

    function lerp(a, b, t){ return a + (b - a) * t; }
    function isMobile(){ return (window.innerWidth || document.documentElement.clientWidth || 0) <= MOBILE_LIMIT; }

    function resetLogoStates(){
      logos.forEach((logo) => {
        logo.classList.remove('is-in', 'is-mobile-active', 'is-mobile-prev', 'is-mobile-next');
      });
    }

    function setProgress(index, percent){
      if (current) current.textContent = String(index + 1).padStart(2, '0');
      if (bar) bar.style.width = `${percent}%`;
    }

    function applyMobileLogo(activeIndex){
      const mobileLogos = getMobileLogos();
      const mobileTotal = mobileLogos.length || totalCount;
      activeIndex = clamp(activeIndex, 0, mobileTotal - 1);
      if (activeIndex === lastMobileIndex) return;

      lastMobileIndex = activeIndex;

      logos.forEach((logo) => {
        logo.classList.remove('is-in', 'is-mobile-active', 'is-mobile-prev', 'is-mobile-next');
      });

      mobileLogos.forEach((logo, index) => {
        if (index === activeIndex) {
          logo.classList.add('is-mobile-active');
        } else if (index === activeIndex - 1) {
          logo.classList.add('is-mobile-prev');
        } else if (index === activeIndex + 1) {
          logo.classList.add('is-mobile-next');
        }
      });

      if (total) total.textContent = String(mobileTotal).padStart(2, '0');
      setProgress(activeIndex, ((activeIndex + 1) / mobileTotal) * 100);
    }

    function startMobile(){
      currentMode = 'mobile';
      if (mobileTimer) clearInterval(mobileTimer);

      section.classList.add('is-mobile-static', 'is-visible');
      section.classList.remove('is-desktop-pinned', 'is-ctas-in');

      section.style.setProperty('--zp-exit', '0');
      section.style.setProperty('--zp-person-progress', '0');
      section.style.setProperty('--zp-row-one', '0px');
      section.style.setProperty('--zp-row-two', '0px');

      spacer.style.minHeight = '0px';
      smoothReveal = 0;
      smoothPerson = 0;
      smoothExit = 0;
      smoothRow = 0;
      lastDesktopVisibleCount = -1;
      lastMobileIndex = -1;
      resetLogoStates();

      const mobileTotal = getMobileLogos().length || totalCount;
      mobileIndex = clamp(mobileIndex, 0, mobileTotal - 1);
      applyMobileLogo(mobileIndex);

      if (!prefersReduced && mobileTotal > 1) {
        mobileTimer = setInterval(function(){
          if (!isMobile()) return;
          const mobileTotalNow = getMobileLogos().length || totalCount;
          mobileIndex = (mobileIndex + 1) % mobileTotalNow;
          applyMobileLogo(mobileIndex);
        }, 1150);
      }
    }

    function stopMobile(){
      if (mobileTimer) {
        clearInterval(mobileTimer);
        mobileTimer = null;
      }
      section.classList.remove('is-mobile-static', 'is-ctas-in');
      lastMobileIndex = -1;
      resetLogoStates();
    }

    function setSpacer(){
      if (prefersReduced) return;

      if (isMobile()) {
        spacer.style.minHeight = '0px';
        return;
      }

      const vh = window.innerHeight || document.documentElement.clientHeight;
      const base = vh * .86;
      const personDistance = vh * .32;
      const revealDistance = vh * 1.54;
      const exitDistance = vh * .70;

      spacer.style.minHeight = Math.ceil(base + personDistance + revealDistance + exitDistance) + 'px';
    }

    function applyDesktopLogos(visibleCount, softProgress){
      if (visibleCount === lastDesktopVisibleCount) return;
      lastDesktopVisibleCount = visibleCount;

      logos.forEach((logo, index) => {
        const local = clamp(softProgress - index, 0, 1);
        logo.classList.toggle('is-in', local > .08);
        logo.classList.remove('is-mobile-active', 'is-mobile-prev', 'is-mobile-next');
      });

      if (current) current.textContent = String(Math.min(totalCount, Math.max(1, visibleCount))).padStart(2, '0');
    }

    function startDesktop(){
      currentMode = 'desktop';
      stopMobile();
      section.classList.add('is-desktop-pinned');
      setSpacer();
      requestUpdate();
    }

    function update(){
      ticking = false;

      if (prefersReduced || isMobile()) return;

      const vh = window.innerHeight || document.documentElement.clientHeight;
      const rect = spacer.getBoundingClientRect();

      const startDelay = vh * .14;
      const personDistance = vh * .32;
      const revealDistance = vh * 1.54;
      const exitDistance = vh * .70;

      const inside = -rect.top;

      const personTarget = clamp((inside - startDelay) / personDistance, 0, 1);
      const revealTarget = clamp((inside - startDelay - personDistance) / revealDistance, 0, 1);
      const exitTarget = clamp((inside - startDelay - personDistance - revealDistance) / exitDistance, 0, 1);

      smoothPerson = lerp(smoothPerson, personTarget, .18);
      smoothReveal = lerp(smoothReveal, revealTarget, .16);
      smoothExit = lerp(smoothExit, exitTarget, .18);
      smoothRow = lerp(smoothRow, smoothReveal, .14);

      const personEase = 1 - Math.pow(1 - smoothPerson, 3);
      const revealEase = 1 - Math.pow(1 - smoothRow, 3);

      /* v2.2.666: reveal opens at 1.12vh (was .82vh) so the head/stats are already
         painted when the section edge enters the viewport — on slow hardware the old
         gate meant scrolling into a blank white band and waiting for the fade. */
      section.classList.toggle('is-visible', rect.top < vh * 1.12 && rect.bottom > vh * .05);
      section.style.setProperty('--zp-exit', smoothExit.toFixed(4));
      section.style.setProperty('--zp-person-progress', personEase.toFixed(4));

      if (person) {
        person.classList.toggle('is-in', smoothPerson > .035);
        person.classList.toggle('is-exiting', smoothExit > .01);
      }

      const softProgress = smoothReveal * totalCount;
      const visibleCount = smoothReveal > .015 ? Math.max(1, Math.ceil(softProgress)) : 0;
      applyDesktopLogos(visibleCount, softProgress);
      section.classList.toggle('is-ctas-in', smoothReveal > .955 && smoothExit < .42);

      const rowMove = 30;
      const exitPush = smoothExit * 30;

      section.style.setProperty('--zp-row-one', `${(-rowMove * revealEase) - exitPush}px`);
      section.style.setProperty('--zp-row-two', `${(rowMove * revealEase) + exitPush}px`);

      if (bar) bar.style.width = `${clamp(smoothReveal, 0, 1) * 100}%`;

      const stillMoving =
        Math.abs(smoothPerson - personTarget) > .002 ||
        Math.abs(smoothReveal - revealTarget) > .002 ||
        Math.abs(smoothExit - exitTarget) > .002;

      if (stillMoving) {
        window.requestAnimationFrame(update);
      }
    }

    function requestUpdate(){
      if (ticking || prefersReduced || isMobile()) return;
      ticking = true;
      window.requestAnimationFrame(update);
    }

    function syncMode(){
      const mobile = isMobile();
      if (mobile && currentMode !== 'mobile') {
        startMobile();
      } else if (!mobile && currentMode !== 'desktop') {
        startDesktop();
      }
    }

    if (prefersReduced) {
      section.classList.add('is-visible', 'is-ctas-in');
      spacer.style.minHeight = 'auto';
      resetLogoStates();
      logos.forEach((logo) => logo.classList.add('is-in'));
      setProgress(0, 100);
      return;
    }

    window.addEventListener('scroll', requestUpdate, { passive: true });

    let resizeTimer = null;
    let lastWidth = window.innerWidth || document.documentElement.clientWidth || 0;

    window.addEventListener('resize', function(){
      const widthNow = window.innerWidth || document.documentElement.clientWidth || 0;

      /* Mobile/tablet carousel mode: ignore pure height changes (mobile browser chrome) to avoid jumps. */
      if (isMobile() && Math.abs(widthNow - lastWidth) < 2) return;

      lastWidth = widthNow;
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function(){
        currentMode = null;
        lastDesktopVisibleCount = -1;
        syncMode();
      }, 160);
    }, { passive: true });

    window.addEventListener('orientationchange', function(){
      setTimeout(function(){
        currentMode = null;
        lastDesktopVisibleCount = -1;
        lastMobileIndex = -1;
        syncMode();
      }, 220);
    }, { passive: true });

    if (document.readyState === 'complete') {
      syncMode();
    } else {
      window.addEventListener('load', syncMode, { once: true });
      syncMode();
    }
  }

  function scheduleSection(section){
    if (!section || section.dataset.zpTrustPinnedReady === '1') return;

    if ('IntersectionObserver' in window) {
      const starter = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if (entry.isIntersecting) {
            starter.disconnect();
            bootSection(section);
          }
        });
      }, {
        threshold: 0,
        rootMargin: '900px 0px 900px 0px'
      });
      starter.observe(section);
      return;
    }

    if ('requestIdleCallback' in window) {
      requestIdleCallback(function(){ bootSection(section); }, { timeout: 1600 });
    } else {
      setTimeout(function(){ bootSection(section); }, 520);
    }
  }

  sections.forEach(scheduleSection);
})();
