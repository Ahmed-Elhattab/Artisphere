<?php
class EvenementModel
{
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO pevent (nom, image, lieu, nombre_place, description, type, prix, date_debut, date_fin, id_createur) VALUES (:nom, :image, :lieu, :nombre_place, :description, :type, :prix, :date_debut, :date_fin, :id_createur)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'          => $data['nom'],
            ':image'        => $data['image'],
            ':lieu'         => $data['lieu'],
            ':nombre_place' => $data['nombre_place'],
            ':description'  => $data['description'],
            ':type'         => $data['type'],
            ':prix'         => $data['prix'],
            ':date_debut'   => $data['date_debut'],
            ':date_fin'     => $data['date_fin'],
            ':id_createur'  => $data['id_createur'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function findByCreateur(int $idCreateur): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id_event, nom, image, lieu, nombre_place, prix, date_debut, date_fin, type, description
                            FROM pevent
                            WHERE id_createur = :id
                            ORDER BY id_event DESC");
        $stmt->execute([':id' => $idCreateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT e.*, u.pseudo AS createur_pseudo FROM pevent e JOIN personne u ON u.id_personne = e.id_createur WHERE e.id_event = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
    public static function listHome(int $limit, int $offset): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id_event, nom, image, lieu, type, date_debut, date_fin FROM pevent 
                            ORDER BY id_event DESC
                            LIMIT :lim OFFSET :off");
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
                    type = :type,
                    prix = :prix,
                    date_debut = :date_debut,
                    date_fin = :date_fin
                WHERE id_event = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'          => $data['nom'],
            ':image'        => $data['image'],
            ':lieu'         => $data['lieu'],
            ':nombre_place' => $data['nombre_place'],
            ':description'  => $data['description'],
            ':type'         => $data['type'],
            ':prix'         => $data['prix'],
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

        // Recherche (nom / description / lieu)
        if (!empty($filters['q'])) {
            $where[] = "(e.nom LIKE :q OR e.description LIKE :q OR e.lieu LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        // Type
        if (!empty($filters['type'])) {
            $where[] = "e.type = :type";
            $params[':type'] = $filters['type'];
        }

        // Places disponibles
        if (!empty($filters['in_stock'])) {
            $where[] = "e.nombre_place > 0";
        }

        // Prix min/max
        if ($filters['min_price'] !== '' && $filters['min_price'] !== null) {
            $where[] = "e.prix >= :minp";
            $params[':minp'] = (float)$filters['min_price'];
        }
        if ($filters['max_price'] !== '' && $filters['max_price'] !== null) {
            $where[] = "e.prix <= :maxp";
            $params[':maxp'] = (float)$filters['max_price'];
        }

        $sql = "SELECT e.id_event, e.nom, e.image, e.lieu, e.nombre_place, e.description,
                    e.type, e.prix, e.date_debut, e.date_fin
                FROM pevent e";

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // exemple : bientôt d'abord
        $sql .= " ORDER BY e.date_debut ASC, e.id_event DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listTypes(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT DISTINCT type FROM pevent ORDER BY type ASC");
        return array_values(array_filter(array_map(fn($r) => $r['type'], $stmt->fetchAll(PDO::FETCH_ASSOC))));
    }

    public static function listByCreator(int $idArtisan): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id_event, nom, image, lieu, prix, nombre_place, date_debut, date_fin, type
                            FROM pevent
                            WHERE id_createur = :id
                            ORDER BY date_debut ASC, id_event DESC");
        $stmt->execute([':id' => $idArtisan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}