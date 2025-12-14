<?php
class avis_controller extends BaseController
{
    public function index(): void
    {
        $this->render('avis.php', [
            'title' => 'Artisphere – Avis Produits',
            'pageCss' => 'avis.css'
        ]);
    }
}