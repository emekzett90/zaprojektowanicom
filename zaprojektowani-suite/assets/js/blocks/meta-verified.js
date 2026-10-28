(function(w,d){
  'use strict';
  if(w.__zpMetaVerified278) return;
  w.__zpMetaVerified278=1;

  function widgets(){return Array.prototype.slice.call(d.querySelectorAll('[data-zp-meta-verified]'));}
  function setOpen(box,on){
    if(!box) return;
    box.classList.toggle('is-open',!!on);
    var btn=box.querySelector('.zpMetaVerified__trigger');
    if(btn) btn.setAttribute('aria-expanded',on?'true':'false');
  }
  function closeOthers(except){widgets().forEach(function(box){if(box!==except)setOpen(box,false);});}
  function init(box){
    if(!box || box.__zpMvReady) return;
    box.__zpMvReady=1;
    var btn=box.querySelector('.zpMetaVerified__trigger');
    if(!btn) return;
    btn.addEventListener('click',function(e){
      e.preventDefault();
      var next=!box.classList.contains('is-open');
      closeOthers(box);
      setOpen(box,next);
    });
    box.addEventListener('keydown',function(e){if(e.key==='Escape'){setOpen(box,false);btn.focus();}});
  }
  function boot(){widgets().forEach(init);}
  if(d.readyState==='loading') d.addEventListener('DOMContentLoaded',boot,{once:true}); else boot();
  d.addEventListener('click',function(e){if(!e.target.closest('[data-zp-meta-verified]')) closeOthers(null);});
})(window,document);
