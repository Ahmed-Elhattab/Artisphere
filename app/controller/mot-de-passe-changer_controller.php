<?php

require_once __DIR__ . '/../model/personne_model.php';

class connexion_controller extends BaseController
{
    public function index(): void
    {
        $this->render('mot-de-passe-changer.php', [
            'title' => 'Artisphere – Mot-de-passe-changer',
            'pageCss' => 'style_Thushjan.css'
        ]);
    }

}