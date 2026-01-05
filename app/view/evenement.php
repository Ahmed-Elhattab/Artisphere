<main>

  <!-- Titre -->
  <section class="section events-header">
    <div class="container">
      <h1>Tous les événements</h1>
      <p class="events-intro">
        Ateliers, expositions, salons et marchés d’artisans :
        retrouve ici l’ensemble des événements référencés sur Artisphere.
      </p>
    </div>
  </section>

  <!-- Filtres -->
  <section class="section">
    <div class="container">

      <div class="events-toolbar">
        <!-- Pills type -->
        <div class="events-filters">
          <?php
            $base = '/artisphere/?controller=evenement&action=index';
            $currentType = $filters['type'] ?? '';
          ?>

          <a class="filter-pill <?= $currentType === '' ? 'filter-pill-active' : '' ?>"
             href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>">
            Tous
          </a>

          <?php foreach ($types as $t): ?>
            <a class="filter-pill <?= ($currentType === $t) ? 'filter-pill-active' : '' ?>"
               href="<?= htmlspecialchars($base . '&type=' . urlencode($t)
                  . ($filters['q'] ? '&q=' . urlencode($filters['q']) : '')
                  . ($filters['min_price'] !== '' ? '&min_price=' . urlencode($filters['min_price']) : '')
                  . ($filters['max_price'] !== '' ? '&max_price=' . urlencode($filters['max_price']) : '')
                  . (!empty($filters['in_stock']) ? '&in_stock=1' : ''), ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Search + extra filters (à droite) -->
        <form class="events-search" method="get" action="/artisphere/">
          <input type="hidden" name="controller" value="evenement">
          <input type="hidden" name="action" value="index">
          <input type="hidden" name="type" value="<?= htmlspecialchars($filters['type'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

          <input class="search-input" type="search" name="q" placeholder="Rechercher un événement…"
                 value="<?= htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

          <input class="price-input" type="number" step="0.01" name="min_price" placeholder="Prix min"
                 value="<?= htmlspecialchars((string)($filters['min_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

          <input class="price-input" type="number" step="0.01" name="max_price" placeholder="Prix max"
                 value="<?= htmlspecialchars((string)($filters['max_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

          <label class="stock-check">
            <input type="checkbox" name="in_stock" value="1" <?= !empty($filters['in_stock']) ? 'checked' : '' ?>>
            Places dispo
          </label>

          <button class="apply-btn" type="submit">OK</button>

          <a class="reset-link" href="/artisphere/?controller=evenement&action=index">Reset</a>
        </form>
      </div>

      <!-- Grille -->
      <?php if (!empty($events)): ?>
        <div class="events-grid grid-3">

          <?php foreach ($events as $e): ?>
            <?php
              $img = !empty($e['image'])
                ? '/artisphere/images/evenements/' . $e['image']
                : '/artisphere/images/image-photo.jpg';

              $isFree = ((float)$e['prix'] <= 0);
              $places = (int)$e['nombre_place'];

              $tagClass = 'event-tag-salon';
              $t = strtolower($e['type'] ?? '');
              if (str_contains($t, 'atelier')) $tagClass = 'event-tag-atelier';
              elseif (str_contains($t, 'expo')) $tagClass = 'event-tag-expo';
            ?>

            <article class="event-card">
              <img class="event-img"
                   src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
                   alt="<?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?>"
                   onerror="this.onerror=null; this.src='/artisphere/images/image-photo.jpg';">

              <span class="event-tag <?= $tagClass ?>">
                <?= htmlspecialchars($e['type'], ENT_QUOTES, 'UTF-8') ?>
              </span>

              <h2 class="event-title"><?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?></h2>

              <p class="event-meta">
                📍 <?= htmlspecialchars($e['lieu'], ENT_QUOTES, 'UTF-8') ?> ·
                🗓 <?= htmlspecialchars($e['date_debut'], ENT_QUOTES, 'UTF-8') ?>
                <?php if (!empty($e['date_fin']) && $e['date_fin'] !== $e['date_debut']): ?>
                  – <?= htmlspecialchars($e['date_fin'], ENT_QUOTES, 'UTF-8') ?>
                <?php endif; ?>
              </p>

              <p class="event-desc">
                <?= nl2br(htmlspecialchars(mb_strimwidth($e['description'], 0, 140, '…'), ENT_QUOTES, 'UTF-8')) ?>
              </p>

              <p class="event-extra">
                <?= $isFree ? 'Entrée gratuite.' : ('Prix : ' . number_format((float)$e['prix'], 2, ',', ' ') . ' €') ?>
                ·
                <?= ($places > 0) ? ('Places : ' . $places) : 'Complet' ?>
              </p>

              <a href="/artisphere/?controller=evenement_show&&action=show&id=<?= (int)$e['id_event'] ?>"
                 class="event-btn">
                Voir le détail
              </a>
            </article>

          <?php endforeach; ?>

        </div>
      <?php else: ?>
        <p class="events-empty">Aucun évènement ne correspond à vos filtres.</p>
      <?php endif; ?>

    </div>
  </section>
</main>