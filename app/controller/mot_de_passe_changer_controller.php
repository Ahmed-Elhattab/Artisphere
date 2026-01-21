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

public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['new-password'];
            $confirmPassword = $_POST['confirm-password'];

            // 1. Vérification de correspondance
            if ($newPassword !== $confirmPassword) {
                die("Erreur : Les mots de passe ne correspondent pas.");
            }

            // 2. Récupération de l'ID utilisateur (via la session)
            if (session_status() === PHP_SESSION_NONE) session_start();
            $userId = $_SESSION['user_id'] ?? null;

            if ($userId) {
                // 3. Hachage du mot de passe
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);

                // 4. Mise à jour via le modèle
                if (PersonneModel::updatePassword($userId, $hash)) {
                    // Redirection avec succès
                    header("Location: /artisphere/?controller=profil&action=index&success=1");
                    exit();
                }
            }
            die("Erreur lors de la mise à jour.");
        }
    }
}
