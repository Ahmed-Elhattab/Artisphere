document.addEventListener('DOMContentLoaded', () => {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ===== 0) Petit style injecté pour animations / boutons (pas besoin de toucher ton CSS) =====
  const style = document.createElement('style');
  style.textContent = `
    .as-float-btn{
      position: fixed;
      right: 18px;
      bottom: 18px;
      width: 48px;
      height: 48px;
      border-radius: 999px;
      border: 1px solid rgba(0,0,0,.12);
      background: rgba(222,214,201,.95);
      color: #6f7c85;
      font-weight: 900;
      cursor: pointer;
      display: grid;
      place-items: center;
      box-shadow: 0 10px 22px rgba(0,0,0,.12);
      z-index: 9999;
      opacity: 0;
      transform: translateY(8px);
      pointer-events: none;
      transition: opacity .18s ease, transform .18s ease;
    }
    .as-float-btn.is-visible{
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }

    .as-discover-btn{
      position: absolute;
      left: 50%;
      bottom: 18px;
      transform: translateX(-50%);
      padding: 10px 14px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,.18);
      background: rgba(0,0,0,.28);
      color: #fff;
      font-weight: 900;
      cursor: pointer;
      z-index: 5;
      backdrop-filter: blur(4px);
      text-decoration: none;
      user-select: none;
      transition: filter .15s ease;
    }
    .as-discover-btn:hover{ filter: brightness(.97); }

    /* Animations d'arrivée */
    .as-reveal{
      opacity: 0;
      transform: translateY(14px);
      transition: opacity .45s ease, transform .45s ease;
      will-change: opacity, transform;
    }
    .as-reveal.is-visible{
      opacity: 1;
      transform: translateY(0);
    }
  `;
  document.head.appendChild(style);

  // ===== 1) Bouton "Découvrir" (scroll vers #decouvrir) =====
  const hero = document.querySelector('.home-hero');
  const target = document.querySelector('#decouvrir');

  if (hero && target && !document.querySelector('.as-discover-btn')) {
    // S'assure que le hero est le conteneur de positionnement
    const heroPos = getComputedStyle(hero).position;
    if (heroPos === 'static') hero.style.position = 'relative';

    const discoverLink = document.createElement('a');
    discoverLink.href = '#decouvrir';
    discoverLink.className = 'as-discover-btn';
    discoverLink.setAttribute('aria-label', 'Aller à la section découvrir');
    discoverLink.textContent = '↓ Découvrir';

    hero.appendChild(discoverLink);

    discoverLink.addEventListener('click', (e) => {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  }

  // ===== 2) Animation progressive des box (et du texte) au chargement =====
  if (!reduceMotion) {
    const toReveal = [
      document.querySelector('.home-hero__title'),
      document.querySelector('.home-hero__subtitle'),
      ...document.querySelectorAll('.home-hero__features .feature-card'),
      document.querySelector('.hero-cta'),
      document.querySelector('.hero-hint')
    ].filter(Boolean);

    // Applique l'état caché
    toReveal.forEach(el => el.classList.add('as-reveal'));

    // Déclenche avec un stagger
    requestAnimationFrame(() => {
      toReveal.forEach((el, i) => {
        setTimeout(() => el.classList.add('is-visible'), 120 + i * 120);
      });
    });
  }

  // ===== 3) Bouton "Retour en haut" =====
  const backTop = document.createElement('button');
  backTop.type = 'button';
  backTop.className = 'as-float-btn';
  backTop.setAttribute('aria-label', 'Revenir en haut de la page');
  backTop.textContent = '↑';
  document.body.appendChild(backTop);

  backTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // Show/hide du bouton selon scroll
  let ticking = false;
  function updateBackTop() {
    const y = window.scrollY || document.documentElement.scrollTop;
    if (y > 500) backTop.classList.add('is-visible');
    else backTop.classList.remove('is-visible');
    ticking = false;
  }
  window.addEventListener('scroll', () => {
    if (!ticking) {
      ticking = true;
      requestAnimationFrame(updateBackTop);
    }
  });
  updateBackTop();

  // ===== 4) Carrousels : flèches + drag souris + clavier (ton code, conservé) =====
  document.querySelectorAll('.h-row').forEach(setupCarouselRow);

  function setupCarouselRow(row) {
    row.setAttribute('tabindex', '0');
    row.style.scrollBehavior = 'smooth';
    row.style.cursor = 'grab';

    const section = row.closest('.home-section') || row.parentElement;
    if (!section) return;

    if (getComputedStyle(section).position === 'static') {
      section.style.position = 'relative';
    }

    const btnLeft = makeBtn('←', 'Faire défiler à gauche');
    const btnRight = makeBtn('→', 'Faire défiler à droite');

    Object.assign(btnLeft.style, {
      position: 'absolute',
      left: '6px',
      top: '55%',
      transform: 'translateY(-50%)',
      zIndex: '5'
    });

    Object.assign(btnRight.style, {
      position: 'absolute',
      right: '6px',
      top: '55%',
      transform: 'translateY(-50%)',
      zIndex: '5'
    });

    const fadeLeft = makeFade('left');
    const fadeRight = makeFade('right');

    // évite doublons si tu recharges partiellement
    if (!section.querySelector('.as-fade-left')) section.appendChild(fadeLeft);
    if (!section.querySelector('.as-fade-right')) section.appendChild(fadeRight);
    if (!section.querySelector('.as-btn-left')) section.appendChild(btnLeft);
    if (!section.querySelector('.as-btn-right')) section.appendChild(btnRight);

    function cardStep() {
      const firstTile = row.querySelector('.tile');
      if (!firstTile) return 260;
      const w = firstTile.getBoundingClientRect().width;
      return Math.round(w + 16);
    }

    btnLeft.addEventListener('click', () => row.scrollBy({ left: -cardStep(), behavior: 'smooth' }));
    btnRight.addEventListener('click', () => row.scrollBy({ left: cardStep(), behavior: 'smooth' }));

    // Drag souris
    let isDown = false;
    let startX = 0;
    let startScrollLeft = 0;

    row.addEventListener('mousedown', (e) => {
      isDown = true;
      row.style.cursor = 'grabbing';
      startX = e.pageX;
      startScrollLeft = row.scrollLeft;
    });

    window.addEventListener('mouseup', () => {
      isDown = false;
      row.style.cursor = 'grab';
    });

    row.addEventListener('mouseleave', () => {
      isDown = false;
      row.style.cursor = 'grab';
    });

    row.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const dx = e.pageX - startX;
      row.scrollLeft = startScrollLeft - dx;
    });

    // Clavier (quand la ligne est focus)
    row.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        row.scrollBy({ left: -cardStep(), behavior: 'smooth' });
      }
      if (e.key === 'ArrowRight') {
        e.preventDefault();
        row.scrollBy({ left: cardStep(), behavior: 'smooth' });
      }
    });

    function updateControls() {
      const maxScroll = row.scrollWidth - row.clientWidth;
      const atStart = row.scrollLeft <= 2;
      const atEnd = row.scrollLeft >= maxScroll - 2;

      btnLeft.style.opacity = atStart ? '0.35' : '1';
      btnLeft.style.pointerEvents = atStart ? 'none' : 'auto';

      btnRight.style.opacity = atEnd ? '0.35' : '1';
      btnRight.style.pointerEvents = atEnd ? 'none' : 'auto';

      fadeLeft.style.opacity = atStart ? '0' : '1';
      fadeRight.style.opacity = atEnd ? '0' : '1';

      const noScroll = row.scrollWidth <= row.clientWidth + 2;
      if (noScroll) {
        btnLeft.style.display = 'none';
        btnRight.style.display = 'none';
        fadeLeft.style.display = 'none';
        fadeRight.style.display = 'none';
      } else {
        btnLeft.style.display = '';
        btnRight.style.display = '';
        fadeLeft.style.display = '';
        fadeRight.style.display = '';
      }
    }

    row.addEventListener('scroll', updateControls);
    window.addEventListener('resize', updateControls);
    updateControls();
  }

  function makeBtn(text, aria) {
    const b = document.createElement('button');
    b.type = 'button';
    b.textContent = text;
    b.setAttribute('aria-label', aria);

    // classes pour éviter doublons
    if (text === '←') b.classList.add('as-btn-left');
    if (text === '→') b.classList.add('as-btn-right');

    Object.assign(b.style, {
      width: '44px',
      height: '44px',
      borderRadius: '999px',
      border: '1px solid rgba(0,0,0,.12)',
      background: 'rgba(222,214,201,.95)',
      color: '#6f7c85',
      fontWeight: '900',
      cursor: 'pointer',
      display: 'grid',
      placeItems: 'center',
      boxShadow: '0 10px 22px rgba(0,0,0,.10)'
    });

    b.addEventListener('mouseenter', () => b.style.filter = 'brightness(0.97)');
    b.addEventListener('mouseleave', () => b.style.filter = '');

    return b;
  }

  function makeFade(side) {
    const d = document.createElement('div');
    d.className = side === 'left' ? 'as-fade-left' : 'as-fade-right';

    Object.assign(d.style, {
      position: 'absolute',
      top: '78px',
      bottom: '6px',
      width: '60px',
      pointerEvents: 'none',
      zIndex: '4',
      opacity: '1'
    });

    if (side === 'left') {
      d.style.left = '0';
      d.style.background = 'linear-gradient(90deg, rgba(196,154,116,1) 0%, rgba(196,154,116,0) 100%)';
    } else {
      d.style.right = '0';
      d.style.background = 'linear-gradient(270deg, rgba(196,154,116,1) 0%, rgba(196,154,116,0) 100%)';
    }
    return d;
  }
});
