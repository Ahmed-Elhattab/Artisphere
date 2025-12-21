<?php
require_once __DIR__ . '/../model/evenement_model.php';

class evenement_update_controller extends BaseController
{
    private function ensureCsrf(): void
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }
    }

    private function checkCsrf(): void
    {
        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }
    }

    public function index(): void
    {
        $this->requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        $event = ($id > 0) ? EvenementModel::findById($id) : null;

        if (!$event) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        // Sécurité : seul le créateur peut éditer
        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        if ($userId <= 0 || (int)$event['id_createur'] !== $userId) {
            http_response_code(403);
            exit("Accès refusé.");
        }

        $this->ensureCsrf();

        $this->render('evenement_update.php', [
            'title' => 'Éditer un évènement – Artisphere',
            'pageCss' => 'evenement_update-style.css',
            'event' => $event,
            'csrf' => $_SESSION['csrf'],
        ]);
    }

    public function submit(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $this->checkCsrf();

        $id = (int)($_POST['id_event'] ?? 0);
        $event = ($id > 0) ? EvenementModel::findById($id) : null;

        if (!$event) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        if ($userId <= 0 || (int)$event['id_createur'] !== $userId) {
            http_response_code(403);
            exit("Accès refusé.");
        }

        // Champs
        $nom = trim($_POST['nom'] ?? '');
        $lieu = trim($_POST['lieu'] ?? '');
        $nombre_place = (int)($_POST['nombre_place'] ?? 0);
        $type = trim($_POST['type'] ?? '');
        $prix = (float)($_POST['prix'] ?? 0);
        $date_debut = trim($_POST['date_debut'] ?? '');
        $date_fin = trim($_POST['date_fin'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $errors = [];
        if ($nom === '') $errors[] = "Le nom est obligatoire.";
        if ($lieu === '') $errors[] = "Le lieu est obligatoire.";
        if ($nombre_place < 0) $errors[] = "Le nombre de places doit être positif.";
        if ($type === '') $errors[] = "Le type est obligatoire.";
        if ($prix < 0) $errors[] = "Le prix doit être positif.";
        if ($date_debut === '' || $date_fin === '') $errors[] = "Les dates sont obligatoires.";
        if ($date_debut !== '' && $date_fin !== '' && $date_fin < $date_debut) {
            $errors[] = "La date de fin doit être après la date de début.";
        }
        if ($description === '') $errors[] = "La description est obligatoire.";

        // Image : garder l’ancienne si pas de nouveau fichier
        $imageFile = $event['image'];

        // Si un nouveau fichier est uploadé, on le remplace
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['image']['tmp_name'];
            $name = $_FILES['image']['name'];

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed, true)) {
                $errors[] = "Format d’image invalide (jpg, jpeg, png, webp).";
            } else {
                $newName = 'event_' . $id . '_' . time() . '.' . $ext;

                // dossier cible (adapte si tu stockes ailleurs)
                $targetDir = __DIR__ . '/../../public/uploads/evenements/';
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0777, true);
                }

                $targetPath = $targetDir . $newName;

                if (!move_uploaded_file($tmp, $targetPath)) {
                    $errors[] = "Impossible d’enregistrer l’image.";
                } else {
                    $imageFile = $newName;
                }
            }
        }

        if (!empty($errors)) {
            // Recharger les valeurs saisies
            $event['nom'] = $nom;
            $event['lieu'] = $lieu;
            $event['nombre_place'] = $nombre_place;
            $event['type'] = $type;
            $event['prix'] = $prix;
            $event['date_debut'] = $date_debut;
            $event['date_fin'] = $date_fin;
            $event['description'] = $description;
            $event['image'] = $imageFile;

            $this->render('evenement_update.php', [
                'title' => 'Éditer un évènement – Artisphere',
                'pageCss' => 'evenement_update-style.css',
                'event' => $event,
                'errors' => $errors,
                'csrf' => $_SESSION['csrf'],
            ]);
            return;
        }

        EvenementModel::updateEvent($id, [
            'nom' => $nom,
            'image' => $imageFile,
            'lieu' => $lieu,
            'nombre_place' => $nombre_place,
            'description' => $description,
            'type' => $type,
            'prix' => $prix,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
        ]);

        header('Location: /artisphere/?controller=evenement_show&action=show&id=' . $id . '&updated=1');
        exit;
    }
}