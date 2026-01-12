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
    <link rel="stylesheet" href="css/styles_Thushjan.css">

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
            <a href="/artisphere/?controller=catalogue&action=index" class="nav-link">Catalogue</a>
            <a href="/artisphere/?controller=artisans&action=index" class="nav-link">Artisan</a>
            <a href="/artisphere/?controller=evenement&action=index" class="nav-link">Évènement</a>
            <a href="/artisphere/?controller=evenement&action=index" class="nav-link">Forum</a>
        </nav>

        <!--gere la redirection de l'icone profil quand un utilisateur est connecter ou non-->
        <?php
        $isLogged = !empty($_SESSION['user']);
        $avatarFile = $_SESSION['user']['avatar'] ?? null;

        $profileUrl = $isLogged
            ? '/artisphere/?controller=profil&action=index'
            : '/artisphere/?controller=connexion&action=index';
        
        // Construction de l’URL avatar (si existant)
        $avatarUrl = null;
        if ($isLogged && $avatarFile) {
            $diskPath = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/artisphere/images/avatars/' . $avatarFile;
            if (is_file($diskPath)) {
                $avatarUrl = '/artisphere/images/avatars/' . $avatarFile;
            }
        }
        ?>
        

        <!-- ZONE PROFIL À DROITE -->
        <div class="profile-zone">

            <a href="<?= $profileUrl ?>" class="profile-icon" aria-label="Mon profil">
                <?php if ($avatarUrl): ?>
                    <img src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                        alt="Avatar utilisateur"
                        onerror="this.onerror=null; this.textContent='👤';">
                <?php else: ?>
                    👤
                <?php endif; ?>
            </a>
 <button id="bouton-dalto" class="bouton-style">Mode Accessibilité </button>
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
<script src="js/accessibilite.js"></script>
</header>
