<?php
require_once __DIR__ . '/../model/apropos_model.php';

class apropos_create_controller extends BaseController
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

    public function create(): void
    {
        $this->requireRole('admin');

        // GET => affichage
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('apropos_create.php', [
                'title' => 'Ajouter un chapitre – À propos',
                'pageCss' => 'apropos_create-style.css',
                'csrf' => $this->csrfToken(),
            ]);
            return;
        }

        // POST => insertion
        $this->checkCsrf();

        $chapitre = trim($_POST['chapitre'] ?? '');
        $contenu  = trim($_POST['contenu'] ?? '');

        $errors = [];
        if ($chapitre === '') $errors[] = "Le titre (chapitre) est obligatoire.";
        if ($contenu === '')  $errors[] = "Le contenu du chapitre est obligatoire.";

        if (!empty($errors)) {
            $this->render('apropos_create.php', [
                'title' => 'Ajouter un chapitre – À propos',
                'pageCss' => 'apropos_create-style.css',
                'csrf' => $this->csrfToken(),
                'errors' => $errors,
                'old' => compact('chapitre', 'contenu'),
            ]);
            return;
        }

        AproposModel::create([
            'chapitre' => $chapitre,
            'contenu' => $contenu,
        ]);

        header('Location: /artisphere/?controller=apropos_create&action=create&success=1');
        exit;
    }
    
}
