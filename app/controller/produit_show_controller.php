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

        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }

        $idProduit = (int)($_POST['id_produit'] ?? 0);
        $back = $_POST['back'] ?? '/artisphere/?controller=index&action=index';
        $idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);

        $quantite = (int)($_POST['quantite'] ?? 1);
        if ($quantite < 1) $quantite = 1;

        $ok = false;
        if ($idProduit > 0 && $idUser > 0) {
            // Optionnel : clamp côté serveur (sécurité) avec les vraies données
            $produit = ProduitModel::findById($idProduit);
            if ($produit) {
                $stockReel = (int)($produit['quantite'] ?? 0);
                $stockReserve = (int)($produit['stock_reserve'] ?? 0);
                $stockDispo = max(0, $stockReel - $stockReserve);

                if ($quantite > $stockDispo) {
                    $quantite = $stockDispo;
                }
            }

            if ($quantite >= 1) {
                $ok = ReservationProduitModel::reserve($idProduit, $idUser, $quantite);
            }
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

        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }

        $idProduit = (int)($_POST['id_produit'] ?? 0);
        $back = $_POST['back'] ?? '/artisphere/';
        $idUser = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);

        $ok = ($idProduit > 0 && $idUser > 0)
            ? ReservationProduitModel::cancel($idProduit, $idUser)
            : false;

        $sep = (str_contains($back, '?') ? '&' : '?');
        header('Location: ' . $back . $sep . 'cancelled=' . ($ok ? '1' : '0'));
        exit;
    }
}