<?php
require_once __DIR__ . '/../model/categorie_model.php';
require_once __DIR__ . '/../model/specialite_model.php';
require_once __DIR__ . '/../model/type_evenement_model.php';

class admin_referentiels_controller extends BaseController
{
    private function requireAdmin(): void
    {
        $this->requireLogin();
        $role = $_SESSION['user']['role'] ?? '';
        if ($role !== 'admin') {
            http_response_code(403);
            exit('Accès refusé.');
        }
    }

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
        $this->requireAdmin();
        $this->ensureCsrf();

        $q = trim($_GET['q'] ?? '');

        $categories = CategorieModel::searchByName($q);
        $specialites = SpecialiteModel::searchByName($q);
        $types = TypeEvenementModel::searchByName($q);

        $this->render('admin_referentiels.php', [
            'title' => 'Gestion des référentiels – Artisphere',
            'pageCss' => 'admin_referentiels-style.css',
            'csrf' => $_SESSION['csrf'],
            'q' => $q,
            'categories' => $categories,
            'specialites' => $specialites,
            'types' => $types,
        ]);
    }

    public function add(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=admin_referentiels&action=index');
            exit;
        }

        $this->checkCsrf();

        $kind = $_POST['kind'] ?? ''; // categorie | specialite | type
        $nom = trim($_POST['nom'] ?? '');
        $q = urlencode(trim($_POST['q'] ?? ''));

        if ($nom === '' || mb_strlen($nom) > 80) {
            header('Location: /artisphere/?controller=admin_referentiels&action=index&q=' . $q . '&error=nom');
            exit;
        }

        $ok = false;
        switch ($kind) {
            case 'categorie':
                $ok = CategorieModel::createIfNotExists($nom);
                break;
            case 'specialite':
                $ok = SpecialiteModel::createIfNotExists($nom);
                break;
            case 'type':
                $ok = TypeEvenementModel::createIfNotExists($nom);
                break;
            default:
                $ok = false;
        }

        header('Location: /artisphere/?controller=admin_referentiels&action=index&q=' . $q . '&added=' . ($ok ? '1' : '0'));
        exit;
    }

    public function delete(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=admin_referentiels&action=index');
            exit;
        }

        $this->checkCsrf();

        $kind = $_POST['kind'] ?? '';
        $id = (int)($_POST['id'] ?? 0);
        $q = urlencode(trim($_POST['q'] ?? ''));

        $ok = false;
        if ($id > 0) {
            switch ($kind) {
                case 'categorie':
                    $ok = CategorieModel::deleteById($id);
                    break;
                case 'specialite':
                    $ok = SpecialiteModel::deleteById($id);
                    break;
                case 'type':
                    $ok = TypeEvenementModel::deleteById($id);
                    break;
            }
        }

        header('Location: /artisphere/?controller=admin_referentiels&action=index&q=' . $q . '&deleted=' . ($ok ? '1' : '0'));
        exit;
    }
}