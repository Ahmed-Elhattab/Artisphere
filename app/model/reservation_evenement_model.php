<?php
class ReservationEventModel
{
    public static function reserve(int $idReserve, int $idPersonne): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            // 1) lock produit
            $stmt = $pdo->prepare("SELECT nombre_place FROM pevent WHERE id_event = :id FOR UPDATE");
            $stmt->execute([':id' => $idReserve]);
            $qte = $stmt->fetchColumn();

            if ($qte === false || (int)$qte <= 0) {
                $pdo->rollBack();
                return false;
            }

            // 2) empêcher double réservation (si tu veux)
            $check = $pdo->prepare("SELECT 1 FROM reservation_event WHERE id_event = :p AND id_personne = :u LIMIT 1");
            $check->execute([':p' => $idReserve, ':u' => $idPersonne]);
            if ($check->fetchColumn()) {
                $pdo->rollBack();
                return false;
            }

            // 3) insert reservation (message/note plus tard => NULL)
            $ins = $pdo->prepare("INSERT INTO reservation_event (id_event, id_personne, message, note)
                                  VALUES (:p, :u, NULL, NULL)");
            $ins->execute([':p' => $idReserve, ':u' => $idPersonne]);

            // 4) décrément quantité
            $upd = $pdo->prepare("UPDATE pevent SET nombre_place = nombre_place - 1 WHERE id_event = :id");
            $upd->execute([':id' => $idReserve]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    public static function exists(int $idEvent, int $idPersonne): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT 1 FROM reservation_event
                               WHERE id_event = :e AND id_personne = :u
                               LIMIT 1");
        $stmt->execute([':e' => $idEvent, ':u' => $idPersonne]);
        return (bool)$stmt->fetchColumn();
    }

     public static function cancel(int $idEvent, int $idPersonne): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $del = $pdo->prepare("DELETE FROM reservation_event
                                  WHERE id_event = :e AND id_personne = :u");
            $del->execute([':e' => $idEvent, ':u' => $idPersonne]);

            if ($del->rowCount() === 0) {
                $pdo->rollBack();
                return false;
            }

            $upd = $pdo->prepare("UPDATE pevent SET nombre_place = nombre_place + 1 WHERE id_event = :id");
            $upd->execute([':id' => $idEvent]);

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

        // On récupère aussi le nom du type via JOIN
        // (table `type` : adapte en `types` si tu l’as renommée)
        $sql = "SELECT
                    re.id_event,
                    e.nom,
                    e.image,
                    e.lieu,
                    e.prix,
                    e.date_debut,
                    e.date_fin,
                    e.id_type,
                    t.nom AS type_nom,
                    re.status
                FROM reservation_event re
                JOIN pevent e ON e.id_event = re.id_event
                JOIN `type` t ON t.id_type = e.id_type
                WHERE re.id_personne = :u
                ORDER BY re.id_event DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':u' => $idPersonne]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function listForUserByStatus(int $idPersonne, string $status): array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT
                    re.id_event,
                    e.nom,
                    e.image,
                    e.lieu,
                    e.prix,
                    e.date_debut,
                    e.date_fin,
                    e.id_type,
                    t.nom AS type_nom,
                    re.status,
                    re.note
                FROM reservation_event re
                JOIN pevent e ON e.id_event = re.id_event
                JOIN `type` t ON t.id_type = e.id_type
                WHERE re.id_personne = :u
                AND re.status = :s
                ORDER BY re.id_event DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':u' => $idPersonne,
            ':s' => $status
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
}