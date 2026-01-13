<?php
class ReservationEventModel
{
    public static function reserve(int $idEvent, int $idPersonne, int $quantite = 1): bool
    {
        $quantite = max(1, $quantite);
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT nombre_place, stock_reserve
                FROM pevent
                WHERE id_event = :id
                FOR UPDATE
            ");
            $stmt->execute([':id' => $idEvent]);
            $ev = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$ev) { $pdo->rollBack(); return false; }

            $dispo = (int)$ev['nombre_place'] - (int)$ev['stock_reserve'];
            if ($dispo < $quantite) { $pdo->rollBack(); return false; }

            $check = $pdo->prepare("
                SELECT id_resa_event, quantite
                FROM reservation_event
                WHERE id_event = :e AND id_personne = :u AND status = 'en cours'
                LIMIT 1
                FOR UPDATE
            ");
            $check->execute([':e' => $idEvent, ':u' => $idPersonne]);
            $existing = $check->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $updResa = $pdo->prepare("
                    UPDATE reservation_event
                    SET quantite = quantite + :q
                    WHERE id_resa_event = :id
                ");
                $updResa->execute([':q' => $quantite, ':id' => (int)$existing['id_resa_event']]);
            } else {
                $ins = $pdo->prepare("
                    INSERT INTO reservation_event (id_event, id_personne, quantite, message, note, status)
                    VALUES (:e, :u, :q, NULL, NULL, 'en cours')
                ");
                $ins->execute([':e' => $idEvent, ':u' => $idPersonne, ':q' => $quantite]);
            }

            $updStock = $pdo->prepare("
                UPDATE pevent
                SET stock_reserve = stock_reserve + :q
                WHERE id_event = :e
            ");
            $updStock->execute([':q' => $quantite, ':e' => $idEvent]);

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
        $stmt = $pdo->prepare("
            SELECT 1
            FROM reservation_event
            WHERE id_event = :e AND id_personne = :u AND status = 'en cours'
            LIMIT 1
        ");
        $stmt->execute([':e' => $idEvent, ':u' => $idPersonne]);
        return (bool)$stmt->fetchColumn();
    }

    public static function cancel(int $idEvent, int $idPersonne): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT id_resa_event, quantite
                FROM reservation_event
                WHERE id_event = :e AND id_personne = :u AND status = 'en cours'
                FOR UPDATE
            ");
            $stmt->execute([':e' => $idEvent, ':u' => $idPersonne]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) { $pdo->rollBack(); return false; }

            $totalQ = 0;
            foreach ($rows as $r) $totalQ += (int)$r['quantite'];

            $del = $pdo->prepare("
                DELETE FROM reservation_event
                WHERE id_event = :e AND id_personne = :u AND status = 'en cours'
            ");
            $del->execute([':e' => $idEvent, ':u' => $idPersonne]);

            $upd = $pdo->prepare("
                UPDATE pevent
                SET stock_reserve = GREATEST(stock_reserve - :q, 0)
                WHERE id_event = :e
            ");
            $upd->execute([':q' => $totalQ, ':e' => $idEvent]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }
    public static function listForUserByStatus(int $idPersonne, string $status): array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT
                    re.id_resa_event,
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
                ORDER BY re.id_resa_event DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':u' => $idPersonne, ':s' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findOneForUser(int $idResaEvent, int $idPersonne): ?array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT
                    re.id_resa_event,
                    re.id_event,
                    re.id_personne,
                    re.quantite,
                    re.status,
                    re.note,
                    re.message,
                    e.nom,
                    e.image,
                    e.lieu,
                    e.prix,
                    e.date_debut,
                    e.date_fin,
                    e.id_type,
                    t.nom AS type_nom
                FROM reservation_event re
                JOIN pevent e ON e.id_event = re.id_event
                JOIN `type` t ON t.id_type = e.id_type
                WHERE re.id_resa_event = :r
                AND re.id_personne = :u
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':r' => $idResaEvent,
            ':u' => $idPersonne,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function setReview(int $idResaEvent, int $idPersonne, int $note, string $message): bool
    {
        $pdo = Database::getConnection();

        $sql = "UPDATE reservation_event
                SET note = :n,
                    message = :m,
                    status = 'notée'
                WHERE id_resa_event = :r
                AND id_personne = :u
                AND status = 'payée'";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':n' => $note,
            ':m' => $message,
            ':r' => $idResaEvent,
            ':u' => $idPersonne,
        ]);

        return $stmt->rowCount() > 0;
    }

    public static function listPendingForArtisan(int $idArtisan, string $q = ''): array
    {
        $pdo = Database::getConnection();

        $whereQ = "";
        $params = [':a' => $idArtisan];

        if ($q !== '') {
            $whereQ = " AND (
                e.nom LIKE :q
                OR c.pseudo LIKE :q
                OR c.nom LIKE :q
                OR c.prenom LIKE :q
                OR c.email LIKE :q
                OR t.nom LIKE :q
            )";
            $params[':q'] = '%' . $q . '%';
        }

        $sql = "
            SELECT
                re.id_resa_event,
                re.quantite,
                re.status,
                re.id_personne AS id_client,
                e.id_event,
                e.nom AS event_nom,
                e.prix,
                e.date_debut,
                e.date_fin,
                t.nom AS type_nom,
                c.pseudo AS client_pseudo,
                c.nom AS client_nom,
                c.prenom AS client_prenom,
                c.email AS client_email
            FROM reservation_event re
            JOIN pevent e ON e.id_event = re.id_event
            JOIN `type` t ON t.id_type = e.id_type
            JOIN personne c ON c.id_personne = re.id_personne
            WHERE e.id_createur = :a
              AND re.status = 'en cours'
            $whereQ
            ORDER BY re.id_resa_event DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markPaidByArtisan(int $idResaEvent, int $idArtisan): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT re.id_resa_event, re.quantite, re.status, re.id_event,
                       e.nombre_place AS places_reelles, e.stock_reserve, e.id_createur
                FROM reservation_event re
                JOIN pevent e ON e.id_event = re.id_event
                WHERE re.id_resa_event = :r
                FOR UPDATE
            ");
            $stmt->execute([':r' => $idResaEvent]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) { $pdo->rollBack(); return false; }
            if ((int)$row['id_createur'] !== $idArtisan) { $pdo->rollBack(); return false; }
            if (($row['status'] ?? '') !== 'en cours') { $pdo->rollBack(); return false; }

            $q = max(1, (int)$row['quantite']);

            if ((int)$row['stock_reserve'] < $q) { $pdo->rollBack(); return false; }
            if ((int)$row['places_reelles'] < $q) { $pdo->rollBack(); return false; }

            $upd = $pdo->prepare("
                UPDATE reservation_event
                SET status = 'payée'
                WHERE id_resa_event = :r
            ");
            $upd->execute([':r' => $idResaEvent]);

            $updStock = $pdo->prepare("
                UPDATE pevent
                SET nombre_place = nombre_place - :q,
                    stock_reserve = GREATEST(stock_reserve - :q, 0)
                WHERE id_event = :e
            ");
            $updStock->execute([':q' => $q, ':e' => (int)$row['id_event']]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    public static function cancelByArtisan(int $idResaEvent, int $idArtisan): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT re.id_resa_event, re.quantite, re.status, re.id_event,
                       e.stock_reserve, e.id_createur
                FROM reservation_event re
                JOIN pevent e ON e.id_event = re.id_event
                WHERE re.id_resa_event = :r
                FOR UPDATE
            ");
            $stmt->execute([':r' => $idResaEvent]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) { $pdo->rollBack(); return false; }
            if ((int)$row['id_createur'] !== $idArtisan) { $pdo->rollBack(); return false; }
            if (($row['status'] ?? '') !== 'en cours') { $pdo->rollBack(); return false; }

            $q = max(1, (int)$row['quantite']);

            $del = $pdo->prepare("DELETE FROM reservation_event WHERE id_resa_event = :r");
            $del->execute([':r' => $idResaEvent]);

            $upd = $pdo->prepare("
                UPDATE pevent
                SET stock_reserve = GREATEST(stock_reserve - :q, 0)
                WHERE id_event = :e
            ");
            $upd->execute([':q' => $q, ':e' => (int)$row['id_event']]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }
}