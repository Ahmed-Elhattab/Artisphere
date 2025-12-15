<?php
class profil_controller extends BaseController
{
    public function index(): void
    {
        // Demo: allow setting role via URL for quick tests (ex: ?controller=profil&action=index&role=artisan)
        /*if (!empty($_GET['role'])) {
            $r = strtolower(trim((string)$_GET['role']));
            if (in_array($r, ['client', 'artisan', 'admin'], true)) {
                $_SESSION['role'] = $r;
            }
        }

        // If no role is chosen yet, go to the type selection page.
        if (empty($_SESSION['role'])) {
            header('Location: ?controller=type_Compte&action=index');
            exit;
        }*/

        $this->render('profil.php', [
            'title' => 'Artisphere – Profil',
            'pageCss' => 'profil.css',
            //'role'    => $_SESSION['role']
        ]);
    }
}