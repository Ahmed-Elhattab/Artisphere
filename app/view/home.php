
<section class="home-hero">
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
  <aside class="home-cta">
    <div class="cta-card">
      <div class="cta-title">PRET A VENDRE OU ACHETER<br>DES ARTICLES ?</div>
      <a class="cta-button" href="<?= htmlspecialchars($ctaHref, ENT_QUOTES, 'UTF-8') ?>">
        Commencer par ici
      </a>
    </div>
  </aside>

  <div class="home-hero__left">
    <p class="home-hero__text">
      Découvrez le talent des artisans et créateurs passionnés près de chez vous.<br>
      Ici, chaque pièce raconte une histoire, chaque artisan partage son savoir-faire.
    </p>

    <div class="home-pill">LES PRODUITS TENDANCES</div>

    <div class="carousel-row">
      <a class="arrow <?= ($pProd <= 1 ? 'disabled' : '') ?>"
        href="/artisphere/?controller=index&action=index&p_prod=<?= max(1, $pProd-1) ?>&p_evt=<?= (int)$pEvt ?>">←</a>

      <div class="home-products">
        <?php foreach ($produits as $p): ?>
          <?php
            $img = !empty($p['image'])
              ? "images/produits/" . $p['image']
              : "/artisphere/images/produit.png";
          ?>
          <article class="product-card">
            <img
              src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
              alt="<?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?>"
              onerror="this.onerror=null; this.src='/artisphere/images/produit.png';"
            >

            <div class="product-name">
              <?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?>
            </div>

            <div class="product-price">
              <?= number_format((float)$p['prix'], 2, ',', ' ') ?> €
            </div>

            <a class="card-link"
              href="/artisphere/?controller=produit_show&action=show&id=<?= (int)$p['id_produit'] ?>">
              Voir
            </a>
          </article>
        <?php endforeach; ?>
      </div>

      <a class="arrow <?= ($pProd >= $pagesProd ? 'disabled' : '') ?>"
        href="/artisphere/?controller=index&action=index&p_prod=<?= min($pagesProd, $pProd+1) ?>&p_evt=<?= (int)$pEvt ?>">→</a>
    </div>

    <div class="home-pill">LES EVENEMENTS A LA UNE</div>

    <div class="carousel-row">
      <a class="arrow <?= ($pEvt <= 1 ? 'disabled' : '') ?>"
        href="/artisphere/?controller=index&action=index&p_evt=<?= max(1, $pEvt-1) ?>&p_prod=<?= (int)$pProd ?>">←</a>

     <div class="home-events">
      <?php foreach ($evenements as $e): ?>
        <?php
          $img = !empty($e['image'])
            ? "images/evenements/" . $e['image']
            : "images/image-photo.jpg";
        ?>
        <article class="event-card">
          <img
            src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
            alt="<?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?>"
            onerror="this.onerror=null; this.src='images/image-photo.jpg';"
          >

          <div class="event-title">
            <?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?>
          </div>

          <div class="event-meta">
            <?= htmlspecialchars($e['lieu'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            <?= !empty($e['type']) ? ' · ' . htmlspecialchars($e['type'], ENT_QUOTES, 'UTF-8') : '' ?>
          </div>

          <div class="event-date">
            DU <?= htmlspecialchars($e['date_debut'], ENT_QUOTES, 'UTF-8') ?>
            AU <?= htmlspecialchars($e['date_fin'], ENT_QUOTES, 'UTF-8') ?>
          </div>

          <a class="card-link"
            href="/artisphere/?controller=evenement_show&action=show&id=<?= (int)$e['id_event'] ?>">
            Voir
          </a>
        </article>
      <?php endforeach; ?>
    </div>

      <a class="arrow <?= ($pEvt >= $pagesEvt ? 'disabled' : '') ?>"
        href="/artisphere/?controller=index&action=index&p_evt=<?= min($pagesEvt, $pEvt+1) ?>&p_prod=<?= (int)$pProd ?>">→</a>
    </div>

</section>