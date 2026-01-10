<?php
require_once __DIR__ . '/../model/personne_model.php';

class admin_pending_users_controller extends BaseController
{
    private function requireAdmin(): void
    {
        $this->requireLogin();
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }
    }

    public function index(): void
    {
        $this->requireAdmin();

        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }

        $q = trim($_GET['q'] ?? '');

        $perPage = 5;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $total = PersonneModel::countPendingUsers($q);
        $pages = max(1, (int)ceil($total / $perPage));
        $page = min($page, $pages);

        $offset = ($page - 1) * $perPage;
        $users = PersonneModel::listPendingUsers($q, $perPage, $offset);

        $this->render('admin_pending_users.php', [
            'title'   => 'Comptes en attente – Artisphere',
            'pageCss' => 'admin_users-style.css', // tu peux réutiliser le même css
            'users'   => $users,
            'q'       => $q,
            'page'    => $page,
            'pages'   => $pages,
            'csrf'    => $_SESSION['csrf'],
        ]);
    }

    public function validate(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=admin_pending_users&action=index');
            exit;
        }

        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }

        $id = (int)($_POST['id'] ?? 0);
        $q = trim($_POST['q'] ?? '');
        $page = max(1, (int)($_POST['page'] ?? 1));

        if ($id > 0) {
            PersonneModel::validateUser($id);
        }

        header('Location: /artisphere/?controller=admin_pending_users&action=index&success=validate&q=' . urlencode($q) . '&page=' . $page);
        exit;
    }

    public function delete(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=admin_pending_users&action=index');
            exit;
        }

        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }

        $id = (int)($_POST['id'] ?? 0);
        $q = trim($_POST['q'] ?? '');
        $page = max(1, (int)($_POST['page'] ?? 1));

        // sécurité: empêcher l’admin de se supprimer lui-même
        $me = (int)($_SESSION['user']['id_personne'] ?? $_SESSION['user']['id'] ?? 0);
        if ($id > 0 && $id !== $me) {
            PersonneModel::deleteUser($id);
        }

        header('Location: /artisphere/?controller=admin_pending_users&action=index&success=delete&q=' . urlencode($q) . '&page=' . $page);
        exit;
    }
}