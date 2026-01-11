<?php
require_once __DIR__ . '/../model/evenement_model.php';

class evenement_controller extends BaseController
{
    public function index(): void
    {
        $filters = [
            // type = id_type (int) ou '' si pas filtré
            'type' => isset($_GET['type']) && $_GET['type'] !== '' ? (int)$_GET['type'] : '',
            'q' => trim($_GET['q'] ?? ''),
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'in_stock' => !empty($_GET['in_stock']) ? 1 : 0,
        ];

        $events = EvenementModel::search($filters);
        $types = EvenementModel::listTypes(); // [{id_type, nom}, ...]

        $this->render('evenement.php', [
            'title' => 'Évènements – Artisphere',
            'pageCss' => 'evenement-style.css',
            'events' => $events,
            'types' => $types,
            'filters' => $filters,
        ]);
    }
}