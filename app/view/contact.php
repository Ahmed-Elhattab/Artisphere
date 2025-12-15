<main class="contact-page">
  <section class="contact-shell">

    <div class="contact-hero">
      <div class="contact-hero__illustrations" aria-hidden="true">
        <!-- Placeholders: replace in public/images or public/assets if you prefer -->
        <img class="contact-illu contact-illu--woman" src="images/image-photo.jpg" alt="">
        <img class="contact-illu contact-illu--plane" src="images/image-bouton-evenement.png" alt="">
      </div>

      <div class="contact-hero__text">
        <h1>Nous contacter</h1>
        <p>
          Une question sur un produit, un évènement ou votre compte ?
          Laissez-nous un message et nous reviendrons vers vous rapidement.
        </p>
      </div>
    </div>

    <form class="contact-form" method="post" action="#" novalidate>
      <div class="form-row">
        <label for="nom">Nom <span aria-hidden="true">*</span></label>
        <input id="nom" name="nom" type="text" autocomplete="family-name" required>
      </div>

      <div class="form-row">
        <label for="prenom">Prénom <span aria-hidden="true">*</span></label>
        <input id="prenom" name="prenom" type="text" autocomplete="given-name" required>
      </div>

      <div class="form-row">
        <label for="email">Email <span aria-hidden="true">*</span></label>
        <input id="email" name="email" type="email" autocomplete="email" required>
      </div>

      <div class="form-row">
        <label for="sujet">Sujet <span aria-hidden="true">*</span></label>
        <input id="sujet" name="sujet" type="text" required>
      </div>

      <div class="form-row">
        <label for="message">Message <span aria-hidden="true">*</span></label>
        <textarea id="message" name="message" rows="6" required></textarea>
      </div>

      <div class="form-actions">
        <button class="btn-primary" type="submit">Envoyer</button>
        <p class="form-hint">Champs obligatoires : *</p>
      </div>
    </form>

  </section>
</main>
