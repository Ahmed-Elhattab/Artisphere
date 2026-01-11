<?php
class EvenementModel
{
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO pevent
                    (nom, image, lieu, nombre_place, description, id_type, prix, date_debut, date_fin, id_createur)
                VALUES
                    (:nom, :image, :lieu, :nombre_place, :description, :id_type, :prix, :date_debut, :date_fin, :id_createur)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'          => $data['nom'],
            ':image'        => $data['image'],
            ':lieu'         => $data['lieu'],
            ':nombre_place' => (int)$data['nombre_place'],
            ':description'  => $data['description'],
            ':id_type'      => (int)$data['id_type'],
            ':prix'         => (float)$data['prix'],
            ':date_debut'   => $data['date_debut'],
            ':date_fin'     => $data['date_fin'],
            ':id_createur'  => (int)$data['id_createur'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function findByCreateur(int $idCreateur): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT e.id_event, e.nom, e.image, e.lieu, e.nombre_place, e.prix,
                   e.date_debut, e.date_fin, e.id_type, t.nom AS type_nom, e.description
            FROM pevent e
            JOIN `type` t ON t.id_type = e.id_type
            WHERE e.id_createur = :id
            ORDER BY e.id_event DESC
        ");
        $stmt->execute([':id' => $idCreateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT e.*, t.nom AS type_nom, u.pseudo AS createur_pseudo
            FROM pevent e
            JOIN personne u ON u.id_personne = e.id_createur
            JOIN `type` t ON t.id_type = e.id_type
            WHERE e.id_event = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function listHome(int $limit, int $offset): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT e.id_event, e.nom, e.image, e.lieu,
                   e.id_type, t.nom AS type_nom,
                   e.date_debut, e.date_fin
            FROM pevent e
            JOIN `type` t ON t.id_type = e.id_type
            ORDER BY e.id_event DESC
            LIMIT :lim OFFSET :off
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll(): int
    {
        $pdo = Database::getConnection();
        return (int)$pdo->query("SELECT COUNT(*) FROM pevent")->fetchColumn();
    }

    public static function updateEvent(int $id, array $data): void
    {
        $pdo = Database::getConnection();

        $sql = "UPDATE pevent
                SET nom = :nom,
                    image = :image,
                    lieu = :lieu,
                    nombre_place = :nombre_place,
                    description = :description,
                    id_type = :id_type,
                    prix = :prix,
                    date_debut = :date_debut,
                    date_fin = :date_fin
                WHERE id_event = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'          => $data['nom'],
            ':image'        => $data['image'],
            ':lieu'         => $data['lieu'],
            ':nombre_place' => (int)$data['nombre_place'],
            ':description'  => $data['description'],
            ':id_type'      => (int)$data['id_type'],
            ':prix'         => (float)$data['prix'],
            ':date_debut'   => $data['date_debut'],
            ':date_fin'     => $data['date_fin'],
            ':id'           => $id,
        ]);
    }

    public static function search(array $filters): array
    {
        $pdo = Database::getConnection();
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = "(e.nom LIKE :q OR e.description LIKE :q OR e.lieu LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        // "type" attendu côté filtre = id_type
        if (!empty($filters['type'])) {
            $where[] = "e.id_type = :id_type";
            $params[':id_type'] = (int)$filters['type'];
        }

        if (!empty($filters['in_stock'])) {
            $where[] = "e.nombre_place > 0";
        }

        if ($filters['min_price'] !== '' && $filters['min_price'] !== null) {
            $where[] = "e.prix >= :minp";
            $params[':minp'] = (float)$filters['min_price'];
        }
        if ($filters['max_price'] !== '' && $filters['max_price'] !== null) {
            $where[] = "e.prix <= :maxp";
            $params[':maxp'] = (float)$filters['max_price'];
        }

        $sql = "SELECT e.id_event, e.nom, e.image, e.lieu, e.nombre_place, e.description,
                       e.id_type, t.nom AS type_nom, e.prix, e.date_debut, e.date_fin
                FROM pevent e
                JOIN `type` t ON t.id_type = e.id_type";

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY e.date_debut ASC, e.id_event DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listTypes(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT id_type, nom FROM `type` ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function typeExists(int $idType): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT 1 FROM `type` WHERE id_type = :id LIMIT 1");
        $stmt->execute([':id' => $idType]);
        return (bool)$stmt->fetchColumn();
    }

    public static function listByCreator(int $idArtisan): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT e.id_event, e.nom, e.image, e.lieu, e.prix, e.nombre_place,
                   e.date_debut, e.date_fin, e.id_type, t.nom AS type_nom
            FROM pevent e
            JOIN `type` t ON t.id_type = e.id_type
            WHERE e.id_createur = :id
            ORDER BY e.date_debut ASC, e.id_event DESC
        ");
        $stmt->execute([':id' => $idArtisan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}