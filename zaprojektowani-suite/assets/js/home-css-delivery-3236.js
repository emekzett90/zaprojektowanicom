/* CSP-friendly fallback for stylesheet onload attributes; no content gate. */
(function () {
  'use strict';
  function release(link) { link.media = 'all'; }
  document.querySelectorAll('link[data-zp-full-css-3236]').forEach(function (link) {
    link.addEventListener('load', function () { release(link); }, {once: true});
    if (link.sheet) release(link);
  });
}());
