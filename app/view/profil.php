<!--<head>
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>-->
<main class="container">
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
                <a class="action-link" href="/artisphere/?controller=avis&action=index"><button class="btn">Donner son avis</button></a>
            </div>
            <div class="order-card">
                <div class="order-title">Tableau</div>
                <a class="action-link" href="/artisphere/?controller=avis&action=index"><button class="btn">Donner son avis</button></a>
            </div>
            <div class="order-card">
                <div class="order-title">Vase</div>
                <a class="action-link" href="/artisphere/?controller=avis&action=index"><button class="btn">Donner son avis</button></a>
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

    <div class="action-buttons">
      <a class="action-link" href="/artisphere/?controller=creer_fiche&action=index"><button class="btn-spe">CRÉER UNE NOUVELLE FICHE</button></a>
    </div>
    <div class="action-buttons">
      <a class="action-link" href="/artisphere/?controller=chercher_compte&action=index"><button class="btn-spe">CHERCHER UN COMPTE</button></a>
    </div>
</main>