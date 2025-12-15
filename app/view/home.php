<section class="home-hero">
  <div class="home-hero__left">
    <p class="home-hero__text">
      Découvrez le talent des artisans et créateurs passionnés près de chez vous.<br>
      Ici, chaque pièce raconte une histoire, chaque artisan partage son savoir-faire.
    </p>

    <div class="home-pill">LES PRODUITS TENDANCES</div>

    <div class="home-products">
      <?php for ($i=0; $i<6; $i++): ?>
        <article class="product-card">
          <img src="/artisphere/images/placeholder_product.jpg" alt="Produit">
          <div class="product-price">TARIF : 250€</div>
        </article>
      <?php endfor; ?>
    </div>

    <div class="home-pill">LES EVENEMENTS A LA UNE</div>

    <div class="home-events">
      <?php for ($i=0; $i<5; $i++): ?>
        <article class="event-card">
          <img src="images/image-photo.jpg" alt="Évènement">
          <div class="event-title">Lumière sur le bois</div>
          <div class="event-date">DU 15 AU 28<br>DÉCEMBRE 2025</div>
        </article>
      <?php endfor; ?>
    </div>
  </div>

  <aside class="home-hero__right">
    <div class="cta-card">
      <div class="cta-title">PRET A VENDRE DES<br>ARTICLES ?</div>
      <a class="cta-button" href="/artisphere/?controller=connexion&action=index">Commencer par ici</a>
    </div>
  </aside>
</section>