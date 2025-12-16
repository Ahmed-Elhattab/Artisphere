<?php
class CategorieModel {
    public static function all(): array {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT id_categorie, nom FROM categorie ORDER BY nom ASC")
                ->fetchAll(PDO::FETCH_ASSOC);
    }
}