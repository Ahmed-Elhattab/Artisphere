<?php

require_once __DIR__ . '/../model/mention_legale_model.php';


class mentions_controller extends BaseController
{
    public function index(): void
    {
        $mentions = MentionLegaleModel::all();

        $this->render('mentions.php', [
            'title'   => 'Artisphere – Mentions légales',
            'pageCss' => 'simple-page.css',
            'mentions' => $mentions
        ]);
    }
}
