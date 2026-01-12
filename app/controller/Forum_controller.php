<?php

require_once __DIR__ . '/../model/apropos_model.php';

class forum_controller extends BaseController
{
    public function index(): void
    {
        $this->render('forum.php', [
            'title' => 'Artisphere – Forum',
            'pageCss' => 'styles_Thushjan.css'
        ]);
    }

}