<?php
require_once __DIR__ . '/../model/mention_legale_model.php';

class mention_legale_create_controller extends BaseController
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

        // GET => formulaire
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('mention_legale_create.php', [
                'title' => 'Ajouter une mention légale – Artisphere',
                'pageCss' => 'mention_legale_create-style.css',
                'csrf' => $this->csrfToken(),
            ]);
            return;
        }

        // POST => insertion
        $this->checkCsrf();

        $titre = trim($_POST['titre'] ?? '');
        $texte = trim($_POST['texte'] ?? '');

        $errors = [];
        if ($titre === '') $errors[] = "Le titre est obligatoire.";
        if ($texte === '') $errors[] = "Le texte est obligatoire.";

        if (!empty($errors)) {
            $this->render('mention_legale_create.php', [
                'title' => 'Ajouter une mention légale – Artisphere',
                'pageCss' => 'mention_legale_create-style.css',
                'csrf' => $this->csrfToken(),
                'errors' => $errors,
                'old' => compact('titre', 'texte'),
            ]);
            return;
        }

        MentionLegaleModel::create([
            'titre' => $titre,
            'texte' => $texte,
        ]);

        header('Location: /artisphere/?controller=mention_legale_create&action=create&success=1');
        exit;
    }
}