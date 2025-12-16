<?php


class BaseController
{

    public function __construct()
    {
        // Démarre la session UNE SEULE FOIS
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * $view : nom du fichier vue sous app/view (ex : 'home.php', 'FAQ.html')
     * $params : variables passées à la vue (ex : ['title' => 'Accueil'])
     */
    protected function render(string $view, array $params = []): void
    {
        $root = dirname(__DIR__, 1); // dossier app

        // ex : app/view/home.php
        $viewPath = $root . '/view/' . $view;

        if (!file_exists($viewPath)) {
            echo "Vue $view introuvable ($viewPath)";
            return;
        }

        // On rend les variables accessibles dans la vue : $title, $dbMessage, etc.
        extract($params);

        // On inclut le header, puis la vue, puis le footer
        require $root . '/view/layout/header2.php';
        require $viewPath;
        require $root . '/view/layout/footer.php';
    }

    #verifie si un utilisateur est connecter 
    protected function isLogged(): bool
    {
        return !empty($_SESSION['user']);
    }

    #si pas de session en cours, redirige vers la page de connexion
    protected function requireLogin(): void
    {
        if (!$this->isLogged()) {
            header('Location: /artisphere/?controller=connexion&action=index');
            exit;
        }
    }

    protected function requireOwner(int $ownerId): void
    {
        $this->requireLogin();

        if ((int)$_SESSION['user']['id'] !== (int)$ownerId) {
            // 403 simple
            http_response_code(403);
            $this->render('not_found.php', [
                'title' => 'Accès refusé – Artisphere',
                'pageCss' => 'details-style.css',
                'message' => "Accès refusé : vous n’êtes pas autorisé à consulter ce contenu."
            ]);
            exit;
        }
    }

    #l'admin outrepasse le require role
    protected function requireRole(string $role): void
    {
        $this->requireLogin();
        if (($_SESSION['user']['role'] ?? '') !== $role && ($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            $this->render('not_found.php', [
                'title' => 'Accès refusé – Artisphere',
                'pageCss' => 'details-style.css',
                'message' => "Accès refusé."
            ]);
            exit;
        }
    }
}