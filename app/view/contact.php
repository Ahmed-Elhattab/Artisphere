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

    <?php if (!empty($_GET['success'])): ?>
      <div class="form-success-box">
        <h3>Message envoyé</h3>
        <p>Merci ! Nous vous répondrons dès que possible.</p>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="form-error-box">
        <h3>Erreur lors de l’envoi</h3>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form class="contact-form" method="post" action="/artisphere/?controller=contact&action=submit" novalidate>
      <div class="form-row">
        <label for="nom">Nom <span aria-hidden="true">*</span></label>
        <input id="nom" name="nom" type="text" required value="<?= htmlspecialchars($old['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>">      
      </div>

      <div class="form-row">
        <label for="prenom">Prénom <span aria-hidden="true">*</span></label>
        <input id="prenom" name="prenom" type="text" required value="<?= htmlspecialchars($old['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-row">
        <label for="email">Email <span aria-hidden="true">*</span></label>
        <input id="email" name="email" type="email" required value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="form-row">
        <label for="message">Message <span aria-hidden="true">*</span></label>
        <textarea id="message" name="message" rows="6" required><?= htmlspecialchars($old['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>

      <div class="form-actions">
        <button class="btn-primary" type="submit">Envoyer</button>
        <p class="form-hint">Champs obligatoires : *</p>
      </div>
    </form>

  </section>
</main>
