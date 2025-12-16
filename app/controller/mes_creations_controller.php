<?php
require_once __DIR__ . '/../model/produit_model.php';
require_once __DIR__ . '/../model/evenement_model.php';

class mes_creations_controller extends BaseController
{
    public function index(): void
    {
        $this->requireRole('artisan');

        $id = (int)$_SESSION['user']['id'];

        $produits = ProduitModel::findByCreateur($id);
        $evenements = EvenementModel::findByCreateur($id);

        $this->render('mes_creations.php', [
            'title' => 'Artisphere – Mes Créations',
            'pageCss' => 'mes_creations-style.css',
            'produits' => $produits,
            'evenements' => $evenements,
        ]);
    }
}