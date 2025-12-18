<main class="apropos-create-page">
  <section class="container apropos-create-card">
    <h1>Ajouter un chapitre (À propos)</h1>
    <p class="sub">
      Le <strong>chapitre</strong> sera le titre affiché sur la page “À propos”.
      Le <strong>contenu</strong> est le texte associé à ce chapitre.
    </p>

    <?php if (!empty($errors)): ?>
      <div class="form-error-box">
        <h3>Erreurs lors de l’ajout</h3>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if (!empty($_GET['success'])): ?>
      <div class="form-success-box">
        <h3>Chapitre ajouté</h3>
        <p>Le contenu “À propos” a bien été enregistré.</p>
      </div>
    <?php endif; ?>

    <form method="post" action="/artisphere/?controller=apropos_create&action=create">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

      <p>Titre du chapitre</p>
      <input type="text" name="chapitre"
             value="<?= htmlspecialchars($old['chapitre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             required>

      <p>Contenu</p>
      <textarea name="contenu" rows="10" required><?= htmlspecialchars($old['contenu'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

      <div class="actions">
        <button class="btn-primary" type="submit">Enregistrer</button>
        <a class="btn-outline" href="/artisphere/?controller=apropos&action=index">Voir la page “À propos”</a>
      </div>
    </form>
  </section>
</main>