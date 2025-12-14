<?php
class inscription_Artisans_controller extends BaseController
{
    public function index(): void
    {
        $this->render('inscription_Artisans.php', [
            'title' => 'Artisphere – Création de compte artisan',
            'pageCss' => 'inscription_Artisans-style.css'
        ]);
    }
}