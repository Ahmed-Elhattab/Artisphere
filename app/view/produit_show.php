<?php
$img = !empty($produit['image']) ? 'images/produits/' . $produit['image'] : null;

$backUrl = $_SESSION['previous_url'] ?? '/artisphere/?controller=index&action=index';

$isLogged = !empty($_SESSION['user']);
$idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
$isOwner = $isLogged && ($idUser > 0) && ((int)$idUser === (int)($produit['id_createur'] ?? 0));

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

// Stock dispo = quantite - stock_reserve
$stockReel = (int)($produit['quantite'] ?? 0);
$stockReserve = (int)($produit['stock_reserve'] ?? 0);
$stockDispo = max(0, $stockReel - $stockReserve);
?>

<main class="details-page">
  <section class="details-card container">
    <?php
    $gallery = $images ?? [];
    $mainImg = null;

    if (!empty($gallery)) {
      $mainImg = 'images/produits/' . $gallery[0]['filename'];
    } elseif (!empty($produit['image'])) {
      $mainImg = 'images/produits/' . $produit['image'];
    }
    ?>

    <div class="details-media <?= $mainImg ? '' : 'noimg' ?>">
      <?php if ($mainImg): ?>
        <img id="mainMedia"
            src="<?= htmlspecialchars($mainImg, ENT_QUOTES, 'UTF-8') ?>"
            alt="Image produit"
            onerror="this.parentNode.classList.add('noimg')">
      <?php endif; ?>
      <div class="details-fallback">📦</div>

      <?php if (count($gallery) > 1): ?>
        <div class="thumbs">
          <?php foreach ($gallery as $g): ?>
            <?php $src = 'images/produits/' . $g['filename']; ?>
            <button type="button" class="thumb" data-src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>">
              <img src="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8') ?>" alt="">
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="details-body">
      <h1><?= htmlspecialchars($produit['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>

      <p class="meta">
        Créé par : <strong><?= htmlspecialchars($produit['createur_pseudo'] ?? 'inconnu', ENT_QUOTES, 'UTF-8') ?></strong>
      </p>

      <p class="meta">
        Stock réel : <?= $stockReel ?> ·
        Réservé : <?= $stockReserve ?> ·
        Disponible : <?= $stockDispo ?>
      </p>

      <p class="meta">
        Prix : <?= htmlspecialchars((string)($produit['prix'] ?? ''), ENT_QUOTES, 'UTF-8') ?> €
      </p>

      <?php if (!empty($produit['materiaux'])): ?>
        <p class="meta">
          Matériaux : <?= htmlspecialchars($produit['materiaux'], ENT_QUOTES, 'UTF-8') ?>
        </p>
      <?php endif; ?>

      <h2>Description</h2>
      <p><?= nl2br(htmlspecialchars($produit['description'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>

      <?php
        $avg = isset($rating['avg']) ? (float)$rating['avg'] : 0.0;
        $nb  = isset($rating['count']) ? (int)$rating['count'] : 0;
        $avgTxt = number_format($avg, 1, ',', ' ');
        $fullStars = (int)floor($avg);
        $half = ($avg - $fullStars) >= 0.5;
        ?>

        <section class="reviews">
          <h2>Avis</h2>

          <div class="rating-summary">
            <div class="stars-line" aria-label="Note moyenne">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php
                  $cls = 'star-empty';
                  if ($i <= $fullStars) $cls = 'star-filled';
                  elseif ($half && $i === $fullStars + 1) $cls = 'star-half';
                ?>
                <span class="star <?= $cls ?>">★</span>
              <?php endfor; ?>
            </div>

            <div class="rating-text">
              <strong><?= htmlspecialchars($avgTxt, ENT_QUOTES, 'UTF-8') ?>/5</strong>
              <span class="muted">(<?= $nb ?> avis)</span>
            </div>
          </div>

          <?php if (!empty($randomReviews)): ?>
            <div class="reviews-list">
              <?php foreach ($randomReviews as $rv): ?>
                <article class="review-card">
                  <div class="review-head">
                    <span class="review-author"><?= htmlspecialchars($rv['auteur'] ?? 'Client', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="review-note"><?= (int)$rv['note'] ?>/5</span>
                  </div>
                  <p class="review-msg"><?= nl2br(htmlspecialchars($rv['message'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                </article>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="muted">Aucun avis pour le moment.</p>
          <?php endif; ?>
        </section>

      <div class="actions">

        <?php if ($isLogged): ?>

          <?php if (!empty($isReserved)): ?>
            <!-- Annuler réservation -->
            <form method="post"
                  action="/artisphere/?controller=produit_show&action=cancelReservation"
                  class="inline">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="id_produit" value="<?= (int)($produit['id_produit'] ?? 0) ?>">
              <input type="hidden" name="back" value="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">
              <button class="btn-outline" type="submit">Annuler la réservation</button>
            </form>
          <?php else: ?>
            <!-- Réserver -->
            <form method="post"
                action="/artisphere/?controller=produit_show&action=reserve"
                class="inline">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id_produit" value="<?= (int)($produit['id_produit'] ?? 0) ?>">
            <input type="hidden" name="back" value="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">

            <label class="qty">
              Nombre :
              <input
                type="number"
                name="quantite"
                min="1"
                max="<?= (int)$stockDispo ?>"
                value="1"
                <?= ($stockDispo <= 0 ? 'disabled' : '') ?>
                required
              >
            </label>

            <button class="btn-primary" type="submit" <?= ($stockDispo <= 0 ? 'disabled' : '') ?>>
              Réserver
            </button>
          </form>
          <?php endif; ?>

        <?php else: ?>
          <a class="btn-primary" href="/artisphere/?controller=connexion&action=index">Réserver</a>
        <?php endif; ?>

        <?php if ($isOwner): ?>
          <a class="btn-outline"
             href="/artisphere/?controller=produit_update&action=edit&id=<?= (int)($produit['id_produit'] ?? 0) ?>">
            Éditer
          </a>
        <?php endif; ?>

        <a class="btn-outline" href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">← Retour</a>
      </div>

      <?php if (isset($_GET['reserved'])): ?>
        <p class="meta">
          <?= ($_GET['reserved'] === '1')
              ? 'Réservation enregistrée.'
              : 'Impossible de réserver (stock insuffisant ou déjà réservé).' ?>
        </p>
      <?php endif; ?>

      <?php if (isset($_GET['cancelled'])): ?>
        <p class="meta">
          <?= ($_GET['cancelled'] === '1')
              ? 'Réservation annulée.'
              : 'Impossible d’annuler la réservation.' ?>
        </p>
      <?php endif; ?>

    </div>
  </section>
</main>