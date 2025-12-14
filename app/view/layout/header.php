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
<body>
    <header class="top-header">
        <div class="header-container">

            <!-- Logo -->
            <div class="logo">
                <img src="images/logo_site.png" alt="Logo Artisphere">
            </div>

            <!-- Navigation -->
            <nav class="nav-links">
                <a href="/artisphere/?controller=index&action=index">CATALOGUE</a>
                <a href="/artisphere/?controller=artisans&action=index">ARTISAN</a>
                <a href="/artisphere/?controller=evenement&action=index">EVENEMENT</a>
                <a href="/artisphere/?controller=profil&action=index">PROFIL</a>
            </nav>

        </div>
    </header>
</body>
</html>


