<section class="home">
  <?php
    // Lien du bouton "Commencer" selon l'état de connexion et le rôle
    $isLogged = !empty($_SESSION['user']);
    $role = $_SESSION['user']['role'] ?? null;

    $ctaHref = '/artisphere/?controller=connexion&action=index';
    if ($isLogged) {
      $ctaHref = in_array($role, ['artisan', 'admin'], true)
        ? '/artisphere/?controller=mes_creations&action=index'
        : '/artisphere/?controller=catalogue&action=index';
    }
  ?>

  <!-- Bandeau d’accueil -->
  <header class="home-hero">
    <div class="home-hero__overlay" aria-hidden="true"></div>

    <div class="home-hero__container">
      <div class="home-hero__intro">
        <h1 class="home-hero__title">Artisphere</h1>
        <p class="home-hero__subtitle">
          Une plateforme pour découvrir, acheter et vendre des créations artisanales.
        </p>

        <!-- Points clés de la plateforme -->
        <div class="home-hero__features" aria-label="À quoi sert Artisphere">
          <article class="feature-card">
            <div class="feature-card__title">Découvrir des créations</div>
            <div class="feature-card__text">
              Parcourez des créations uniques (déco, accessoires, cadeaux) et filtrez par catégorie, prix et localisation pour trouver la pièce parfaite.
            </div>
          </article>

          <article class="feature-card">
            <div class="feature-card__title">Mettre en avant les artisans</div>
            <div class="feature-card__text">
              Découvrez les profils : univers, matières, technique, réalisations. Suivez vos artisans préférés et contactez-les facilement.
            </div>
          </article>

          <article class="feature-card">
            <div class="feature-card__title">Participer à des événements</div>
            <div class="feature-card__text">
              Consultez les événements à venir (ateliers, expos, marchés), voyez le lieu et les dates, et repérez ceux proches de chez vous.
            </div>
          </article>
        </div>

        <!-- Appel à l’action principal -->
        <a class="hero-cta" href="<?= htmlspecialchars($ctaHref, ENT_QUOTES, 'UTF-8') ?>">
          Commencer
        </a>

        <!-- Petit texte d’aide selon connexion -->
        <div class="hero-hint">
          <?php if (!$isLogged): ?>
            Connexion / inscription rapide.
          <?php else: ?>
            Accès direct à votre espace.
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <!-- Contenu principal : produits + événements -->
  <main class="home-content" id="decouvrir">

    <!-- Sélection de produits -->
    <section class="home-section" aria-label="Sélection de produits">
      <div class="home-section__head">
        <h2 class="home-section__title">Sélection de produits</h2>
        <a class="home-section__link" href="/artisphere/?controller=catalogue&action=index">Voir tout</a>
      </div>

      <div class="h-row" role="list" aria-label="Produits en vedette">
        <?php if (!empty($produits)): ?>
          <?php foreach ($produits as $p): ?>
            <?php
              $img = !empty($p['image'])
                ? "images/produits/" . $p['image']
                : "images/produit.png";
            ?>
            <article class="tile" role="listitem">
              <img class="tile__img"
                   src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
                   alt="<?= htmlspecialchars($p['nom'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?>"
                   onerror="this.onerror=null; this.src='images/produit.png';">

              <div class="tile__body">
                <div class="tile__title">
                  <?= htmlspecialchars($p['nom'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div class="tile__meta">
                  <span class="pill">
                    <?= number_format((float)($p['prix'] ?? 0), 2, ',', ' ') ?> €
                  </span>
                </div>

                <a class="tile__btn"
                   href="/artisphere/?controller=produit_show&action=show&id=<?= (int)($p['id_produit'] ?? 0) ?>">
                  Voir
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="home-empty">Aucun produit à afficher pour le moment.</div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Sélection d'événements -->
    <section class="home-section" aria-label="Sélection d'événements">
      <div class="home-section__head">
        <h2 class="home-section__title">Événements</h2>
        <a class="home-section__link" href="/artisphere/?controller=evenement&action=index">Voir tout</a>
      </div>

      <div class="h-row" role="list" aria-label="Événements à la une">
        <?php if (!empty($evenements)): ?>
          <?php foreach ($evenements as $e): ?>
            <?php
              $img = !empty($e['image'])
                ? "images/evenements/" . $e['image']
                : "images/image-photo.jpg";
            ?>
            <article class="tile tile--event" role="listitem">
              <img class="tile__img"
                   src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
                   alt="<?= htmlspecialchars($e['nom'] ?? 'Événement', ENT_QUOTES, 'UTF-8') ?>"
                   onerror="this.onerror=null; this.src='images/image-photo.jpg';">

              <div class="tile__body">
                <div class="tile__title">
                  <?= htmlspecialchars($e['nom'] ?? 'Événement', ENT_QUOTES, 'UTF-8') ?>
                </div>

                <div class="tile__meta">
                  <span class="pill">
                    <?= htmlspecialchars($e['lieu'] ?? 'Lieu', ENT_QUOTES, 'UTF-8') ?>
                    <?= !empty($e['type']) ? ' · ' . htmlspecialchars($e['type'], ENT_QUOTES, 'UTF-8') : '' ?>
                  </span>
                </div>

                <div class="tile__date">
                  Du <?= htmlspecialchars($e['date_debut'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                  au <?= htmlspecialchars($e['date_fin'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </div>

                <a class="tile__btn"
                   href="/artisphere/?controller=evenement_show&action=show&id=<?= (int)($e['id_event'] ?? 0) ?>">
                  Voir
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="home-empty">Aucun événement à afficher pour le moment.</div>
        <?php endif; ?>
      </div>
    </section>

  </main>
</section>

<style>
  /* Mise en page locale à la page */
  .home, .home * { box-sizing: border-box; }

  /* Bandeau d’accueil */
  .home .home-hero{
    position: relative;
    min-height: 560px;
    display: flex;
    align-items: flex-start;
    padding: 90px 40px 60px;
    overflow: hidden;
    background: url("images/Home.png") center/cover no-repeat;
  }
  .home .home-hero__overlay{
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(0,0,0,.62) 0%, rgba(0,0,0,.38) 55%, rgba(0,0,0,.18) 100%);
  }
  .home .home-hero__container{
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 1100px;
  }
  .home .home-hero__title{
    margin: 0 0 10px;
    color: #fff;
    font-size: 52px;
    line-height: 1.05;
  }
  .home .home-hero__subtitle{
    margin: 0 0 50px;
    color: rgba(255,255,255,.92);
    font-size: 18px;
    line-height: 1.55;
    max-width: 720px;
  }

  .home .home-hero__features{
    display: grid;
    grid-template-columns: repeat(3, minmax(240px, 340px));
    gap: 14px;
    width: 100%;
    max-width: 1150px;
    margin-top: 60px;
    margin-left: 70px;
  }
  .home .feature-card{
    background: rgba(222,214,201,.94);
    border-radius: 18px;
    padding: 14px;
    box-shadow: 0 10px 22px rgba(0,0,0,.18);
  }
  .home .feature-card__title{
    font-weight: 900;
    color: #3c2b20;
    font-size: 13px;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: .4px;
  }
  .home .feature-card__text{
    color: #6f7c85;
    font-size: 13px;
    line-height: 1.35;
  }

  .home .hero-cta{
    display: inline-block;
    margin-top: 110px;
    background: #b6a894;
    color: #fff;
    text-decoration: none;
    padding: 12px 18px;
    border-radius: 999px;
    font-weight: 900;
  }
  .home .hero-cta:hover{ filter: brightness(.97); }
  .home .hero-hint{ margin-top: 10px; font-size: 12px; color: rgba(255,255,255,.85); }

  /* Apparition progressive (utilisée par le JS) */
  .home .reveal{
    opacity: 0;
    transform: translateY(14px);
    transition: opacity .5s ease, transform .5s ease;
    will-change: opacity, transform;
  }
  .home .reveal.is-in{
    opacity: 1;
    transform: translateY(0);
  }

  /* Contenu bas */
  .home .home-content{ background: #c49a74; padding: 40px 40px 70px; }
  .home .home-section{ max-width: 1100px; margin: 0 auto 34px; }
  .home .home-section__head{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 14px;
  }
  .home .home-section__title{ margin: 0; font-size: 26px; font-weight: 900; color: #fff; }
  .home .home-section__link{
    text-decoration: none;
    font-weight: 900;
    color: rgba(255,255,255,.92);
    background: rgba(0,0,0,.18);
    border: 1px solid rgba(255,255,255,.15);
    padding: 10px 14px;
    border-radius: 999px;
  }

  .home .h-row{
    display: flex;
    gap: 16px;
    overflow-x: auto;
    padding: 4px 2px 14px;
    scroll-snap-type: x mandatory;
  }
  .home .h-row::-webkit-scrollbar{ height: 10px; }
  .home .h-row::-webkit-scrollbar-thumb{ background: rgba(0,0,0,.18); border-radius: 999px; }

  .home .tile{
    flex: 0 0 220px;
    border-radius: 22px;
    overflow: hidden;
    background: rgba(222,214,201,.95);
    box-shadow: 0 10px 22px rgba(0,0,0,.12);
    scroll-snap-align: start;
    display: flex;
    flex-direction: column;
  }
  .home .tile__img{
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    display: block;
    background: #e9e2d6;
  }
  .home .tile__body{ padding: 14px; display: flex; flex-direction: column; gap: 10px; }
  .home .tile__title{
    font-weight: 900;
    color: #3c2b20;
    font-size: 14px;
    line-height: 1.2;
    min-height: 34px;
  }
  .home .pill{
    display: inline-block;
    background: #eee6d8;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 900;
    font-size: 12px;
    color: #3c2b20;
  }
  .home .tile__date{ font-size: 12px; font-weight: 800; color: #6f7c85; line-height: 1.25; }
  .home .tile__btn{
    display: inline-block;
    align-self: flex-start;
    text-decoration: none;
    font-weight: 900;
    font-size: 12px;
    color: #3c2b20;
    background: #b6a894;
    padding: 10px 14px;
    border-radius: 999px;
  }
  .home .tile__btn:hover{ filter: brightness(.97); }

  .home .home-empty{
    padding: 14px;
    border-radius: 14px;
    background: rgba(0,0,0,.08);
    font-weight: 700;
    color: #fff;
  }

  /* Bouton "retour en haut" */

.back-to-top{
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
  z-index: 999999; /* pour passer au-dessus du footer */
  opacity: 0;
  transform: translateY(8px);
  pointer-events: none;
  transition: opacity .18s ease, transform .18s ease;
}
.back-to-top.is-visible{
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

  /* Responsive */
  @media (max-width: 1000px){
    .home .home-hero__features{
      grid-template-columns: 1fr;
      max-width: 720px;
      margin-left: 0;
      margin-top: 24px;
    }
    .home .hero-cta{ margin-top: 28px; }
    .home .home-hero__title{ font-size: 40px; }
  }
  @media (max-width: 720px){
    .home .home-hero{ padding: 70px 18px 50px; min-height: 620px; }
    .home .home-content{ padding: 30px 18px 60px; }
    .home .tile{ flex: 0 0 200px; }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Respect de l’accessibilité : si l’utilisateur préfère réduire les animations
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Apparition progressive des éléments du hero
    if (!reduceMotion) {
      const items = [
        document.querySelector('.home-hero__title'),
        document.querySelector('.home-hero__subtitle'),
        ...document.querySelectorAll('.home-hero__features .feature-card'),
        document.querySelector('.hero-cta'),
        document.querySelector('.hero-hint')
      ].filter(Boolean);

      items.forEach(el => el.classList.add('reveal'));

      requestAnimationFrame(() => {
        items.forEach((el, i) => {
          setTimeout(() => el.classList.add('is-in'), 120 + i * 120);
        });
      });
    }

    // Bouton "retour en haut"
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'back-to-top';
    btn.textContent = '↑';
    btn.setAttribute('aria-label', 'Revenir en haut de la page');
    document.body.appendChild(btn);

    btn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });

    const toggleBtn = () => {
      const y = window.scrollY || document.documentElement.scrollTop;
      btn.classList.toggle('is-visible', y > 450);
    };

    window.addEventListener('scroll', toggleBtn, { passive: true });
    toggleBtn();
  });
</script>
