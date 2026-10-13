/*! Studio USD range; native budget state/submission retains exact allowed PLN ticks. */
(function () {
  'use strict';
  var config=document.getElementById('zp-en-currency-config'),cfg;
  if (!config) return;
  try { cfg=JSON.parse(config.textContent); } catch(e) { return; }
  if (cfg.source!=='/studio-wyceny/'||cfg.policy!=='usd_nbp_human_v1') return;
  var states=new WeakMap(),pending=0,observer=null;
  var CAPS={web:[0,30000,500],shop:[0,60000,1000],brand:[1000,15000,500],ads:[0,20000,250]};
  function english() { return /^en(?:-|$)/i.test(document.documentElement.lang||''); }
  function api() { var a=window.ZP_EN_Currency;return a&&a.enabled&&a.policy===cfg.policy&&typeof a.usd==='function'?a:null; }
  function ticks(min,max,step,price) {
    if (!Number.isInteger(min)||!Number.isInteger(max)||!Number.isInteger(step)||step<=0||max<min||(max-min)/step>128) return [];
    var rows=[];for(var pln=min;pln<=max;pln+=step) { var usd=price(pln*100);if (!Number.isInteger(usd)||usd<0) return [];rows.push({pln:pln,usd:usd}); }return rows;
  }
  function nearest(rows,value,currentPLN) {
    if (!rows.length||!Number.isFinite(value)) return null;
    var best=rows[0];rows.forEach(function(row){var d=Math.abs(row.usd-value),b=Math.abs(best.usd-value);if(d<b||(d===b&&row.usd>best.usd)||(d===b&&row.usd===best.usd&&Math.abs(row.pln-currentPLN)<Math.abs(best.pln-currentPLN))) best=row;});return best;
  }
  function currentRow(s) { return s.rows.find(function(row){return row.pln===Number(s.source.value);})||null; }
  function sync(s) {
    var row=currentRow(s);if (!row) return;
    s.proxy.value=String(row.usd);s.proxy.setAttribute('aria-valuetext','USD '+String(row.usd).replace(/\B(?=(\d{3})+(?!\d))/g,','));
    s.proxy.disabled=s.source.disabled;
  }
  function apply(s,row) {
    if (!english()||!api()||!row||s.source.isConnected===false) return;
    s.source.value=String(row.pln);
    // Same original input event updates state.budget, native summary and submitted data().budget.
    s.source.dispatchEvent(new Event('input',{bubbles:true}));sync(s);
  }
  function decorate(source,root) {
    if (states.has(source)) { sync(states.get(source));return; }
    var price=api(),service=root.getAttribute('data-service'),cap=CAPS[service];
    if (!price||!cap||source.type!=='range'||source.getAttribute('data-bound')!=='1'||[source.min,source.max,source.step].some(function(v,i){return Number(v)!==cap[i];})) return;
    var rows=ticks(cap[0],cap[1],cap[2],price.usd),actual=rows.find(function(row){return row.pln===Number(source.value);});if(!actual) return;
    var proxy=source.cloneNode(false);proxy.removeAttribute('id');proxy.removeAttribute('name');proxy.removeAttribute('data-budget-range');proxy.removeAttribute('data-bound');proxy.setAttribute('data-zp-usd-budget','');
    proxy.min=String(rows[0].usd);proxy.max=String(rows[rows.length-1].usd);proxy.step='any';proxy.value=String(actual.usd);
    proxy.setAttribute('aria-label','Approximate budget in US dollars');proxy.setAttribute('data-zpl-skip','');
    proxy.setAttribute('title','Budget range: USD '+rows[0].usd+'–USD '+String(rows[rows.length-1].usd).replace(/\B(?=(\d{3})+(?!\d))/g,','));
    var s={source:source,proxy:proxy,rows:rows};states.set(source,s);
    source.insertAdjacentElement('afterend',proxy);source.style.setProperty('display','none','important');source.setAttribute('aria-hidden','true');
    proxy.addEventListener('input',function(){apply(s,nearest(rows,Number(proxy.value),Number(source.value)));});
    proxy.addEventListener('keydown',function(e){
      var row=currentRow(s);if(!row) return;var unique=[];rows.forEach(function(r){if(unique.indexOf(r.usd)<0) unique.push(r.usd);});var i=unique.indexOf(row.usd),next=i;
      if(e.key==='ArrowRight'||e.key==='ArrowUp')next=i+1;else if(e.key==='ArrowLeft'||e.key==='ArrowDown')next=i-1;else if(e.key==='PageUp')next=i+10;else if(e.key==='PageDown')next=i-10;else if(e.key==='Home')next=0;else if(e.key==='End')next=unique.length-1;else return;
      e.preventDefault();apply(s,nearest(rows,unique[Math.max(0,Math.min(unique.length-1,next))],Number(source.value)));
    });
    source.addEventListener('input',function(){sync(s);});source.addEventListener('change',function(){sync(s);});sync(s);
  }
  function run() {
    pending=0;if(!english()||!api())return;var root=document.getElementById('zpbsUltimate');if(!root)return;
    Array.prototype.forEach.call(root.querySelectorAll('input[data-budget-range]'),function(source){decorate(source,root);});
    if (!observer&&window.MutationObserver) { observer=new MutationObserver(function(){if(!pending)pending=requestAnimationFrame(run);});observer.observe(root,{subtree:true,childList:true}); }
  }
  // Exposed pure arithmetic is read-only; no route, form submission, network or state store is added.
  window.ZP_EN_StudioBudget={ticks:ticks,nearest:nearest};
  document.addEventListener('zpl:change',run);
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',run,{once:true});else run();
})();
