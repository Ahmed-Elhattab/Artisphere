<?php

require_once __DIR__ . '/../model/personne_model.php';

class inscription_Artisans_controller extends BaseController
{
    public function index(): void
    {
        $this->render('inscription_Artisans.php', [
            'title' => 'Artisphere – Création de compte artisan',
            'pageCss' => 'inscription_Artisans-style.css',
            'pageJs'  => 'password_rules.js'
        ]);
    }

    #envoi du formulaire à la table personne de la BD
     public function submit(): void
    {
        //sécurité : on refuse autre chose que POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=inscription_Artisans&action=index');
            exit;
        }

        //le role artisan est géré dans personne_model
        $pseudo   = trim($_POST['username'] ?? '');
        $prenom   = trim($_POST['name'] ?? '');
        $nom      = trim($_POST['last_name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $adresse  = trim($_POST['address'] ?? '');

        $errors = [];

        //validations des règles de création
        //POSSIBILITE D'EN RAJOUTER
        //ATTENTION : SI MODIF, PENSER A CHANGER FICHIER JS ET BOX DES CONTRAINTES SUR MDP
        if ($pseudo === '' || $prenom === '' || $nom === '' || $email === '' || $adresse === '' || $password === '' || $confirm === '') {
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

        if (empty($_POST['accept_terms'])) {
            $errors[] = "Vous devez accepter les conditions d’utilisation du site.";
        }

        //si erreurs, retour au formulaire
        if (!empty($errors)) {
            $this->render('inscription_Artisans.php', [
                'title'   => 'Artisphere – Création de compte artisan',
                'pageCss' => 'inscription_Artisans-style.css',
                'pageJs'  => 'password_rules.js',
                'errors'  => $errors,
                'old'     => compact('pseudo','prenom','nom','email','adresse')
            ]);
            return;
        }

        //Hashing du mdp -> permet sécurité et chiffrement du mdp
        $hash = password_hash($password, PASSWORD_DEFAULT);

        //envoi les infos au modèle qui gère la table Personne, en spécifiant qu'il s'agit d'un artisan
        $id = PersonneModel::createPersonne([
            'pseudo'   => $pseudo,
            'prenom'   => $prenom,
            'nom'      => $nom,
            'email'    => $email,
            'mdp_hash' => $hash,
            'adresse'  => $adresse
        ], 'artisan');

        $user = PersonneModel::findById($id);

        // Connexion automatique
        $_SESSION['user'] = [
            'id'     => (int)$user['id_personne'],
            'pseudo' => $user['pseudo'],
            'nom'    => $user['nom'],
            'prenom' => $user['prenom'],
            'email'  => $user['email'],
            'role'   => $user['role'],
            'adresse'=> $user['adresse'] ?? null,
            'avatar' => $user['avatar'] ?? null,
        ];

        header('Location: /artisphere/?controller=index&action=index&welcome=1');
        exit;
        }
}