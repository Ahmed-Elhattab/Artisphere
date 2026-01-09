<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. On récupère l'email (vérifie bien que l'input s'appelle 'email' dans ton HTML)
    $email = htmlspecialchars($_POST['email'] ?? '');

    // On utilise l'URL du routeur PHP
    header("Location: /artisphere/?controller=connexion&action=index&msg=sent");
    exit;
}
?>