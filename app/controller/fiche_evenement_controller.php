<?php


class fiche_evenement_controller extends BaseController
{
    public function index(): void
    {
        $this->render('fiche_evenement.php', [
            'title' => 'Artisphere - fiche-evenement',
            'pageCss' => 'fiche-EetP-style.css'
        ]);
    }
}