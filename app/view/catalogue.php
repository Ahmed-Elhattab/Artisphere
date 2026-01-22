<!-- MAIN CONTENT -->
<main class="main-content">

    <!-- Sidebar toggle button (mobile & tablet) -->
    <button class="sidebar-toggle" id="sidebarToggle">
        ☰
    </button>

    <!-- LEFT SIDEBAR -->
    <aside class="sidebar" id="sidebar">

        <!-- Search form (JS live filter) -->
        <form class="search-form" onsubmit="return false;">
            <input
                type="search"
                id="productSearch"
                placeholder="Search products">
        </form>

        <div class="categories-title">CATEGORIES :</div>

        <ul class="categories-list">
            <?php
                // "All products" is active when no category is selected
                $allActive = empty($currentCat) ? 'active' : '';
            ?>
            <li>
                <a class="<?= $allActive ?>"
                   href="/artisphere/?controller=catalogue&action=index">
                    TOUS LES PRODUITS
                </a>
            </li>

            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $c): ?>
                    <?php
                        $isActive = (!empty($currentCat) && (int)$currentCat === (int)$c['id_categorie'])
                            ? 'active'
                            : '';
                    ?>
                    <li>
                        <a class="<?= $isActive ?>"
                           href="/artisphere/?controller=catalogue&action=index&cat=<?= (int)$c['id_categorie'] ?>">
                            <?= htmlspecialchars($c['nom'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

    </aside>

    <!-- RIGHT PRODUCTS AREA -->
    <section class="products">

        <h1 class="products-title">TOUS LES PRODUITS</h1>

        <div class="product-grid">
            <?php if (!empty($produits)): ?>
                <?php foreach ($produits as $p): ?>
                    <?php
                        // Image path
                        $imgPath = !empty($p['image'])
                            ? "images/produits/" . $p['image']
                            : "/artisphere/images/produit.png";

                        // Product name
                        $productName = $p['nom'] ?? 'Product';

                        // Product price
                        $price = number_format((float)($p['prix'] ?? 0), 2, ',', ' ');
                    ?>

                    <div class="product-card">

                        <div class="product-image-wrapper">
                            <img
                                src="<?= htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>"
                                onerror="this.onerror=null; this.src='/artisphere/images/produit.png';">
                        </div>

                        <div class="product-info">
                            <div class="product-name">
                                <?= htmlspecialchars($productName, ENT_QUOTES, 'UTF-8') ?>
                            </div>

                            <div class="product-price">
                                <?= $price ?> €
                            </div>

                            <a class="product-link"
                               href="/artisphere/?controller=produit_show&action=show&id=<?= (int)$p['id_produit'] ?>">
                                Voir
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products available.</p>
            <?php endif; ?>
        </div>

        <!-- PAGINATION -->
        <?php if (!empty($pagesTotal) && $pagesTotal > 1): ?>
            <?php
                // Keep category in pagination if exists
                $catParam = !empty($currentCat) ? '&cat=' . (int)$currentCat : '';
            ?>
            <div class="page-swiper">

                <?php if ($page > 1): ?>
                    <a class="next-previous"
                       href="/artisphere/?controller=catalogue&action=index<?= $catParam ?>&page=<?= $page - 1 ?>">
                        ← Previous
                    </a>
                <?php endif; ?>

                <span class="catalogue-page-count">
                    Page <?= (int)$page ?> / <?= (int)$pagesTotal ?>
                </span>

                <?php if ($page < $pagesTotal): ?>
                    <a class="next-previous"
                       href="/artisphere/?controller=catalogue&action=index<?= $catParam ?>&page=<?= $page + 1 ?>">
                        Next →
                    </a>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </section>

</main>
