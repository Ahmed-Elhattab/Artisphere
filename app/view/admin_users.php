<main class="admin-users-page">
  <section class="container admin-users-card">
    <h1>Gestion des comptes</h1>

    <?php if (!empty($_GET['success']) && $_GET['success'] === 'promote'): ?>
      <div class="form-success-box"><h3>Compte promu</h3><p>L’utilisateur est maintenant administrateur.</p></div>
    <?php elseif (!empty($_GET['success']) && $_GET['success'] === 'delete'): ?>
      <div class="form-success-box"><h3>Compte supprimé</h3><p>L’utilisateur a été supprimé.</p></div>
    <?php endif; ?>

    <form class="search-bar" method="get" action="/artisphere/">
      <input type="hidden" name="controller" value="admin_users">
      <input type="hidden" name="action" value="index">
      <input type="text" name="q" placeholder="Rechercher par nom, prénom, pseudo ou email"
             value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <button class="btn-primary" type="submit">Rechercher</button>
    </form>

    <div class="table-wrap">
      <table class="users-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Pseudo</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($users)): ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?= (int)$u['id_personne'] ?></td>
              <td><?= htmlspecialchars($u['nom'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($u['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($u['pseudo'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><span class="role-badge role-<?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td class="actions-col">

                <?php if ($u['role'] !== 'admin'): ?>
                  <!--<form method="post" action="/artisphere/?controller=admin_users&action=promote" class="inline">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= (int)$u['id_personne'] ?>">
                    <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="page" value="<?= (int)$page ?>">
                    <button class="btn-outline" type="submit">Promouvoir</button>
                  </form>-->
                <?php endif; ?>

                <?php if ((int)$_SESSION['user']['id'] !== (int)$u['id_personne']): ?>
                  <form method="post" action="/artisphere/?controller=admin_users&action=delete" class="inline"
                        onsubmit="return confirm('Supprimer ce compte ?');">
                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= (int)$u['id_personne'] ?>">
                    <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="page" value="<?= (int)$page ?>">
                    <button class="btn-danger" type="submit">Supprimer</button>
                  </form>
                <?php else: ?>
                  <span class="muted">(vous)</span>
                <?php endif; ?>

              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="empty">Aucun compte trouvé.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="pagination">
      <?php
        $baseQ = urlencode($q ?? '');
        $prev = max(1, $page - 1);
        $next = min($pages, $page + 1);
      ?>
      <a class="page-link <?= $page <= 1 ? 'disabled' : '' ?>"
         href="/artisphere/?controller=admin_users&action=index&q=<?= $baseQ ?>&page=<?= $prev ?>">←</a>

      <span class="page-info">Page <?= (int)$page ?> / <?= (int)$pages ?></span>

      <a class="page-link <?= $page >= $pages ? 'disabled' : '' ?>"
         href="/artisphere/?controller=admin_users&action=index&q=<?= $baseQ ?>&page=<?= $next ?>">→</a>
    </div>
  </section>
</main>