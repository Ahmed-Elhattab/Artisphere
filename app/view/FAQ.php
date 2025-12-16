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
        <!--si admin = possibilité de creer des FAQ-->
        <?php if (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <div class="add-buttons">
                <div class="action-buttons">
                    <a class="action-link" href="/artisphere/?controller=FAQ_create&action=create"><button class="btn-spe">CREER UNE QUESTION/REPONSE</button></a>
                </div>
            </div>
        <?php endif; ?>
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

    <section class="section faq-contact-cta">
        <div class="container faq-contact-box">
            <div>
                <h2>Vous ne trouvez pas votre réponse&nbsp;?</h2>
                <p>
                    Contacte-nous et nous reviendrons vers toi au plus vite pour t'aider.
                    Tu peux aussi préciser si tu es artisan ou client pour que l'on t'oriente mieux.
                </p>
            </div>
            <a href="/artisphere/?controller=contact&action=index" class="btn-primary">Contacter l'équipe Artisphere</a>
        </div>
    </section>

</main>