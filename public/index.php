<?php
/*
IMPORTANT : ce fichier ne sert que de routeur entre les différentes pages du site
*/

$root = dirname(__DIR__);

// core
require_once $root . '/app/core/base_controller.php';
require_once $root . '/app/core/database.php';

// paramètres de l'URL
$controllerParam = $_GET['controller'] ?? 'index';   // ex: index, FAQ…
$action          = $_GET['action'] ?? 'index';       // ex: index, listePersonnes…

// nom de la classe de contrôleur
$controllerClass = $controllerParam . '_controller';     // ex: index_controller
$controllerFile  = $root . '/app/controller/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "Controller $controllerClass introuvable.";
    exit;
}

require_once $controllerFile;

if (!class_exists($controllerClass)) {
    http_response_code(500);
    echo "Classe $controllerClass non trouvée dans $controllerFile.";
    exit;
}

$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo "Action $action introuvable dans $controllerClass.";
    exit;
}

// méthode du contrôleur
$controller->$action();