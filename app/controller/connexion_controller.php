<?php
class connexion_controller extends BaseController
{
    public function index(): void
    {
        $this->render('connexion.php', [
            'title' => 'Artisphere – Connexion',
            'pageCss' => 'connexion-style.css'
        ]);
    }
}