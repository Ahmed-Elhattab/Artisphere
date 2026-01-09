<section class="section">
  <div class="container">

    <div class="artisans-toolbar">

      <!-- Pills spécialités -->
      <div class="artisans-filter-pills">
        <?php
          $base = '/artisphere/?controller=artisans&action=index';
          $currentSpec = (int)($filters['id_specialite'] ?? 0);
        ?>

        <a class="filter-pill <?= $currentSpec === 0 ? 'filter-pill-active' : '' ?>"
           href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>">
          Tous
        </a>

        <?php foreach ($specialites as $s): ?>
          <a class="filter-pill <?= $currentSpec === (int)$s['id_specialite'] ? 'filter-pill-active' : '' ?>"
             href="<?= htmlspecialchars($base
                 . '&spec=' . (int)$s['id_specialite']
                 . ($filters['q'] ? '&q=' . urlencode($filters['q']) : '')
                 . ($filters['min_note'] !== '' ? '&min_note=' . urlencode($filters['min_note']) : '')
                 . (!empty($filters['sort']) ? '&sort=' . urlencode($filters['sort']) : ''), ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($s['nom'], ENT_QUOTES, 'UTF-8') ?>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Search + notes (à droite) -->
      <form class="artisans-searchbar" method="get" action="/artisphere/">
        <input type="hidden" name="controller" value="artisans">
        <input type="hidden" name="action" value="index">
        <input type="hidden" name="spec" value="<?= (int)($filters['id_specialite'] ?? 0) ?>">

        <input type="search"
               class="artisans-search"
               name="q"
               placeholder="Rechercher un artisan, une spécialité, une ville…"
               value="<?= htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <select name="min_note" class="note-select">
          <option value="">Note min</option>
          <?php for ($i=5; $i>=0; $i--): ?>
            <option value="<?= $i ?>" <?= ((string)($filters['min_note'] ?? '') === (string)$i) ? 'selected' : '' ?>>
              ≥ <?= $i ?>/5
            </option>
          <?php endfor; ?>
        </select>

        <select name="sort" class="note-select">
          <option value="best" <?= (($filters['sort'] ?? 'best') === 'best') ? 'selected' : '' ?>>Meilleurs</option>
          <option value="new"  <?= (($filters['sort'] ?? '') === 'new') ? 'selected' : '' ?>>Plus récents</option>
        </select>

        <button class="search-btn" type="submit">OK</button>

        <a class="reset-link" href="/artisphere/?controller=artisans&action=index">Reset</a>
      </form>

    </div>

    <!-- GRILLE ARTISANS -->
    <?php if (!empty($artisans)): ?>
      <div class="grid-3 artisans-grid">

        <?php foreach ($artisans as $a): ?>
          <?php
            // avatar
            $avatarUrl = null;
            if (!empty($a['avatar']) && is_file('images/avatars/' . $a['avatar'])) {
              $avatarUrl = 'images/avatars/' . $a['avatar'];
            }

            $initials = mb_strtoupper(mb_substr($a['prenom'] ?? '', 0, 1) . mb_substr($a['nom'] ?? '', 0, 1));
            if (trim($initials) === '') $initials = '👤';

            $avg = (float)$a['avg_note'];
            $nb = (int)$a['nb_avis'];
            $specName = $a['specialite_nom'] ?? 'Spécialité non renseignée';
          ?>

          <article class="artisan-card">
            <div class="artisan-header">

              <div class="artisan-avatar">
                <?php if ($avatarUrl): ?>
                  <img src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                       alt="Avatar"
                       onerror="this.style.display='none';">
                <?php else: ?>
                  <?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?>
                <?php endif; ?>
              </div>

              <div>
                <h2 class="artisan-name"><?= htmlspecialchars($a['pseudo'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="artisan-location">
                  <?= htmlspecialchars($specName, ENT_QUOTES, 'UTF-8') ?>
                  <?php if (!empty($a['adresse'])): ?>
                    – <?= htmlspecialchars($a['adresse'], ENT_QUOTES, 'UTF-8') ?>
                  <?php endif; ?>
                </p>
              </div>
            </div>

            <p class="artisan-bio">
              <?= htmlspecialchars($a['prenom'] . ' ' . $a['nom'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <div class="artisan-tags">
              <span class="artisan-tag"><?= htmlspecialchars($specName, ENT_QUOTES, 'UTF-8') ?></span>
              <span class="artisan-tag rating-tag">
                ⭐ <?= number_format($avg, 1, ',', ' ') ?>/5 (<?= $nb ?>)
              </span>
            </div>

            <a href="/artisphere/?controller=artisan_show&action=show&id=<?= (int)$a['id_personne'] ?>"
               class="artisan-btn">
              Voir le profil
            </a>
          </article>

        <?php endforeach; ?>

      </div>
    <?php else: ?>
      <p class="empty">Aucun artisan ne correspond à vos filtres.</p>
    <?php endif; ?>

  </div>
</section>