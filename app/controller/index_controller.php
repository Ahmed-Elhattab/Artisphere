<?php


require_once __DIR__ . '/../model/personne_model.php';
require_once __DIR__ . '/../model/produit_model.php';
require_once __DIR__ . '/../model/evenement_model.php';



class index_controller extends BaseController
{
    //page d’accueil
    public function index(): void
    {
        $limit = 5;

        $pProd = max(1, (int)($_GET['p_prod'] ?? 1));
        $pEvt  = max(1, (int)($_GET['p_evt'] ?? 1));

        $offsetProd = ($pProd - 1) * $limit;
        $offsetEvt  = ($pEvt - 1) * $limit;

        // À faire via tes models
        $produits = ProduitModel::listHome($limit, $offsetProd);
        $totalProduits = ProduitModel::countAll();
        $pagesProd = max(1, (int)ceil($totalProduits / $limit));

        $evenements = EvenementModel::listHome($limit, $offsetEvt);
        $totalEvenements = EvenementModel::countAll();
        $pagesEvt = max(1, (int)ceil($totalEvenements / $limit));

        // Récupère toutes les personnes en BDD
        $personnes = PersonneModel::getAll();

        // Affiche la vue home.php
       $this->render('home.php', [
        'title' => 'Accueil - Artisphere',
        'pageCss' => 'home.css',
        'personnes' => $personnes,
        'produits' => $produits,
        'evenements' => $evenements,
        'pProd' => $pProd,
        'pEvt' => $pEvt,
        'pagesProd' => $pagesProd,
        'pagesEvt' => $pagesEvt,
        ]);
    }

    //liste des personnes (page dédiée)
    public function listePersonnes(): void
    {
        try {
            $pdo = Database::getConnection();
            $dbMessage = "Connexion BDD OK ";
        } catch (PDOException $e) {
            $dbMessage = "Erreur de connexion : " . $e->getMessage();
        }

        $personnes = PersonneModel::getAll();

        $this->render('home.php', [
            'title'     => 'Liste des personnes',
            'dbMessage' => $dbMessage,
            'personnes' => $personnes,
        ]);
    }
}