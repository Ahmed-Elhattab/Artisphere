<?php
require_once __DIR__ . '/../model/evenement_model.php';

class evenement_show_controller extends BaseController
{
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $evenement = EvenementModel::findById($id);
        if (!$evenement) {
            $this->render('not_found.php', [
                'title' => 'Évènement introuvable – Artisphere',
                'pageCss' => 'details-style.css',
                'message' => "Cet évènement n'existe pas (ou a été supprimé)."
            ]);
            return;
        }

        if (!empty($_GET['mode']) && $_GET['mode'] === 'mine') {
            $this->requireOwner((int)$evenement['id_createur']);
        }

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];

            // Sécurité : on accepte seulement les URLs internes
            if (str_contains($url, '/artisphere/')) {
                $_SESSION['previous_url'] = $url;
            }
        }

        $this->render('evenement_show.php', [
            'title' => $evenement['nom'] . ' – Artisphere',
            'pageCss' => 'details-style.css',
            'evenement' => $evenement
        ]);
    }
}