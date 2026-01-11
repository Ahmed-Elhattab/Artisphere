<?php
$img = !empty($produit['image']) ? 'images/produits/' . $produit['image'] : null;

$backUrl = $_SESSION['previous_url'] ?? '/artisphere/?controller=index&action=index';

$isLogged = !empty($_SESSION['user']);
$idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
$isOwner = $isLogged && ($idUser > 0) && ((int)$idUser === (int)($produit['id_createur'] ?? 0));

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

// ✅ Stock dispo = quantite - stock_reserve
$stockReel = (int)($produit['quantite'] ?? 0);
$stockReserve = (int)($produit['stock_reserve'] ?? 0);
$stockDispo = max(0, $stockReel - $stockReserve);
?>

<main class="details-page">
  <section class="details-card container">
    <div class="details-media <?= $img ? '' : 'noimg' ?>">
      <?php if ($img): ?>
        <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
             alt="Image produit"
             onerror="this.parentNode.classList.add('noimg')">
      <?php endif; ?>
      <div class="details-fallback">📦</div>
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