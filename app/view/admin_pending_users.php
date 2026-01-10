<main class="admin-users-page">
  <section class="container admin-users-card">
    <h1>Comptes en attente</h1>

    <?php if (!empty($_GET['success']) && $_GET['success'] === 'validate'): ?>
      <div class="form-success-box"><h3>Compte validé</h3><p>L’utilisateur peut maintenant se connecter.</p></div>
    <?php elseif (!empty($_GET['success']) && $_GET['success'] === 'delete'): ?>
      <div class="form-success-box"><h3>Compte supprimé</h3><p>Le compte en attente a été supprimé.</p></div>
    <?php endif; ?>

    <form class="search-bar" method="get" action="/artisphere/">
      <input type="hidden" name="controller" value="admin_pending_users">
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
            <th>Statut</th>
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
              <td>
                <span class="role-badge role-<?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>">
                  <?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td>
                <span class="status-badge status-pending">en attente</span>
              </td>

              <td class="actions-col">
                <form method="post" action="/artisphere/?controller=admin_pending_users&action=validate" class="inline">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="id" value="<?= (int)$u['id_personne'] ?>">
                  <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="page" value="<?= (int)$page ?>">
                  <button class="btn-outline" type="submit">Valider</button>
                </form>

                <?php
                  $me = (int)($_SESSION['user']['id_personne'] ?? $_SESSION['user']['id'] ?? 0);
                ?>
                <?php if ((int)$u['id_personne'] !== $me): ?>
                  <form method="post" action="/artisphere/?controller=admin_pending_users&action=delete" class="inline"
                        onsubmit="return confirm('Supprimer ce compte en attente ?');">
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
          <tr><td colspan="8" class="empty">Aucun compte en attente.</td></tr>
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
         href="/artisphere/?controller=admin_pending_users&action=index&q=<?= $baseQ ?>&page=<?= $prev ?>">←</a>

      <span class="page-info">Page <?= (int)$page ?> / <?= (int)$pages ?></span>

      <a class="page-link <?= $page >= $pages ? 'disabled' : '' ?>"
         href="/artisphere/?controller=admin_pending_users&action=index&q=<?= $baseQ ?>&page=<?= $next ?>">→</a>
    </div>

  </section>
</main>