<?php
require_once __DIR__ . '/../model/contact_model.php';

class admin_contact_controller extends BaseController
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
        $etat = trim($_GET['etat'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));

        $limit = 5;
        $offset = ($page - 1) * $limit;

        $total = ContactModel::count($q, $etat);
        $contacts = ContactModel::search($q, $etat, $limit, $offset);
        $pages = max(1, (int)ceil($total / $limit));

        $this->render('admin_contact.php', [
            'title' => 'Demandes de contact – Artisphere',
            'pageCss' => 'admin_contact-style.css',
            'contacts' => $contacts,
            'q' => $q,
            'etat' => $etat,
            'page' => $page,
            'pages' => $pages,
            'csrf' => $this->csrfToken(),
        ]);
    }

    public function updateEtat(): void
    {
        $this->requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=admin_contact&action=index');
            exit;
        }
        $this->checkCsrf();

        $id = (int)($_POST['id_contact'] ?? 0);
        $etat = trim($_POST['etat'] ?? '');

        if ($id > 0) {
            ContactModel::updateEtat($id, $etat);
        }

        $q = urlencode($_POST['q'] ?? '');
        $etatFilter = urlencode($_POST['etatFilter'] ?? '');
        $page = (int)($_POST['page'] ?? 1);

        header("Location: /artisphere/?controller=admin_contact&action=index&q={$q}&etat={$etatFilter}&page={$page}&success=1");
        exit;
    }
}