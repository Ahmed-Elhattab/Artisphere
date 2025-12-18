<main class="simple-page">
  <div class="simple-card">
    <h1>Mentions légales</h1>

    <?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
      <div class="add-buttons">
        <div class="action-buttons">
          <a class="action-link" href="/artisphere/?controller=mention_legale_create&action=create">
            <button class="btn-spe">AJOUTER UNE MENTION</button>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Intro (tu peux la garder fixe) -->
    <p class="apropos-intro">
      Le présent site et son contenu respectent le RGPD.
    </p>

    <?php if (!empty($mentions)): ?>
      <div class="apropos-sections">
        <?php foreach ($mentions as $m): ?>
          <section class="apropos-section">
            <h2><?= htmlspecialchars($m['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= nl2br(htmlspecialchars($m['texte'], ENT_QUOTES, 'UTF-8')) ?></p>
          </section>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <!-- Fallback si la table est vide -->
      <ul>
        <li>Éditeur : Artisphere</li>
        <li>Contact : voir la page Contact</li>
        <li>Hébergement : localhost (XAMPP)</li>
      </ul>
    <?php endif; ?>

  </div>
</main>