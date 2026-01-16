<main id="page-fiche-evenement">

  <?php if (!empty($errors)): ?>
    <div class="form-error-box">
      <h3>Erreurs lors de l’enregistrement</h3>
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($_GET['success'])): ?>
    <div class="form-success-box">
      <h3>Évènement enregistré</h3>
      <p>Votre évènement a bien été ajouté.</p>
    </div>
  <?php endif; ?>

  <form method="post"
        action="/artisphere/?controller=fiche_evenement&action=submit"
        enctype="multipart/form-data">

    <h1 class="titre-produit">NOUVEL EVENEMENT</h1>

    <div class="event-photo-block">
      <div class="event-avatar" id="eventAvatar">
        <span class="event-avatar__placeholder">📷</span>
      </div>

      <div class="event-photo-actions">
        <input type="file"
          id="eventImage"
          name="images[]"
          accept="image/png, image/jpeg, image/webp"
          multiple
          required>
        <p class="hint">Pour selectionner plusieurs photos, ctrl + clique sur les photos souhaitées (max 6) - JPG/PNG/WEBP (max 3 Mo par image)</p>

        <!-- Miniatures -->
        <div class="thumbs" id="eventThumbs"></div>
      </div>
    </div>

    <p class="para_produit">Nom</p>
    <input class="nom_produit" type="text" name="nom"
           value="<?= htmlspecialchars($old['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_lieu">Lieu</p>
    <input type="text" class="Lieu" name="lieu"
           value="<?= htmlspecialchars($old['lieu'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_places">Nombre de places</p>
    <input type="number" class="Places" name="nombre_place" min="0"
           value="<?= htmlspecialchars((string)($old['nombre_place'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_type">Type d’évènement</p>
    <select class="Type" name="id_type" required>
      <option value="">-- Choisir un type --</option>
      <?php foreach (($types ?? []) as $t): ?>
        <option value="<?= (int)$t['id_type'] ?>"
          <?= ((int)($old['id_type'] ?? 0) === (int)$t['id_type']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($t['nom'], ENT_QUOTES, 'UTF-8') ?>
        </option>
      <?php endforeach; ?>
    </select>

    <p class="para_prix">Prix</p>
    <input type="number" class="Prix" name="prix" min="0" step="0.01"
           value="<?= htmlspecialchars($old['prix'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_date_debut">Date de début</p>
    <input type="date" class="DateDebut" name="date_debut"
           value="<?= htmlspecialchars($old['date_debut'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_date_fin">Date de fin</p>
    <input type="date" class="DateFin" name="date_fin"
           value="<?= htmlspecialchars($old['date_fin'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_descriptionV2">Description</p>
    <textarea class="DescriptionV2" name="description" rows="5" required><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

    <button class="bouton-enregistrerV2" type="submit">Enregistrer</button>
  </form>
</main>