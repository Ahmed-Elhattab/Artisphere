<main class="main-content">

    <!-- LEFT SIDEBAR -->
    <aside class="sidebar">

        <!-- search + filters (GET) -->
        <form class="search-form" method="get" action="/artisphere/">
        <input type="hidden" name="controller" value="catalogue">
        <input type="hidden" name="action" value="index">

        <input type="search" name="q" placeholder="Rechercher"
                value="<?= htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="filter-block">
            <label class="filter-label">Prix min</label>
            <input type="number" step="0.01" name="min_price"
                value="<?= htmlspecialchars((string)($filters['min_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="filter-block">
    _  
            <label class="filter-label">Prix max</label>
            <input type="number" step="0.01" name="max_price"
                value="<?= htmlspecialchars((string)($filters['max_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <label class="filter-check">
            <input type="checkbox" name="in_stock" value="1" <?= !empty($filters['in_stock']) ? 'checked' : '' ?>>
            En stock uniquement
        </label>

        <button class="apply-btn" type="submit">Appliquer</button>

        <a class="reset-link" href="/artisphere/?controller=catalogue&action=index">Réinitialiser</a>
        </form>

        <div class="categories-title">CATEGORIES :</div>

        <!-- Categories : pas besoin que ce soit des liens (tu as dit ok), mais le plus simple c'est liens GET -->
        <ul class="categories-list">
        <li>
            <a href="/artisphere/?controller=catalogue&action=index"
            class="<?= empty($filters['id_categorie']) ? 'active' : '' ?>">
            TOUS LES PRODUITS
            </a>
        </li>

        <?php foreach ($categories as $c): ?>
            <li>
            <a
                href="/artisphere/?controller=catalogue&action=index&cat=<?= (int)$c['id_categorie'] ?>
                <?= $filters['q'] ? '&q=' . urlencode($filters['q']) : '' ?>
                <?= ($filters['min_price'] !== '' ? '&min_price=' . urlencode($filters['min_price']) : '') ?>
                <?= ($filters['max_price'] !== '' ? '&max_price=' . urlencode($filters['max_price']) : '') ?>
                <?= (!empty($filters['in_stock']) ? '&in_stock=1' : '') ?>"
                class="<?= ((int)$filters['id_categorie'] === (int)$c['id_categorie']) ? 'active' : '' ?>"
            >
                <?= htmlspecialchars(strtoupper($c['nom']), ENT_QUOTES, 'UTF-8') ?>
            </a>
            </li>
        <?php endforeach; ?>
        </ul>

    </aside>

    <!-- RIGHT PRODUCTS AREA -->
    <section class="products">
        <h1 class="products-title">CATALOGUE</h1>

        <?php if (!empty($produits)): ?>
        <div class="product-grid">
            <?php foreach ($produits as $p): ?>
            <?php
                $img = !empty($p['image'])
                ? '../images/produits/' . $p['image']
                : '../images/produit.png';
            ?>

            <a class="product-card"
                href="/artisphere/?controller=produit_show&action=show&id=<?= (int)$p['id_produit'] ?>">
                <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?>"
                    onerror="this.onerror=null; this.src='../images/produit.png';">

                <div class="price-tag">
                <?= htmlspecialchars($p['nom'], ENT_QUOTES, 'UTF-8') ?>
                : <?= number_format((float)$p['prix'], 2, ',', ' ') ?>€
                </div>

                <?php if ((int)$p['quantite'] <= 0): ?>
                <div class="stock-badge out">Rupture</div>
                <?php else: ?>
                <div class="stock-badge in">Stock : <?= (int)$p['quantite'] ?></div>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="empty-state">Aucun produit ne correspond à vos filtres.</p>
        <?php endif; ?>

    </section>
</main>