<?php
require_once __DIR__ . '/../model/specialite_model.php';
require_once __DIR__ . '/../model/artisan_model.php';

class artisans_controller extends BaseController
{
    public function index(): void
    {
        $filters = [
            'id_specialite' => (int)($_GET['spec'] ?? 0),
            'q' => trim($_GET['q'] ?? ''),
            'min_note' => $_GET['min_note'] ?? '',
            'sort' => $_GET['sort'] ?? 'best',
        ];

        $specialites = SpecialiteModel::all();
        $artisans = ArtisanModel::search($filters);

        $this->render('artisans.php', [
            'title' => 'Artisans – Artisphere',
            'pageCss' => 'artisans-style.css',
            'specialites' => $specialites,
            'artisans' => $artisans,
            'filters' => $filters,
        ]);
    }
}