<?php
class ProduitModel
{
    public static function getAll(): array
    {
        // Database vient de app/core/database.php
        $pdo = Database::getConnection();

        $sql = "SELECT * FROM pproduit";
        $stmt = $pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO pproduit (nom, image, quantite, materiaux, prix, id_createur, description, id_categorie)
                VALUES (:nom, :image, :quantite, :materiaux, :prix, :id_createur, :description, :id_categorie)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
        ':nom'        => $data['nom'],
        ':image'      => $data['image'],
        ':quantite'   => $data['quantite'],
        ':materiaux'  => $data['materiaux'],
        ':prix'       => $data['prix'],
        ':id_createur'=> $data['id_createur'],
        ':description'=> $data['description'],
        ':id_categorie'=> $data['id_categorie'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function findByCreateur(int $idCreateur): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id_produit, nom, image, quantite, prix, description
                            FROM pproduit
                            WHERE id_createur = :id
                            ORDER BY id_produit DESC");
        $stmt->execute([':id' => $idCreateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $sql = "SELECT p.*, u.pseudo AS createur_pseudo
                FROM pproduit p
                JOIN personne u ON u.id_personne = p.id_createur
                WHERE p.id_produit = :id
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function listHome(int $limit, int $offset): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id_produit, nom, image, prix
                            FROM pproduit
                            ORDER BY id_produit DESC
                            LIMIT :lim OFFSET :off");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll(): int
    {
        $pdo = Database::getConnection();
        return (int)$pdo->query("SELECT COUNT(*) FROM pproduit")->fetchColumn();
    }

    public static function updateProduit(int $idProduit, int $idCreateur, array $data): bool
    {
        $pdo = Database::getConnection();

        // Sécurité : update seulement si le créateur correspond
        $sql = "UPDATE pproduit
                SET nom = :nom,
                    image = :image,
                    quantite = :quantite,
                    materiaux = :materiaux,
                    prix = :prix,
                    description = :description,
                    id_categorie = :id_categorie
                WHERE id_produit = :id_produit AND id_createur = :id_createur";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom'],
            ':image' => $data['image'],
            ':quantite' => $data['quantite'],
            ':materiaux' => $data['materiaux'],
            ':prix' => $data['prix'],
            ':description' => $data['description'],
            ':id_categorie' => $data['id_categorie'],
            ':id_produit' => $idProduit,
            ':id_createur' => $idCreateur,
        ]);

        return $stmt->rowCount() > 0;
    }
}