<?php
require_once __DIR__ . '/../model/evenement_model.php';
require_once __DIR__ . '/../model/reservation_evenement_model.php';

class evenement_show_controller extends BaseController
{
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $evenement = EvenementModel::findById($id);
        if (!$evenement) {
            $this->render('not_found.php', [
                'title' => 'Évènement introuvable – Artisphere',
                'pageCss' => 'details-style.css',
                'message' => "Cet évènement n'existe pas (ou a été supprimé)."
            ]);
            return;
        }

        if (!empty($_GET['mode']) && $_GET['mode'] === 'mine') {
            $this->requireOwner((int)$evenement['id_createur']);
        }

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
            if (str_contains($url, '/artisphere/')) {
                $_SESSION['previous_url'] = $url;
            }
        }

        $isLogged = !empty($_SESSION['user']);
        $idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);

        $isOwner = $isLogged && ($idUser > 0) && ((int)$evenement['id_createur'] === $idUser);

        $backUrl = $_SESSION['previous_url'] ?? '/artisphere/?controller=index&action=index';

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }

        $isReserved = false;
        if ($isLogged && $idUser > 0) {
            $isReserved = ReservationEventModel::exists((int)$evenement['id_event'], $idUser);
        }

        $this->render('evenement_show.php', [
            'title' => $evenement['nom'] . ' – Artisphere',
            'pageCss' => 'details-style.css',
            'evenement' => $evenement,
            'isLogged' => $isLogged,
            'isOwner' => $isOwner,
            'isReserved' => $isReserved,
            'backUrl' => $backUrl
        ]);
    }

    public function reserve(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }

        $idEvent = (int)($_POST['id_evenement'] ?? 0);
        $back = $_POST['back'] ?? '/artisphere/?controller=index&action=index';

        $idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);

        $ok = false;
        if ($idEvent > 0 && $idUser > 0) {
            $ok = ReservationEventModel::reserve($idEvent, $idUser);
        }

        $sep = (str_contains($back, '?') ? '&' : '?');
        header('Location: ' . $back . $sep . ($ok ? 'reserved=1' : 'reserved=0'));
        exit;
    }

    public function cancelReservation(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }

        $idEvent = (int)($_POST['id_evenement'] ?? 0);
        $back = $_POST['back'] ?? '/artisphere/?controller=index&action=index';

        $idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);

        $ok = ($idEvent > 0 && $idUser > 0)
            ? ReservationEventModel::cancel($idEvent, $idUser)
            : false;

        $sep = (str_contains($back, '?') ? '&' : '?');
        header('Location: ' . $back . $sep . 'cancelled=' . ($ok ? '1' : '0'));
        exit;
    }
}