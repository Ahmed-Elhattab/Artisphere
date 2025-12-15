<?php
class inscription_Client_controller extends BaseController
{
    public function index(): void
    {
        $this->render('inscription_Client.php', [
            'title' => 'Artisphere – Création de compte client',
            'pageCss' => 'inscription_Client-style.css'
        ]);
    }
}