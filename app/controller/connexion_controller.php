<?php

require_once __DIR__ . '/../model/personne_model.php';

class connexion_controller extends BaseController
{
    public function index(): void
    {
        $this->render('connexion.php', [
            'title' => 'Artisphere – Connexion',
            'pageCss' => 'connexion2-style.css'
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
                'pageCss' => 'connexion2-style.css',
                'errors'  => $errors
            ]);
            return;
        }

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

        $_SESSION = []; // vide la session

        // optionnel mais recommandé : supprimer le cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header('Location: /artisphere/?controller=index&action=index');
        exit;
    }
}