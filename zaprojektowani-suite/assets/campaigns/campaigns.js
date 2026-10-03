(() => {
      const header = document.getElementById('siteHeader');
      const toggle = document.querySelector('.menu-toggle');
      const progress = document.querySelector('.scroll-progress');
      const navLinks = header ? [...header.querySelectorAll('.nav a')] : [];
      const revealItems = document.querySelectorAll('.reveal, .campaign-cockpit, .process-track, .process-step');
      const sections = [...document.querySelectorAll('main section[id]')];

      const fab = document.getElementById('callbackFab');
      const processSection = document.querySelector('.process');
      const processHead = document.querySelector('.process .process-headline');

      const updateScroll = () => {
        const y = window.scrollY;
        const max = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
        if (header) header.classList.toggle('is-scrolled', y > 24);
        if (progress) progress.style.transform = `scaleX(${Math.min(1, y / max)})`;
        if (fab) fab.classList.toggle('is-shown', y > 480);

        /* Siedzący Mateusz w sekcji proces chowa się przy scrollu (jak na logo-branding). */
        if (processSection && processHead && window.innerWidth > 680) {
          const rect = processHead.getBoundingClientRect();
          const travel = Math.max(0, Math.min(1, (window.innerHeight * 0.15 - rect.top) / Math.max(1, rect.height * 0.88)));
          const fade = Math.max(0, Math.min(1, (travel - 0.7) / 0.3));
          processSection.style.setProperty('--process-person-shift', `${Math.round(-travel * 115)}px`);
          processSection.style.setProperty('--process-person-opacity', (1 - fade).toFixed(3));
        }

        let current = 'start';
        sections.forEach(section => {
          if (y >= section.offsetTop - 180) current = section.id;
        });
        navLinks.forEach(link => link.classList.toggle('is-active', link.getAttribute('href') === `#${current}`));
      };

      const closeMenu = () => {
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
        if (header) header.classList.remove('is-open');
        document.body.classList.remove('menu-open');
      };

      if (toggle && header) {
        toggle.addEventListener('click', () => {
          const open = toggle.getAttribute('aria-expanded') !== 'true';
          toggle.setAttribute('aria-expanded', String(open));
          header.classList.toggle('is-open', open);
          document.body.classList.toggle('menu-open', open);
        });
        navLinks.forEach(link => link.addEventListener('click', closeMenu));
      }

      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -7% 0px' });
      revealItems.forEach(item => observer.observe(item));

      document.querySelectorAll('.faq-item').forEach(item => {
        item.addEventListener('toggle', () => {
          if (!item.open) return;
          document.querySelectorAll('.faq-item[open]').forEach(other => {
            if (other !== item) other.removeAttribute('open');
          });
        });
      });

      const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

      /* Twarda gwarancja separatora tysięcy (spacja nierozdzielająca) + przecinek dziesiętny,
         niezależnie od obsługi Intl w przeglądarce — np. 355100 -> „355 100". */
      const nf = (v, d) => {
        const neg = v < 0, fixed = Math.abs(v).toFixed(d), parts = fixed.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return (neg ? '-' : '') + (parts[1] ? parts[0] + ',' + parts[1] : parts[0]);
      };
      const runCount = el => {
        const to = parseFloat(el.dataset.count);
        const d = parseInt(el.dataset.decimals || '0', 10);
        const suffix = el.dataset.suffix || '';
        if (reduced || !isFinite(to)) { el.textContent = nf(to, d) + suffix; return; }
        const t0 = performance.now(), dur = 1500;
        const tick = now => {
          const p = Math.min(1, (now - t0) / dur);
          const e = 1 - Math.pow(1 - p, 4);
          el.textContent = nf(to * e, d) + suffix;
          if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
        setTimeout(() => { el.textContent = nf(to, d) + suffix; }, dur + 200);
      };
      const countObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (!entry.isIntersecting) return;
          runCount(entry.target);
          countObserver.unobserve(entry.target);
        });
      }, { threshold: .6 });
      document.querySelectorAll('[data-count]').forEach(el => countObserver.observe(el));

      const donut = document.getElementById('mixDonut');
      if (donut) {
        const target = parseFloat(donut.dataset.a);
        const paint = v => { donut.style.background = `conic-gradient(#8ec8f7 0 ${v}%, #7c82ff ${v}% 100%)`; };
        const donutObserver = new IntersectionObserver(entries => {
          entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            donutObserver.disconnect();
            if (reduced) { paint(target); return; }
            const t0 = performance.now();
            const tick = now => {
              const p = Math.min(1, (now - t0) / 1300);
              paint(target * (1 - Math.pow(1 - p, 3)));
              if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
            setTimeout(() => paint(target), 1500);
          });
        }, { threshold: .5 });
        donutObserver.observe(donut);
      }

      document.querySelectorAll('.channel-card,.placement-card,.package-card,.case-card').forEach(card => {
        const spot = document.createElement('i');
        spot.className = 'card-spot';
        spot.setAttribute('aria-hidden', 'true');
        card.appendChild(spot);
        card.addEventListener('pointermove', e => {
          const r = card.getBoundingClientRect();
          card.style.setProperty('--mx', `${e.clientX - r.left}px`);
          card.style.setProperty('--my', `${e.clientY - r.top}px`);
        });
      });

      const panel = document.getElementById('callbackPanel');
      const cbForm = panel ? panel.querySelector('.callback-form') : null;
      const cbInput = document.getElementById('cbPhone');
      const setOpen = open => {
        panel.classList.toggle('is-open', open);
        panel.setAttribute('aria-hidden', String(!open));
        fab.setAttribute('aria-expanded', String(open));
        if (open) setTimeout(() => cbInput.focus(), 260);
      };
      if (fab && panel && cbForm && cbInput) {
      fab.addEventListener('click', () => setOpen(!panel.classList.contains('is-open')));
      panel.querySelector('.callback-close').addEventListener('click', () => setOpen(false));
      document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });
      document.addEventListener('click', e => {
        if (panel.classList.contains('is-open') && !panel.contains(e.target) && !fab.contains(e.target)) setOpen(false);
      });
      document.querySelectorAll('[data-open-callback]').forEach(el => el.addEventListener('click', e => {
        e.preventDefault();
        fab.classList.add('is-shown');
        setOpen(true);
      }));
      cbInput.addEventListener('input', () => {
        const digits = cbInput.value.replace(/\D/g, '').slice(0, 9);
        cbInput.value = digits.replace(/(\d{3})(?=\d)/g, '$1 ');
        panel.classList.remove('is-error');
      });
      cbForm.addEventListener('submit', e => {
        e.preventDefault();
        const digits = cbInput.value.replace(/\D/g, '');
        if (digits.length !== 9) { panel.classList.add('is-error'); cbInput.focus(); return; }
        panel.querySelector('[data-cb-num]').textContent = `+48 ${cbInput.value}`;
        cbForm.hidden = true;
        panel.querySelector('.callback-note').hidden = true;
        panel.querySelector('.callback-legal').hidden = true;
        panel.querySelector('.callback-done').hidden = false;
      });
      }

      window.addEventListener('scroll', updateScroll, { passive: true });
      window.addEventListener('resize', () => { if (window.innerWidth > 1100) closeMenu(); });
      const year = document.getElementById('year');
      if (year) year.textContent = new Date().getFullYear();
      updateScroll();
    })();
