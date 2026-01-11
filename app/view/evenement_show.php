<?php
$img = !empty($evenement['image']) ? 'images/evenements/' . $evenement['image'] : null;

$backUrl = $_SESSION['previous_url'] ?? '/artisphere/?controller=index&action=index';

$isLogged = !empty($_SESSION['user']);
$idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
$isOwner = $isLogged && ($idUser > 0) && ((int)$idUser === (int)$evenement['id_createur']);

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
?>

<main class="details-page">
  <section class="details-card container">
    <div class="details-media <?= $img ? '' : 'noimg' ?>">
      <?php if ($img): ?>
        <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
             alt="Image évènement"
             onerror="this.parentNode.classList.add('noimg')">
      <?php endif; ?>
      <div class="details-fallback">🎟️</div>
    </div>

    <div class="details-body">
      <h1><?= htmlspecialchars($evenement['nom'], ENT_QUOTES, 'UTF-8') ?></h1>

      <p class="meta">
        Créé par : <strong><?= htmlspecialchars($evenement['createur_pseudo'] ?? 'inconnu', ENT_QUOTES, 'UTF-8') ?></strong>
      </p>

      <p class="meta">
        <?= htmlspecialchars($evenement['lieu'], ENT_QUOTES, 'UTF-8') ?> ·
        <?= htmlspecialchars($evenement['type_nom'] ?? 'Non renseigné', ENT_QUOTES, 'UTF-8') ?>
      </p>

      <p class="meta">
        Du <?= htmlspecialchars($evenement['date_debut'], ENT_QUOTES, 'UTF-8') ?>
        au <?= htmlspecialchars($evenement['date_fin'], ENT_QUOTES, 'UTF-8') ?>
      </p>

      <p class="meta">
        Places : <?= (int)$evenement['nombre_place'] ?> ·
        Prix : <?= htmlspecialchars((string)$evenement['prix'], ENT_QUOTES, 'UTF-8') ?> €
      </p>

      <h2>Description</h2>
      <p><?= nl2br(htmlspecialchars($evenement['description'], ENT_QUOTES, 'UTF-8')) ?></p>

      <div class="actions">
        <?php if ($isLogged): ?>

          <?php if (!empty($isReserved)): ?>
            <form method="post" action="/artisphere/?controller=evenement_show&action=cancelReservation" class="inline">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="id_evenement" value="<?= (int)$evenement['id_event'] ?>">
              <input type="hidden" name="back" value="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">
              <button class="btn-outline" type="submit">Annuler la réservation</button>
            </form>
          <?php else: ?>
            <form method="post" action="/artisphere/?controller=evenement_show&action=reserve" class="inline">
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="id_evenement" value="<?= (int)$evenement['id_event'] ?>">
              <input type="hidden" name="back" value="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>">
              <button class="btn-primary" type="submit" <?= ((int)$evenement['nombre_place'] <= 0 ? 'disabled' : '') ?>>
                Réserver
              </button>
            </form>
          <?php endif; ?>

        <?php else: ?>
          <a class="btn-primary" href="/artisphere/?controller=connexion&action=index">Réserver</a>
        <?php endif; ?>

        <?php if ($isOwner): ?>
          <a class="btn-outline" href="/artisphere/?controller=evenement_update&action=index&id=<?= (int)$evenement['id_event'] ?>">
            Éditer
          </a>
        <?php endif; ?>

        <?php if ($isOwner): ?>
          <a class="btn-outline" href="/artisphere/?controller=mes_creations&action=index">← Retour</a>
        <?php else: ?>
          <a class="btn-outline" href="/artisphere/?controller=index&action=index">← Retour</a>
        <?php endif; ?>
      </div>

      <?php if (isset($_GET['reserved'])): ?>
        <p class="meta">
          <?= ($_GET['reserved'] === '1') ? 'Réservation enregistrée' : 'Impossible de réserver (stock épuisé ou déjà réservé).' ?>
        </p>
      <?php endif; ?>

      <?php if (isset($_GET['cancelled'])): ?>
        <p class="meta">
          <?= ($_GET['cancelled'] === '1') ? 'Réservation annulée' : 'Impossible d’annuler la réservation.' ?>
        </p>
      <?php endif; ?>

    </div>
  </section>
</main>