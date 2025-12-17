<?php

require_once __DIR__ . '/../model/personne_model.php';

class profil_edit_controller extends BaseController
{
    public function edit(): void
    {
        $this->requireLogin();

        $user = $_SESSION['user'];

        $this->render('profil_edit.php', [
            'title'   => 'Éditer mon profil – Artisphere',
            'pageCss' => 'profil_edit-style.css',
            'old' => [
                'nom'    => $user['nom'] ?? '',
                'prenom' => $user['prenom'] ?? '',
                'pseudo' => $user['pseudo'] ?? '',
                'email'  => $user['email'] ?? '',
                'adresse'=> $user['adresse'] ?? '',
            ],
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=profil_edit&action=edit');
            exit;
        }

        $id = (int)$_SESSION['user']['id'];
        $role = $_SESSION['user']['role'] ?? 'client';


        $nom     = trim($_POST['nom'] ?? '');
        $prenom  = trim($_POST['prenom'] ?? '');
        $pseudo  = trim($_POST['pseudo'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        // Si pas artisan => adresse = NULL (peu importe ce que le form envoie)
        $adresse = ($role === 'artisan') ? trim($_POST['adresse'] ?? '') : null;

        $errors = [];
        if ($nom === '')    $errors[] = "Le nom est obligatoire.";
        if ($prenom === '') $errors[] = "Le prénom est obligatoire.";
        if ($pseudo === '') $errors[] = "Le pseudo est obligatoire.";
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse email invalide.";
        }
        if (PersonneModel::emailExistsEditionMode($email, $id)) {
            $errors[] = "Cet email est déjà utilisé.";
        }
        if (PersonneModel::pseudoExistsEditionMode($pseudo, $id)) {
            $errors[] = "Ce pseudo est déjà utilisé.";
        }

        //adresse obligatoire seulement artisan
        if ($role === 'artisan' && ($adresse === null || $adresse === '')) {
            $errors[] = "L'adresse est obligatoire pour un artisan.";
        }

        if (!empty($errors)) {
            $this->render('profil_edit.php', [
                'title'   => 'Éditer mon profil – Artisphere',
                'pageCss' => 'profil_edit-style.css',
                'errors'  => $errors,
                'old'     => compact('nom','prenom','pseudo','email','adresse'),
            ]);
            return;
        }

        PersonneModel::updateProfile($id, [
            'nom' => $nom,
            'prenom' => $prenom,
            'pseudo' => $pseudo,
            'email' => $email,
            'adresse' => $adresse,
        ]);

        // Synchroniser la session (super important)
        $_SESSION['user']['nom'] = $nom;
        $_SESSION['user']['prenom'] = $prenom;
        $_SESSION['user']['pseudo'] = $pseudo;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['adresse'] = $adresse;

        header('Location: /artisphere/?controller=profil&action=index&updated=1');
        exit;
    }
}