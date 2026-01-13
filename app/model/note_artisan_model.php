<?php

class NoteArtisanModel
{
    public static function averageForArtisan(int $idArtisan): ?float
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT AVG(note) FROM note_artisan WHERE id_artisan = :a");
        $stmt->execute([':a' => $idArtisan]);
        $avg = $stmt->fetchColumn();
        return ($avg === null) ? null : (float)$avg;
    }

    public static function listForArtisan(int $idArtisan): array
    {
        $pdo = Database::getConnection();
        $sql = "
            SELECT na.id_note, na.id_client, na.note, na.commentaire,
                   p.pseudo AS client_pseudo
            FROM note_artisan na
            LEFT JOIN personne p ON p.id_personne = na.id_client
            WHERE na.id_artisan = :a
            ORDER BY na.id_note DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':a' => $idArtisan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Le client a-t-il déjà noté cet artisan ?
    public static function existsForClient(int $idArtisan, int $idClient): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT 1 FROM note_artisan
            WHERE id_artisan = :a AND id_client = :c
            LIMIT 1
        ");
        $stmt->execute([':a' => $idArtisan, ':c' => $idClient]);
        return (bool)$stmt->fetchColumn();
    }

    // ✅ Le client peut-il noter (a payé produit OU évènement de cet artisan) ?
    // On accepte aussi 'notée' côté réservations, car c’est une commande payée déjà notée.
    public static function clientCanRate(int $idArtisan, int $idClient): bool
    {
        $pdo = Database::getConnection();

        // Produits payés chez l’artisan
        $stmt = $pdo->prepare("
            SELECT 1
            FROM reservation_produit rp
            JOIN pproduit p ON p.id_produit = rp.id_produit
            WHERE rp.id_personne = :c
              AND p.id_createur = :a
              AND rp.status IN ('payée','notée')
            LIMIT 1
        ");
        $stmt->execute([':c' => $idClient, ':a' => $idArtisan]);
        if ($stmt->fetchColumn()) return true;

        // Évènements payés chez l’artisan
        $stmt = $pdo->prepare("
            SELECT 1
            FROM reservation_event re
            JOIN pevent e ON e.id_event = re.id_event
            WHERE re.id_personne = :c
              AND e.id_createur = :a
              AND re.status IN ('payée','notée')
            LIMIT 1
        ");
        $stmt->execute([':c' => $idClient, ':a' => $idArtisan]);
        return (bool)$stmt->fetchColumn();
    }

    // ✅ Créer la note artisan
    public static function create(int $idArtisan, int $idClient, int $note, string $commentaire): bool
    {
        $pdo = Database::getConnection();
        $sql = "INSERT INTO note_artisan (id_artisan, id_client, note, commentaire)
                VALUES (:a, :c, :n, :m)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':a' => $idArtisan,
            ':c' => $idClient,
            ':n' => $note,
            ':m' => $commentaire,
        ]);
        return $stmt->rowCount() > 0;
    }
}