<main class="auth-page">

  <!-- Bouton retour (en haut à gauche) -->
  <a href="?controller=type_Compte&action=index" class="back-btn">Retour</a>

  <!-- Bloc centré -->
  <section class="auth-card" aria-label="Création de compte artisan">

    <div class="brand">
      <img src="images/logo_site.png" alt="Logo Artisphere" class="brand-logo" />
      <p class="brand-subtitle">Création de compte artisan</p>
    </div>

    <form class="auth-form" action="#" method="post">

      <label class="field">
        <input type="text" name="username" placeholder="Identifiant" required />
      </label>

      <label class="field">
        <input type="email" name="email" placeholder="Adresse mail" required />
      </label>

      <label class="field">
        <input type="password" name="password" placeholder="Mot de passe" required />
      </label>

      <label class="field">
        <input type="password" name="password_confirm" placeholder="Vérifier le mot de passe" required />
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