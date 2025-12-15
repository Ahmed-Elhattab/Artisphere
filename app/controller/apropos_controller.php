<?php

class apropos_controller extends BaseController
{
    public function index(): void
    {
        $this->render('apropos.php', [
            'title'   => 'Artisphere – À propos',
            'pageCss' => 'simple-page.css'
        ]);
    }
}
