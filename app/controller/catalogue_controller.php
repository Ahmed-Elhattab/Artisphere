<?php
require_once __DIR__ . '/../model/produit_model.php';
require_once __DIR__ . '/../model/categorie_model.php';
class catalogue_controller extends BaseController
{
    public function index(): void
    {
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'id_categorie' => (int)($_GET['cat'] ?? 0),
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'in_stock' => !empty($_GET['in_stock']) ? 1 : 0,
        ];

        $produits = ProduitModel::search($filters);
        $categories = CategorieModel::all();

        $this->render('catalogue.php', [
            'title' => 'Catalogue – Artisphere',
            'pageCss' => 'catalogue-style.css',
            'produits' => $produits,
            'categories' => $categories,
            'filters' => $filters
        ]);
    }
}