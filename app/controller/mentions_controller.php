<?php

class mentions_controller extends BaseController
{
    public function index(): void
    {
        $this->render('mentions.php', [
            'title'   => 'Artisphere – Mentions légales',
            'pageCss' => 'simple-page.css'
        ]);
    }
}
