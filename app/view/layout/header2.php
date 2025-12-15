<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'Artisphere', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    // Base URL (works whether your folder is /chabchoub/public or /artisphere/public, etc.)
    // All asset paths become: css/..., images/..., js/... and all links become: ?controller=...&action=...
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
    ?>
    <base href="<?= htmlspecialchars($base . '/', ENT_QUOTES, 'UTF-8') ?>">

    <link rel="stylesheet" href="css/header2.css">
    <link rel="stylesheet" href="css/footer.css">

    <?php if (!empty($pageCss)): ?>
        <link rel="stylesheet" href="css/<?= htmlspecialchars($pageCss, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
</head>

<header class="site-header">
    <div class="header-inner">

        <!-- LOGO À GAUCHE -->
        <div class="logo-zone">
            <a class="logo_clickable" href="?controller=index&action=index">
                <img src="images/logo_site.png" alt="Logo Artisphere" class="logo-img">
            </a>
        </div>

        <!-- MENU AU CENTRE -->
        <nav class="main-nav">
            <a href="/artisphere/?controller=index&action=index" class="nav-link">Catalogue</a>
            <a href="/artisphere/?controller=artisans&action=index" class="nav-link">Artisan</a>
            <a href="/artisphere/?controller=evenement&action=index" class="nav-link">Évènement</a>
        </nav>

        <!--gere la redirection de l'icone profil quand un utilisateur est connecter ou non-->
        <?php
        $isLogged = !empty($_SESSION['user']);

        $profileUrl = $isLogged
            ? '/artisphere/?controller=profil&action=index'
            : '/artisphere/?controller=connexion&action=index';
        ?>

        <!-- ZONE PROFIL À DROITE -->
        <div class="profile-zone">

            <a href="<?= $profileUrl ?>" class="profile-icon" aria-label="Mon profil">
                <span>👤</span>
            </a>

            <?php if ($isLogged): ?>
                <div id="text-profil-zone">
                    <span class="profile-greeting">
                        <?= htmlspecialchars($_SESSION['user']['pseudo'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <a href="/artisphere/?controller=connexion&action=logout" class="logout-link">
                        Déconnexion
                    </a>
                </div>
            <?php endif; ?>

        </div>

    </div>
</header>
