<!--<head>
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>-->
<main class="container">
    

    <!-- Demo helpers (you can remove before submission if you want) -->
    <!--<div class="role-bar">
        <span class="role-badge">Rôle : <//?=htmlspecialchars($role ?? 'inconnu', ENT_QUOTES, 'UTF-8') ?></span>
        <div class="role-links">
            <a href="?controller=profil&action=index&role=client">Client</a>
            <a href="?controller=profil&action=index&role=artisan">Artisan</a>
            <a href="?controller=profil&action=index&role=admin">Admin</a>
        </div>
    </div>-->

    <!--gere les erreurs lors de la mise a jour des champs personnels-->
    <?php if (!empty($errors)): ?>
    <div class="form-error-box">
        <h3>Erreur lors de la mise à jour de la photo</h3>
        <ul>
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <section class="profile-section">
        <div class="photo-block">
            <!--gere la photo de profil-->
            <?php
            
            $avatarFile = $_SESSION['user']['avatar'] ?? null; // chemin de l'avatar stocké dans la session
            $avatarDir  = 'images/avatars/'; //chemin dans projet visual code
            $avatarUrl  = null;

            //verif si le fichier n'a pas été supprimmé
            if ($avatarFile) {
                $fullPath = $avatarDir . $avatarFile;
                if (is_file($fullPath)) {
                    $avatarUrl = 'images/avatars/' . $avatarFile;
                }
            }
            ?>

            <div class="avatar">
                <?php if ($avatarUrl): ?>
                    <img src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Photo de profil" onerror="this.onerror=null; this.replaceWith(document.createTextNode('👤'));">
                <?php else: ?>
                    👤
                <?php endif; ?>
            </div>
            <form action="/artisphere/?controller=profil&action=updateAvatar" method="post" enctype="multipart/form-data" class="photo-form">
                <input type="file" name="avatar" accept="image/png, image/jpeg, image/webp" required>
                <button class="btn" type="submit">CHANGER MA PHOTO</button>
            </form>
        </div>
        <span class="personal-info"><?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') ?></span>
        <span class="personal-info"><?= htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8') ?></span>
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

    <?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'artisan' || $_SESSION['user']['role'] === 'admin'): ?>
    <div class="action-buttons">
      <a class="action-link" href="/artisphere/?controller=creer_fiche&action=index"><button class="btn-spe">CRÉER UNE NOUVELLE FICHE</button></a>
    </div>
    <div class="action-buttons">
      <a class="action-link" href="/artisphere/?controller=mes_creations&action=index"><button class="btn-spe">MES PRODUITS ET EVENEMENTS</button></a>
    </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
    <div class="action-buttons">
      <a class="action-link" href="/artisphere/?controller=chercher_compte&action=index"><button class="btn-spe">CHERCHER UN COMPTE</button></a>
    </div>
    <?php endif; ?>
</main>