(function(){
      'use strict';
      const root=document.getElementById('zpShowcaseServices');
      if(!root || root.dataset.zpInit==='1') return;
      root.dataset.zpInit='1';

      const initIcons=()=>{
        if(window.lucide && typeof window.lucide.createIcons==='function'){
          try{window.lucide.createIcons({attrs:{'stroke-width':2,'stroke-linecap':'round','stroke-linejoin':'round','aria-hidden':'true'}})}catch(e){}
        }
      };
      if(window.lucide){initIcons()}else{setTimeout(initIcons,260)}
      window.addEventListener('load',initIcons,{once:true,passive:true});

      const cards=Array.from(root.querySelectorAll('[data-zp-ss-card]'));
      if(!cards.length) return;

      /* v2.2.347: desktop smoothness — cache expensive mockup nodes once
         and run scroll work only while the section is near viewport. */
      const cardMotionNodes = cards.map(card => Array.from(card.querySelectorAll('.zpSSCard__mock,.zpSSCard__laptop3d,.zpSSMockAura,.zpSSMockIcon')));
      let sectionHot = true;
      if('IntersectionObserver' in window){
        const sectionIo = new IntersectionObserver(entries => {
          entries.forEach(entry => {
            sectionHot = !!entry.isIntersecting;
            if(sectionHot) requestActive();
          });
        }, {rootMargin:'42% 0px 42% 0px', threshold:0});
        sectionIo.observe(root);
      }

      const moveBadgesForViewport=()=>{
        const isMobile=window.matchMedia && window.matchMedia('(max-width: 860px)').matches;
        cards.forEach(card=>{
          const copy=card.querySelector('.zpSSCard__copy');
          const actions=card.querySelector('.zpSSCard__actions');
          const review=card.querySelector(':scope > .zpSSReview, :scope > .zpSSSignature');
          const badges=card.querySelector('.zpSSBadges');
          if(!copy || !actions || !review || !badges) return;

          if(isMobile){
            if(badges.parentElement!==card){
              review.insertAdjacentElement('afterend',badges);
            }
          }else{
            if(badges.parentElement!==copy){
              actions.insertAdjacentElement('afterend',badges);
            }
          }
        });
        initIcons();
      };

      moveBadgesForViewport();

      if(!('IntersectionObserver' in window)){
        cards.forEach(card=>card.classList.add('isVisible'));
        return;
      }

      let activeTicking=false;
      const updateActiveCard=()=>{
        activeTicking=false;
        const vh=window.innerHeight||800;
        const center=vh*.52;
        let active=null;
        let best=Infinity;
        cards.forEach(card=>{
          const rect=card.getBoundingClientRect();
          if(rect.bottom<0 || rect.top>vh) return;
          const dist=Math.abs((rect.top+rect.height*.5)-center);
          if(dist<best){best=dist;active=card}
        });
        cards.forEach(card=>{
          const visible=card.classList.contains('isVisible');
          card.classList.toggle('isActive',visible && card===active);
          card.classList.toggle('isDim',visible && active && card!==active);
        });
      };
      const requestActive=()=>{if(activeTicking)return;activeTicking=true;requestAnimationFrame(updateActiveCard)};

      const io=new IntersectionObserver((entries)=>{
        entries.forEach(entry=>{
          if(entry.isIntersecting){entry.target.classList.add('isVisible');requestActive();io.unobserve(entry.target)}
        });
      },{threshold:.06,rootMargin:'0px 0px 34% 0px'});
      cards.forEach(card=>io.observe(card));

      const reduce=window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const isCoarse=window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
      const isMobileView=()=>window.matchMedia && window.matchMedia('(max-width: 860px)').matches;

      let ticking=false;
      const updateParallax=()=>{
        ticking=false;

        if(reduce || isCoarse || isMobileView()){
          cardMotionNodes.forEach(nodes=>nodes.forEach(el=>el.style.removeProperty('--move')));
          return;
        }

        if(!sectionHot) return;
        const vh=window.innerHeight||800;
        cards.forEach((card, cardIndex)=>{
          const rect=card.getBoundingClientRect();
          if(rect.bottom<0 || rect.top>vh) return;
          const progress=(rect.top+rect.height*.5-vh*.5)/vh;
          const move=Math.max(-14,Math.min(14,progress*-18));
          const nodes = cardMotionNodes[cardIndex] || [];
          nodes.forEach((el,idx)=>{
            const factor=idx ? .45 : 1;
            el.style.setProperty('--move',(move*factor).toFixed(2)+'px');
          });
        });
      };

      const onScroll=()=>{
        if(!sectionHot) return;
        requestActive();
        if(ticking) return;
        ticking=true;
        requestAnimationFrame(updateParallax);
      };

      window.addEventListener('scroll',onScroll,{passive:true});
      window.addEventListener('resize',()=>{moveBadgesForViewport();onScroll();},{passive:true});
      updateParallax();
      updateActiveCard();
    })();
