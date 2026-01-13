<?php
require_once __DIR__ . '/../model/note_artisan_model.php';
require_once __DIR__ . '/../model/personne_model.php';

class avis_artisan_controller extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();

        $idClient = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $idArtisan = (int)($_GET['id_artisan'] ?? 0);

        if ($idClient <= 0 || $idArtisan <= 0) {
            header('Location: /artisphere/?controller=profil&action=index');
            exit;
        }

        // artisan existe ?
        $artisan = PersonneModel::findArtisanById($idArtisan);
        if (!$artisan) {
            $this->render('not_found.php', [
                'title' => 'Artisan introuvable – Artisphere',
                'pageCss' => 'avis.css',
                'message' => "Cet artisan n'existe pas."
            ]);
            return;
        }

        // droit de noter ?
        if (!NoteArtisanModel::clientCanRate($idArtisan, $idClient)) {
            $this->render('not_found.php', [
                'title' => 'Accès refusé – Artisphere',
                'pageCss' => 'avis.css',
                'message' => "Vous ne pouvez noter cet artisan que si vous avez une commande payée chez lui."
            ]);
            return;
        }

        // déjà noté ?
        if (NoteArtisanModel::existsForClient($idArtisan, $idClient)) {
            header('Location: /artisphere/?controller=artisan_show&action=show&id=' . $idArtisan . '&alreadyRated=1');
            exit;
        }

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }

        $this->render('avis_artisan.php', [
            'title' => 'Artisphere – Avis Artisan',
            'pageCss' => 'avis.css',
            'pageJs' => 'avis.js',
            'artisan' => $artisan,
            'csrf' => $_SESSION['csrf'],
            'userPseudo' => $_SESSION['user']['pseudo'] ?? 'User',
        ]);
    }

    public function submit(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=profil&action=index');
            exit;
        }

        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }

        $idClient = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $idArtisan = (int)($_POST['id_artisan'] ?? 0);
        $note = (int)($_POST['rating'] ?? 0);
        $commentaire = trim($_POST['message'] ?? '');

        $errors = [];
        if ($idClient <= 0 || $idArtisan <= 0) $errors[] = "Requête invalide.";
        if ($note < 1 || $note > 5) $errors[] = "La note doit être entre 1 et 5.";
        if (mb_strlen($commentaire) > 1000) $errors[] = "Commentaire trop long (max 1000 caractères).";

        if (!NoteArtisanModel::clientCanRate($idArtisan, $idClient)) {
            $errors[] = "Vous ne pouvez noter cet artisan que si vous avez une commande payée chez lui.";
        }
        if (NoteArtisanModel::existsForClient($idArtisan, $idClient)) {
            $errors[] = "Vous avez déjà noté cet artisan.";
        }

        $artisan = PersonneModel::findArtisanById($idArtisan);

        if (!empty($errors) || !$artisan) {
            $this->render('avis_artisan.php', [
                'title' => 'Artisphere – Avis Artisan',
                'pageCss' => 'avis.css',
                'pageJs' => 'avis.js',
                'errors' => $errors ?: ["Artisan introuvable."],
                'artisan' => $artisan,
                'csrf' => $_SESSION['csrf'],
                'userPseudo' => $_SESSION['user']['pseudo'] ?? 'User',
                'old' => [
                    'rating' => ($note >= 1 && $note <= 5) ? $note : 5,
                    'message' => $commentaire,
                ],
            ]);
            return;
        }

        $ok = NoteArtisanModel::create($idArtisan, $idClient, $note, $commentaire);

        header('Location: /artisphere/?controller=artisan_show&action=show&id=' . $idArtisan . '&rated=' . ($ok ? '1' : '0'));
        exit;
    }
}