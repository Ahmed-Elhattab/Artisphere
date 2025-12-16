<?php
require_once __DIR__ . '/../model/produit_model.php';

class produit_show_controller extends BaseController
{
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $produit = ProduitModel::findById($id);
        if (!$produit) {
            $this->render('not_found.php', [
                'title' => 'Produit introuvable – Artisphere',
                'pageCss' => 'details-style.css',
                'message' => "Ce produit n'existe pas (ou a été supprimé)."
            ]);
            return;
        }

        //mode=mine => page privée (seul le créateur)
        if (!empty($_GET['mode']) && $_GET['mode'] === 'mine') {
            $this->requireOwner((int)$produit['id_createur']);
        }

        $this->render('produit_show.php', [
            'title' => $produit['nom'] . ' – Artisphere',
            'pageCss' => 'details-style.css',
            'produit' => $produit
        ]);
    }
}