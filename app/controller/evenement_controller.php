<?php

require_once __DIR__ . '/../model/evenement_model.php';

class evenement_controller extends BaseController
{
    // this function shows the events list page
    public function index(): void
    {
        // number of events to show on one page
        $limit = 8;

        // current events page, for example: ?p_evt=2
        $pEvt = isset($_GET['p_evt']) ? (int)$_GET['p_evt'] : 1;
        if ($pEvt < 1) {
            $pEvt = 1;
        }

        // start position for the SQL query
        $offsetEvt = ($pEvt - 1) * $limit;

        // get events from the database
        $evenements = EvenementModel::listHome($limit, $offsetEvt);

        // total number of events in the database
        $totalEvenements = EvenementModel::countAll();

        // total number of pages for events
        $pagesEvt = max(1, (int)ceil($totalEvenements / $limit));

        // show the events view
        $this->render('evenement.php', [
            'title'      => 'Artisphere – Tous les événements',
            // important: keep the same CSS name used in the old version
            'pageCss'    => 'evenement-style.css',
            'evenements' => $evenements,
            'pEvt'       => $pEvt,
            'pagesEvt'   => $pagesEvt,
        ]);
    }
}

