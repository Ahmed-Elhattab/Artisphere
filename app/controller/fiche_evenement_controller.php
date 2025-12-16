<?php

require_once __DIR__ . '/../model/evenement_model.php';

class fiche_evenement_controller extends BaseController
{
    public function index(): void
    {
        $this->render('fiche_evenement.php', [
            'title' => 'Artisphere - fiche-evenement',
            'pageCss' => 'fiche-evenement-style.css',
            'pageJs' => 'evenement_image_preview.js'
        ]);
    }

    public function submit(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /artisphere/?controller=fiche_evenement&action=index');
        exit;
        }

        $nom = trim($_POST['nom'] ?? '');
        $lieu = trim($_POST['lieu'] ?? '');
        $nombre_place = (int)($_POST['nombre_place'] ?? -1);
        $description = trim($_POST['description'] ?? '');
        $id_createur = (int)($_SESSION['user']['id'] ?? 0);
        $type        = trim($_POST['type'] ?? '');
        $prix        = (float)($_POST['prix'] ?? -1);
        $date_debut  = $_POST['date_debut'] ?? '';
        $date_fin    = $_POST['date_fin'] ?? '';

        $errors = [];
        if ($nom === '') $errors[] = "Le nom de l’évènement est obligatoire.";
        if ($lieu === '') $errors[] = "Le lieu est obligatoire.";
        if ($nombre_place < 0) $errors[] = "Le nombre de places doit être positif.";
        if ($description === '') $errors[] = "La description est obligatoire.";
        if ($type === '') {
            $errors[] = "Le type d’évènement est obligatoire.";
        }
        if ($prix < 0) {
            $errors[] = "Le prix doit être positif.";
        }
        if ($date_debut === '' || $date_fin === '') {
            $errors[] = "Les dates de début et de fin sont obligatoires.";
        } elseif ($date_fin < $date_debut) {
            $errors[] = "La date de fin doit être postérieure à la date de début.";
        }

        // Image
        $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        ];

        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Veuillez ajouter une image valide.";
        } else {
        $maxSize = 3 * 1024 * 1024;
        if ($_FILES['image']['size'] > $maxSize) $errors[] = "Image trop lourde (max 3 Mo).";

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['image']['tmp_name']);
        if (!isset($allowed[$mime])) $errors[] = "Format image non accepté (JPG/PNG/WEBP).";
        }

        if (!empty($errors)) {
        $this->render('fiche_evenement.php', [
            'title' => 'Nouvel évènement',
            'pageCss' => 'fiche-evenement-style.css',
            'pageJs' => 'evenement_image_preview.js',
            'errors' => $errors,
            'old' => [
                'nom' => $nom,
                'lieu' => $lieu,
                'nombre_place' => $nombre_place,
                'description' => $description,
                'type' => $type,
                'prix' => $prix >= 0 ? (string)$prix : '',
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
            ],
        ]);
        return;
        }

        // Enregistrer fichier
        $ext = $allowed[$mime];
        $filename = 'e' . $id_createur . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

        $destDir = dirname(__DIR__, 2) . '/public/images/evenements/';
        if (!is_dir($destDir)) mkdir($destDir, 0775, true);

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destDir . $filename)) {
        $this->render('fiche_evenement.php', [
            'title' => 'Nouvel évènement',
            'pageCss' => 'fiche-evenement-style.css',
            'pageJs' => 'evenement_image_preview.js',
            'errors' => ["Erreur lors de l'enregistrement de l'image."],
        ]);
        return;
        }

        EvenementModel::create([
            'nom' => $nom,
            'image' => $filename,
            'lieu' => $lieu,
            'nombre_place' => $nombre_place,
            'description' => $description,
            'type' => $type,
            'prix' => $prix,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'id_createur' => $id_createur,
        ]);

        header('Location: /artisphere/?controller=fiche_evenement&action=index&success=1');
        exit;
    }
}