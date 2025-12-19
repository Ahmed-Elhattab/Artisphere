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
}