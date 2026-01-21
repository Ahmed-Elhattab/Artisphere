<?php
$oldRating = (int)($old['rating'] ?? 5);
$oldMsg = (string)($old['message'] ?? '');
$img = !empty($artisan['avatar']) ? '/public/images/avatars/' . $artisan['avatar'] : null;
?>

<main class="container">
  <h1 class="page-title">AVIS ARTISAN</h1>

  <?php if (!empty($errors)): ?>
    <div class="form-error-box">
      <h3>Erreur</h3>
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <section class="card">
    <h2>Laisser une évaluation</h2>

    <div class="user-line">
      <span class="avatar">🧑‍🎨</span>
      <span class="username"><?= htmlspecialchars($userPseudo ?? 'User', ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <hr>

    <div class="art-row">
      <div>
        <?= htmlspecialchars($artisan['pseudo'] ?? 'Artisan', ENT_QUOTES, 'UTF-8') ?>
      </div>

      <?php if ($img && is_file($img)): ?>
        <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="Artisan" class="art-thumb">
      <?php else: ?>
        <img src="images/produit.png" alt="Artisan" class="art-thumb">
      <?php endif; ?>
    </div>

    <form method="post" action="/artisphere/?controller=avis_artisan&action=submit">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="id_artisan" value="<?= (int)($artisan['id_personne'] ?? 0) ?>">

      <div class="stars" role="radiogroup" aria-label="Note (de 1 à 5 étoiles)">
        <button class="star" data-value="1" type="button" aria-label="1 étoile">★</button>
        <button class="star" data-value="2" type="button" aria-label="2 étoiles">★</button>
        <button class="star" data-value="3" type="button" aria-label="3 étoiles">★</button>
        <button class="star" data-value="4" type="button" aria-label="4 étoiles">★</button>
        <button class="star" data-value="5" type="button" aria-label="5 étoiles">★</button>
      </div>

      <input type="hidden" id="rating" name="rating" value="<?= (int)$oldRating ?>">

      <hr>

      <textarea name="message" id="avis" placeholder="Décris ton avis ici ..." class="input"><?= htmlspecialchars($oldMsg, ENT_QUOTES, 'UTF-8') ?></textarea>

      <hr>

      <button class="btn" id="sendBtn" type="submit">Envoyer</button>
    </form>
  </section>
</main>