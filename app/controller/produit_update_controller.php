<?php
require_once __DIR__ . '/../model/produit_model.php';
require_once __DIR__ . '/../model/categorie_model.php';
require_once __DIR__ . '/../model/produit_image_model.php';

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

        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        if ($userId !== (int)$produit['id_createur']) {
            header('Location: /artisphere/?controller=produit_show&action=show&id=' . $id);
            exit;
        }

        $categories = CategorieModel::all();
        $images = ProduitImageModel::listForProduit((int)$produit['id_produit']);
        $this->ensureCsrf();

        $this->render('produit_update.php', [
            'title' => 'Éditer le produit – Artisphere',
            'pageCss' => 'produit_update-style.css',
            'produit' => $produit,
            'categories' => $categories,
            'images' => $images,
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

        $pdo = Database::getConnection();

        // ====== Champs texte ======
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

        // ✅ IDs à supprimer (depuis la vue => delete_images[])
        $toDelete = $_POST['delete_images'] ?? [];
        if (!is_array($toDelete)) $toDelete = [];
        $toDelete = array_values(array_filter(array_map('intval', $toDelete)));

        // Image principale choisie (filename)
        $mainImage = trim($_POST['main_image'] ?? '');

        // ====== Upload images ======
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        $maxSize = 3 * 1024 * 1024;

        $destDir = dirname(__DIR__, 2) . '/public/images/produits/';
        if (!is_dir($destDir)) mkdir($destDir, 0775, true);

        $uploaded = [];

        try {
            $pdo->beginTransaction();

            // 1) Supprimer images demandées (DB + fichiers)
            if (!empty($toDelete)) {
                // On récupère les filenames avant suppression DB
                $imgs = ProduitImageModel::findByIdsForProduit($idProduit, $toDelete);

                // On supprime en DB (IMPORTANT : doit faire un DELETE réel)
                ProduitImageModel::deleteManyForProduit($idProduit, $toDelete);

                // On supprime les fichiers
                foreach ($imgs as $im) {
                    if (!empty($im['filename'])) {
                        @unlink($destDir . $im['filename']);
                    }
                }

                // Si l'image principale actuelle a été supprimée => on la vide (on recalcule après)
                foreach ($imgs as $im) {
                    if (!empty($im['filename']) && $produit['image'] === $im['filename']) {
                        $produit['image'] = null;
                    }
                }

                // Si l’utilisateur avait choisi en "main_image" une image supprimée => on invalide
                $deletedNames = array_map(fn($x) => (string)($x['filename'] ?? ''), $imgs);
                if ($mainImage !== '' && in_array($mainImage, $deletedNames, true)) {
                    $mainImage = '';
                }
            }

            // 2) Ajouter nouvelles images (si envoyées)
            $files = $_FILES['images'] ?? null;
            if ($files && !empty($files['name']) && is_array($files['name'])) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $count = count($files['name']);

                for ($i = 0; $i < $count; $i++) {
                    if (empty($files['name'][$i])) continue;
                    if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
                    if (($files['size'][$i] ?? 0) > $maxSize) continue;

                    $tmp = $files['tmp_name'][$i];
                    $mime = $finfo->file($tmp);
                    if (!isset($allowed[$mime])) continue;

                    $fn = 'p' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
                    if (!move_uploaded_file($tmp, $destDir . $fn)) {
                        throw new RuntimeException("Erreur upload image.");
                    }
                    $uploaded[] = $fn;
                }

                if (!empty($uploaded)) {
                    ProduitImageModel::insertMany($idProduit, $uploaded);
                }
            }

            // 3) Re-lister images restantes
            $images = ProduitImageModel::listForProduit($idProduit);
            $filenames = array_values(array_filter(array_map(fn($r) => $r['filename'] ?? '', $images)));

            // 4) Définir image principale
            if ($mainImage !== '' && in_array($mainImage, $filenames, true)) {
                $imagePrincipale = $mainImage;
            } elseif (!empty($produit['image']) && in_array($produit['image'], $filenames, true)) {
                $imagePrincipale = $produit['image'];
            } else {
                $imagePrincipale = $filenames[0] ?? null;
            }

            // 5) Si erreurs de formulaire => rollback
            if (!empty($errors)) {
                throw new RuntimeException("form_errors");
            }

            // 6) Update produit
            ProduitModel::updateProduit($idProduit, $userId, [
                'nom' => $nom,
                'image' => $imagePrincipale,
                'quantite' => $quantite,
                'materiaux' => $materiaux,
                'prix' => $prix,
                'description' => $description,
                'id_categorie' => $idCategorie,
            ]);

            $pdo->commit();

            header('Location: /artisphere/?controller=produit_show&action=show&id=' . $idProduit . '&updated=1');
            exit;

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();

            // supprime les fichiers uploadés si rollback
            foreach ($uploaded as $fn) @unlink($destDir . $fn);

            if ($e->getMessage() !== "form_errors") {
                $errors[] = "Erreur : " . $e->getMessage();
            }

            $categories = CategorieModel::all();
            $images = ProduitImageModel::listForProduit($idProduit);

            $this->render('produit_update.php', [
                'title' => 'Éditer le produit – Artisphere',
                'pageCss' => 'produit_update-style.css',
                'errors' => $errors,
                'csrf' => $_SESSION['csrf'],
                'categories' => $categories,
                'images' => $images,
                'produit' => array_merge($produit, [
                    'nom' => $nom,
                    'quantite' => $quantite,
                    'materiaux' => $materiaux,
                    'prix' => $prix,
                    'description' => $description,
                    'id_categorie' => $idCategorie,
                ]),
            ]);
            return;
        }
    }

    public function delete(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $this->checkCsrf();

        $idProduit = (int)($_POST['id_produit'] ?? 0);
        if ($idProduit <= 0) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $produit = ProduitModel::findById($idProduit);
        if (!$produit) {
            header('Location: /artisphere/?controller=index&action=index');
            exit;
        }

        $userId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['id_personne'] ?? 0);
        if ($userId !== (int)$produit['id_createur']) {
            header('Location: /artisphere/?controller=produit_show&action=show&id=' . $idProduit);
            exit;
        }

        $pdo = Database::getConnection();
        $destDir = dirname(__DIR__, 2) . '/public/images/produits/';

        try {
            $pdo->beginTransaction();

            // 1) récupérer toutes les images (table enfant)
            $images = ProduitImageModel::listForProduit($idProduit);

            // 2) supprimer en DB les images enfants
            $ids = array_values(array_filter(array_map(fn($im) => (int)($im['id_image'] ?? 0), $images)));
            if (!empty($ids)) {
                ProduitImageModel::deleteManyForProduit($idProduit, $ids);
            }

            // 3) supprimer le produit
            ProduitModel::deleteProduit($idProduit, $userId);

            $pdo->commit();

            // 4) supprimer les fichiers après commit
            foreach ($images as $im) {
                $fn = (string)($im['filename'] ?? '');
                if ($fn !== '') @unlink($destDir . $fn);
            }

            // + image principale si elle existe (au cas où elle n'est pas dans produit_image)
            if (!empty($produit['image'])) {
                @unlink($destDir . $produit['image']);
            }

            header('Location: /artisphere/?controller=mes_creations&action=index&deleted=1');
            exit;

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            header('Location: /artisphere/?controller=produit_update&action=edit&id=' . $idProduit . '&deleted=0');
            exit;
        }
    }

}