<?php
$img = !empty($resa['image']) ? 'images/evenements/' . $resa['image'] : 'images/image-photo.jpg';

$currentRating = (int)($old['rating'] ?? ($resa['note'] ?? 5));
$currentRating = max(1, min(5, $currentRating));

$currentMessage = (string)($old['message'] ?? ($resa['message'] ?? ''));

$isAlreadyRated = (($resa['status'] ?? '') === 'notée');

$dates = htmlspecialchars($resa['date_debut'] ?? '', ENT_QUOTES, 'UTF-8');
if (!empty($resa['date_fin']) && $resa['date_fin'] !== $resa['date_debut']) {
    $dates .= ' – ' . htmlspecialchars($resa['date_fin'], ENT_QUOTES, 'UTF-8');
}
?>

<main class="container">
  <h1 class="page-title">AVIS ÉVÈNEMENTS</h1>

  <?php if (!empty($errors)): ?>
    <div class="form-error-box" style="background:#fbecec;padding:12px;border-radius:10px;margin-bottom:18px;">
      <h3 style="margin-bottom:10px;">Erreur</h3>
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['sent'])): ?>
    <div class="form-success-box" style="background:#eaf7ea;padding:12px;border-radius:10px;margin-bottom:18px;">
      <?= ($_GET['sent'] === '1')
          ? "<strong>Merci !</strong> Votre avis a été enregistré."
          : "<strong>Oups.</strong> Impossible d'enregistrer l'avis." ?>
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
        <?= htmlspecialchars($resa['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
        <small style="opacity:.8;">
          <?= htmlspecialchars($resa['lieu'] ?? '', ENT_QUOTES, 'UTF-8') ?>
          · <?= htmlspecialchars($resa['type_nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>
          · <?= $dates ?>
        </small>
      </div>
      <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="Évènement" class="art-thumb"
           onerror="this.onerror=null; this.src='images/image-photo.jpg';">
    </div>

    <form method="post" action="/artisphere/?controller=avis_evenement&action=submit">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="id_resa" value="<?= (int)($resa['id_resa_event'] ?? 0) ?>">

      <div class="stars" role="radiogroup" aria-label="Note (de 1 à 5 étoiles)">
        <button class="star" type="button" data-value="1" aria-label="1 étoile" aria-checked="false">★</button>
        <button class="star" type="button" data-value="2" aria-label="2 étoiles" aria-checked="false">★</button>
        <button class="star" type="button" data-value="3" aria-label="3 étoiles" aria-checked="false">★</button>
        <button class="star" type="button" data-value="4" aria-label="4 étoiles" aria-checked="false">★</button>
        <button class="star" type="button" data-value="5" aria-label="5 étoiles" aria-checked="false">★</button>
      </div>

      <input type="hidden" id="rating" name="rating" value="<?= (int)$currentRating ?>">

      <hr>

      <textarea id="avis" name="message" placeholder="Décrit ton avis ici ..." class="input"
        <?= $isAlreadyRated ? 'disabled' : '' ?>
      ><?= htmlspecialchars($currentMessage, ENT_QUOTES, 'UTF-8') ?></textarea>

      <hr>

      <?php if ($isAlreadyRated): ?>
        <button class="btn" type="button" disabled>Déjà notée</button>
      <?php else: ?>
        <button class="btn" id="sendBtn" type="submit">Envoyer</button>
      <?php endif; ?>
    </form>

    <div style="margin-top:14px;">
      <a href="/artisphere/?controller=profil&action=index" style="text-decoration:none;font-weight:700;color:#5f4b3a;">
        ← Retour profil
      </a>
    </div>
  </section>
</main>