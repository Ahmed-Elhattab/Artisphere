<?php
class evenement_controller extends BaseController
{
    public function index(): void
    {
        $this->render('evenement.php', [
            'title' => 'Artisphere – Tous les événements',
            'pageCss' => 'evenement-style.css'
        ]);
    }
}