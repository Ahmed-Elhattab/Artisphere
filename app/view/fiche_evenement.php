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

    <!-- Bloc image (cercle + input fichier) -->
    <div class="event-photo-block">
      <div class="event-avatar" id="eventAvatar">
        <span class="event-avatar__placeholder">📷</span>
      </div>

      <div class="event-photo-actions">
        <input type="file"
               id="eventImage"
               name="image"
               accept="image/png, image/jpeg, image/webp"
               required>
        <p class="hint">Formats : JPG/PNG/WEBP (max 3 Mo)</p>
      </div>
    </div>

    <p class="para_produit">Nom</p>
    <input class="nom_produit" type="text" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_lieu">Lieu</p>
    <input type="text" class="Lieu" name="lieu" value="<?= htmlspecialchars($old['lieu'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_places">Nombre de places</p>
    <input type="number" class="Places" name="nombre_place" min="0" value="<?= htmlspecialchars($old['nombre_place'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_type">Type d’évènement</p>
    <input type="text" class="Type" name="type" value="<?= htmlspecialchars($old['type'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_prix">Prix</p>
    <input type="number" class="Prix" name="prix" min="0" step="0.01" value="<?= htmlspecialchars($old['prix'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_date_debut">Date de début</p>
    <input type="date" class="DateDebut" name="date_debut" value="<?= htmlspecialchars($old['date_debut'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_date_fin">Date de fin</p>
    <input type="date" class="DateFin" name="date_fin" value="<?= htmlspecialchars($old['date_fin'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

    <p class="para_descriptionV2">Description</p>
    <textarea class="DescriptionV2" name="description" rows="5" required><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

    <button class="bouton-enregistrerV2" type="submit">Enregistrer</button>
  </form>
</main>