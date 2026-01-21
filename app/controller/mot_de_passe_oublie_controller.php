<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../model/personne_model.php';

class mot_de_passe_oublie_controller extends BaseController
{
    public function index(): void
    {
        $this->render('mot_de_passe_oublie.php', [
            'title' => 'Artisphere – Mot_de_passe_oublie',
            'pageCss' => 'styles_Thushjan.css'
        ]);
    }

public function nouveau(): void
{
    // Cette fonction affiche la page pour saisir le nouveau mot de passe
    $this->render('mot_de_passe_changer.php', [
        'title' => 'Artisphere – Mot de passe changer',
        'pageCss' => 'styles_Thushjan.css'
    ]);
}

    public function envoyer(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email_dest = htmlspecialchars($_POST['email'] ?? '');  

        // --- ÉTAPE CRUCIALE : Trouver l'utilisateur pour avoir son ID ---
        $user = PersonneModel::findByPseudoOrEmail($email_dest);

        if ($user) {
            $userId = $user['id_personne']; // On récupère l'ID
            
            require __DIR__ . '/../../libs/PHPMailer/Exception.php';
            require __DIR__ . '/../../libs/PHPMailer/PHPMailer.php';
            require __DIR__ . '/../../libs/PHPMailer/SMTP.php';

            $mail = new PHPMailer(true);

            try {
                // Configuration Mailtrap
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Port       = 587;
                $mail->Username   = 'thushjan9@gmail.com'; 
                $mail->Password   = 'acik kzjw daik sqdb'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

                // Destinataires
                $mail->setFrom('thushjan9@gmail.com', 'Artisphere');
                $mail->addAddress($email_dest);

                // Contenu
                $mail->isHTML(true);
                $mail->Subject = 'Reinitialisation de votre mot de passe';
                $lien = "http://localhost/artisphere/?controller=mot_de_passe_oublie&action=nouveau&id=" . $userId;
                $mail->Body = "Cliquez ici pour changer votre mot de passe : <a href='$lien'>Lien de réinitialisation</a>";
                $mail->send();
            } catch (Exception $e) {
                
            }

            // Redirection vers la page de connexion 
            header("Location: ?controller=connexion&action=index&status=success");
            exit();
        }
    }
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $newPassword = $_POST['new-password'] ?? '';
            $confirmPassword = $_POST['confirm-password'] ?? '';

            if ($userId && $newPassword === $confirmPassword && !empty($newPassword)) {
                // On hache le mot de passe
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);

                // On appelle la fonction de ton modèle (qui est correcte !)
                if (PersonneModel::updatePassword((int)$userId, $hash)) {
                    // Succès : on redirige vers la connexion
                    header("Location: /artisphere/?controller=connexion&action=index&msg=success_pwd");
                    exit();
                }
            }
            // Si ça échoue
            die("Erreur : Les mots de passe ne correspondent pas ou l'ID est invalide.");
        }
    }

}