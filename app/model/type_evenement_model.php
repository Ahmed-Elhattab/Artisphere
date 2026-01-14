<?php
class TypeEvenementModel
{
    public static function searchByName(string $q): array
    {
        $pdo = Database::getConnection();
        if ($q === '') {
            return $pdo->query("SELECT id_type, nom FROM `type` ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt = $pdo->prepare("SELECT id_type, nom FROM `type` WHERE nom LIKE :q ORDER BY nom ASC");
        $stmt->execute([':q' => '%' . $q . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createIfNotExists(string $nom): bool
    {
        $pdo = Database::getConnection();
        $nom = trim($nom);

        $stmt = $pdo->prepare("SELECT 1 FROM `type` WHERE LOWER(nom)=LOWER(:n) LIMIT 1");
        $stmt->execute([':n' => $nom]);
        if ($stmt->fetchColumn()) return false;

        $ins = $pdo->prepare("INSERT INTO `type` (nom) VALUES (:n)");
        return $ins->execute([':n' => $nom]);
    }

    public static function deleteById(int $id): bool
    {
        $pdo = Database::getConnection();
        try {
            $stmt = $pdo->prepare("DELETE FROM `type` WHERE id_type = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            return false;
        }
    }
}