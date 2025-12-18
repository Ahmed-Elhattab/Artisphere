<main class="mention-create-page">
  <section class="container mention-create-card">
    <h1>Ajouter une mention légale</h1>
    <p class="sub">
      Ajoute une nouvelle section (titre + texte) qui sera affichée sur la page Mentions légales.
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
        <h3>Ajout effectué</h3>
        <p>La mention légale a bien été enregistrée.</p>
      </div>
    <?php endif; ?>

    <form method="post" action="/artisphere/?controller=mention_legale_create&action=create">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

      <p>Titre</p>
      <input type="text" name="titre"
             value="<?= htmlspecialchars($old['titre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             required>

      <p>Texte</p>
      <textarea name="texte" rows="10" required><?= htmlspecialchars($old['texte'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

      <div class="actions">
        <button class="btn-primary" type="submit">Enregistrer</button>
        <a class="btn-outline" href="/artisphere/?controller=mentions&action=index">Voir les mentions</a>
      </div>
    </form>
  </section>
</main>