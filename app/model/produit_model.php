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
}