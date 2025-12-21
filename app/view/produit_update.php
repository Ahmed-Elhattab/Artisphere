<?php
$imgUrl = !empty($produit['image']) ? 'images/produits/' . $produit['image'] : null;
?>

<main class="product-edit-page">
  <section class="container product-edit-card">
    <h1>Éditer le produit</h1>

    <?php if (!empty($errors)): ?>
      <div class="form-error-box">
        <h3>Erreurs lors de la modification</h3>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="/artisphere/?controller=produit_update&action=update" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="id_produit" value="<?= (int)$produit['id_produit'] ?>">

      <div class="image-row">
        <div class="preview">
          <?php if ($imgUrl): ?>
            <img src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Image produit"
                 onerror="this.style.display='none'; this.parentNode.classList.add('noimg');">
          <?php endif; ?>
          <div class="fallback">📦</div>
        </div>

        <div class="upload">
          <label class="label">Changer l’image</label>
          <input type="file" name="image" accept="image/png, image/jpeg, image/webp">
          <p class="hint">Si tu ne choisis rien, l’image actuelle est conservée.</p>
        </div>
      </div>

      <label class="field">
        <span>Nom</span>
        <input type="text" name="nom" required value="<?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?>">
      </label>

      <label class="field">
        <span>Quantité</span>
        <input type="number" name="quantite" min="0" required value="<?= (int)$produit['quantite'] ?>">
      </label>

      <label class="field">
        <span>Matériaux</span>
        <input type="text" name="materiaux" required value="<?= htmlspecialchars($produit['materiaux'], ENT_QUOTES, 'UTF-8') ?>">
      </label>

      <label class="field">
        <span>Prix (€)</span>
        <input type="number" name="prix" min="0" step="0.01" required value="<?= htmlspecialchars((string)$produit['prix'], ENT_QUOTES, 'UTF-8') ?>">
      </label>

      <label class="field">
        <span>Catégorie</span>
        <select name="id_categorie" required>
          <option value="">— Choisir —</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['id_categorie'] ?>"
              <?= ((int)$produit['id_categorie'] === (int)$c['id_categorie']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nom'], ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="field">
        <span>Description</span>
        <textarea name="description" rows="6" required><?= htmlspecialchars($produit['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
      </label>

      <div class="actions">
        <button class="btn-primary" type="submit">Enregistrer</button>
        <a class="btn-outline" href="/artisphere/?controller=produit_show&action=show&id=<?= (int)$produit['id_produit'] ?>">Annuler</a>
      </div>
    </form>
  </section>
</main>