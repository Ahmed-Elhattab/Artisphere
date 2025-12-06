<?php


class FAQ_controller extends Controller
{
    public function index(): void
    {
        $this->render('FAQ.html', [
            'title' => 'FAQ - Artisphere',
        ]);
    }
}