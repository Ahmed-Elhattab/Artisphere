<h2>Bienvenue sur la page d’accueil</h2>

<?php if (!empty($dbMessage)): ?>
    <p><?= htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<p>Contenu de ta page d’accueil ici.</p>