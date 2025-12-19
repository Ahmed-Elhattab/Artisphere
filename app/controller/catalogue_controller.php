<?php
class catalogue_controller extends BaseController
{
    public function index(): void
    {
        $this->render('catalogue.php', [
            'title' => 'Artisphere – Catalogue',
            'pageCss' => 'catalogue-style.css'
        ]);
    }
}