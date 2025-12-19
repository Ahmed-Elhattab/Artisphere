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

        $this->render('produit_show.php', [
            'title' => $produit['nom'] . ' – Artisphere',
            'pageCss' => 'details-style.css',
            'produit' => $produit,
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
}