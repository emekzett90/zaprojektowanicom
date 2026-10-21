/* Native scrolling remains available without JavaScript. */
(function () {
  'use strict';
  var rail = document.getElementById('zpCaseChoices');
  if (!rail) return;
  var controls = document.querySelector('.zpCS46__railControls');
  var cueObserver = null;
  var cueDismissed = false;
  function dismissCue() {
    cueDismissed = true;
    if (cueObserver) cueObserver.disconnect();
    if (controls) controls.classList.remove('is-cue-visible');
  }
  // Start the short hint when the visitor reaches it, not while it is offscreen.
  if (controls && 'IntersectionObserver' in window) {
    cueObserver = new IntersectionObserver(function (entries) {
      if (cueDismissed || !entries.some(function (entry) { return entry.isIntersecting; })) return;
      if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        controls.classList.add('is-cue-visible');
      }
      cueObserver.disconnect();
    }, { threshold: 0.3 });
    cueObserver.observe(controls);
  }
  rail.addEventListener('pointerdown', dismissCue, { passive: true });
  rail.addEventListener('keydown', dismissCue);
  document.querySelectorAll('[data-zp-cases-prev], [data-zp-cases-next]').forEach(function (button) {
    button.addEventListener('click', function () {
      dismissCue();
      var first = rail.firstElementChild;
      var distance = first ? first.getBoundingClientRect().width + 12 : rail.clientWidth;
      rail.scrollBy({
        left: button.hasAttribute('data-zp-cases-next') ? distance : -distance,
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'
      });
    });
  });
}());

/* Project technology rows: short, visible-on-arrival mobile hints. */
(function () {
  'use strict';
  var mobile = window.matchMedia('(max-width: 760px)');
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  document.querySelectorAll('[data-zp-tech-stack]').forEach(function (stack) {
    var rail = stack.querySelector('.zpCS18__techMarks');
    var button = stack.querySelector('[data-zp-tech-next]');
    if (!rail || !button) return;
    var visible = false;
    var dismissed = false;
    var started = false;
    function dismiss() {
      dismissed = true;
      stack.classList.remove('is-cue-visible');
    }
    function refresh() {
      var overflow = rail.scrollWidth > rail.clientWidth + 2;
      stack.classList.toggle('is-overflowing', overflow);
      button.disabled = !overflow || rail.scrollLeft + rail.clientWidth >= rail.scrollWidth - 2;
      if (!mobile.matches || reduced.matches) stack.classList.remove('is-cue-visible');
      if (visible && overflow && mobile.matches && !reduced.matches && !dismissed && !started) {
        started = true;
        stack.classList.add('is-cue-visible');
      }
    }
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        visible = entries[0].isIntersecting;
        refresh();
      }, { threshold: 0.5 });
      observer.observe(stack);
    }
    if ('ResizeObserver' in window) new ResizeObserver(refresh).observe(rail);
    else window.addEventListener('resize', refresh, { passive: true });
    rail.addEventListener('pointerdown', dismiss, { passive: true });
    rail.addEventListener('keydown', dismiss);
    rail.addEventListener('scroll', function () { dismiss(); refresh(); }, { passive: true });
    button.addEventListener('click', function () {
      dismiss();
      rail.scrollBy({ left: Math.max(180, rail.clientWidth * 0.75), behavior: reduced.matches ? 'instant' : 'smooth' });
    });
    button.addEventListener('animationend', dismiss);
    if (reduced.addEventListener) reduced.addEventListener('change', refresh);
    refresh();
  });
}());
