<?php
require_once __DIR__ . '/../model/personne_model.php';

class admin_users_controller extends BaseController
{
    private function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['csrf'];
    }

    private function checkCsrf(): void
    {
        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            $this->render('not_found.php', [
                'title' => 'Accès refusé – Artisphere',
                'pageCss' => 'details-style.css',
                'message' => "Requête refusée (CSRF)."
            ]);
            exit;
        }
    }

    public function index(): void
    {
        $this->requireRole('admin');

        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $total = PersonneModel::countUsers($q);
        $users = PersonneModel::searchUsers($q, $limit, $offset);

        $pages = max(1, (int)ceil($total / $limit));

        $this->render('admin_users.php', [
            'title' => 'Gestion des comptes – Artisphere',
            'pageCss' => 'admin_users-style.css',
            'users' => $users,
            'q' => $q,
            'page' => $page,
            'pages' => $pages,
            'csrf' => $this->csrfToken(),
        ]);
    }

    public function promote(): void
    {
        $this->requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=admin_users&action=index');
            exit;
        }
        $this->checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Optionnel: empêcher de promouvoir un id inexistant (sinon update no-op)
            PersonneModel::promoteToAdmin($id);
        }

        $q = urlencode($_POST['q'] ?? '');
        $page = (int)($_POST['page'] ?? 1);
        header("Location: /artisphere/?controller=admin_users&action=index&q={$q}&page={$page}&success=promote");
        exit;
    }

    public function delete(): void
    {
        $this->requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=admin_users&action=index');
            exit;
        }
        $this->checkCsrf();

        $id = (int)($_POST['id'] ?? 0);

        // Sécurité basique : empêcher l’admin de se supprimer lui-même
        if ($id > 0 && (int)$_SESSION['user']['id'] !== $id) {
            PersonneModel::deleteById($id);
        }

        $q = urlencode($_POST['q'] ?? '');
        $page = (int)($_POST['page'] ?? 1);
        header("Location: /artisphere/?controller=admin_users&action=index&q={$q}&page={$page}&success=delete");
        exit;
    }
}