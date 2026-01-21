<?php 
// Avatar
$avatarFile = $artisan['avatar'] ?? null;
$avatarDir  = '/public/images/avatars/';
$avatarUrl  = null;

if ($avatarFile) {
    $fullPath = $avatarDir . $avatarFile;
    if (is_file($fullPath)) {
        $avatarUrl = $avatarDir . $avatarFile;
    }
}

function stars(int $note): string {
    $note = max(0, min(5, $note));
    return str_repeat('★', $note) . str_repeat('☆', 5 - $note);
}
?>

<main class="artisan-page">
  <section class="container artisan-card">

    <div class="artisan-top">
      <div class="avatar">
        <?php if ($avatarUrl): ?>
          <img src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Photo artisan"
               onerror="this.onerror=null; this.replaceWith(document.createTextNode('👤'));">
        <?php else: ?>
          👤
        <?php endif; ?>
      </div>

      <div class="artisan-info">
        <h1><?= htmlspecialchars($artisan['pseudo'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="muted">
          <?= htmlspecialchars($artisan['prenom'], ENT_QUOTES, 'UTF-8') ?>
          <?= htmlspecialchars($artisan['nom'], ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p class="muted">📧 <?= htmlspecialchars($artisan['email'], ENT_QUOTES, 'UTF-8') ?></p>

        <?php if (!empty($artisan['adresse'])): ?>
          <p class="muted">📍 <?= htmlspecialchars($artisan['adresse'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="actions">
          <a class="btn-outline" href="/artisphere/?controller=artisans&action=index">← Retour</a>
        </div>
      </div>
    </div>

    <!-- Produits -->
    <h2 class="section-title">Produits proposés</h2>

    <?php if (!empty($produits)): ?>
      <div class="products-grid">
        <?php foreach ($produits as $p): ?>
          <?php
            $img = !empty($p['image']) ? 'images/produits/' . $p['image'] : 'images/produit.png';
          ?>
          <a class="product-card"
             href="/artisphere/?controller=produit_show&action=show&id=<?= (int)$p['id_produit'] ?>">
            <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
                 alt="<?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?>"
                 onerror="this.onerror=null; this.src='images/produit.png';">
            <div class="product-name"><?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="product-meta">
              <?= number_format((float)$p['prix'], 2, ',', ' ') ?> € ·
              <?= ((int)$p['quantite'] > 0) ? ('Stock : ' . (int)$p['quantite']) : 'Rupture' ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="empty">Aucun produit pour le moment.</p>
    <?php endif; ?>

    <!-- Evenements -->
    <h2 class="section-title">Évènements proposés</h2>

    <?php if (!empty($evenements)): ?>
    <div class="events-grid">
        <?php foreach ($evenements as $ev): ?>
        <?php
            $imgEv = !empty($ev['image'])
            ? 'images/evenements/' . $ev['image']
            : 'images/image-photo.jpg';

            $dates = htmlspecialchars($ev['date_debut'], ENT_QUOTES, 'UTF-8');
            if (!empty($ev['date_fin']) && $ev['date_fin'] !== $ev['date_debut']) {
              $dates .= ' – ' . htmlspecialchars($ev['date_fin'], ENT_QUOTES, 'UTF-8');
            }

            $typeNom = (string)($ev['type_nom'] ?? '');
        ?>

        <a class="event-card"
            href="/artisphere/?controller=evenement_show&action=show&id=<?= (int)$ev['id_event'] ?>">
            <img src="<?= htmlspecialchars($imgEv, ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($ev['nom'], ENT_QUOTES, 'UTF-8') ?>"
                onerror="this.onerror=null; this.src='images/image-photo.jpg';">

            <div class="event-name"><?= htmlspecialchars($ev['nom'], ENT_QUOTES, 'UTF-8') ?></div>

            <div class="event-meta">
              <?= htmlspecialchars($typeNom !== '' ? $typeNom : 'Type', ENT_QUOTES, 'UTF-8') ?> ·
              📍 <?= htmlspecialchars($ev['lieu'], ENT_QUOTES, 'UTF-8') ?>
            </div>

            <div class="event-meta">
              🗓 <?= $dates ?> ·
              <?= ((float)$ev['prix'] <= 0) ? 'Gratuit' : (number_format((float)$ev['prix'], 2, ',', ' ') . ' €') ?> ·
              <?= ((int)$ev['nombre_place'] > 0) ? ('Places : ' . (int)$ev['nombre_place']) : 'Complet' ?>
            </div>
        </a>

        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="empty">Aucun évènement pour le moment.</p>
    <?php endif; ?>

    <!-- Avis -->
    <h2 class="section-title">Avis clients</h2>

    <div class="rating-box">
      <div class="rating-avg">
        <?php if ($avg === null): ?>
          <strong>Aucune note</strong>
        <?php else: ?>
          <strong><?= number_format($avg, 1, ',', ' ') ?>/5</strong>
          <span class="muted">(<?= count($avis) ?> avis)</span>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($avis)): ?>
      <div class="reviews">
        <?php foreach ($avis as $a): ?>
          <article class="review">
            <div class="review-head">
              <div class="review-user">
                <?= htmlspecialchars($a['client_pseudo'] ?? ('Client #' . (int)$a['id_client']), ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="review-note">
                <?= htmlspecialchars(stars((int)$a['note']), ENT_QUOTES, 'UTF-8') ?>
                <span class="muted">(<?= (int)$a['note'] ?>/5)</span>
              </div>
            </div>

            <?php if (!empty($a['commentaire'])): ?>
              <p class="review-text"><?= nl2br(htmlspecialchars($a['commentaire'], ENT_QUOTES, 'UTF-8')) ?></p>
            <?php else: ?>
              <p class="review-text muted">Aucun commentaire.</p>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="empty">Aucun avis pour le moment.</p>
    <?php endif; ?>

    <div class="actions">
      <?php if (!empty($canRate)): ?>
        <a class="btn-outline"
          href="/artisphere/?controller=avis_artisan&action=index&id_artisan=<?= (int)$artisan['id_personne'] ?>">
          Noter cet artisan
        </a>
      <?php elseif (!empty($alreadyRated)): ?>
        <span class="muted">Vous avez déjà noté cet artisan.</span>
      <?php endif; ?>
    </div>

  </section>
</main>