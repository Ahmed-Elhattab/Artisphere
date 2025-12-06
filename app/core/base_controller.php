<?php


class BaseController
{
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
        require $root . '/view/layout/header.php';
        require $viewPath;
        require $root . '/view/layout/footer.php';
    }
}