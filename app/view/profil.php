<!--<head>
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>-->
<main class="container">

    <!-- Demo helpers (you can remove before submission if you want) -->
    <div class="role-bar">
        <span class="role-badge">Rôle : <?= htmlspecialchars($role ?? 'inconnu', ENT_QUOTES, 'UTF-8') ?></span>
        <div class="role-links">
            <a href="?controller=profil&action=index&role=client">Client</a>
            <a href="?controller=profil&action=index&role=artisan">Artisan</a>
            <a href="?controller=profil&action=index&role=admin">Admin</a>
        </div>
    </div>
    <section class="profile-section">
        <div class="photo-block">
            <div class="avatar">👤</div>
            <button class="btn">CHANGER MA PHOTO</button>
        </div>
        <input type="text" placeholder="NOM" class="input">
        <input type="text" placeholder="PRENOM" class="input">
        <button class="btn">EDITER MES INFORMATIONS</button>
        <!-- Première section : Mes commandes en cours -->
        <div class="orders">
            <h2>MES COMMANDES EN COURS</h2>
            <div class="order-card">
                <div class="order-title">Collier en or</div>
            </div>
            <!-- Deuxième section : Commandes -->
            <h2>COMMANDES</h2>
            <div class="order-card">
                <div class="order-title">Collier en argent</div>
                <a class="action-link" href="?controller=avis&action=index"><button class="btn">Donner son avis</button></a>
            </div>
            <div class="order-card">
                <div class="order-title">Tableau</div>
                <a class="action-link" href="?controller=avis&action=index"><button class="btn">Donner son avis</button></a>
            </div>
            <div class="order-card">
                <div class="order-title">Vase</div>
                <a class="action-link" href="?controller=avis&action=index"><button class="btn">Donner son avis</button></a>
            </div>
        </div>
    </section>
    <h1 class="page-title">MES COMMANDES PASSÉES</h1>
  
    <div class="order-item">
      <div class="order-header">
        <span class="order-name">Pot à lait en céramique</span>
        <div class="status-dot"></div>
      </div>
      <div class="order-content"></div>
    </div>

    <!-- PRODUITS EN VENTE (artisan/admin) -->
    <?php if (!empty($role) && in_array($role, ['artisan', 'admin'], true)): ?>
      <section class="sell-section" aria-label="Produits en vente">
        <h2 class="sell-title">PRODUITS EN VENTE</h2>

        <div class="sell-grid">
          <form class="sell-card" method="post" action="#">
            <img class="sell-img" src="images/produit.png" alt="Produit 1">
            <label class="sell-label">Nom
              <input class="sell-input" name="p1_name" value="Bougie artisanale" required>
            </label>
            <label class="sell-label">Prix (€)
              <input class="sell-input" type="number" min="0" step="0.01" name="p1_price" value="25" required>
            </label>
            <button class="btn sell-save" type="submit">Enregistrer</button>
          </form>

          <form class="sell-card" method="post" action="#">
            <img class="sell-img" src="images/produit.png" alt="Produit 2">
            <label class="sell-label">Nom
              <input class="sell-input" name="p2_name" value="Tasse en céramique" required>
            </label>
            <label class="sell-label">Prix (€)
              <input class="sell-input" type="number" min="0" step="0.01" name="p2_price" value="18" required>
            </label>
            <button class="btn sell-save" type="submit">Enregistrer</button>
          </form>
        </div>
      </section>

      <section class="reviews-section" aria-label="Notes et avis">
        <h2 class="sell-title">NOTES ET AVIS</h2>
        <div class="review-row">
          <div class="stars" aria-label="4 sur 5">
            <span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star">★</span>
          </div>
          <p class="review-text">"Très belle finition, livraison rapide."</p>
        </div>
        <div class="review-row">
          <div class="stars" aria-label="5 sur 5">
            <span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span>
          </div>
          <p class="review-text">"Produit conforme, artisan sérieux."</p>
        </div>
      </section>

      <div class="action-buttons">
        <a class="action-link" href="?controller=creer_fiche&action=index">
          <button class="btn-spe" type="button">CRÉER UNE NOUVELLE FICHE</button>
        </a>
      </div>
    <?php endif; ?>
</main>