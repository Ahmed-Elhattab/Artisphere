<main class="admin-users-page">
  <section class="container admin-users-card">
    <h1>Gestion des référentiels</h1>

    <?php if (!empty($_GET['added'])): ?>
      <div class="form-success-box">
        <h3><?= $_GET['added'] === '1' ? 'Ajout effectué' : 'Ajout impossible' ?></h3>
        <p><?= $_GET['added'] === '1' ? 'La valeur a été ajoutée.' : 'Valeur déjà existante ou invalide.' ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($_GET['deleted'])): ?>
      <div class="form-success-box">
        <h3><?= $_GET['deleted'] === '1' ? 'Suppression effectuée' : 'Suppression impossible' ?></h3>
        <p><?= $_GET['deleted'] === '1' ? 'La valeur a été supprimée.' : 'Elle est peut-être utilisée ailleurs (contrainte FK).' ?></p>
      </div>
    <?php endif; ?>

    <form class="search-bar" method="get" action="/artisphere/">
      <input type="hidden" name="controller" value="admin_referentiels">
      <input type="hidden" name="action" value="index">
      <input type="text" name="q" placeholder="Rechercher une valeur"
             value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <button class="btn-primary" type="submit">Rechercher</button>
    </form>

    <!-- ====== 3 colonnes / 3 tables ====== -->
    <div class="refs-grid">

      <!-- Catégories -->
      <div class="ref-card">
        <h2>Catégories produit</h2>

        <form method="post" action="/artisphere/?controller=admin_referentiels&action=add" class="add-row">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="kind" value="categorie">
          <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <input type="text" name="nom" placeholder="Nouvelle catégorie" maxlength="80" required>
          <button class="btn-outline" type="submit">Ajouter</button>
        </form>

        <div class="table-wrap">
          <table class="users-table">
            <thead><tr><th>ID</th><th>Nom</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (!empty($categories)): ?>
              <?php foreach ($categories as $c): ?>
                <tr>
                  <td><?= (int)$c['id_categorie'] ?></td>
                  <td><?= htmlspecialchars($c['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="actions-col">
                    <form method="post" action="/artisphere/?controller=admin_referentiels&action=delete" class="inline"
                          onsubmit="return confirm('Supprimer cette catégorie ?');">
                      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="kind" value="categorie">
                      <input type="hidden" name="id" value="<?= (int)$c['id_categorie'] ?>">
                      <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                      <button class="btn-danger" type="submit">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="empty">Aucune catégorie.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Spécialités -->
      <div class="ref-card">
        <h2>Spécialités artisan</h2>

        <form method="post" action="/artisphere/?controller=admin_referentiels&action=add" class="add-row">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="kind" value="specialite">
          <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <input type="text" name="nom" placeholder="Nouvelle spécialité" maxlength="80" required>
          <button class="btn-outline" type="submit">Ajouter</button>
        </form>

        <div class="table-wrap">
          <table class="users-table">
            <thead><tr><th>ID</th><th>Nom</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (!empty($specialites)): ?>
              <?php foreach ($specialites as $s): ?>
                <tr>
                  <td><?= (int)$s['id_specialite'] ?></td>
                  <td><?= htmlspecialchars($s['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="actions-col">
                    <form method="post" action="/artisphere/?controller=admin_referentiels&action=delete" class="inline"
                          onsubmit="return confirm('Supprimer cette spécialité ?');">
                      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="kind" value="specialite">
                      <input type="hidden" name="id" value="<?= (int)$s['id_specialite'] ?>">
                      <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                      <button class="btn-danger" type="submit">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="empty">Aucune spécialité.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Types évènements -->
      <div class="ref-card">
        <h2>Types d’évènement</h2>

        <form method="post" action="/artisphere/?controller=admin_referentiels&action=add" class="add-row">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="kind" value="type">
          <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <input type="text" name="nom" placeholder="Nouveau type" maxlength="80" required>
          <button class="btn-outline" type="submit">Ajouter</button>
        </form>

        <div class="table-wrap">
          <table class="users-table">
            <thead><tr><th>ID</th><th>Nom</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (!empty($types)): ?>
              <?php foreach ($types as $t): ?>
                <tr>
                  <td><?= (int)$t['id_type'] ?></td>
                  <td><?= htmlspecialchars($t['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="actions-col">
                    <form method="post" action="/artisphere/?controller=admin_referentiels&action=delete" class="inline"
                          onsubmit="return confirm('Supprimer ce type ?');">
                      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="kind" value="type">
                      <input type="hidden" name="id" value="<?= (int)$t['id_type'] ?>">
                      <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                      <button class="btn-danger" type="submit">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="empty">Aucun type.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </section>
</main>