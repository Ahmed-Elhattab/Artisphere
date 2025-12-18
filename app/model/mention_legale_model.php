<?php

class MentionLegaleModel
{
    public static function all(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT id_mention, titre, texte
                             FROM mention_legale
                             ORDER BY id_mention ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO mention_legale (titre, texte)
                VALUES (:titre, :texte)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titre' => $data['titre'],
            ':texte' => $data['texte'],
        ]);

        return (int)$pdo->lastInsertId();
    }
}