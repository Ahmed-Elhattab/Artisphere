<main class="admin-users-page">
  <section class="container admin-users-card">
    <h1>Commandes passées chez vous</h1>

    <?php if (!empty($_GET['success'])): ?>
      <?php if ($_GET['success'] === 'pay_prod'): ?>
        <div class="form-success-box"><h3>Paiement validé</h3><p>La commande produit est maintenant payée.</p></div>
      <?php elseif ($_GET['success'] === 'cancel_prod'): ?>
        <div class="form-success-box"><h3>Commande annulée</h3><p>La commande produit a été supprimée.</p></div>
      <?php elseif ($_GET['success'] === 'pay_ev'): ?>
        <div class="form-success-box"><h3>Paiement validé</h3><p>La commande évènement est maintenant payée.</p></div>
      <?php elseif ($_GET['success'] === 'cancel_ev'): ?>
        <div class="form-success-box"><h3>Commande annulée</h3><p>La commande évènement a été supprimée.</p></div>
      <?php endif; ?>
    <?php endif; ?>

    <form class="search-bar" method="get" action="/artisphere/">
      <input type="hidden" name="controller" value="artisan_commandes">
      <input type="hidden" name="action" value="index">
      <input type="text" name="q" placeholder="Rechercher par produit/évènement ou client (nom, pseudo, email)"
             value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <button class="btn-primary" type="submit">Rechercher</button>
    </form>

    <h2 class="section-subtitle">Produits</h2>
    <div class="table-wrap">
      <table class="users-table">
        <thead>
          <tr>
            <th>ID resa</th>
            <th>Produit</th>
            <th>Qté</th>
            <th>Client</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($produits)): ?>
          <?php foreach ($produits as $r): ?>
            <tr>
              <td><?= (int)$r['id_resa_produit'] ?></td>
              <td><?= htmlspecialchars($r['produit_nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= (int)($r['quantite'] ?? 1) ?></td>
              <td><?= htmlspecialchars($r['client_pseudo'] ?? ($r['client_prenom'].' '.$r['client_nom']), ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($r['client_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>

              <td class="actions-col">
                <form method="post" action="/artisphere/?controller=artisan_commandes&action=payProduit" class="inline">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="id_resa" value="<?= (int)$r['id_resa_produit'] ?>">
                  <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <button class="btn-outline" type="submit">Valider le paiement</button>
                </form>

                <form method="post" action="/artisphere/?controller=artisan_commandes&action=cancelProduit" class="inline"
                      onsubmit="return confirm('Annuler cette commande produit ?');">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="id_resa" value="<?= (int)$r['id_resa_produit'] ?>">
                  <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <button class="btn-danger" type="submit">Annuler la commande</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="empty">Aucune commande produit en cours.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <h2 class="section-subtitle">Évènements</h2>
    <div class="table-wrap">
      <table class="users-table">
        <thead>
          <tr>
            <th>ID resa</th>
            <th>Évènement</th>
            <th>Type</th>
            <th>Qté</th>
            <th>Client</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($evenements)): ?>
          <?php foreach ($evenements as $ev): ?>
            <tr>
              <td><?= (int)$ev['id_resa_event'] ?></td>
              <td><?= htmlspecialchars($ev['event_nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($ev['type_nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= (int)($ev['quantite'] ?? 1) ?></td>
              <td><?= htmlspecialchars($ev['client_pseudo'] ?? ($ev['client_prenom'].' '.$ev['client_nom']), ENT_QUOTES, 'UTF-8') ?></td>

              <td class="actions-col">
                <form method="post" action="/artisphere/?controller=artisan_commandes&action=payEvent" class="inline">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="id_resa" value="<?= (int)$ev['id_resa_event'] ?>">
                  <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <button class="btn-outline" type="submit">Valider le paiement</button>
                </form>

                <form method="post" action="/artisphere/?controller=artisan_commandes&action=cancelEvent" class="inline"
                      onsubmit="return confirm('Annuler cette commande évènement ?');">
                  <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="id_resa" value="<?= (int)$ev['id_resa_event'] ?>">
                  <input type="hidden" name="q" value="<?= htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <button class="btn-danger" type="submit">Annuler la commande</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="empty">Aucune commande évènement en cours.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  </section>
</main>