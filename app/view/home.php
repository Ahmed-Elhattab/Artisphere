<h2>Bienvenue sur la page d’accueil</h2>

<?php if (!empty($dbMessage)): ?>
    <p><?= htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if (!empty($personnes)): ?>
    <h3>Liste des personnes</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($personnes as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id_personne'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($p['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucune personne à afficher.</p>
<?php endif; ?>

<p>Contenu de ta page d’accueil ici.</p>