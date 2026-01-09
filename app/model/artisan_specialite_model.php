<?php

class ArtisanSpecialiteModel
{
    public static function getForArtisan(int $idPersonne): ?int
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id_specialite FROM artisan_specialite WHERE id_personne = :id LIMIT 1");
        $stmt->execute([':id' => $idPersonne]);
        $val = $stmt->fetchColumn();
        return ($val === false) ? null : (int)$val;
    }

    public static function setForArtisan(int $idPersonne, ?int $idSpecialite): void
    {
        $pdo = Database::getConnection();

        // Si null => on supprime la spécialité (optionnel)
        if ($idSpecialite === null) {
            $stmt = $pdo->prepare("DELETE FROM artisan_specialite WHERE id_personne = :id");
            $stmt->execute([':id' => $idPersonne]);
            return;
        }

        // Upsert “manuel” compatible MySQL/MariaDB
        $exists = $pdo->prepare("SELECT id_as FROM artisan_specialite WHERE id_personne = :id LIMIT 1");
        $exists->execute([':id' => $idPersonne]);
        $idAs = $exists->fetchColumn();

        if ($idAs) {
            $upd = $pdo->prepare("UPDATE artisan_specialite SET id_specialite = :s WHERE id_personne = :id");
            $upd->execute([':s' => $idSpecialite, ':id' => $idPersonne]);
        } else {
            $ins = $pdo->prepare("INSERT INTO artisan_specialite (id_personne, id_specialite) VALUES (:id, :s)");
            $ins->execute([':id' => $idPersonne, ':s' => $idSpecialite]);
        }
    }
}