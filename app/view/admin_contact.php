<main class="admin-contact-page">
  <section class="container admin-contact-card">
    <h1>Demandes de contact</h1>

    <?php if (!empty($_GET['success'])): ?>
      <div class="form-success-box">
        <h3>Mise à jour effectuée</h3>
        <p>L’état de la demande a été mis à jour.</p>
      </div>
    <?php endif; ?>

    <form class="filters" method="get" action="/artisphere/">
      <input type="hidden" name="controller" value="admin_contact">
      <input type="hidden" name="action" value="index">

      <input type="text" name="q"
             placeholder="Rechercher (nom, email, message...)"
             value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">

      <select name="etat">
        <option value="" <?= ($etat === '' ? 'selected' : '') ?>>Tous les états</option>
        <option value="nouveau" <?= ($etat === 'nouveau' ? 'selected' : '') ?>>nouveau</option>
        <option value="en cours" <?= ($etat === 'en cours' ? 'selected' : '') ?>>en cours</option>
        <option value="fini" <?= ($etat === 'fini' ? 'selected' : '') ?>>fini</option>
      </select>

      <button class="btn-primary" type="submit">Filtrer</button>
    </form>

    <div class="list">
      <?php if (!empty($contacts)): ?>
        <?php foreach ($contacts as $c): ?>
          <article class="contact-item">
            <div class="contact-head">
              <div>
                <h3><?= htmlspecialchars($c['firstName'] . ' ' . $c['lastName'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="meta">
                  <?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>
                  · <?= htmlspecialchars($c['dateMsg'], ENT_QUOTES, 'UTF-8') ?>
                  · <span class="badge badge-<?= htmlspecialchars(str_replace(' ', '-', $c['etat']), ENT_QUOTES, 'UTF-8') ?>">
                      <?= htmlspecialchars($c['etat'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </p>
              </div>

              <form class="etat-form" method="post" action="/artisphere/?controller=admin_contact&action=updateEtat">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id_contact" value="<?= (int)$c['id_contact'] ?>">
                <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="etatFilter" value="<?= htmlspecialchars($etat ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="page" value="<?= (int)$page ?>">

                <select name="etat">
                  <option value="nouveau" <?= ($c['etat'] === 'nouveau' ? 'selected' : '') ?>>nouveau</option>
                  <option value="en cours" <?= ($c['etat'] === 'en cours' ? 'selected' : '') ?>>en cours</option>
                  <option value="fini" <?= ($c['etat'] === 'fini' ? 'selected' : '') ?>>fini</option>
                </select>

                <button class="btn-outline" type="submit">Mettre à jour</button>
              </form>
            </div>

            <div class="contact-message">
              <?= nl2br(htmlspecialchars($c['message'], ENT_QUOTES, 'UTF-8')) ?>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="empty">Aucune demande trouvée.</p>
      <?php endif; ?>
    </div>

    <div class="pagination">
      <?php
        $baseQ = urlencode($q ?? '');
        $baseEtat = urlencode($etat ?? '');
        $prev = max(1, $page - 1);
        $next = min($pages, $page + 1);
      ?>

      <a class="page-link <?= $page <= 1 ? 'disabled' : '' ?>"
         href="/artisphere/?controller=admin_contact&action=index&q=<?= $baseQ ?>&etat=<?= $baseEtat ?>&page=<?= $prev ?>">←</a>

      <span class="page-info">Page <?= (int)$page ?> / <?= (int)$pages ?></span>

      <a class="page-link <?= $page >= $pages ? 'disabled' : '' ?>"
         href="/artisphere/?controller=admin_contact&action=index&q=<?= $baseQ ?>&etat=<?= $baseEtat ?>&page=<?= $next ?>">→</a>
    </div>

  </section>
</main>