<?php

require_once __DIR__ . '/../model/personne_model.php';

class inscription_Client_controller extends BaseController
{
    public function index(): void
    {
        $this->render('inscription_Client.php', [
            'title' => 'Artisphere – Création de compte client',
            'pageCss' => 'inscription_Client-style.css',
            'pageJs'  => 'password_rules.js'
        ]);
    }

    #envoi du formulaire à la table personne de la BD
     public function submit(): void
    {
        //sécurité : on refuse autre chose que POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=inscription_Client&action=index');
            exit;
        }

        //le role artisan est géré dans personne_model
        $pseudo   = trim($_POST['username'] ?? '');
        $prenom   = trim($_POST['name'] ?? '');
        $nom      = trim($_POST['last_name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $errors = [];

        //validations des règles de création
        //POSSIBILITE D'EN RAJOUTER
        //ATTENTION : SI MODIF, PENSER A CHANGER FICHIER JS ET BOX DES CONTRAINTES SUR MDP
        if ($pseudo === '' || $prenom === '' || $nom === '' || $email === '' || $password === '' || $confirm === '') {
            $errors[] = "Tous les champs sont obligatoires.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse email invalide.";
        }

        if ($password !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        if (strlen($password) < 4) {
            $errors[] = "Le mot de passe doit contenir au moins 4 caractères.";
        }

        if (PersonneModel::emailExists($email)) {
            $errors[] = "Cette adresse email est déjà utilisée.";
        }

        if (PersonneModel::pseudoExists($pseudo)) {
            $errors[] = "Cet identifiant est déjà utilisé.";
        }

        //si erreurs, retour au formulaire
        if (!empty($errors)) {
            $this->render('inscription_Client.php', [
                'title'   => 'Artisphere – Création de compte client',
                'pageCss' => 'inscription_Client-style.css',
                'pageJs'  => 'password_rules.js',
                'errors'  => $errors,
                'old'     => compact('pseudo','prenom','nom','email')
            ]);
            return;
        }

        //Hashing du mdp -> permet sécurité et chiffrement du mdp
        $hash = password_hash($password, PASSWORD_DEFAULT);

        //envoi les infos au modèle qui gère la table Personne, en spécifiant qu'il s'agit d'un artisan
        PersonneModel::createPersonne([
            'pseudo'   => $pseudo,
            'prenom'   => $prenom,
            'nom'      => $nom,
            'email'    => $email,
            'mdp_hash' => $hash,
        ], 'client');

        // Redirection post-succès (PRG pattern)
        header('Location: /artisphere/?controller=index&action=index&success=1');
        exit;
    }
}