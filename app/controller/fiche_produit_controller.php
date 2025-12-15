<?php


class fiche_produit_controller extends BaseController
{
    public function index(): void
    {
        $this->render('fiche_produit.php', [
            'title' => 'Artisphere - fiche-produit',
            'pageCss' => 'fiche-EetP-style.css'
        ]);
    }
}