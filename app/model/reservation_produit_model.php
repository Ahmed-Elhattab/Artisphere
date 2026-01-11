<?php
class ReservationProduitModel
{
    public static function reserve(int $idProduit, int $idPersonne): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            // 1) lock produit
            $stmt = $pdo->prepare("SELECT quantite FROM pproduit WHERE id_produit = :id FOR UPDATE");
            $stmt->execute([':id' => $idProduit]);
            $qte = $stmt->fetchColumn();

            if ($qte === false || (int)$qte <= 0) {
                $pdo->rollBack();
                return false;
            }

            // 2) empêcher double réservation (si tu veux)
            $check = $pdo->prepare("SELECT 1 FROM reservation_produit WHERE id_produit = :p AND id_personne = :u LIMIT 1");
            $check->execute([':p' => $idProduit, ':u' => $idPersonne]);
            if ($check->fetchColumn()) {
                $pdo->rollBack();
                return false;
            }

            // 3) insert reservation (message/note plus tard => NULL)
            $ins = $pdo->prepare("INSERT INTO reservation_produit (id_produit, id_personne, message, note)
                                  VALUES (:p, :u, NULL, NULL)");
            $ins->execute([':p' => $idProduit, ':u' => $idPersonne]);

            // 4) décrément quantité
            $upd = $pdo->prepare("UPDATE pproduit SET quantite = quantite - 1 WHERE id_produit = :id");
            $upd->execute([':id' => $idProduit]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }
    public static function listForUser(int $idPersonne): array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT
                rp.id_produit,
                p.nom,
                p.image,
                p.prix
                FROM reservation_produit rp
                JOIN pproduit p ON p.id_produit = rp.id_produit
                WHERE rp.id_personne = :u
                ORDER BY rp.id_produit DESC";  // ou ORDER BY rp.date si tu ajoutes un champ date plus tard

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':u' => $idPersonne]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function exists(int $idProduit, int $idPersonne): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT 1 FROM reservation_produit
             WHERE id_produit = :p AND id_personne = :u
             LIMIT 1"
        );
        $stmt->execute([
            ':p' => $idProduit,
            ':u' => $idPersonne
        ]);
        return (bool)$stmt->fetchColumn();
    }

    public static function cancel(int $idProduit, int $idPersonne): bool
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // supprimer la réservation
            $stmt = $pdo->prepare(
                "DELETE FROM reservation_produit
                 WHERE id_produit = :p AND id_personne = :u"
            );
            $stmt->execute([
                ':p' => $idProduit,
                ':u' => $idPersonne
            ]);

            if ($stmt->rowCount() === 0) {
                $pdo->rollBack();
                return false;
            }

            // remettre +1 en quantité
            $stmt = $pdo->prepare(
                "UPDATE pproduit
                 SET quantite = quantite + 1
                 WHERE id_produit = :p"
            );
            $stmt->execute([':p' => $idProduit]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
    public static function listForUserByStatus(int $idPersonne, string $status): array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT
                    rp.id_produit,
                    p.nom,
                    p.image,
                    p.prix,
                    rp.status,
                    rp.note
                FROM reservation_produit rp
                JOIN pproduit p ON p.id_produit = rp.id_produit
                WHERE rp.id_personne = :u
                AND rp.status = :s
                ORDER BY rp.id_produit DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':u' => $idPersonne,
            ':s' => $status
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}