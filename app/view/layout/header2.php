<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'Artisphere', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="css/header.css">

    <!--rajoute un fichier css étant spécifié dans le controleur-->
    <?php if (!empty($pageCss)): ?>
        <link rel="stylesheet" href="/artisphere/css/<?= htmlspecialchars($pageCss, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
</head>
<header class="site-header">
    <div class="header-inner">

        <!-- LOGO À GAUCHE -->
        <div class="logo-zone">
            <img src="images/logo_site.png" alt="Logo Artisphere" class="logo-img">
        </div>

        <!-- MENU AU CENTRE -->
        <nav class="main-nav">
            <a href="/artisphere/catalogue" class="nav-link">Catalogue</a>
            <a href="/artisphere/artisan" class="nav-link">Artisan</a>
            <a href="/artisphere/evenement" class="nav-link">Évènement</a>
        </nav>

        <!-- ICÔNE PROFIL À DROITE -->
        <a href="/artisphere/?controller=profil&action=index" class="profile-icon" aria-label="Mon profil">
            <span>👤</span>
        </a>

    </div>
</header>
