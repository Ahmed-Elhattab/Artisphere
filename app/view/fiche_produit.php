<main id="page-fiche-produit">

    <?php if (!empty($errors)): ?>
        <div class="form-error-box">
        <h3>Erreurs lors de l'enregistrement</h3>
        <ul>
            <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['success'])): ?>
        <div class="form-success-box">
        <h3>Produit enregistré</h3>
        <p>Votre produit a bien été ajouté.</p>
        </div>
    <?php endif; ?>

    <form method="post" action="/artisphere/?controller=fiche_produit&action=submit" enctype="multipart/form-data">
        <h1 class="titre-produit">NOUVEAU PRODUIT</h1>

        <!--bloc image produit-->
        <div class="product-photo-block">
            <div class="product-avatar" id="productAvatar">
                <!-- preview image injectée par JS -->
                <span class="product-avatar__placeholder">📷</span>
            </div>

            <div class="product-photo-actions">
                <input type="file"
                    id="importImage"
                    name="image"
                    accept="image/png, image/jpeg, image/webp"
                    required>
                <p class="hint">Formats acceptés : JPG, PNG, WEBP (max 3 Mo)</p>
            </div>
        </div>

        <p class="para_produit">Nom<br></p>
        <input class="nom_produit" type="text" id="nom_produit" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <p class="para_catégorie">Catégorie<br></p>
        <select class="choixcategorie" id="choixcategorie" name="id_categorie" required>
        <option value="" disabled <?= empty($old['id_categorie']) ? 'selected' : '' ?>>Choisissez une option</option>
        <?php foreach (($categories ?? []) as $cat): ?>
            <option value="<?= (int)$cat['id_categorie'] ?>"
            <?= (!empty($old['id_categorie']) && (int)$old['id_categorie'] === (int)$cat['id_categorie']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nom'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
        </select>

        <p class="para_quantités">Quantités<br></p>
        <input type="number" class="Quantités" id="Quantités" name="quantite" min="0" value="<?= htmlspecialchars($old['quantite'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <p class="para_matériaux"><br>Matériaux</p>
        <input type="text" class="Matériaux" id="Matériaux" name="materiaux" value="<?= htmlspecialchars($old['materiaux'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <p class="para_prix"><br>Prix</p>
        <input type="number" class="Prix" id="Prix" name="prix" min="0" step="0.01" value="<?= htmlspecialchars($old['prix'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <p class="para_description"><br>Description</p>
        <input type="text" class="Description" id="Description" name="description" value="<?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <button class="bouton-enregistrer" type="submit">Enregistrer</button>
    </form>
</main>