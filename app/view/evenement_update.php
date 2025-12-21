<?php
$imgUrl = !empty($event['image'])
  ? 'images/evenements/' . $event['image']
  : null;
?>

<main class="event-edit-page">
  <section class="container event-edit-card">
    <h1>Éditer l’évènement</h1>

    <?php if (!empty($errors)): ?>
      <div class="form-error-box">
        <h3>Erreurs lors de la mise à jour</h3>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="image-row">
      <div class="avatar big">
        <?php if ($imgUrl): ?>
          <img src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8') ?>"
               alt="Image évènement"
               onerror="this.onerror=null; this.replaceWith(document.createTextNode('📅'));">
        <?php else: ?>
          📅
        <?php endif; ?>
      </div>
    </div>

    <form method="post"
          action="/artisphere/?controller=evenement_update&action=submit"
          enctype="multipart/form-data">

      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="id_event" value="<?= (int)$event['id_event'] ?>">

      <div class="form-row">
        <label>Image</label>
        <input type="file" name="image" accept="image/png, image/jpeg, image/webp">
        <p class="hint">Laisse vide pour conserver l’image actuelle.</p>
      </div>

      <div class="grid">
        <div class="form-row">
          <label>Nom</label>
          <input type="text" name="nom" required value="<?= htmlspecialchars($event['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-row">
          <label>Lieu</label>
          <input type="text" name="lieu" required value="<?= htmlspecialchars($event['lieu'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-row">
          <label>Type</label>
          <input type="text" name="type" required value="<?= htmlspecialchars($event['type'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-row">
          <label>Prix (€)</label>
          <input type="number" step="0.01" min="0" name="prix" required value="<?= htmlspecialchars((string)($event['prix'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-row">
          <label>Places</label>
          <input type="number" min="0" name="nombre_place" required value="<?= (int)($event['nombre_place'] ?? 0) ?>">
        </div>

        <div class="form-row">
          <label>Date début</label>
          <input type="date" name="date_debut" required value="<?= htmlspecialchars($event['date_debut'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-row">
          <label>Date fin</label>
          <input type="date" name="date_fin" required value="<?= htmlspecialchars($event['date_fin'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
      </div>

      <div class="form-row">
        <label>Description</label>
        <textarea name="description" rows="6" required><?= htmlspecialchars($event['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>

      <div class="actions">
        <button class="btn-primary" type="submit">Enregistrer</button>
        <a class="btn-outline" href="/artisphere/?controller=evenement_show&action=show&id=<?= (int)$event['id_event'] ?>">Annuler</a>
      </div>
    </form>
  </section>
</main>