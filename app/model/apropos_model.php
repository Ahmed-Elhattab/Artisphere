<?php

class AproposModel
{
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO apropos (chapitre, contenu)
                VALUES (:chapitre, :contenu)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':chapitre' => $data['chapitre'],
            ':contenu'  => $data['contenu'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function all(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT id_apropos, chapitre, contenu
                            FROM apropos
                            ORDER BY id_apropos ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}