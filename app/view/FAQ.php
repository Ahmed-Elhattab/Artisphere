<main>

  <!-- En-tête FAQ -->
  <section class="section faq-header">
    <div class="container faq-title-box">
      <h1>Foire aux questions</h1>
      <p class="faq-intro">
        Tu te poses des questions sur Artisphere, le fonctionnement de la plateforme ou
        la gestion des commandes ? Trouve ici les réponses aux questions les plus fréquentes.
      </p>
    </div>

    <?php if (!empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? null) === 'admin'): ?>
      <div class="add-buttons">
        <div class="action-buttons">
          <a class="action-link" href="/artisphere/?controller=FAQ_create&action=create">
            <button class="btn-spe" type="button">CREER UNE QUESTION/REPONSE</button>
          </a>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <!-- Liste des questions-réponses -->
  <section class="section">
    <div class="container faq-list">

      <!-- recherche + ouvrir/fermer -->
      <div class="faq-tools" style="display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; margin-bottom:14px; background:#efe6d8; border:1px solid rgba(0,0,0,.08); border-radius:16px; padding:14px; box-shadow:0 6px 16px rgba(0,0,0,.08);">
        <input
          id="faqSearch"
          type="search"
          placeholder="Rechercher une question (ex: livraison, paiement, compte...)"
          aria-label="Rechercher dans la FAQ"
          autocomplete="off"
          style="flex:1; min-width:240px; max-width:560px; height:44px; padding:12px 14px 12px 44px; border-radius:999px; border:1px solid rgba(0,0,0,.12); outline:none; background:#fff; color:#3c2b20; font-weight:600;
                 background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2218%22 height=%2218%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236f7c85%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Ccircle cx=%2211%22 cy=%2211%22 r=%228%22/%3E%3Cline x1=%2221%22 y1=%2221%22 x2=%2216.65%22 y2=%2216.65%22/%3E%3C/svg%3E');
                 background-repeat:no-repeat; background-position:16px 50%;"
        >

        <div style="display:flex; gap:10px;">
          <button id="faqOpenAll" type="button"
            style="height:44px; padding:0 16px; border-radius:999px; border:1px solid rgba(0,0,0,.12); background:#b6a894; color:#fff; font-weight:800; cursor:pointer;">
            Tout ouvrir
          </button>
          <button id="faqCloseAll" type="button"
            style="height:44px; padding:0 16px; border-radius:999px; border:1px solid rgba(0,0,0,.12); background:#b6a894; color:#fff; font-weight:800; cursor:pointer;">
            Tout fermer
          </button>
        </div>
      </div>

      <!-- Compteur -->
      <p id="faqCount" style="margin:0 0 10px; color:#3c2b20; font-weight:600; opacity:.9;" aria-live="polite"></p>

      <?php if (!empty($faqByCat)): ?>
        <?php foreach ($faqByCat as $categorie => $items): ?>

          <h2 class="faq-category-title">
            <?= htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8') ?>
          </h2>

          <?php foreach ($items as $faq): ?>
            <details class="faq-item">
              <summary><?= htmlspecialchars($faq['question'] ?? '', ENT_QUOTES, 'UTF-8') ?></summary>
              <div class="faq-body">
                <p><?= nl2br(htmlspecialchars($faq['reponse'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
              </div>
            </details>
          <?php endforeach; ?>

        <?php endforeach; ?>
      <?php else: ?>
        <p>Aucune question/réponse n'a été trouvée dans la base.</p>
      <?php endif; ?>

    </div>
  </section>

  <!-- CTA contact -->
  <section class="section faq-contact-cta">
    <div class="container faq-contact-box">
      <div>
        <h2>Vous ne trouvez pas votre réponse&nbsp;?</h2>
        <p>
          Contacte-nous et nous reviendrons vers toi au plus vite pour t'aider.
          Tu peux aussi préciser si tu es artisan ou client pour que l'on t'oriente mieux.
        </p>
      </div>
      <a href="/artisphere/?controller=contact&action=index" class="btn-primary">
        Contacter l'équipe Artisphere
      </a>
    </div>
  </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('faqSearch');
  const btnOpen = document.getElementById('faqOpenAll');
  const btnClose = document.getElementById('faqCloseAll');
  const countEl = document.getElementById('faqCount');

  const details = Array.from(document.querySelectorAll('.faq-item'));
  const catTitles = Array.from(document.querySelectorAll('.faq-category-title'));

  // Sauvegarde du contenu initial (pour retirer le surlignage proprement)
  details.forEach(d => {
    const s = d.querySelector('summary');
    const p = d.querySelector('.faq-body p');
    d.__origSummary = s ? s.innerHTML : '';
    d.__origBody = p ? p.innerHTML : '';
  });

  function escapeRegExp(str){
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

  function resetHighlight(){
    details.forEach(d => {
      const s = d.querySelector('summary');
      const p = d.querySelector('.faq-body p');
      if (s) s.innerHTML = d.__origSummary;
      if (p) p.innerHTML = d.__origBody;
    });
  }

  function highlight(el, query){
    if (!el || !query) return;
    const rx = new RegExp('(' + escapeRegExp(query) + ')', 'gi');
    el.innerHTML = el.innerHTML.replace(
      rx,
      '<mark style="background:#fde68a; padding:0 2px; border-radius:4px;">$1</mark>'
    );
  }

  function updateCount(n){
    if (!countEl) return;
    if (n === 0) countEl.textContent = "Aucun résultat.";
    else if (n === 1) countEl.textContent = "1 résultat.";
    else countEl.textContent = n + " résultats.";
  }

  function applyFilter(){
    const q = (input?.value || '').trim().toLowerCase();
    resetHighlight();

    let visibleCount = 0;

    details.forEach(d => {
      const s = d.querySelector('summary');
      const p = d.querySelector('.faq-body p');

      const text = (
        (s ? s.textContent : '') + ' ' +
        (p ? p.textContent : '')
      ).toLowerCase();

      const match = q === '' || text.includes(q);

      d.style.display = match ? '' : 'none';

      if (!match) {
        d.removeAttribute('open');
        return;
      }

      visibleCount++;

      if (q) {
        if (s) highlight(s, q);
        if (p) highlight(p, q);
      }
    });

    // Cache un titre de catégorie si tous ses items sont masqués
    catTitles.forEach(title => {
      let el = title.nextElementSibling;
      let hasVisible = false;

      while (el && !el.classList.contains('faq-category-title')) {
        if (el.classList.contains('faq-item') && el.style.display !== 'none') {
          hasVisible = true;
          break;
        }
        el = el.nextElementSibling;
      }

      title.style.display = hasVisible ? '' : 'none';
    });

    updateCount(visibleCount);
  }

  btnOpen?.addEventListener('click', () => {
    details.forEach(d => {
      if (d.style.display !== 'none') d.setAttribute('open', '');
    });
  });

  btnClose?.addEventListener('click', () => {
    details.forEach(d => d.removeAttribute('open'));
  });

  let t;
  input?.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(applyFilter, 80);
  });

  applyFilter();
});
</script>
