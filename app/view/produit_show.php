<?php
$img = !empty($produit['image']) ? 'images/produits/' . $produit['image'] : null;
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
      <h1><?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?></h1>
      <p class="meta">
        Créé par : <strong><?= htmlspecialchars($produit['createur_pseudo'] ?? 'inconnu', ENT_QUOTES, 'UTF-8') ?></strong>
      </p>

      <p class="meta">
        Quantité : <?= (int)$produit['quantite'] ?> ·
        Prix : <?= htmlspecialchars((string)$produit['prix'], ENT_QUOTES, 'UTF-8') ?> €
      </p>

      <p class="meta">
        Matériaux : <?= htmlspecialchars($produit['materiaux'], ENT_QUOTES, 'UTF-8') ?>
      </p>

      <h2>Description</h2>
      <p><?= nl2br(htmlspecialchars($produit['description'], ENT_QUOTES, 'UTF-8')) ?></p>

      <?php
        $backUrl = $_SESSION['previous_url'] ?? '/artisphere/?controller=index&action=index';
      ?>

      <?php
        $backUrl = $_SESSION['previous_url'] ?? '/artisphere/?controller=index&action=index';
        $isLogged = !empty($_SESSION['user']);
        $isOwner = $isLogged && ((int)$_SESSION['user']['id'] === (int)$produit['id_createur']);

        if (empty($_SESSION['csrf'])) {
          $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }
      ?>

      <div class="actions">
        <?php if ($isLogged): ?>
          <form method="post" action="/artisphere/?controller=produit_show&action=reserve" class="inline">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="id_produit" value="<?= (int)$produit['id_produit'] ?>">
            <input type="hidden" name="back" value="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">
            <button class="btn-primary" type="submit" <?= ((int)$produit['quantite'] <= 0 ? 'disabled' : '') ?>>
              Réserver
            </button>
          </form>
        <?php else: ?>
          <a class="btn-primary" href="/artisphere/?controller=connexion&action=index">Réserver</a>
        <?php endif; ?>

        <?php if ($isOwner): ?>
          <a class="btn-outline" href="/artisphere/?controller=fiche_produit&action=edit&id=<?= (int)$produit['id_produit'] ?>">
            Éditer
          </a>
        <?php endif; ?>

        <a class="btn-outline" href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">← Retour</a>
      </div>

      <?php if (isset($_GET['reserved'])): ?>
        <p class="meta">
          <?= ($_GET['reserved'] === '1') ? 'Réservation enregistrée' : 'Impossible de réserver (stock épuisé ou déjà réservé).' ?>
        </p>
      <?php endif; ?>
    </div>
  </section>
</main>