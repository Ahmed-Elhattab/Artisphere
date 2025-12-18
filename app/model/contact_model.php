<?php

class ContactModel
{
    public static function create(array $data): int
    {
        $pdo = Database::getConnection();

        // etat: on met une valeur par défaut (ex: 'nouveau')
        // dateMsg: on met la date/heure côté serveur
        $sql = "INSERT INTO contact (firstName, lastName, email, message, etat, dateMsg)
                VALUES (:firstName, :lastName, :email, :message, :etat, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':firstName' => $data['firstName'],
            ':lastName'  => $data['lastName'],
            ':email'     => $data['email'],
            ':message'   => $data['message'],
            ':etat'      => $data['etat'] ?? 'nouveau',
        ]);

        return (int)$pdo->lastInsertId();
    }
    
    public static function search(string $q, string $etat, int $limit, int $offset): array
    {
        $pdo = Database::getConnection();

        $where = [];
        $params = [];

        if ($q !== '') {
            $where[] = "(firstName LIKE :q OR lastName LIKE :q OR email LIKE :q OR message LIKE :q)";
            $params[':q'] = '%' . $q . '%';
        }

        if ($etat !== '' && in_array($etat, ['nouveau', 'en cours', 'fini'], true)) {
            $where[] = "etat = :etat";
            $params[':etat'] = $etat;
        }

        $sql = "SELECT id_contact, firstName, lastName, email, message, etat, dateMsg
                FROM contact";

        if ($where) $sql .= " WHERE " . implode(" AND ", $where);

        $sql .= " ORDER BY
                    CASE etat
                      WHEN 'nouveau' THEN 1
                      WHEN 'en cours' THEN 2
                      WHEN 'fini' THEN 3
                    END,
                    id_contact DESC
                  LIMIT :lim OFFSET :off";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function count(string $q, string $etat): int
    {
        $pdo = Database::getConnection();

        $where = [];
        $params = [];

        if ($q !== '') {
            $where[] = "(firstName LIKE :q OR lastName LIKE :q OR email LIKE :q OR message LIKE :q)";
            $params[':q'] = '%' . $q . '%';
        }

        if ($etat !== '' && in_array($etat, ['nouveau', 'en cours', 'fini'], true)) {
            $where[] = "etat = :etat";
            $params[':etat'] = $etat;
        }

        $sql = "SELECT COUNT(*) FROM contact";
        if ($where) $sql .= " WHERE " . implode(" AND ", $where);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    public static function updateEtat(int $idContact, string $etat): void
    {
        if (!in_array($etat, ['nouveau', 'en cours', 'fini'], true)) return;

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE contact SET etat = :etat WHERE id_contact = :id");
        $stmt->execute([
            ':etat' => $etat,
            ':id' => $idContact
        ]);
    }
}