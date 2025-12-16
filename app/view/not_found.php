<main class="details-page">
  <section class="details-card container">
    <div class="details-body">
      <h1>Introuvable</h1>
      <p><?= htmlspecialchars($message ?? "Contenu introuvable.", ENT_QUOTES, 'UTF-8') ?></p>
      <a class="btn-outline" href="/artisphere/?controller=index&action=index">Retour accueil</a>
    </div>
  </section>
</main>