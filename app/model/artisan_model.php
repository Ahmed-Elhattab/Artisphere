<?php
class ArtisanModel
{
    public static function search(array $filters): array
    {
        $pdo = Database::getConnection();

        $where = ["p.role = 'artisan'"];
        $params = [];

        // Recherche (pseudo, nom, prenom, adresse, specialite)
        if (!empty($filters['q'])) {
            $where[] = "(p.pseudo LIKE :q OR p.nom LIKE :q OR p.prenom LIKE :q OR p.adresse LIKE :q OR s.nom LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        // Filtre spécialité
        if (!empty($filters['id_specialite'])) {
            $where[] = "s.id_specialite = :sid";
            $params[':sid'] = (int)$filters['id_specialite'];
        }

        // Note minimale (moyenne)
        // On filtre après agrégation => HAVING
        $having = "";
        if ($filters['min_note'] !== '' && $filters['min_note'] !== null) {
            $having = " HAVING avg_note >= :minnote ";
            $params[':minnote'] = (float)$filters['min_note'];
        }

        // Tri
        $orderBy = " ORDER BY p.id_personne DESC ";
        if (!empty($filters['sort']) && $filters['sort'] === 'best') {
            $orderBy = " ORDER BY avg_note DESC, p.id_personne DESC ";
        }

        $sql = "
            SELECT
              p.id_personne, p.pseudo, p.nom, p.prenom, p.adresse, p.avatar,
              s.id_specialite, s.nom AS specialite_nom,
              COALESCE(AVG(na.note), 0) AS avg_note,
              COUNT(na.id_note) AS nb_avis
            FROM personne p
            LEFT JOIN artisan_specialite asp ON asp.id_personne = p.id_personne
            LEFT JOIN specialite s ON s.id_specialite = asp.id_specialite
            LEFT JOIN note_artisan na ON na.id_artisan = p.id_personne
        ";

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " GROUP BY p.id_personne ";

        $sql .= $having;

        $sql .= $orderBy;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}