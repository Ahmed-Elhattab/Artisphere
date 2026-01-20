<?php

require_once __DIR__ . '/../model/apropos_model.php';

class Forum_contenu_sujet_controller extends BaseController
{
    public function index(): void
    {
        $this->render('Forum_contenu_sujet.php', [
            'title' => 'Artisphere – Forum Contenu Sujet',
            'pageCss' => 'styles_Thushjan.css'
        ]);
    }

}