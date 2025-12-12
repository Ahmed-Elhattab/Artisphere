<?php


require_once __DIR__ . '/../model/personne_model.php';

class index_controller extends BaseController
{
    //page d’accueil
    public function index(): void
    {
        // Récupère toutes les personnes en BDD
        $personnes = PersonneModel::getAll();

        // Affiche la vue home.php
       $this->render('home.php', [
        'title' => 'Accueil - Artisphere',
        'pageCss' => 'home.css',
        'personnes' => $personnes
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