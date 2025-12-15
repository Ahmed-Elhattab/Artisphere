<?php

require_once __DIR__ . '/../model/faq_model.php';

class FAQ_controller extends BaseController
{
    public function index(): void
    {
        $faqByCat = FaqModel::getAllGroupedByCategorie();

        $this->render('FAQ.php', [
            'title' => 'Artisphere - FAQ',
            'pageCss' => 'FAQ-style.css',
            'faqByCat'=> $faqByCat
        ]);
    }
}