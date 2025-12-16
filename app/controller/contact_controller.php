<?php

require_once __DIR__ . '/../model/contact_model.php';

class contact_controller extends BaseController
{
    public function index(): void
    {
        $this->render('contact.php', [
            'title'   => 'Artisphere – Contact',
            'pageCss' => 'contact-style.css',
            'pageJs'  => ['contact.js']
        ]);
    }

    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=contact&action=index');
            exit;
        }

        $nom     = trim($_POST['nom'] ?? '');
        $prenom  = trim($_POST['prenom'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];

        if ($nom === '' || $prenom === '' || $email === '' || $message === '') {
            $errors[] = "Tous les champs sont obligatoires.";
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse email invalide.";
        }
        if (mb_strlen($message) > 4000) {
            $errors[] = "Message trop long (max 4000 caractères).";
        }

        if (!empty($errors)) {
            $this->render('contact.php', [
                'title'   => 'Nous contacter – Artisphere',
                'pageCss' => 'contact-style.css',
                'errors'  => $errors,
                'old'     => [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'message' => $message,
                ],
            ]);
            return;
        }

        ContactModel::create([
            'firstName' => $prenom,
            'lastName'  => $nom,
            'email'     => $email,
            'message'   => $message,
            'etat'      => 'nouveau',
        ]);

        // PRG : Post -> Redirect -> Get
        header('Location: /artisphere/?controller=contact&action=index&success=1');
        exit;
    }
}
