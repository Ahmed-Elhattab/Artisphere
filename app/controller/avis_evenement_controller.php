<?php

require_once __DIR__ . '/../model/reservation_evenement_model.php';

class avis_evenement_controller extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();

        $idPersonne = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $idResa = (int)($_GET['id_resa'] ?? 0);

        if ($idPersonne <= 0 || $idResa <= 0) {
            header('Location: /artisphere/?controller=profil&action=index');
            exit;
        }

        $resa = ReservationEventModel::findOneForUser($idResa, $idPersonne);
        if (!$resa) {
            $this->render('not_found.php', [
                'title' => 'Avis introuvable – Artisphere',
                'pageCss' => 'avis.css',
                'message' => "Cette réservation n'existe pas ou ne vous appartient pas."
            ]);
            return;
        }

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }

        $this->render('avis_evenement.php', [
            'title' => 'Artisphere – Avis Évènements',
            'pageCss' => 'avis.css',
            'pageJs' => 'avis.js', // tu peux réutiliser le même JS étoiles
            'resa' => $resa,
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

        $idPersonne = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $idResa = (int)($_POST['id_resa'] ?? 0);
        $note = (int)($_POST['rating'] ?? 0);
        $message = trim($_POST['message'] ?? '');

        $errors = [];
        if ($idPersonne <= 0 || $idResa <= 0) $errors[] = "Requête invalide.";
        if ($note < 1 || $note > 5) $errors[] = "La note doit être entre 1 et 5.";
        if (mb_strlen($message) > 1000) $errors[] = "Message trop long (max 1000 caractères).";

        $resa = ($idResa > 0 && $idPersonne > 0)
            ? ReservationEventModel::findOneForUser($idResa, $idPersonne)
            : null;

        if (!$resa) {
            $errors[] = "Réservation introuvable.";
        } else {
            if (($resa['status'] ?? '') !== 'payée') {
                $errors[] = "Vous ne pouvez laisser un avis que pour une réservation payée.";
            }
        }

        if (!empty($errors)) {
            $this->render('avis_evenement.php', [
                'title' => 'Artisphere – Avis Évènements',
                'pageCss' => 'avis.css',
                'pageJs' => 'avis.js',
                'errors' => $errors,
                'resa' => $resa,
                'csrf' => $_SESSION['csrf'],
                'userPseudo' => $_SESSION['user']['pseudo'] ?? 'User',
                'old' => [
                    'rating' => $note ?: 5,
                    'message' => $message,
                ],
            ]);
            return;
        }

        $ok = ReservationEventModel::setReview($idResa, $idPersonne, $note, $message);

        header('Location: /artisphere/?controller=avis_evenement&action=index&id_resa=' . $idResa . '&sent=' . ($ok ? '1' : '0'));
        exit;
    }
}