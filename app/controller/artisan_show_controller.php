<?php
require_once __DIR__ . '/../model/personne_model.php';
require_once __DIR__ . '/../model/produit_model.php';
require_once __DIR__ . '/../model/note_artisan_model.php';
require_once __DIR__ . '/../model/evenement_model.php';


class artisan_show_controller extends BaseController
{
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $artisan = PersonneModel::findArtisanById($id);
        if (!$artisan) {
            $this->render('not_found.php', [
                'title' => 'Artisan introuvable – Artisphere',
                'pageCss' => 'artisan_show-style.css',
                'message' => "Cet artisan n'existe pas (ou n'est pas disponible)."
            ]);
            return;
        }

        // Produits de l'artisan
        $produits = ProduitModel::listByCreator($id);
        $evenements = EvenementModel::listByCreator($id);


        // Notes / avis
        $avg = NoteArtisanModel::averageForArtisan($id);
        $avis = NoteArtisanModel::listForArtisan($id);

        // Back url (optionnel, pratique)
        $backUrl = $_SESSION['previous_url'] ?? '/artisphere/?controller=index&action=index';

        $this->render('artisan_show.php', [
            'title' => 'Profil de ' . $artisan['pseudo'] . ' – Artisphere',
            'pageCss' => 'artisan_show-style.css',
            'artisan' => $artisan,
            'produits' => $produits,
            'evenements' => $evenements, 
            'avg' => $avg,
            'avis' => $avis,
            'backUrl' => $backUrl
        ]);
    }
}