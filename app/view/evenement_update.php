<?php
$images = $images ?? [];
$main = $event['image'] ?? null;

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$filenames = array_values(array_filter(array_map(fn($im) => $im['filename'] ?? '', $images)));
if (!$main && !empty($filenames)) $main = $filenames[0];
?>

<main class="event-edit-page">
  <section class="container event-edit-card">
    <h1>Éditer l’évènement</h1>

    <?php if (!empty($errors)): ?>
      <div class="form-error-box">
        <h3>Erreurs lors de la mise à jour</h3>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= h($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- FORM UPDATE -->
    <form method="post"
          action="/artisphere/?controller=evenement_update&action=submit"
          enctype="multipart/form-data"
          id="eventEditForm">

      <input type="hidden" name="csrf" value="<?= h($csrf ?? '') ?>">
      <input type="hidden" name="id_event" value="<?= (int)($event['id_event'] ?? 0) ?>">

      <div id="deletedImages"></div>

      <section class="gallery">
        <h2>Photos</h2>

        <?php if (!empty($images)): ?>
          <div class="gallery-grid" id="galleryGrid">
            <?php foreach ($images as $im): ?>
              <?php
                $idImage  = (int)($im['id_image'] ?? 0);
                $filename = (string)($im['filename'] ?? '');
                $src      = 'images/evenements/' . $filename;
                $isMain   = ($main === $filename);
              ?>

              <div class="gitem <?= $isMain ? 'is-main' : '' ?>"
                   data-img-id="<?= $idImage ?>"
                   data-filename="<?= h($filename) ?>">

                <div class="gthumb">
                  <img src="<?= h($src) ?>" alt="Photo évènement"
                       onerror="this.style.display='none'; this.closest('.gitem').classList.add('noimg');">
                </div>

                <div class="gactions">
                  <label class="radio">
                    <input type="radio"
                           name="main_image"
                           value="<?= h($filename) ?>"
                           <?= $isMain ? 'checked' : '' ?>>
                    Principale
                  </label>

                  <button type="button"
                          class="btn-danger js-del-img"
                          data-id="<?= $idImage ?>"
                          data-filename="<?= h($filename) ?>">
                    Supprimer
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="hint">Aucune photo pour cet évènement. Ajoute-en ci-dessous.</p>
        <?php endif; ?>

        <div class="upload-block">
          <label class="label">Ajouter des photos</label>
          <input type="file" name="images[]" accept="image/png, image/jpeg, image/webp" multiple>
          <p class="hint">Jusqu’à 6 images. 3 Mo max par image.</p>
        </div>
      </section>

      <div class="grid">
        <div class="form-row">
          <label>Nom</label>
          <input type="text" name="nom" required value="<?= h($event['nom'] ?? '') ?>">
        </div>

        <div class="form-row">
          <label>Lieu</label>
          <input type="text" name="lieu" required value="<?= h($event['lieu'] ?? '') ?>">
        </div>

        <div class="form-row">
          <label>Type</label>
          <select name="id_type" required>
            <option value="">-- Choisir un type --</option>
            <?php foreach (($types ?? []) as $t): ?>
              <option value="<?= (int)$t['id_type'] ?>"
                <?= ((int)($event['id_type'] ?? 0) === (int)$t['id_type']) ? 'selected' : '' ?>>
                <?= h($t['nom'] ?? '') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row">
          <label>Prix (€)</label>
          <input type="number" step="0.01" min="0" name="prix" required value="<?= h((string)($event['prix'] ?? '')) ?>">
        </div>

        <div class="form-row">
          <label>Places</label>
          <input type="number" min="0" name="nombre_place" required value="<?= (int)($event['nombre_place'] ?? 0) ?>">
        </div>

        <div class="form-row">
          <label>Date début</label>
          <input type="date" name="date_debut" required value="<?= h($event['date_debut'] ?? '') ?>">
        </div>

        <div class="form-row">
          <label>Date fin</label>
          <input type="date" name="date_fin" required value="<?= h($event['date_fin'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row">
        <label>Description</label>
        <textarea name="description" rows="6" required><?= h($event['description'] ?? '') ?></textarea>
      </div>

      <div class="actions">
        <button class="btn-primary" type="submit">Enregistrer</button>

        <a class="btn-outline"
           href="/artisphere/?controller=evenement_show&action=show&id=<?= (int)($event['id_event'] ?? 0) ?>">
          Annuler
        </a>

        <!-- bouton supprimer aligné (form séparé) -->
        <button type="submit"
                form="deleteEventForm"
                class="btn-danger btn-danger--ghost">
          Supprimer l’évènement
        </button>
      </div>
    </form>

    <!-- FORM DELETE (séparé, non imbriqué) -->
    <form id="deleteEventForm"
          method="post"
          action="/artisphere/?controller=evenement_update&action=delete"
          onsubmit="return confirm('Supprimer définitivement cet évènement ? Cette action est irréversible.');">
      <input type="hidden" name="csrf" value="<?= h($csrf ?? '') ?>">
      <input type="hidden" name="id_event" value="<?= (int)($event['id_event'] ?? 0) ?>">
    </form>

  </section>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const deletedBox = document.getElementById("deletedImages");
  const form = document.getElementById("eventEditForm");

  if (!deletedBox || !form) return;

  function getMainRadio() {
    return form.querySelector('input[name="main_image"]:checked');
  }

  function selectFirstAvailableAsMain() {
    const firstRadio = form.querySelector('input[name="main_image"]');
    if (firstRadio) firstRadio.checked = true;
  }

  document.querySelectorAll(".js-del-img").forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      const filename = btn.dataset.filename || "";

      if (!confirm("Supprimer cette photo ?")) return;

      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "delete_images[]";
      input.value = id;
      deletedBox.appendChild(input);

      const main = getMainRadio();
      const card = btn.closest("[data-img-id]");
      if (card) card.remove();

      if (main && main.value === filename) {
        selectFirstAvailableAsMain();
      }
    });
  });

  form.addEventListener("submit", () => {
    if (!getMainRadio()) selectFirstAvailableAsMain();
  });
});
</script>
