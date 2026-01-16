<?php

require_once __DIR__ . '/../model/categorie_model.php';
require_once __DIR__ . '/../model/produit_model.php';
require_once __DIR__ . '/../model/produit_image_model.php';


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

        // Upload images
        $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        ];
        $maxSize = 3 * 1024 * 1024; // 3 Mo par image
        $uploadedNames = [];

        $files = $_FILES['images'] ?? null;

        if (!$files || empty($files['name']) || !is_array($files['name']) || count(array_filter($files['name'])) === 0) {
        $errors[] = "Veuillez ajouter au moins une image valide.";
        } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        // (optionnel) limite
        $count = count($files['name']);
        if ($count > 6) $errors[] = "Maximum 6 images.";

        for ($i = 0; $i < $count; $i++) {
            if (empty($files['name'][$i])) continue;

            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur d’upload sur une des images.";
            continue;
            }

            if ($files['size'][$i] > $maxSize) {
            $errors[] = "Une image dépasse 3 Mo.";
            continue;
            }

            $mime = $finfo->file($files['tmp_name'][$i]);
            if (!isset($allowed[$mime])) {
            $errors[] = "Format non accepté (JPG/PNG/WEBP).";
            continue;
            }
        }
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

        // Transaction : DB + fichiers
        $pdo = Database::getConnection();
        $destDir = dirname(__DIR__, 2) . '/public/images/produits/';
        if (!is_dir($destDir)) mkdir($destDir, 0775, true);

        try {
        $pdo->beginTransaction();

        // 1) Insert produit (on garde image principale = première image après upload)
        $idProduit = ProduitModel::create([
            'nom' => $nom,
            'image' => null, // on mettra la première après upload
            'quantite' => $quantite,
            'materiaux' => $materiaux,
            'prix' => $prix,
            'id_createur' => $id_createur,
            'description' => $description,
            'id_categorie' => $id_categorie,
        ]);

        // 2) Upload fichiers
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if (empty($files['name'][$i])) continue;

            $tmp = $files['tmp_name'][$i];
            $mime = $finfo->file($tmp);
            if (!isset($allowed[$mime])) continue;

            $ext = $allowed[$mime];
            $fn = 'p' . $id_createur . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

            if (!move_uploaded_file($tmp, $destDir . $fn)) {
            throw new RuntimeException("Erreur lors de l'enregistrement d'une image.");
            }
            $uploadedNames[] = $fn;
        }

        if (empty($uploadedNames)) {
            throw new RuntimeException("Aucune image n'a été enregistrée.");
        }

        // 3) Insert en table enfant
        ProduitImageModel::insertMany($idProduit, $uploadedNames);

        // 4) (fallback) on remplit pproduit.image avec la première image (comme avant)
        // => ajoute une méthode dans ProduitModel OU fais un UPDATE ici
        $stmt = $pdo->prepare("UPDATE pproduit SET image = :img WHERE id_produit = :id");
        $stmt->execute([':img' => $uploadedNames[0], ':id' => $idProduit]);

        $pdo->commit();

        header('Location: /artisphere/?controller=fiche_produit&action=index&success=1');
        exit;

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();

            // (optionnel) supprimer les fichiers déjà uploadés
            foreach ($uploadedNames as $fn) {
                @unlink($destDir . $fn);
            }

            $this->render('fiche_produit.php', [
                'title' => 'Artisphere - fiche-produit',
                'pageCss' => 'fiche-produit-style.css',
                'pageJs'  => 'produit_image_preview.js',
                'categories' => $categories,
                'errors' => ["Erreur : " . $e->getMessage()],
            ]);
            return;
        }
    }

    
}