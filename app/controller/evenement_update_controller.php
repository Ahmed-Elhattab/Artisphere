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
        $types = EvenementModel::listTypes();

        if (!$event) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

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
            'types' => $types,
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
        $types = EvenementModel::listTypes();

        if (!$event) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        if ($userId <= 0 || (int)$event['id_createur'] !== $userId) {
            http_response_code(403);
            exit("Accès refusé.");
        }

        $nom = trim($_POST['nom'] ?? '');
        $lieu = trim($_POST['lieu'] ?? '');
        $nombre_place = (int)($_POST['nombre_place'] ?? 0);
        $id_type = (int)($_POST['id_type'] ?? 0);
        $prix = (float)($_POST['prix'] ?? 0);
        $date_debut = trim($_POST['date_debut'] ?? '');
        $date_fin = trim($_POST['date_fin'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $errors = [];
        if ($nom === '') $errors[] = "Le nom est obligatoire.";
        if ($lieu === '') $errors[] = "Le lieu est obligatoire.";
        if ($nombre_place < 0) $errors[] = "Le nombre de places doit être positif.";

        if ($id_type <= 0) $errors[] = "Le type est obligatoire.";
        elseif (!EvenementModel::typeExists($id_type)) $errors[] = "Type invalide.";

        if ($prix < 0) $errors[] = "Le prix doit être positif.";
        if ($date_debut === '' || $date_fin === '') $errors[] = "Les dates sont obligatoires.";
        if ($date_debut !== '' && $date_fin !== '' && $date_fin < $date_debut) {
            $errors[] = "La date de fin doit être après la date de début.";
        }
        if ($description === '') $errors[] = "La description est obligatoire.";

        $imageFile = $event['image'];

        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['image']['tmp_name'];
            $name = $_FILES['image']['name'];

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $allowed, true)) {
                $errors[] = "Format d’image invalide (jpg, jpeg, png, webp).";
            } else {
                $newName = 'event_' . $id . '_' . time() . '.' . $ext;

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
            $event['nom'] = $nom;
            $event['lieu'] = $lieu;
            $event['nombre_place'] = $nombre_place;
            $event['id_type'] = $id_type;
            $event['prix'] = $prix;
            $event['date_debut'] = $date_debut;
            $event['date_fin'] = $date_fin;
            $event['description'] = $description;
            $event['image'] = $imageFile;

            $this->render('evenement_update.php', [
                'title' => 'Éditer un évènement – Artisphere',
                'pageCss' => 'evenement_update-style.css',
                'event' => $event,
                'types' => $types, // ✅ indispensable
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
            'id_type' => $id_type,
            'prix' => $prix,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
        ]);

        header('Location: /artisphere/?controller=evenement_show&action=show&id=' . $id . '&updated=1');
        exit;
    }
}