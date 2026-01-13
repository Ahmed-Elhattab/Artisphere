<?php
require_once __DIR__ . '/../model/reservation_produit_model.php';
require_once __DIR__ . '/../model/reservation_evenement_model.php';

class artisan_commandes_controller extends BaseController
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
        $this->requireRole('artisan'); // (ou autoriser admin aussi si tu veux)

        $idArtisan = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $q = trim($_GET['q'] ?? '');

        $this->ensureCsrf();

        $produits = ReservationProduitModel::listPendingForArtisan($idArtisan, $q);
        $evenements = ReservationEventModel::listPendingForArtisan($idArtisan, $q);

        $this->render('artisan_commandes.php', [
            'title' => 'Artisphere – Commandes en cours',
            'pageCss' => 'artisan_commandes-style.css',
            'csrf' => $_SESSION['csrf'],
            'q' => $q,
            'produits' => $produits,
            'evenements' => $evenements,
        ]);
    }

    public function payProduit(): void
    {
        $this->requireLogin();
        $this->requireRole('artisan');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=artisan_commandes&action=index');
            exit;
        }
        $this->checkCsrf();

        $idArtisan = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $idResa = (int)($_POST['id_resa'] ?? 0);
        $q = trim($_POST['q'] ?? '');

        $ok = ($idResa > 0 && $idArtisan > 0)
            ? ReservationProduitModel::markPaidByArtisan($idResa, $idArtisan)
            : false;

        header('Location: /artisphere/?controller=artisan_commandes&action=index&q=' . urlencode($q) . '&success=' . ($ok ? 'pay_prod' : '0'));
        exit;
    }

    public function cancelProduit(): void
    {
        $this->requireLogin();
        $this->requireRole('artisan');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=artisan_commandes&action=index');
            exit;
        }
        $this->checkCsrf();

        $idArtisan = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $idResa = (int)($_POST['id_resa'] ?? 0);
        $q = trim($_POST['q'] ?? '');

        $ok = ($idResa > 0 && $idArtisan > 0)
            ? ReservationProduitModel::cancelByArtisan($idResa, $idArtisan)
            : false;

        header('Location: /artisphere/?controller=artisan_commandes&action=index&q=' . urlencode($q) . '&success=' . ($ok ? 'cancel_prod' : '0'));
        exit;
    }

    public function payEvent(): void
    {
        $this->requireLogin();
        $this->requireRole('artisan');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=artisan_commandes&action=index');
            exit;
        }
        $this->checkCsrf();

        $idArtisan = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $idResa = (int)($_POST['id_resa'] ?? 0);
        $q = trim($_POST['q'] ?? '');

        $ok = ($idResa > 0 && $idArtisan > 0)
            ? ReservationEventModel::markPaidByArtisan($idResa, $idArtisan)
            : false;

        header('Location: /artisphere/?controller=artisan_commandes&action=index&q=' . urlencode($q) . '&success=' . ($ok ? 'pay_ev' : '0'));
        exit;
    }

    public function cancelEvent(): void
    {
        $this->requireLogin();
        $this->requireRole('artisan');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=artisan_commandes&action=index');
            exit;
        }
        $this->checkCsrf();

        $idArtisan = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        $idResa = (int)($_POST['id_resa'] ?? 0);
        $q = trim($_POST['q'] ?? '');

        $ok = ($idResa > 0 && $idArtisan > 0)
            ? ReservationEventModel::cancelByArtisan($idResa, $idArtisan)
            : false;

        header('Location: /artisphere/?controller=artisan_commandes&action=index&q=' . urlencode($q) . '&success=' . ($ok ? 'cancel_ev' : '0'));
        exit;
    }
}