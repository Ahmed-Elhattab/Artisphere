<main class="auth-page">

  <!-- Bouton retour -->
  <a href="/artisphere/?controller=index&action=index" class="back-btn">Retour</a>

  <!-- Bloc centré -->
  <section class="auth-card" aria-label="Connexion">

    <div class="brand">
      <img src="images/logo_site.png" alt="Logo Artisphere" class="brand-logo" />
      <p class="brand-subtitle">Connexion</p>
    </div>

    <!--gère les erreurs détéctées par php lors de l'envoi du form-->
    <?php if (!empty($errors)): ?>
      <div class="form-error-box">
        <h3>Erreurs lors de la connexion</h3>
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

<!--Confirmation mail envoyer -->
<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
  <div class="message-success">
      ✅ Un lien de réinitialisation a été envoyé à votre adresse.
  </div>
<?php endif; ?>

    <form class="auth-form" action="/artisphere/?controller=connexion&action=submit" method="post">

      <label class="field">
        <input type="text" name="username" placeholder="Identifiant ou email" required />
      </label>

      <label class="field">
        <input type="password" name="password" placeholder="Mot de passe" required />
      </label>

      <button type="submit" class="primary-btn">Se connecter</button>

      <!-- Liens en bas (2 colonnes) -->
      <div class="auth-links">
        <a href="/artisphere/?controller=type_Compte&action=index" class="small-link">Créer un compte</a>
        <a href="/artisphere/?controller=mot_de_passe_oublie&action=index" class="small-link">Mot de passe oublié ? </a>
      </div>

    </form>

  </section>

</main>
