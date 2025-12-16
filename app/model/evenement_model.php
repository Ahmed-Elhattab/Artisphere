<?php
class EvenementModel
{
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO pevent (nom, image, lieu, nombre_place, description, type, prix, date_debut, date_fin, id_createur) VALUES (:nom, :image, :lieu, :nombre_place, :description, :type, :prix, :date_debut, :date_fin, :id_createur)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'          => $data['nom'],
            ':image'        => $data['image'],
            ':lieu'         => $data['lieu'],
            ':nombre_place' => $data['nombre_place'],
            ':description'  => $data['description'],
            ':type'         => $data['type'],
            ':prix'         => $data['prix'],
            ':date_debut'   => $data['date_debut'],
            ':date_fin'     => $data['date_fin'],
            ':id_createur'  => $data['id_createur'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function findByCreateur(int $idCreateur): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id_event, nom, image, lieu, nombre_place, prix, date_debut, date_fin, type, description
                            FROM pevent
                            WHERE id_createur = :id
                            ORDER BY id_event DESC");
        $stmt->execute([':id' => $idCreateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM pevent WHERE id_event = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}