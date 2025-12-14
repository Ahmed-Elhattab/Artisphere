<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'Artisphere', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="css/header.css">
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
                <a href="/artisphere/catalogue">CATALOGUE</a>
                <a href="/artisphere/artisan">ARTISAN</a>
                <a href="/artisphere/evenement">EVENEMENT</a>
                <a href="/artisphere/?controller=FAQ&action=index">PROFIL</a>
            </nav>

        </div>
    </header>
</body>
</html>


