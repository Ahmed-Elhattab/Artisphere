
<section class="home">
  <?php
    $isLogged = !empty($_SESSION['user']);
    $role = $_SESSION['user']['role'] ?? null;

    $ctaHref = '/artisphere/?controller=connexion&action=index';
    if ($isLogged) {
      if ($role === 'artisan' || $role === 'admin') {
        $ctaHref = '/artisphere/?controller=mes_creations&action=index';
      } else {
        $ctaHref = '/artisphere/?controller=catalogue&action=index';
      }
    }
  ?>

  <header class="home-hero">
    <!-- image de fond (via CSS) + overlay -->
    <div class="home-hero__overlay" aria-hidden="true"></div>

    <div class="home-hero__container">
      <div class="home-hero__intro">
        <h1 class="home-hero__title">Artisphere</h1>
        <p class="home-hero__subtitle">
          Une plateforme pour découvrir, acheter et vendre des créations artisanales.
        </p>

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

        <a class="hero-cta"
           href="<?= htmlspecialchars($ctaHref, ENT_QUOTES, 'UTF-8') ?>">
          Commencer
        </a>

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
</section>
