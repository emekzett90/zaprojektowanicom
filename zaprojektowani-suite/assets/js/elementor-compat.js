/* Keep deprecated Elementor aliases callable, without exposing their getters
   to incidental Object.keys() scans (Meta automaticmatchingregex). */
(function (w) {
  'use strict';
  var common = w.elementorModules && w.elementorModules.common;
  if (!common || typeof Object.getOwnPropertyDescriptor !== 'function') { return; }

  ['Component', 'ComponentModal', 'HookBreak'].forEach(function (name) {
    var descriptor = Object.getOwnPropertyDescriptor(common, name);
    if (!descriptor || !descriptor.enumerable || !descriptor.configurable || typeof descriptor.get !== 'function') { return; }
    var source = Function.prototype.toString.call(descriptor.get);
    if (source.indexOf('elementorModules.common.' + name) === -1 || source.indexOf('.deprecation.deprecated') === -1) { return; }
    // Preserve the exact getter, setter and flags. Explicit legacy API reads
    // still return the original class and still emit Elementor's warning.
    descriptor.enumerable = false;
    Object.defineProperty(common, name, descriptor);
  });
})(window);
