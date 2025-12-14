<?php
class type_Compte_controller extends BaseController
{
    public function index(): void
    {
        $this->render('type_Compte.php', [
            'title' => 'Artisphere – Choisir son profil',
            'pageCss' => 'type_Compte-style.css'
        ]);
    }
}