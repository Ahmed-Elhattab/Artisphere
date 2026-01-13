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

        $produits = ProduitModel::listByCreator($id);
        $evenements = EvenementModel::listByCreator($id);

        $avg = NoteArtisanModel::averageForArtisan($id);
        $avis = NoteArtisanModel::listForArtisan($id);

        $backUrl = $_SESSION['previous_url'] ?? '/artisphere/?controller=index&action=index';

        // ✅ Affichage bouton "Noter cet artisan"
        $isLogged = !empty($_SESSION['user']);
        $idClient = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);

        $canRate = false;
        $alreadyRated = false;

        if ($isLogged && $idClient > 0) {
            // éviter qu’un artisan se note lui-même
            if ($idClient !== (int)$artisan['id_personne']) {
                $alreadyRated = NoteArtisanModel::existsForClient($id, $idClient);
                if (!$alreadyRated) {
                    $canRate = NoteArtisanModel::clientCanRate($id, $idClient);
                }
            }
        }

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }

        $this->render('artisan_show.php', [
            'title' => 'Profil de ' . $artisan['pseudo'] . ' – Artisphere',
            'pageCss' => 'artisan_show-style.css',
            'artisan' => $artisan,
            'produits' => $produits,
            'evenements' => $evenements,
            'avg' => $avg,
            'avis' => $avis,
            'backUrl' => $backUrl,
            'canRate' => $canRate,
            'alreadyRated' => $alreadyRated,
        ]);
    }
}