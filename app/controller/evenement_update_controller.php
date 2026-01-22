<?php
require_once __DIR__ . '/../model/evenement_model.php';
require_once __DIR__ . '/../model/event_image_model.php';

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

        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        if ($userId <= 0 || (int)$event['id_createur'] !== $userId) {
            http_response_code(403);
            exit("Accès refusé.");
        }

        $types  = EvenementModel::listTypes();
        $images = EventImageModel::listForEvent((int)$event['id_event']);

        $this->ensureCsrf();

        $this->render('evenement_update.php', [
            'title'   => 'Éditer un évènement – Artisphere',
            'pageCss' => 'evenement_update-style.css',
            'event'   => $event,
            'types'   => $types,
            'images'  => $images,
            'csrf'    => $_SESSION['csrf'],
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

        $types = EvenementModel::listTypes();
        $pdo   = Database::getConnection();

        // ===== Champs =====
        $nom          = trim($_POST['nom'] ?? '');
        $lieu         = trim($_POST['lieu'] ?? '');
        $nombre_place = (int)($_POST['nombre_place'] ?? 0);
        $id_type      = (int)($_POST['id_type'] ?? 0);
        $prix         = (float)($_POST['prix'] ?? 0);
        $date_debut   = trim($_POST['date_debut'] ?? '');
        $date_fin     = trim($_POST['date_fin'] ?? '');
        $description  = trim($_POST['description'] ?? '');

        $errors = [];
        if ($nom === '') $errors[] = "Le nom est obligatoire.";
        if ($lieu === '') $errors[] = "Le lieu est obligatoire.";
        if ($nombre_place < 0) $errors[] = "Le nombre de places doit être positif.";
        if ($id_type <= 0) $errors[] = "Le type est obligatoire.";
        elseif (!EvenementModel::typeExists($id_type)) $errors[] = "Type invalide.";
        if ($prix < 0) $errors[] = "Le prix doit être positif.";
        if ($date_debut === '' || $date_fin === '') $errors[] = "Les dates sont obligatoires.";
        if ($date_debut !== '' && $date_fin !== '' && $date_fin < $date_debut) $errors[] = "La date de fin doit être après la date de début.";
        if ($description === '') $errors[] = "La description est obligatoire.";

        // ===== Images (suppression + ajout + image principale) =====
        $toDelete = $_POST['delete_images'] ?? [];
        if (!is_array($toDelete)) $toDelete = [];
        $toDelete = array_values(array_filter(array_map('intval', $toDelete)));

        $mainImage = trim($_POST['main_image'] ?? ''); // filename

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        $maxSize = 3 * 1024 * 1024;

        $destDir = dirname(__DIR__, 2) . '/public/images/evenements/';
        if (!is_dir($destDir)) mkdir($destDir, 0775, true);

        $uploaded = [];

        try {
            $pdo->beginTransaction();

            // 1) Suppression demandée
            if (!empty($toDelete)) {
                $imgs = EventImageModel::findByIdsForEvent($id, $toDelete);
                EventImageModel::deleteManyForEvent($id, $toDelete);

                foreach ($imgs as $im) {
                    if (!empty($im['filename'])) {
                        @unlink($destDir . $im['filename']);
                    }
                    // si l'image principale était supprimée
                    if (!empty($im['filename']) && $event['image'] === $im['filename']) {
                        $event['image'] = null;
                    }
                }

                $deletedNames = array_map(fn($x) => (string)($x['filename'] ?? ''), $imgs);
                if ($mainImage !== '' && in_array($mainImage, $deletedNames, true)) {
                    $mainImage = '';
                }
            }

            // 2) Ajout nouvelles images
            $files = $_FILES['images'] ?? null;
            if ($files && !empty($files['name']) && is_array($files['name'])) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $count = count($files['name']);

                for ($i = 0; $i < $count; $i++) {
                    if (empty($files['name'][$i])) continue;
                    if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
                    if (($files['size'][$i] ?? 0) > $maxSize) continue;

                    $tmp = $files['tmp_name'][$i];
                    $mime = $finfo->file($tmp);
                    if (!isset($allowed[$mime])) continue;

                    $fn = 'e' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
                    if (!move_uploaded_file($tmp, $destDir . $fn)) {
                        throw new RuntimeException("Erreur upload image.");
                    }
                    $uploaded[] = $fn;
                }

                if (!empty($uploaded)) {
                    EventImageModel::insertMany($id, $uploaded);
                }
            }

            // 3) Re-lister images restantes
            $images = EventImageModel::listForEvent($id);
            $filenames = array_values(array_filter(array_map(fn($r) => $r['filename'] ?? '', $images)));

            // 4) Définir image principale (evenement.image)
            if ($mainImage !== '' && in_array($mainImage, $filenames, true)) {
                $imagePrincipale = $mainImage;
            } elseif (!empty($event['image']) && in_array($event['image'], $filenames, true)) {
                $imagePrincipale = $event['image'];
            } else {
                $imagePrincipale = $filenames[0] ?? null;
            }

            // 5) Erreurs => rollback
            if (!empty($errors)) {
                throw new RuntimeException("form_errors");
            }

            // 6) Update event
            EvenementModel::updateEvent($id, [
                'nom' => $nom,
                'image' => $imagePrincipale,
                'lieu' => $lieu,
                'nombre_place' => $nombre_place,
                'description' => $description,
                'id_type' => $id_type,
                'prix' => $prix,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
            ]);

            $pdo->commit();

            header('Location: /artisphere/?controller=evenement_show&action=show&id=' . $id . '&updated=1');
            exit;

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            foreach ($uploaded as $fn) @unlink($destDir . $fn);

            if ($e->getMessage() !== "form_errors") {
                $errors[] = "Erreur : " . $e->getMessage();
            }

            $images = EventImageModel::listForEvent($id);

            $event['nom'] = $nom;
            $event['lieu'] = $lieu;
            $event['nombre_place'] = $nombre_place;
            $event['id_type'] = $id_type;
            $event['prix'] = $prix;
            $event['date_debut'] = $date_debut;
            $event['date_fin'] = $date_fin;
            $event['description'] = $description;

            $this->render('evenement_update.php', [
                'title'   => 'Éditer un évènement – Artisphere',
                'pageCss' => 'evenement_update-style.css',
                'event'   => $event,
                'types'   => $types,
                'images'  => $images,
                'errors'  => $errors,
                'csrf'    => $_SESSION['csrf'],
            ]);
            return;
        }
    }

    public function delete(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }
        $this->checkCsrf();

        $id = (int)($_POST['id_event'] ?? 0);
        if ($id <= 0) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $event = EvenementModel::findById($id);
        if (!$event) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        if ($userId <= 0 || (int)$event['id_createur'] !== $userId) {
            http_response_code(403);
            exit("Accès refusé.");
        }

        $pdo = Database::getConnection();

        // dossier fichiers
        $destDir = dirname(__DIR__, 2) . '/public/images/evenements/';

        try {
            $pdo->beginTransaction();

            // 1) supprimer les réservations si ta DB n'a pas ON DELETE CASCADE
            // (adapte le nom de table si besoin)
            $pdo->prepare("DELETE FROM reservation_event WHERE id_event = ?")->execute([$id]);

            // 2) récupérer les images liées + supprimer en DB
            $imgs = EventImageModel::listForEvent($id);
            if (!empty($imgs)) {
                $ids = array_values(array_filter(array_map(fn($r) => (int)($r['id_image'] ?? 0), $imgs)));
                if (!empty($ids)) {
                    EventImageModel::deleteManyForEvent($id, $ids);
                }
            }

            // 3) supprimer l'évènement
            $ok = EvenementModel::deleteEvent($id, $userId);
            if (!$ok) {
                throw new RuntimeException("Suppression refusée ou évènement introuvable.");
            }

            $pdo->commit();

            // 4) supprimer les fichiers (après commit)
            foreach ($imgs as $im) {
                $fn = $im['filename'] ?? '';
                if ($fn) @unlink($destDir . $fn);
            }

            // si tu as aussi une image principale stockée dans pevent.image,
            // et qu'elle existe en fichier (selon ton usage), tu peux aussi la supprimer :
            if (!empty($event['image'])) {
                @unlink($destDir . $event['image']);
            }

            header('Location: /artisphere/?controller=mes_creations&action=index&deleted=1');
            exit;

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            http_response_code(500);
            exit("Erreur suppression : " . $e->getMessage());
        }
    }

}