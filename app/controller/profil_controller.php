<?php
class profil_controller extends BaseController
{
    public function index(): void
    {
        $this->render('profil.php', [
            'title' => 'Artisphere – Profil',
            'pageCss' => 'profil.css'
        ]);
    }
}