(function(){
 'use strict';
 function initialize(root){
  if(root.dataset.zpReviewReady==='1')return;
  var rail=root.querySelector('[data-zp-review-rail]');
  var cards=rail?Array.from(rail.querySelectorAll('.zpReviews24__card')):[];
  var controls=root.querySelector('[data-zp-review-controls]');
  var prev=root.querySelector('[data-zp-review-prev]'),next=root.querySelector('[data-zp-review-next]');
  if(!rail||!cards.length||!controls||!prev||!next)return;
  root.dataset.zpReviewReady='1';controls.hidden=false;
  var reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var position=root.querySelector('[data-zp-review-position]');
  var autoplay=root.querySelector('[data-zp-review-autoplay]');
  var frame=0,timer=null,inView=false,userPaused=false,hovered=false,focused=false;
  function shuffle(list){for(var i=list.length-1;i>0;i--){var j=Math.floor(Math.random()*(i+1));var item=list[i];list[i]=list[j];list[j]=item;}return list;}
  // Shuffle each real source independently and mix them, keeping both sources visible.
  function reorder(){
   // Disable snap while moving nodes: browsers otherwise follow the old snapped card.
   var originalSnap=rail.style.scrollSnapType;rail.style.scrollSnapType='none';rail.getBoundingClientRect();
   var google=shuffle(cards.filter(function(c){return c.dataset.reviewSource==='Google';}));
   var facebook=shuffle(cards.filter(function(c){return c.dataset.reviewSource==='Facebook';}));
   var list=[],googleFirst=Math.random()>.5;
   while(google.length||facebook.length){
    var first=googleFirst?google:facebook,second=googleFirst?facebook:google;
    if(first.length)list.push(first.shift());if(second.length)list.push(second.shift());
   }
   var fragment=document.createDocumentFragment();list.forEach(function(c){fragment.appendChild(c);});rail.appendChild(fragment);cards=list;rail.scrollTo({left:0,behavior:'auto'});rail.getBoundingClientRect();rail.style.scrollSnapType=originalSnap;
  }
  reorder();
  function step(){return cards[0].getBoundingClientRect().width+(parseFloat(getComputedStyle(rail).gap)||20);}
  function update(){
   frame=0;var max=Math.max(0,rail.scrollWidth-rail.clientWidth);
   prev.disabled=rail.scrollLeft<=2;next.disabled=rail.scrollLeft>=max-2;
   var first=Math.max(0,Math.round(rail.scrollLeft/step()));
   var visible=Math.max(1,Math.round(rail.clientWidth/step()));
   if(position)position.textContent=(first+1)+'–'+Math.min(cards.length,first+visible)+' / '+cards.length;
   if(rail.scrollLeft>8)root.classList.add('zpReviews24--explored');
  }
  function schedule(){if(!frame)frame=window.requestAnimationFrame(update);}
  function stop(){if(timer!==null){window.clearInterval(timer);timer=null;}}
  function advance(){
   if(rail.scrollLeft>=rail.scrollWidth-rail.clientWidth-2){reorder();update();}
   else rail.scrollBy({left:step(),behavior:reduced?'auto':'smooth'});
  }
  function syncAutoplay(){
   stop();
   if(autoplay){
    autoplay.hidden=reduced;
    autoplay.setAttribute('aria-label',userPaused?autoplay.dataset.playLabel:autoplay.dataset.pauseLabel);
    var pauseIcon=autoplay.querySelector('[data-zp-review-pause-icon]'),playIcon=autoplay.querySelector('[data-zp-review-play-icon]');
    if(pauseIcon)pauseIcon.hidden=userPaused;if(playIcon)playIcon.hidden=!userPaused;
   }
   if(reduced||userPaused||hovered||focused||!inView||document.hidden||rail.querySelector('details[open]'))return;
   timer=window.setInterval(advance,10000);
  }
  function pauseManually(){userPaused=true;syncAutoplay();}
  function move(direction){
   pauseManually();rail.scrollBy({left:step()*Math.max(1,Math.round(rail.clientWidth/step()))*direction,behavior:reduced?'auto':'smooth'});
  }
  prev.addEventListener('click',function(){move(-1);});next.addEventListener('click',function(){move(1);});
  if(autoplay)autoplay.addEventListener('click',function(){userPaused=!userPaused;if(!userPaused){focused=false;hovered=false;advance();}syncAutoplay();});
  rail.addEventListener('scroll',schedule,{passive:true});
  rail.addEventListener('pointerdown',pauseManually,{passive:true});
  rail.addEventListener('touchstart',pauseManually,{passive:true});
  rail.addEventListener('wheel',pauseManually,{passive:true});
  rail.addEventListener('mouseenter',function(){hovered=true;syncAutoplay();});
  rail.addEventListener('mouseleave',function(){hovered=false;syncAutoplay();});
  root.addEventListener('focusin',function(){focused=true;syncAutoplay();});
  root.addEventListener('focusout',function(event){focused=root.contains(event.relatedTarget);syncAutoplay();});
  rail.addEventListener('toggle',syncAutoplay,true);
  document.addEventListener('visibilitychange',syncAutoplay);
  rail.addEventListener('keydown',function(event){
   if(event.target!==rail)return;
   if(event.key==='ArrowRight'||event.key==='ArrowLeft'){event.preventDefault();move(event.key==='ArrowRight'?1:-1);}
  });
  if('ResizeObserver'in window){new ResizeObserver(schedule).observe(rail);}else{window.addEventListener('resize',schedule,{passive:true});}
  if('IntersectionObserver'in window){new IntersectionObserver(function(entries){inView=entries.some(function(e){return e.isIntersecting&&e.intersectionRatio>=.1;});syncAutoplay();},{threshold:.1}).observe(rail);}
  else{inView=true;}
  update();syncAutoplay();
  window.addEventListener('pagehide',stop);window.addEventListener('pageshow',syncAutoplay);
 }
 // The rendered cards remain usable without JS. Prepare the interactive rail
 // shortly before it enters view, instead of measuring/reordering it at page start.
 document.querySelectorAll('.zpReviews24').forEach(function(root){
  if(root.dataset.zpReviewReady==='1'||root.dataset.zpReviewPending==='1')return;
  root.dataset.zpReviewPending='1';
  var observer=null,started=false;
  function activate(){
   if(started)return;started=true;
   if(observer)observer.disconnect();
   root.removeEventListener('focusin',activate);
   root.removeEventListener('pointerdown',activate);
   initialize(root);
  }
  root.addEventListener('focusin',activate);
  root.addEventListener('pointerdown',activate,{passive:true});
  if('IntersectionObserver' in window){
   observer=new IntersectionObserver(function(entries){
    if(entries.some(function(entry){return entry.isIntersecting;}))activate();
   },{rootMargin:'400px 0px'});
   observer.observe(root);
  }else activate();
 });
})();
