<!-- MAIN CONTENT -->
<main class="events-main">
    <!-- Title -->
    <h1 class="events-title">A L'AFFICHE</h1>

    <!-- events grid -->
    <section class="events-grid">

        <?php if (!empty($evenements)): ?>
            <?php foreach ($evenements as $e): ?>
                <article class="event-card">

                    <!-- the image display -->
                    <?php
                        $img = !empty($e['image'])
                            ? "images/evenements/" . $e['image']
                            : "images/image-photo.jpg";
                    ?>
                    <img
                        src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?>"
                        onerror="this.onerror=null; this.src='images/image-photo.jpg';"
                    >

                    <div class="event-body">
                        <h2 class="event-name"><?= htmlspecialchars($e['nom']) ?></h2>

                        <?php if (!empty($e['lieu'])): ?>
                            <p class="event-location"><?= htmlspecialchars($e['lieu']) ?></p>
                        <?php endif; ?>

                        <p class="event-date">
                            Du <?= htmlspecialchars($e['date_debut']) ?>
                            au <?= htmlspecialchars($e['date_fin']) ?>
                        </p>

                        <?php
            
            
                        ?>
                        <a class="event-link"
                            href="/artisphere/?controller=evenement_show&action=show&id=<?= (int)$e['id_event'] ?>">
                                Voir
                        </a>

                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-events-message">Aucun évènement pour le moment.</p>
        <?php endif; ?>

    </section>

    <!-- Button under cards -->
    <div class="events-button-wrapper">
        <!-- this button can reload the full events list -->
        <a href="/artisphere/?controller=tous_les_evenements&action=index" class="btn-events">VOIR TOUS LES EVENEMENTS</a>
    </div>
</main>
