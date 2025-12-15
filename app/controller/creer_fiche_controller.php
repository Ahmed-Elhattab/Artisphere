<?php


class creer_fiche_controller extends BaseController
{
    public function index(): void
    {
        $this->render('creer_fiche.php', [
            'title' => 'Artisphere - creation de fiche',
            'pageCss' => 'creer_fiche-style.css'
        ]);
    }
}