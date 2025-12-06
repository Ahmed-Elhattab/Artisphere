<?php



class index_controller extends BaseController
{
    public function index(): void
    {
        // Test de connexion à la BD
        try {
            $pdo = Database::getConnection();
            $dbMessage = "Connexion BDD OK ";
        } catch (PDOException $e) {
            $dbMessage = "Erreur de connexion : " . $e->getMessage();
        }

        // On affiche la vue home.php avec un titre et le message de BDD
        $this->render('home.php', [
            'title'     => 'Accueil - Artisphere',
            'dbMessage' => $dbMessage,
        ]);
    }
}