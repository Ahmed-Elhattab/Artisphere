<main class="faq-create-page">

  <section class="container">
    <h1>Ajouter une question à la FAQ</h1>

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
        <h3>Question ajoutée</h3>
        <p>La question a bien été ajoutée à la FAQ.</p>
      </div>
    <?php endif; ?>

    <form method="post" action="/artisphere/?controller=faq_create&action=create">

      <p>Catégorie</p>
      <input type="text" name="categorie"
             value="<?= htmlspecialchars($old['categorie'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             required>

      <p>Question</p>
      <input type="text" name="question"
             value="<?= htmlspecialchars($old['question'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             required>

      <p>Réponse</p>
      <textarea name="reponse" rows="6" required><?= htmlspecialchars($old['reponse'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

      <button class="btn-primary" type="submit">Ajouter à la FAQ</button>
    </form>
  </section>

</main>