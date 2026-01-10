<?php

require_once __DIR__ . '/../model/personne_model.php';

class connexion_controller extends BaseController
{
    public function index(): void
    {
        $this->render('connexion.php', [
            'title' => 'Artisphere – Connexion',
            'pageCss' => 'connexion-style.css'
        ]);
    }

    //envoi le formulaire au modèle personne (qui gère connexion avec BD)
    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=connexion&action=index');
            exit;
        }

        $identifier = trim($_POST['username'] ?? '');
        $password   = $_POST['password'] ?? '';

        $errors = [];

        if ($identifier === '' || $password === '') {
            $errors[] = "Identifiant et mot de passe requis.";
        }

        $user = PersonneModel::findByPseudoOrEmail($identifier);

        if (!$user || !password_verify($password, $user['mdp'])) {
            $errors[] = "Identifiant ou mot de passe incorrect.";
        }else {
            if ($user['validationStatus'] === 'en attente') {
                $errors[] = "Compte en attente de validation par un administrateur.";
            }
        }

        if (!empty($errors)) {
            $this->render('connexion.php', [
                'title'   => 'Artisphere – Connexion',
                'pageCss' => 'connexion-style.css',
                'errors'  => $errors
            ]);
            return;
        }

        // connexion réussie = démarrage de la session
        session_start();

        $_SESSION['user'] = [
            'id'     => $user['id_personne'],
            'pseudo' => $user['pseudo'],
            'prenom' => $user['prenom'],
            'nom' => $user['nom'],
            'role'   => $user['role'],
            'email'   => $user['email'],
            'adresse'=> $user['adresse'] ?? null,
            'avatar' => $user['avatar'] ?? null,
        ];

        header('Location: /artisphere/?controller=index&action=index');
        exit;
    }

    #deconnexion de l'utilisateur
    public function logout(): void
    {
        session_start();
        session_destroy();

        header('Location: /artisphere/?controller=index&action=index');
        exit;
    }
}