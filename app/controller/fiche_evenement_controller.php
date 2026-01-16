<?php
require_once __DIR__ . '/../model/evenement_model.php';
require_once __DIR__ . '/../model/event_image_model.php';


class fiche_evenement_controller extends BaseController
{
    public function index(): void
    {
        $types = EvenementModel::listTypes();

        $this->render('fiche_evenement.php', [
            'title' => 'Artisphere - fiche-evenement',
            'pageCss' => 'fiche-evenement-style.css',
            'pageJs' => 'evenement_image_preview.js',
            'types' => $types,
        ]);
    }

    public function submit(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=fiche_evenement&action=index');
            exit;
        }

        $types = EvenementModel::listTypes();

        $nom = trim($_POST['nom'] ?? '');
        $lieu = trim($_POST['lieu'] ?? '');
        $nombre_place = (int)($_POST['nombre_place'] ?? -1);
        $description = trim($_POST['description'] ?? '');

        $id_createur = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);

        // maintenant c’est un id numérique
        $id_type = (int)($_POST['id_type'] ?? 0);

        $prix = (float)($_POST['prix'] ?? -1);
        $date_debut = $_POST['date_debut'] ?? '';
        $date_fin = $_POST['date_fin'] ?? '';

        $errors = [];
        if ($nom === '') $errors[] = "Le nom de l’évènement est obligatoire.";
        if ($lieu === '') $errors[] = "Le lieu est obligatoire.";
        if ($nombre_place < 0) $errors[] = "Le nombre de places doit être positif.";
        if ($description === '') $errors[] = "La description est obligatoire.";

        if ($id_type <= 0) {
            $errors[] = "Le type d’évènement est obligatoire.";
        } elseif (!EvenementModel::typeExists($id_type)) {
            $errors[] = "Type invalide.";
        }

        if ($prix < 0) {
            $errors[] = "Le prix doit être positif.";
        }

        if ($date_debut === '' || $date_fin === '') {
            $errors[] = "Les dates de début et de fin sont obligatoires.";
        } elseif ($date_fin < $date_debut) {
            $errors[] = "La date de fin doit être postérieure à la date de début.";
        }

        // Images
        $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        ];
        $maxSize = 3 * 1024 * 1024;
        $uploadedNames = [];
        $files = $_FILES['images'] ?? null;

        if (!$files || empty($files['name']) || !is_array($files['name']) || count(array_filter($files['name'])) === 0) {
        $errors[] = "Veuillez ajouter au moins une image valide.";
        } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $count = count($files['name']);
        if ($count > 6) $errors[] = "Maximum 6 images.";

        for ($i = 0; $i < $count; $i++) {
            if (empty($files['name'][$i])) continue;

            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur d’upload sur une des images.";
            continue;
            }

            if ($files['size'][$i] > $maxSize) {
            $errors[] = "Une image dépasse 3 Mo.";
            continue;
            }

            $mime = $finfo->file($files['tmp_name'][$i]);
            if (!isset($allowed[$mime])) {
            $errors[] = "Format non accepté (JPG/PNG/WEBP).";
            continue;
            }
        }
        }

        if (!empty($errors)) {
        $this->render('fiche_evenement.php', [
            'title' => 'Nouvel évènement',
            'pageCss' => 'fiche-evenement-style.css',
            'pageJs' => 'evenement_image_preview.js',
            'types' => $types,
            'errors' => $errors,
            'old' => [
            'nom' => $nom,
            'lieu' => $lieu,
            'nombre_place' => $nombre_place,
            'description' => $description,
            'id_type' => $id_type,
            'prix' => $prix >= 0 ? (string)$prix : '',
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            ],
        ]);
        return;
        }

        // Transaction
        $pdo = Database::getConnection();
        $destDir = dirname(__DIR__, 2) . '/public/images/evenements/';
        if (!is_dir($destDir)) mkdir($destDir, 0775, true);

        try {
        $pdo->beginTransaction();

        // 1) create event (image = null pour l’instant)
        $idEvent = EvenementModel::create([
            'nom' => $nom,
            'image' => null,
            'lieu' => $lieu,
            'nombre_place' => $nombre_place,
            'description' => $description,
            'id_type' => $id_type,
            'prix' => $prix,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'id_createur' => $id_createur,
        ]);

        // 2) upload fichiers
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if (empty($files['name'][$i])) continue;

            $tmp = $files['tmp_name'][$i];
            $mime = $finfo->file($tmp);
            if (!isset($allowed[$mime])) continue;

            $ext = $allowed[$mime];
            $fn = 'e' . $id_createur . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

            if (!move_uploaded_file($tmp, $destDir . $fn)) {
            throw new RuntimeException("Erreur lors de l'enregistrement d'une image.");
            }
            $uploadedNames[] = $fn;
        }

        if (empty($uploadedNames)) {
            throw new RuntimeException("Aucune image n'a été enregistrée.");
        }

        // 3) insert images table
        EventImageModel::insertMany($idEvent, $uploadedNames);

        // 4) remplir pevent.image avec la première image (fallback)
        $stmt = $pdo->prepare("UPDATE pevent SET image = :img WHERE id_event = :id");
        $stmt->execute([':img' => $uploadedNames[0], ':id' => $idEvent]);

        $pdo->commit();

        header('Location: /artisphere/?controller=fiche_evenement&action=index&success=1');
        exit;

        } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        foreach ($uploadedNames as $fn) @unlink($destDir . $fn);

        $this->render('fiche_evenement.php', [
            'title' => 'Nouvel évènement',
            'pageCss' => 'fiche-evenement-style.css',
            'pageJs' => 'evenement_image_preview.js',
            'types' => $types,
            'errors' => ["Erreur : " . $e->getMessage()],
        ]);
        return;
        }
    }
}