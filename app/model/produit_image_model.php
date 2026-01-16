<?php
class ProduitImageModel
{
    public static function insertMany(int $idProduit, array $filenames): void
    {
        if (empty($filenames)) return;

        $pdo = Database::getConnection();
        $sql = "INSERT INTO produit_image (id_produit, filename, ordre)
                VALUES (:id, :fn, :ord)";
        $stmt = $pdo->prepare($sql);

        $ordre = 0;
        foreach ($filenames as $fn) {
            $stmt->execute([
                ':id' => $idProduit,
                ':fn' => $fn,
                ':ord' => $ordre++,
            ]);
        }
    }

    public static function listForProduit(int $idProduit): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT id_image, id_produit, filename, ordre
            FROM produit_image
            WHERE id_produit = :id
            ORDER BY ordre ASC, id_image ASC
        ");
        $stmt->execute([':id' => $idProduit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteOne(int $idImage, int $idProduit): ?string
    {
        $pdo = Database::getConnection();

        // récupérer filename
        $stmt = $pdo->prepare("SELECT filename FROM produit_image WHERE id_image = :i AND id_produit = :p LIMIT 1");
        $stmt->execute([':i' => $idImage, ':p' => $idProduit]);
        $fn = $stmt->fetchColumn();
        if (!$fn) return null;

        // supprimer ligne
        $del = $pdo->prepare("DELETE FROM produit_image WHERE id_image = :i AND id_produit = :p");
        $del->execute([':i' => $idImage, ':p' => $idProduit]);

        return (string)$fn;
    }
    public static function findByIdsForProduit(int $idProduit, array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $pdo = Database::getConnection();

        // ?,?,? dynamique
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "
            SELECT id_image, filename
            FROM produit_image
            WHERE id_produit = ?
            AND id_image IN ($placeholders)
        ";

        $stmt = $pdo->prepare($sql);

        // id_produit en premier puis les ids images
        $stmt->execute(array_merge([$idProduit], $ids));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteManyForProduit(int $idProduit, array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $pdo = Database::getConnection();

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "
            DELETE FROM produit_image
            WHERE id_produit = ?
            AND id_image IN ($placeholders)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute(array_merge([$idProduit], $ids));
    }
}