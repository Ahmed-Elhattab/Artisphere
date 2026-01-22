<?php
// Images existantes (table enfant)
$images = $images ?? [];

// Image principale stockée dans pproduit.image (filename)
$main = $produit['image'] ?? null;

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Déterminer l'image principale affichée (fallback si $main vide)
$filenames = array_values(array_filter(array_map(fn($im) => $im['filename'] ?? '', $images)));
if (!$main && !empty($filenames)) $main = $filenames[0];
?>

<main class="product-edit-page">
  <section class="container product-edit-card">
    <h1>Éditer le produit</h1>

    <?php if (!empty($errors)): ?>
      <div class="form-error-box">
        <h3>Erreurs lors de la modification</h3>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= h($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- ======= FORM UPDATE (UNIQUE) ======= -->
    <form method="post"
          action="/artisphere/?controller=produit_update&action=update"
          enctype="multipart/form-data"
          id="productEditForm">

      <input type="hidden" name="csrf" value="<?= h($csrf ?? '') ?>">
      <input type="hidden" name="id_produit" value="<?= (int)$produit['id_produit'] ?>">

      <!-- Ici on injecte les delete_images[] en JS -->
      <div id="deletedImages"></div>

      <!-- ================== GALERIE PHOTOS ================== -->
      <section class="gallery">
        <h2>Photos</h2>

        <?php if (!empty($images)): ?>
          <div class="gallery-grid" id="galleryGrid">
            <?php foreach ($images as $im): ?>
              <?php
                $idImage   = (int)($im['id_image'] ?? 0);
                $filename  = (string)($im['filename'] ?? '');
                $src       = 'images/produits/' . $filename;
                $isMain    = ($main === $filename);
              ?>

              <div class="gitem <?= $isMain ? 'is-main' : '' ?>"
                   data-img-id="<?= $idImage ?>"
                   data-filename="<?= h($filename) ?>">

                <div class="gthumb">
                  <img src="<?= h($src) ?>"
                       alt="Photo produit"
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

                  <!-- ✅ PAS submit -->
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
          <p class="hint">Aucune photo pour ce produit. Ajoute-en ci-dessous.</p>
        <?php endif; ?>

        <div class="upload-block">
          <label class="label">Ajouter des photos</label>
          <input type="file"
                 name="images[]"
                 accept="image/png, image/jpeg, image/webp"
                 multiple>
          <p class="hint">Jusqu’à 6 images. 3 Mo max par image.</p>
        </div>
      </section>

      <!-- ================== INFOS PRODUIT ================== -->
      <label class="field">
        <span>Nom</span>
        <input type="text" name="nom" required value="<?= h($produit['nom'] ?? '') ?>">
      </label>

      <label class="field">
        <span>Quantité</span>
        <input type="number" name="quantite" min="0" required value="<?= (int)($produit['quantite'] ?? 0) ?>">
      </label>

      <label class="field">
        <span>Matériaux</span>
        <input type="text" name="materiaux" required value="<?= h($produit['materiaux'] ?? '') ?>">
      </label>

      <label class="field">
        <span>Prix (€)</span>
        <input type="number" name="prix" min="0" step="0.01" required value="<?= h($produit['prix'] ?? '') ?>">
      </label>

      <label class="field">
        <span>Catégorie</span>
        <select name="id_categorie" required>
          <option value="">— Choisir —</option>
          <?php foreach (($categories ?? []) as $c): ?>
            <option value="<?= (int)$c['id_categorie'] ?>"
              <?= ((int)($produit['id_categorie'] ?? 0) === (int)$c['id_categorie']) ? 'selected' : '' ?>>
              <?= h($c['nom'] ?? '') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="field">
        <span>Description</span>
        <textarea name="description" rows="6" required><?= h($produit['description'] ?? '') ?></textarea>
      </label>

      <!-- ================== ACTIONS ================== -->
      <div class="actions">
        <button class="btn-primary" type="submit">Enregistrer</button>

        <a class="btn-outline"
           href="/artisphere/?controller=produit_show&action=show&id=<?= (int)$produit['id_produit'] ?>">
          Annuler
        </a>

        <!-- bouton supprimer (dans le même alignement) -->
        <button type="submit"
                form="deleteProductForm"
                class="btn-danger btn-danger--ghost">
          Supprimer le produit
        </button>
      </div>

    </form>
    <!-- ======= FIN FORM UPDATE ======= -->

    <!-- ======= FORM DELETE (SÉPARÉ, NON IMBRIQUÉ) ======= -->
    <form id="deleteProductForm"
          method="post"
          action="/artisphere/?controller=produit_update&action=delete"
          onsubmit="return confirm('Supprimer définitivement ce produit ? Cette action est irréversible.');">
      <input type="hidden" name="csrf" value="<?= h($csrf ?? '') ?>">
      <input type="hidden" name="id_produit" value="<?= (int)$produit['id_produit'] ?>">
    </form>

  </section>
</main>

<!-- ✅ JS intégré (si tu ne veux pas créer un fichier séparé) -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const deletedBox = document.getElementById("deletedImages");
  const form = document.getElementById("productEditForm");

  if (!deletedBox || !form) return;

  function getMainRadio() {
    return form.querySelector('input[name="main_image"]:checked');
  }

  function selectFirstAvailableAsMain() {
    const firstRadio = form.querySelector('input[name="main_image"]');
    if (firstRadio) firstRadio.checked = true;
  }

  // Boutons supprimer (sans submit)
  document.querySelectorAll(".js-del-img").forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      const filename = btn.dataset.filename || "";

      if (!confirm("Supprimer cette photo ?")) return;

      // Ajoute delete_images[]
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "delete_images[]";
      input.value = id;
      deletedBox.appendChild(input);

      // Si on supprime l'image principale, on bascule sur une autre
      const main = getMainRadio();
      const card = btn.closest("[data-img-id]");

      if (card) card.remove();

      if (main && main.value === filename) {
        selectFirstAvailableAsMain();
      }
    });
  });

  // Sécurité : si aucune radio sélectionnée au submit (cas rare), on en sélectionne une
  form.addEventListener("submit", () => {
    const main = getMainRadio();
    if (!main) selectFirstAvailableAsMain();
  });
});
</script>
