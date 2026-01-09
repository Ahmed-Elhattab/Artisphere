<?php

class NoteArtisanModel
{
    public static function averageForArtisan(int $idArtisan): ?float
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT AVG(note) AS avg_note
                               FROM note_artisan
                               WHERE id_artisan = :id");
        $stmt->execute([':id' => $idArtisan]);
        $avg = $stmt->fetchColumn();
        return ($avg === null) ? null : (float)$avg;
    }

    public static function listForArtisan(int $idArtisan): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT na.note, na.commentaire, na.id_client,
                   p.pseudo AS client_pseudo
            FROM note_artisan na
            JOIN personne p ON p.id_personne = na.id_client
            WHERE na.id_artisan = :id
            ORDER BY na.id_note DESC
        ");
        $stmt->execute([':id' => $idArtisan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}