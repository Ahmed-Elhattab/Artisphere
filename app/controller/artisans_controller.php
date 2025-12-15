<?php


class artisans_controller extends BaseController
{
    public function index(): void
    {
        $this->render('artisans.php', [
            'title' => 'Artisphere – Tous les artisans',
            'pageCss' => 'artisans-style.css'
        ]);
    }
}