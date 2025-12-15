<?php

class contact_controller extends BaseController
{
    public function index(): void
    {
        $this->render('contact.php', [
            'title'   => 'Artisphere – Contact',
            'pageCss' => 'contact-style.css',
            'pageJs'  => ['contact.js']
        ]);
    }
}
