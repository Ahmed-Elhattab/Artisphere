<?php
require_once __DIR__ . '/../model/produit_model.php';
require_once __DIR__ . '/../model/categorie_model.php';

class produit_update_controller extends BaseController
{
    private function ensureCsrf(): void
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }
    }

    private function checkCsrf(): void
    {
        $token = $_POST['csrf'] ?? '';
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF');
        }
    }

    public function edit(): void
    {
        $this->requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        $produit = ($id > 0) ? ProduitModel::findById($id) : null;

        if (!$produit) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        // Autorisation : seul le créateur peut éditer
        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        if ($userId !== (int)$produit['id_createur']) {
            header('Location: /artisphere/?controller=produit_show&action=show&id=' . $id);
            exit;
        }

        $categories = CategorieModel::all();
        $this->ensureCsrf();

        $this->render('produit_update.php', [
            'title' => 'Éditer le produit – Artisphere',
            'pageCss' => 'produit_update-style.css',
            'produit' => $produit,
            'categories' => $categories,
            'csrf' => $_SESSION['csrf'],
        ]);
    }

    public function update(): void
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }
        $this->checkCsrf();

        $idProduit = (int)($_POST['id_produit'] ?? 0);
        $produit = ($idProduit > 0) ? ProduitModel::findById($idProduit) : null;
        if (!$produit) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        if ($userId !== (int)$produit['id_createur']) {
            header('Location: /artisphere/?controller=produit_show&action=show&id=' . $idProduit);
            exit;
        }

        $nom = trim($_POST['nom'] ?? '');
        $quantite = (int)($_POST['quantite'] ?? 0);
        $materiaux = trim($_POST['materiaux'] ?? '');
        $prix = (float)($_POST['prix'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $idCategorie = (int)($_POST['id_categorie'] ?? 0);

        $errors = [];
        if ($nom === '') $errors[] = "Le nom est obligatoire.";
        if ($quantite < 0) $errors[] = "La quantité ne peut pas être négative.";
        if ($materiaux === '') $errors[] = "Les matériaux sont obligatoires.";
        if ($prix < 0) $errors[] = "Le prix ne peut pas être négatif.";
        if ($description === '') $errors[] = "La description est obligatoire.";
        if ($idCategorie <= 0) $errors[] = "Veuillez choisir une catégorie.";

        // Gestion image : si pas d’upload => garder l’ancienne
        $imageName = $produit['image'];

        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Erreur lors de l’upload de l’image.";
            } else {
                $allowed = ['image/jpeg','image/png','image/webp'];
                if (!in_array(mime_content_type($file['tmp_name']), $allowed, true)) {
                    $errors[] = "Format d’image non supporté (jpg/png/webp uniquement).";
                } else {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $imageName = uniqid('prod_', true) . '.' . strtolower($ext);

                    $destDir = __DIR__ . '/../../public/images/produits/';
                    if (!is_dir($destDir)) mkdir($destDir, 0777, true);

                    $destPath = $destDir . $imageName;
                    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                        $errors[] = "Impossible d’enregistrer l’image sur le serveur.";
                    }
                }
            }
        }

        $categories = CategorieModel::all();

        if (!empty($errors)) {
            // réaffiche le form avec valeurs saisies
            $this->render('produit_update.php', [
                'title' => 'Éditer le produit – Artisphere',
                'pageCss' => 'produit_update-style.css',
                'errors' => $errors,
                'csrf' => $_SESSION['csrf'],
                'categories' => $categories,
                'produit' => array_merge($produit, [
                    'nom' => $nom,
                    'quantite' => $quantite,
                    'materiaux' => $materiaux,
                    'prix' => $prix,
                    'description' => $description,
                    'id_categorie' => $idCategorie,
                    'image' => $imageName,
                ]),
            ]);
            return;
        }

        ProduitModel::updateProduit($idProduit, $userId, [
            'nom' => $nom,
            'image' => $imageName,
            'quantite' => $quantite,
            'materiaux' => $materiaux,
            'prix' => $prix,
            'description' => $description,
            'id_categorie' => $idCategorie,
        ]);

        header('Location: /artisphere/?controller=produit_show&action=show&id=' . $idProduit . '&updated=1');
        exit;
    }
}