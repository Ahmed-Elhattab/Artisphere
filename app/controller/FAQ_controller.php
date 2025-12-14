<?php


class FAQ_controller extends BaseController
{
    public function index(): void
    {
        $this->render('FAQ.php', [
            'title' => 'Artisphere - FAQ',
            'pageCss' => 'FAQ-style.css'
        ]);
    }
}