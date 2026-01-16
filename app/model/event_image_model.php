<?php
class EventImageModel
{
    public static function insertMany(int $idEvent, array $filenames): void
    {
        if (empty($filenames)) return;

        $pdo = Database::getConnection();

        // ordre = max(ordre)+1 pour ajouter à la suite
        $q = $pdo->prepare("SELECT COALESCE(MAX(ordre), -1) FROM event_image WHERE id_event = :id");
        $q->execute([':id' => $idEvent]);
        $start = (int)$q->fetchColumn() + 1;

        $sql = "INSERT INTO event_image (id_event, filename, ordre)
                VALUES (:id, :fn, :ord)";
        $stmt = $pdo->prepare($sql);

        $ordre = $start;
        foreach ($filenames as $fn) {
            $stmt->execute([
                ':id' => $idEvent,
                ':fn' => $fn,
                ':ord' => $ordre++,
            ]);
        }
    }

    public static function listForEvent(int $idEvent): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT id_image, id_event, filename, ordre
            FROM event_image
            WHERE id_event = :id
            ORDER BY ordre ASC, id_image ASC
        ");
        $stmt->execute([':id' => $idEvent]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByIdsForEvent(int $idEvent, array $ids): array
    {
        if (empty($ids)) return [];

        $pdo = Database::getConnection();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "
            SELECT id_image, filename
            FROM event_image
            WHERE id_event = ?
              AND id_image IN ($placeholders)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge([$idEvent], $ids));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteManyForEvent(int $idEvent, array $ids): void
    {
        if (empty($ids)) return;

        $pdo = Database::getConnection();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "
            DELETE FROM event_image
            WHERE id_event = ?
              AND id_image IN ($placeholders)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge([$idEvent], $ids));
    }
}