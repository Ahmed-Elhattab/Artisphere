<main class="auth-page">

  <!-- Bouton retour (en haut à gauche) -->
  <a href="/artisphere/?controller=type_Compte&action=index" class="back-btn">Retour</a>

  <!-- Bloc centré -->
  <section class="auth-card" aria-label="Création de compte artisan">

    <div class="brand">
      <img src="images/logo_site.png" alt="Logo Artisphere" class="brand-logo" />
      <p class="brand-subtitle">Création de compte artisan</p>
    </div>

    <!--affiche dynamiquement les contraintes sur les mot de passe via JS -->
    <div id="password-rules-box" class="pwd-rules-box" hidden>
      <h3>Règles du mot de passe</h3>
      <ul class="pwd-rules-list">
        <li id="rule-length" class="rule bad">✖ Au moins 4 caractères</li>
        <li id="rule-match" class="rule bad">✖ Les deux mots de passe sont identiques</li>
      </ul>
    </div>

    <!--gère les erreurs détéctées par php lors de l'envoi du form-->
    <?php if (!empty($errors)): ?>
      <div class="form-error-box">
        <h3>Erreurs lors de la création de compte</h3>
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!--le formulaire de création de compte artisan-->
    <form class="auth-form" action="/artisphere/?controller=inscription_Artisans&action=submit" method="post">

      <label class="field">
        <input type="text" name="username" placeholder="Identifiant" required />
      </label>

      <label class="field">
        <input type="text" name="name" placeholder="Prenom" required />
      </label>

      <label class="field">
        <input type="text" name="last_name" placeholder="Nom" required />
      </label>

      <label class="field">
        <input type="email" name="email" placeholder="Adresse mail" required />
      </label>

      <label class="field">
        <input id="password" type="password" name="password" placeholder="Mot de passe" required />
      </label>

      <label class="field">
        <input id="password_confirm" type="password" name="password_confirm" placeholder="Vérifier le mot de passe" required />
      </label>

      <!-- Champ en plus pour artisan -->
      <label class="field">
        <input type="text" name="address" placeholder="Adresse physique" required />
      </label>

      <button type="submit" class="primary-btn">Créer le compte</button>

      <a href="#" class="help-link">Besoin d’aide ?</a>

    </form>

  </section>

</main>