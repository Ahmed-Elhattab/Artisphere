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

      <div class="actions">
        <a class="btn-outline" href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">← Retour</a>
      </div>
    </div>
  </section>
</main>