
<main class="auth-page">

  <!-- Bouton retour -->
  <a href="index.html" class="back-btn">Retour</a>

  <!-- Bloc centré -->
  <section class="auth-card" aria-label="Connexion">

    <div class="brand">
      <img src="images/logo_site.png" alt="Logo Artisphere" class="brand-logo" />
      <p class="brand-subtitle">Connexion</p>
    </div>

    <form class="auth-form" action="#" method="post">

      <label class="field">
        <input type="text" name="username" placeholder="Identifiant" required />
      </label>

      <label class="field">
        <input type="password" name="password" placeholder="Mot de passe" required />
      </label>

      <button type="submit" class="primary-btn">Se connecter</button>

      <!-- Liens en bas (2 colonnes) -->
      <div class="auth-links">
        <a href="choix-profil.html" class="small-link">Créer un compte</a>
        <a href="#" class="small-link">Mot de passe oublié ?</a>
      </div>

    </form>

  </section>

</main>
