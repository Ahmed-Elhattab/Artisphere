<?php

// Chemin de base du projet (dossier SITE)
$root = dirname(__DIR__);

// On charge les classes de base (core)
require_once $root . '/app/core/controller.php';
require_once $root . '/app/core/database.php';

// On récupère les paramètres d'URL
// ex : ?controller=FAQ&action=index → FAQ_controller::index()
$controllerParam = $_GET['controller'] ?? 'index';   // par défaut : index_controller
$action          = $_GET['action'] ?? 'index';       // par défaut : méthode index()

// On construit le nom de la classe de contrôleur
// "index"      → "index_controller"
// "FAQ"        → "FAQ_controller"
$controllerClass = $controllerParam . '_controller';
$controllerFile  = $root . '/app/controller/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "Controller $controllerClass introuvable (fichier $controllerFile).";
    exit;
}

require_once $controllerFile;

if (!class_exists($controllerClass)) {
    http_response_code(500);
    echo "Classe $controllerClass introuvable dans $controllerFile.";
    exit;
}

$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo "Action $action introuvable dans le contrôleur $controllerClass.";
    exit;
}

// On appelle l'action demandée
$controller->$action();