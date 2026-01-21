<?php

require_once __DIR__ . '/../model/personne_model.php';

class mot_de_passe_changer_controller extends BaseController
{
    public function index(): void
    {
        $this->render('mot_de_passe_changer.php', [
            'title' => 'Artisphere – Mot_de_passe_changer',
            'pageCss' => 'styles_Thushjan.css'
        ]);
    }

public function update(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        // Test : affiche l'erreur session pour vérifier
        die("Erreur : Vous n'êtes pas connecté (Session vide).");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newPassword = $_POST['new-password'];
        $confirmPassword = $_POST['confirm-password'];

        if ($newPassword !== $confirmPassword) {
            die("Erreur : Les mots de passe ne correspondent pas.");
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        if (PersonneModel::updatePassword($userId, $hash)) {
            header("Location: /artisphere/?controller=profil&action=index&success=1");
            exit();
        } else {
            die("Erreur : La base de données a refusé la mise à jour.");
        }
    }
}
}
