<?php
class ReservationProduitModel
{
    public static function reserve(int $idProduit, int $idPersonne, int $quantite = 1): bool
    {
        $quantite = max(1, $quantite);
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            // Lock produit (stock réel + réservé)
            $stmt = $pdo->prepare("
                SELECT quantite, stock_reserve
                FROM pproduit
                WHERE id_produit = :id
                FOR UPDATE
            ");
            $stmt->execute([':id' => $idProduit]);
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$prod) { $pdo->rollBack(); return false; }

            $dispo = (int)$prod['quantite'] - (int)$prod['stock_reserve'];
            if ($dispo < $quantite) { $pdo->rollBack(); return false; }

            // Vérifier s'il existe déjà une réservation EN COURS pour ce user+produit
            $check = $pdo->prepare("
                SELECT id_resa_produit, quantite
                FROM reservation_produit
                WHERE id_produit = :p AND id_personne = :u AND status = 'en cours'
                LIMIT 1
                FOR UPDATE
            ");
            $check->execute([':p' => $idProduit, ':u' => $idPersonne]);
            $existing = $check->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // On augmente la quantité de la réservation existante
                $updResa = $pdo->prepare("
                    UPDATE reservation_produit
                    SET quantite = quantite + :q
                    WHERE id_resa_produit = :id
                ");
                $updResa->execute([
                    ':q'  => $quantite,
                    ':id' => (int)$existing['id_resa_produit'],
                ]);
            } else {
                // Nouvelle réservation
                $ins = $pdo->prepare("
                    INSERT INTO reservation_produit (id_produit, id_personne, quantite, message, note, status)
                    VALUES (:p, :u, :q, NULL, NULL, 'en cours')
                ");
                $ins->execute([':p' => $idProduit, ':u' => $idPersonne, ':q' => $quantite]);
            }

            // Incrémenter stock_reserve
            $updStock = $pdo->prepare("
                UPDATE pproduit
                SET stock_reserve = stock_reserve + :q
                WHERE id_produit = :p
            ");
            $updStock->execute([':q' => $quantite, ':p' => $idProduit]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    public static function exists(int $idProduit, int $idPersonne): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT 1
            FROM reservation_produit
            WHERE id_produit = :p AND id_personne = :u AND status = 'en cours'
            LIMIT 1
        ");
        $stmt->execute([':p' => $idProduit, ':u' => $idPersonne]);
        return (bool)$stmt->fetchColumn();
    }

    // ✅ Annuler = supprimer la réservation EN COURS + décrémenter stock_reserve du bon montant
    public static function cancel(int $idProduit, int $idPersonne): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            // Lock réservation(s) en cours
            $stmt = $pdo->prepare("
                SELECT id_resa_produit, quantite
                FROM reservation_produit
                WHERE id_produit = :p AND id_personne = :u AND status = 'en cours'
                FOR UPDATE
            ");
            $stmt->execute([':p' => $idProduit, ':u' => $idPersonne]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) { $pdo->rollBack(); return false; }

            $totalQ = 0;
            foreach ($rows as $r) {
                $totalQ += (int)$r['quantite'];
            }

            // Supprimer les réservations en cours (historique supprimé comme tu veux)
            $del = $pdo->prepare("
                DELETE FROM reservation_produit
                WHERE id_produit = :p AND id_personne = :u AND status = 'en cours'
            ");
            $del->execute([':p' => $idProduit, ':u' => $idPersonne]);

            // Décrémenter stock_reserve
            $upd = $pdo->prepare("
                UPDATE pproduit
                SET stock_reserve = GREATEST(stock_reserve - :q, 0)
                WHERE id_produit = :p
            ");
            $upd->execute([':q' => $totalQ, ':p' => $idProduit]);

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
                    rp.id_resa_produit,
                    rp.id_produit,
                    rp.quantite,
                    rp.status,
                    rp.note,
                    p.nom,
                    p.image,
                    p.prix
                FROM reservation_produit rp
                JOIN pproduit p ON p.id_produit = rp.id_produit
                WHERE rp.id_personne = :u
                AND rp.status = :s
                ORDER BY rp.id_resa_produit DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':u' => $idPersonne,
            ':s' => $status
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findOneForUser(int $idResaProduit, int $idPersonne): ?array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT
                    rp.id_resa_produit,
                    rp.id_produit,
                    rp.id_personne,
                    rp.quantite,
                    rp.status,
                    rp.note,
                    rp.message,
                    p.nom,
                    p.image,
                    p.prix
                FROM reservation_produit rp
                JOIN pproduit p ON p.id_produit = rp.id_produit
                WHERE rp.id_resa_produit = :r
                AND rp.id_personne = :u
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':r' => $idResaProduit,
            ':u' => $idPersonne,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function setReview(int $idResaProduit, int $idPersonne, int $note, string $message): bool
    {
        $pdo = Database::getConnection();

        $sql = "UPDATE reservation_produit
                SET note = :n,
                    message = :m,
                    status = 'notée'
                WHERE id_resa_produit = :r
                AND id_personne = :u
                AND status = 'payée'";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':n' => $note,
            ':m' => $message,
            ':r' => $idResaProduit,
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
                p.nom LIKE :q
                OR c.pseudo LIKE :q
                OR c.nom LIKE :q
                OR c.prenom LIKE :q
                OR c.email LIKE :q
            )";
            $params[':q'] = '%' . $q . '%';
        }

        $sql = "
            SELECT
                rp.id_resa_produit,
                rp.quantite,
                rp.status,
                rp.id_personne AS id_client,
                p.id_produit,
                p.nom AS produit_nom,
                p.prix,
                c.pseudo AS client_pseudo,
                c.nom AS client_nom,
                c.prenom AS client_prenom,
                c.email AS client_email
            FROM reservation_produit rp
            JOIN pproduit p ON p.id_produit = rp.id_produit
            JOIN personne c ON c.id_personne = rp.id_personne
            WHERE p.id_createur = :a
              AND rp.status = 'en cours'
            $whereQ
            ORDER BY rp.id_resa_produit DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markPaidByArtisan(int $idResaProduit, int $idArtisan): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            // Lock réservation + produit
            $stmt = $pdo->prepare("
                SELECT rp.id_resa_produit, rp.quantite, rp.status, rp.id_produit,
                       p.quantite AS stock_reel, p.stock_reserve, p.id_createur
                FROM reservation_produit rp
                JOIN pproduit p ON p.id_produit = rp.id_produit
                WHERE rp.id_resa_produit = :r
                FOR UPDATE
            ");
            $stmt->execute([':r' => $idResaProduit]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) { $pdo->rollBack(); return false; }
            if ((int)$row['id_createur'] !== $idArtisan) { $pdo->rollBack(); return false; }
            if (($row['status'] ?? '') !== 'en cours') { $pdo->rollBack(); return false; }

            $q = max(1, (int)$row['quantite']);

            // sécurité : il faut que stock_reserve >= q
            if ((int)$row['stock_reserve'] < $q) { $pdo->rollBack(); return false; }
            // sécurité : stock réel suffisant
            if ((int)$row['stock_reel'] < $q) { $pdo->rollBack(); return false; }

            // 1) statut => payée
            $upd = $pdo->prepare("
                UPDATE reservation_produit
                SET status = 'payée'
                WHERE id_resa_produit = :r
            ");
            $upd->execute([':r' => $idResaProduit]);

            // 2) stock réel ↓ et stock_reserve ↓
            $updStock = $pdo->prepare("
                UPDATE pproduit
                SET quantite = quantite - :q,
                    stock_reserve = GREATEST(stock_reserve - :q, 0)
                WHERE id_produit = :p
            ");
            $updStock->execute([':q' => $q, ':p' => (int)$row['id_produit']]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    public static function cancelByArtisan(int $idResaProduit, int $idArtisan): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                SELECT rp.id_resa_produit, rp.quantite, rp.status, rp.id_produit,
                       p.stock_reserve, p.id_createur
                FROM reservation_produit rp
                JOIN pproduit p ON p.id_produit = rp.id_produit
                WHERE rp.id_resa_produit = :r
                FOR UPDATE
            ");
            $stmt->execute([':r' => $idResaProduit]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) { $pdo->rollBack(); return false; }
            if ((int)$row['id_createur'] !== $idArtisan) { $pdo->rollBack(); return false; }
            if (($row['status'] ?? '') !== 'en cours') { $pdo->rollBack(); return false; }

            $q = max(1, (int)$row['quantite']);

            // Supprimer la réservation
            $del = $pdo->prepare("DELETE FROM reservation_produit WHERE id_resa_produit = :r");
            $del->execute([':r' => $idResaProduit]);

            // stock_reserve ↓
            $updStock = $pdo->prepare("
                UPDATE pproduit
                SET stock_reserve = GREATEST(stock_reserve - :q, 0)
                WHERE id_produit = :p
            ");
            $updStock->execute([':q' => $q, ':p' => (int)$row['id_produit']]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }
}