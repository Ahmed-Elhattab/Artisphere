<?php
class type_Compte_controller extends BaseController
{
    public function index(): void
    {
        $this->render('type_Compte.php', [
            'title' => 'Artisphere – Choisir son profil',
            'pageCss' => 'type_Compte2-style.css'
        ]);
    }

    /**
     * Choix du type de compte (client / artisan) : on le stocke en session.
     * Pour le projet, cela permet ensuite d'afficher les bons boutons (profil artisan, page créer fiche, etc.)
     */
    /*public function choose(): void
    {
        $type = $_GET['type'] ?? '';
        $type = strtolower(trim($type));

        if (!in_array($type, ['client', 'artisan'], true)) {
            header('Location: ?controller=type_Compte&action=index');
            exit;
        }

        $_SESSION['role'] = ($type === 'artisan') ? 'artisan' : 'client';

        // Redirection vers l'inscription correspondante (UI existante)
        if ($type === 'artisan') {
            header('Location: ?controller=inscription_Artisans&action=index');
        } else {
            header('Location: ?controller=inscription_Client&action=index');
        }
        exit;
    }*/
}