<main class="mycrea-page">
    <section class="mycrea-header container">
        <h1>Mes créations</h1>
        <p>Retrouvez ici les produits et évènements que vous avez créés.</p>
    </section>

    <section class="container mycrea-section">
        <div class="mycrea-section-title">
        <h2>Mes produits</h2>
        <a class="btn-light" href="/artisphere/?controller=fiche_produit&action=index">Ajouter un produit</a>
        </div>

        <?php if (!empty($produits)): ?>
        <div class="cards-grid">
            <?php foreach ($produits as $p): ?>
            <?php
                $img = !empty($p['image']) ? 'images/produits/' . $p['image'] : null;
            ?>
            <article class="card">
                <div class="card-media <?= empty($img) ? 'noimg' : '' ?>">
                <?php if ($img): ?>
                    <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
                        alt="Image produit"
                        onerror="this.onerror=null; this.style.display='none'; this.parentNode.classList.add('noimg');">
                <?php endif; ?>
                <div class="card-media-fallback">📦</div>
                </div>

                <div class="card-body">
                <h3 class="card-title"><?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="card-meta">
                    Quantité : <?= (int)$p['quantite'] ?> · Prix : <?= htmlspecialchars((string)$p['prix'], ENT_QUOTES, 'UTF-8') ?> €
                </p>
                <p class="card-desc">
                    <?= htmlspecialchars(mb_strimwidth((string)$p['description'], 0, 120, '…'), ENT_QUOTES, 'UTF-8') ?>
                </p>

                <div class="card-actions">
                    <a class="btn-outline" href="/artisphere/?controller=produit_show&action=show&id=<?= (int)$p['id_produit'] ?>&mode=mine">Voir</a>
                </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="empty">Vous n’avez pas encore créé de produit.</p>
        <?php endif; ?>
    </section>

    <section class="container mycrea-section">
        <div class="mycrea-section-title">
        <h2>Mes évènements</h2>
        <a class="btn-light" href="/artisphere/?controller=fiche_evenement&action=index">Ajouter un évènement</a>
        </div>

        <?php if (!empty($evenements)): ?>
        <div class="cards-grid">
            <?php foreach ($evenements as $e): ?>
            <?php
                $img = !empty($e['image']) ? 'images/evenements/' . $e['image'] : null;
                $typeNom = (string)($e['type_nom'] ?? '');
            ?>
            <article class="card">
                <div class="card-media <?= empty($img) ? 'noimg' : '' ?>">
                <?php if ($img): ?>
                    <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
                        alt="Image évènement"
                        onerror="this.onerror=null; this.style.display='none'; this.parentNode.classList.add('noimg');">
                <?php endif; ?>
                <div class="card-media-fallback">🎟️</div>
                </div>

                <div class="card-body">
                <h3 class="card-title"><?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="card-meta">
                    <?= htmlspecialchars($e['lieu'], ENT_QUOTES, 'UTF-8') ?>
                    · <?= htmlspecialchars($typeNom !== '' ? $typeNom : 'Type', ENT_QUOTES, 'UTF-8') ?>
                </p>
                <p class="card-meta">
                    Du <?= htmlspecialchars($e['date_debut'], ENT_QUOTES, 'UTF-8') ?>
                    au <?= htmlspecialchars($e['date_fin'], ENT_QUOTES, 'UTF-8') ?>
                    · Places : <?= (int)$e['nombre_place'] ?>
                    · Prix : <?= htmlspecialchars((string)$e['prix'], ENT_QUOTES, 'UTF-8') ?> €
                </p>

                <div class="card-actions">
                    <a class="btn-outline" href="/artisphere/?controller=evenement_show&action=show&id=<?= (int)$e['id_event'] ?>&mode=mine">Voir</a>
                </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="empty">Vous n’avez pas encore créé d’évènement.</p>
        <?php endif; ?>
    </section>
</main>