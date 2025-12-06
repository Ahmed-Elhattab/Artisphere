<?php
require __DIR__ . '/layout/header.php'; ?>
<main>
    <h1>Bienvenue sur la page d’accueil</h1>
    <?php if (!empty($dbMessage)): ?>
        <p><?= htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>