<?php

require_once __DIR__ . '/../model/categorie_model.php';
require_once __DIR__ . '/../model/produit_model.php';
class fiche_produit_controller extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();

        $categories = CategorieModel::all();

        $this->render('fiche_produit.php', [
            'title' => 'Artisphere - fiche-produit',
            'pageCss' => 'fiche-produit-style.css',
            'pageJs'  => 'produit_image_preview.js',
            'categories'=> $categories
        ]);
    }

    public function submit(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /artisphere/?controller=fiche_produit&action=index');
        exit;
        }

        $categories = CategorieModel::all();

        $nom = trim($_POST['nom'] ?? '');
        $quantite = (int)($_POST['quantite'] ?? -1);
        $materiaux = trim($_POST['materiaux'] ?? '');
        $prix = (float)($_POST['prix'] ?? -1);
        $description = trim($_POST['description'] ?? '');
        $id_categorie = (int)($_POST['id_categorie'] ?? 0);
        $id_createur = (int)($_SESSION['user']['id'] ?? 0);

        $errors = [];
        if ($nom === '') $errors[] = "Le nom du produit est obligatoire.";
        if ($quantite < 0) $errors[] = "La quantité doit être positive.";
        if ($materiaux === '') $errors[] = "Les matériaux sont obligatoires.";
        if ($prix < 0) $errors[] = "Le prix doit être positif.";
        if ($description === '') $errors[] = "La description est obligatoire.";
        if ($id_categorie <= 0) $errors[] = "Veuillez choisir une catégorie.";

        // Vérif que la catégorie existe (FK)
        $catIds = array_map(fn($c) => (int)$c['id_categorie'], $categories);
        if ($id_categorie > 0 && !in_array($id_categorie, $catIds, true)) {
        $errors[] = "Catégorie invalide.";
        }

        // Upload image
        $filename = null;
        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Veuillez ajouter une image valide.";
        } else {
        $maxSize = 3 * 1024 * 1024; // 3 Mo
        if ($_FILES['image']['size'] > $maxSize) $errors[] = "Image trop lourde (max 3 Mo).";

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['image']['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($allowed[$mime])) $errors[] = "Format image non accepté (JPG/PNG/WEBP).";
        }

        if (!empty($errors)) {
        $this->render('fiche_produit.php', [
            'title' => 'Artisphere - fiche-produit',
            'pageCss' => 'fiche-produit-style.css',
            'pageJs'  => 'produit_image_preview.js',
            'categories' => $categories,
            'errors' => $errors,
            'old' => [
            'nom' => $nom,
            'quantite' => $quantite >= 0 ? (string)$quantite : '',
            'materiaux' => $materiaux,
            'prix' => $prix >= 0 ? (string)$prix : '',
            'description' => $description,
            'id_categorie' => $id_categorie ?: '',
            ],
        ]);
        return;
        }

        //sauvegarde dans fichiers
        $ext = $allowed[$mime];
        $filename = 'p' . $id_createur . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

        $destDir = dirname(__DIR__, 2) . '/public/images/produits/';
        if (!is_dir($destDir)) mkdir($destDir, 0775, true);

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $destDir . $filename)) {
        $this->render('fiche_produit.php', [
            'title' => 'Artisphere - fiche-produit',
            'pageCss' => 'fiche-produit-style.css',
            'pageJs'  => 'produit_image_preview.js',
            'categories' => $categories,
            'errors' => ["Erreur lors de l'enregistrement de l'image."],
        ]);
        return;
        }

        // Insertion DB (image = nom de fichier)
        ProduitModel::create([
        'nom' => $nom,
        'image' => $filename,
        'quantite' => $quantite,
        'materiaux' => $materiaux,
        'prix' => $prix,
        'id_createur' => $id_createur,
        'description' => $description,
        'id_categorie' => $id_categorie,
        ]);

        header('Location: /artisphere/?controller=fiche_produit&action=index&success=1');
        exit;
    }

    
}