<?php


class FAQ_controller extends BaseController
{
    public function index(): void
    {
        $this->render('FAQ.html', [
            'title' => 'FAQ - Artisphere',
        ]);
    }
}