/* The footer binds its own control; legal documents need their own scoped CMP action. */
(function(){
  'use strict';
  document.addEventListener('click',function(event){
    var control=event.target.closest&&event.target.closest('.zpLegal [data-zp-cookie-settings]');
    if(!control)return;
    var cmp=window.Cookiebot;
    if(cmp&&typeof cmp.show==='function'){event.preventDefault();cmp.show();}
    else if(cmp&&typeof cmp.renew==='function'){event.preventDefault();cmp.renew();}
    // Otherwise preserve the native link to the visible instructions in /cookies/.
  });
})();
