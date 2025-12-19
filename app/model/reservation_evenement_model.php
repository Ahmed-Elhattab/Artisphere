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
}