<main>

    <!-- En-tête de la FAQ -->
    <section class="section faq-header">
        <div class="container">
            <h1>Foire aux questions</h1>
            <p class="faq-intro">
                Tu te poses des questions sur Artisphere, le fonctionnement de la plateforme ou
                la gestion des commandes ? Trouve ici les réponses aux questions les plus fréquentes.
            </p>
        </div>
    </section>

    <!-- liste des questions-réponses -->
    <section class="section">
        <div class="container faq-list">

            <?php if (!empty($faqByCat)): ?>
                <?php foreach ($faqByCat as $categorie => $items): ?>

                    <h2 class="faq-category-title">
                        <?= htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8') ?>
                    </h2>

                    <?php foreach ($items as $i => $faq): ?>
                        <details class="faq-item" >
                            <summary><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></summary>
                            <div class="faq-body">
                                <p><?= nl2br(htmlspecialchars($faq['reponse'], ENT_QUOTES, 'UTF-8')) ?></p>
                            </div>
                        </details>
                    <?php endforeach; ?>

                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune question/réponse n'a été trouvée dans la base.</p>
            <?php endif; ?>

        </div>
    </section>

    <!-- Lien vers la page contact (comme la maquette : en bas à droite, juste au-dessus du footer) -->
    <div class="container faq-contact-right">
        <a href="?controller=contact&action=index" class="faq-contact-link">Contactez-nous</a>
    </div>

</main>