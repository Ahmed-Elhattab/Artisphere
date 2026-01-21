<?php

require_once __DIR__ . '/../model/personne_model.php';
require_once __DIR__ . '/../model/reservation_produit_model.php';
require_once __DIR__ . '/../model/reservation_evenement_model.php';



class profil_controller extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();

        $user = $_SESSION['user'];
        $idPersonne = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);

        // En cours
        $reservationsProduitsEnCours = ReservationProduitModel::listForUserByStatus($idPersonne, 'en cours');
        $reservationsEvenementsEnCours = ReservationEventModel::listForUserByStatus($idPersonne, 'en cours');

        // Payées (dernières commandes)
        $reservationsProduitsPayees = ReservationProduitModel::listForUserByStatus($idPersonne, 'payée');
        $reservationsEvenementsPayees = ReservationEventModel::listForUserByStatus($idPersonne, 'payée');

        $this->render('profil.php', [
            'title' => 'Artisphere – Profil',
            'pageCss' => 'profil.css',
            'nom'    => $user['nom'] ?? '',
            'prenom' => $user['prenom'] ?? '',
            'pseudo' => $user['pseudo'] ?? '',
            'role'   => $user['role'] ?? '',
            'reservationsProduitsEnCours' => $reservationsProduitsEnCours,
            'reservationsEvenementsEnCours' => $reservationsEvenementsEnCours,
            'reservationsProduitsPayees' => $reservationsProduitsPayees,
            'reservationsEvenementsPayees' => $reservationsEvenementsPayees,
        ]);
    }
    #modifie la photo de profil de l'utilisateur, et vérifie la conformité de celle ci (niveau sécu)
    public function updateAvatar(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=profil&action=index');
            exit;
        }

        $errors = [];

        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Aucun fichier valide n'a été envoyé.";
        } 
        else {
            $file = $_FILES['avatar'];

            // Limite taille (ex: 2 Mo)
            $maxSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                $errors[] = "La photo est trop lourde (max 2 Mo).";
            }

            // Vérifier type MIME réel (plus fiable que l’extension)
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);

            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];

            if (!isset($allowed[$mime])) {
                $errors[] = "Format non accepté. Utilise JPG, PNG ou WEBP.";
            }
        }

        if (!empty($errors)) {
            // Réaffiche profil avec erreurs
            $this->render('profil.php', [
                'title' => 'Artisphere – Connexion',
                'pageCss' => 'connexion2-style.css',
                'errors'  => $errors,
            ]);
            return;
        }

        // -- enregistrement du fichier
        //genere un nom unique dans dossier avatars + extension selon mime
        $ext = $allowed[$mime];
        $newName = 'u' . (int)$_SESSION['user']['id'] . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

        //dossier images (dans public)
        $destDir = dirname(__DIR__, 2) . '/public/images/avatars/';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $destPath = $destDir . $newName;

        //cas d'érreur lors de l'enregistrement du fichier
        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
            $this->render('profil.php', [
                'title' => 'Artisphere – Connexion',
                'pageCss' => 'connexion2-style.css',
                'errors'  => ["Erreur lors de l'enregistrement du fichier."],
            ]);
            return;
        }

        //supprime l’ancien avatar, si existant, dans le dossier image
        $oldAvatar = $_SESSION['user']['avatar'] ?? null;
        if ($oldAvatar) {
            $oldPath = $destDir . $oldAvatar;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        //maj BD
        PersonneModel::updateAvatar((int)$_SESSION['user']['id'], $newName);

        //maj session
        $_SESSION['user']['avatar'] = $newName;

        // Redirection PRG
        header('Location: /artisphere/?controller=profil&action=index');
        exit;
    }
}