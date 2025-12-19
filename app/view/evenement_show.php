<?php
$img = !empty($evenement['image']) ? 'images/evenements/' . $evenement['image'] : null;
?>
<main class="details-page">
  <section class="details-card container">
    <div class="details-media <?= $img ? '' : 'noimg' ?>">
      <?php if ($img): ?>
        <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
             alt="Image évènement"
             onerror="this.parentNode.classList.add('noimg')">
      <?php endif; ?>
      <div class="details-fallback">🎟️</div>
    </div>

    <div class="details-body">
      <h1><?= htmlspecialchars($evenement['nom'], ENT_QUOTES, 'UTF-8') ?></h1>

      <p class="meta">
        <?= htmlspecialchars($evenement['lieu'], ENT_QUOTES, 'UTF-8') ?> ·
        <?= htmlspecialchars($evenement['type'], ENT_QUOTES, 'UTF-8') ?>
      </p>

      <p class="meta">
        Du <?= htmlspecialchars($evenement['date_debut'], ENT_QUOTES, 'UTF-8') ?>
        au <?= htmlspecialchars($evenement['date_fin'], ENT_QUOTES, 'UTF-8') ?>
      </p>

      <p class="meta">
        Places : <?= (int)$evenement['nombre_place'] ?> ·
        Prix : <?= htmlspecialchars((string)$evenement['prix'], ENT_QUOTES, 'UTF-8') ?> €
      </p>

      <h2>Description</h2>
      <p><?= nl2br(htmlspecialchars($evenement['description'], ENT_QUOTES, 'UTF-8')) ?></p>

      <?php
        $backUrl = $_SESSION['previous_url']?? '/artisphere/?controller=index&action=index';
      ?>

      <div class="actions">
        <a class="btn-outline" href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">← Retour</a>
      </div>
    </div>
  </section>
</main>