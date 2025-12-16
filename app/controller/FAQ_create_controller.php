<?php
require_once __DIR__ . '/../model/faq_model.php';

class faq_create_controller extends BaseController
{
    public function create(): void
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        // GET → affichage formulaire
        $this->render('faq_create.php', [
            'title' => 'Ajouter une question – FAQ',
            'pageCss' => 'faq_create-style.css'
        ]);
    }

    private function store(): void
    {
        $categorie = trim($_POST['categorie'] ?? '');
        $question  = trim($_POST['question'] ?? '');
        $reponse   = trim($_POST['reponse'] ?? '');

        $errors = [];

        if ($categorie === '') $errors[] = "La catégorie est obligatoire.";
        if ($question === '')  $errors[] = "La question est obligatoire.";
        if ($reponse === '')   $errors[] = "La réponse est obligatoire.";

        if (!empty($errors)) {
            $this->render('faq_create.php', [
                'title' => 'Ajouter une question – FAQ',
                'pageCss' => 'faq_create-style.css',
                'errors' => $errors,
                'old' => compact('categorie', 'question', 'reponse')
            ]);
            return;
        }

        FaqModel::create([
            'categorie' => $categorie,
            'question'  => $question,
            'reponse'   => $reponse
        ]);

        header('Location: /artisphere/?controller=faq_create&action=create&success=1');
        exit;
    }
}