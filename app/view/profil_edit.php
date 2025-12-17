<main class="profile-edit-page">
    <section class="container profile-edit-card">
        <h1>Éditer mon profil</h1>

        <?php if (!empty($errors)): ?>
        <div class="form-error-box">
            <h3>Erreurs lors de la mise à jour</h3>
            <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="post" action="/artisphere/?controller=profil_edit&action=update">
        <p>Nom</p>
        <input type="text" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <p>Prénom</p>
        <input type="text" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <p>Pseudo</p>
        <input type="text" name="pseudo" value="<?= htmlspecialchars($old['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <p>Email</p>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

        <?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'artisan'): ?>
            <p>Adresse</p>
            <input type="text" name="adresse" value="<?= htmlspecialchars($old['adresse'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        <?php endif; ?>

        <div class="actions">
            <button class="btn-primary" type="submit">Enregistrer</button>
            <a class="btn-outline" href="/artisphere/?controller=profil&action=index">Annuler</a>
        </div>
        </form>
    </section>
</main>