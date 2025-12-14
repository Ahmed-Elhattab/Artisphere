<?php


class FAQ_controller extends BaseController
{
    public function index(): void
    {
        $this->render('FAQ.php', [
            'title' => 'FAQ - Artisphere',
        ]);
    }
}