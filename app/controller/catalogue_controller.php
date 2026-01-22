<?php

// Load models
require_once __DIR__ . '/../model/produit_model.php';
require_once __DIR__ . '/../model/categorie_model.php';

class catalogue_controller extends BaseController
{
    public function index(): void
    {
        // Number of products per page
        $limit = 8;

        // Current page (from URL ?page=)
        // If not present, default to page 1
        $page = max(1, (int)($_GET['page'] ?? 1));

        // Calculate SQL offset
        $offset = ($page - 1) * $limit;

        // Current category (from URL ?cat=)
        // If not present or empty, set to null
        $currentCat = isset($_GET['cat']) && $_GET['cat'] !== ''
            ? (int)$_GET['cat']
            : null;

        // Get all categories for sidebar
        $categories = CategorieModel::all();

        // Get products depending on category filter
        if ($currentCat !== null) {
            // Products filtered by category
            $produits = ProduitModel::listByCategory($currentCat, $limit, $offset);
            $totalProduits = ProduitModel::countByCategory($currentCat);
        } else {
            // All products (no filter)
            $produits = ProduitModel::listHome($limit, $offset);
            $totalProduits = ProduitModel::countAll();
        }

        // Calculate total number of pages
        $pagesTotal = max(1, (int)ceil($totalProduits / $limit));

        // Render the view and pass data to it
        $this->render('catalogue.php', [
            'title'       => 'Artisphere – Catalogue',
            'pageCss'     => 'catalogue-style.css',
            'pageJs'      => 'catalogue.js',
            'produits'    => $produits,
            'categories'  => $categories,
            'page'        => $page,
            'pagesTotal'  => $pagesTotal,
            'currentCat'  => $currentCat, // IMPORTANT for active category + pagination
        ]);
    }
}
