<?php
class tous_les_evenements_controller extends BaseController
{
    public function index(): void
    {
        $this->render('tous_les_evenements.php', [
            'title' => 'Artisphere – Tous les événements',
            'pageCss' => 'tous_les_evenements-style.css'
        ]);
    }
}