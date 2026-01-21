<?php

require_once __DIR__ . '/../model/personne_model.php';

class mot_de_passe_changer_controller extends BaseController
{
    public function index(): void
    {
        $this->render('mot_de_passe_changer.php', [
            'title' => 'Artisphere – Mot_de_passe_changer',
            'pageCss' => 'styles_Thushjan.css'
        ]);
    }

}
