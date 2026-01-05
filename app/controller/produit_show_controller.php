<?php
require_once __DIR__ . '/../model/produit_model.php';
require_once __DIR__ . '/../model/reservation_produit_model.php';


class produit_show_controller extends BaseController
{
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $produit = ProduitModel::findById($id);
        if (!$produit) {
            $this->render('not_found.php', [
                'title' => 'Produit introuvable – Artisphere',
                'pageCss' => 'details-style.css',
                'message' => "Ce produit n'existe pas (ou a été supprimé)."
            ]);
            return;
        }

        //mode=mine => page privée (seul le créateur)
        if (!empty($_GET['mode']) && $_GET['mode'] === 'mine') {
            $this->requireOwner((int)$produit['id_createur']);
        }

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];

            // Sécurité : on accepte seulement les URLs internes
            if (str_contains($url, '/artisphere/')) {
                $_SESSION['previous_url'] = $url;
            }
        }

        $isLogged = !empty($_SESSION['user']);
        $idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $isOwner = $isLogged && ($idUser > 0) && ((int)$produit['id_createur'] === $idUser);
        $backUrl = $_SESSION['previous_url'] ?? '/artisphere/?controller=index&action=index';
        
        // CSRF 
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }

        // Déjà réservé ?
        $isReserved = false;
        if ($isLogged && $idUser > 0) {
            $isReserved = ReservationProduitModel::exists((int)$produit['id_produit'], $idUser);
        }

        $this->render('produit_show.php', [
            'title' => $produit['nom'] . ' – Artisphere',
            'pageCss' => 'details-style.css',
            'produit' => $produit,
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

        // CSRF (si tu as déjà une méthode, utilise-la)
        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }

        $idProduit = (int)($_POST['id_produit'] ?? 0);
        $back = $_POST['back'] ?? '/artisphere/?controller=index&action=index';

        $ok = false;
        if ($idProduit > 0) {
            $ok = ReservationProduitModel::reserve($idProduit, (int)$_SESSION['user']['id']);
        }

        $sep = (str_contains($back, '?') ? '&' : '?');
        header('Location: ' . $back . $sep . ($ok ? 'reserved=1' : 'reserved=0'));
        exit;
    }

    public function cancelReservation(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/');
            exit;
        }

        // CSRF
        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }

        $idProduit = (int)($_POST['id_produit'] ?? 0);
        $back = $_POST['back'] ?? '/artisphere/';

        $idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);

        if ($idProduit <= 0 || $idUser <= 0) {
            header('Location: ' . $back);
            exit;
        }

        $ok = ReservationProduitModel::cancel($idProduit, $idUser);

        header('Location: ' . $back . ($ok ? '&cancelled=1' : '&cancelled=0'));
        exit;
    }
}