<?php

require_once __DIR__ . '/../model/apropos_model.php';

class apropos_controller extends BaseController
{
    public function index(): void
    {
        $chapitres = AproposModel::all();

        $this->render('apropos.php', [
            'title'   => 'Artisphere – À propos',
            'pageCss' => 'simple-page.css',
            'chapitres' => $chapitres
        ]);
    }
}
