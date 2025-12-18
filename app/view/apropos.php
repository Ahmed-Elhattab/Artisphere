<main class="simple-page">
  <div class="simple-card">
    <h1>À propos de nous</h1>

    <!-- si admin = possibilité de creer des chapitres -->
    <?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
      <div class="add-buttons">
        <div class="action-buttons">
          <a class="action-link" href="/artisphere/?controller=apropos_create&action=create">
            <button class="btn-spe">CREER UN CHAPITRE</button>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <p class="apropos-intro">
      Artisphere est une plateforme qui met en relation des artisans et des passionnés d’objets faits main.
      Notre objectif : valoriser les savoir-faire, faciliter la découverte de créations uniques et promouvoir les évènements artisanaux.
    </p>

    <?php if (!empty($chapitres)): ?>
      <div class="apropos-sections">
        <?php foreach ($chapitres as $c): ?>
          <section class="apropos-section">
            <h2><?= htmlspecialchars($c['chapitre'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= nl2br(htmlspecialchars($c['contenu'], ENT_QUOTES, 'UTF-8')) ?></p>
          </section>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="apropos-empty">Aucun chapitre “À propos” n’a encore été ajouté.</p>
    <?php endif; ?>

  </div>
</main>