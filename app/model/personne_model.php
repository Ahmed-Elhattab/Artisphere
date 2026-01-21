<?php
class PersonneModel
{
    public static function getAll(): array
    {
        // Database vient de app/core/database.php
        $pdo = Database::getConnection();

        $sql = "SELECT * FROM personne";
        $stmt = $pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    #envoi les infos du form de création d'artisan ou de client à la table personne dans la BD
   public static function createPersonne(array $data, string $role): int
    {
        // Sécurité : rôles autorisés uniquement
        $allowedRoles = ['artisan', 'client'];

        if (!in_array($role, $allowedRoles, true)) {
            throw new InvalidArgumentException('Rôle utilisateur invalide');
        }

        // Adresse obligatoire uniquement pour artisan
        $adresse = null;
        if ($role === 'artisan') {
            $adresse = $data['adresse'];
        }

        $pdo = Database::getConnection();

        $sql = "INSERT INTO personne (nom, prenom, pseudo, email, mdp, role, adresse)
                VALUES (:nom, :prenom, :pseudo, :email, :mdp, :role, :adresse)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':pseudo' => $data['pseudo'],
            ':email' => $data['email'],
            ':mdp' => $data['mdp_hash'],
            ':role' => $role,
            ':adresse' => ($role === 'artisan') ? $data['adresse'] : null,
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function emailExists(string $email): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT 1 FROM personne WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return (bool)$stmt->fetchColumn();
    }

    public static function pseudoExists(string $pseudo): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT 1 FROM personne WHERE pseudo = :pseudo LIMIT 1");
        $stmt->execute([':pseudo' => $pseudo]);
        return (bool)$stmt->fetchColumn();
    }

    #pareil que email exist, mais en excluant l'email de la personne qui l'utilise
    public static function emailExistsEditionMode(string $email, ?int $excludeId = null): bool
    {
        $pdo = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM personne WHERE email = :email";
        if ($excludeId !== null) $sql .= " AND id_personne <> :id";

        $stmt = $pdo->prepare($sql);
        $params = [':email' => $email];
        if ($excludeId !== null) $params[':id'] = $excludeId;

        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    #pareil que pseudo exist, mais en excluant le pseudo de la personne qui l'utilise
    public static function pseudoExistsEditionMode(string $pseudo, ?int $excludeId = null): bool
    {
        $pdo = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM personne WHERE pseudo = :pseudo";
        if ($excludeId !== null) $sql .= " AND id_personne <> :id";

        $stmt = $pdo->prepare($sql);
        $params = [':pseudo' => $pseudo];
        if ($excludeId !== null) $params[':id'] = $excludeId;

        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function findByPseudoOrEmail(string $identifier): ?array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT *
                FROM personne
                WHERE pseudo = :id OR email = :id
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $identifier]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    #met à jour le champ avatar (= le champ stockant LE CHEMIN vers la photo de profil)
    public static function updateAvatar(int $idPersonne, ?string $filename): void
    {
        $pdo = Database::getConnection();

        $sql = "UPDATE personne SET avatar = :avatar WHERE id_personne = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':avatar' => $filename, //peut être NULL
            ':id'     => $idPersonne,
        ]);
    }

    public static function updateProfile(int $id, array $data): void
    {
        $pdo = Database::getConnection();

        $sql = "UPDATE personne
                SET nom = :nom,
                    prenom = :prenom,
                    pseudo = :pseudo,
                    email = :email,
                    adresse = :adresse
                WHERE id_personne = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'     => $data['nom'],
            ':prenom'  => $data['prenom'],
            ':pseudo'  => $data['pseudo'],
            ':email'   => $data['email'],
            ':adresse' => $data['adresse'],
            ':id'      => $id,
        ]);
    }

    public static function searchUsers(string $q, int $limit, int $offset): array
    {
        $pdo = Database::getConnection();

        $q = trim($q);
        if ($q === '') {
            $stmt = $pdo->prepare("SELECT id_personne, nom, prenom, pseudo, email, role
                                FROM personne
                                ORDER BY id_personne DESC
                                LIMIT :lim OFFSET :off");
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $like = '%' . $q . '%';
        $stmt = $pdo->prepare("SELECT id_personne, nom, prenom, pseudo, email, role
                            FROM personne
                            WHERE nom LIKE :q OR prenom LIKE :q OR pseudo LIKE :q OR email LIKE :q
                            ORDER BY id_personne DESC
                            LIMIT :lim OFFSET :off");
        $stmt->bindValue(':q', $like, PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countUsers(string $q): int
    {
        $pdo = Database::getConnection();

        $q = trim($q);
        if ($q === '') {
            return (int)$pdo->query("SELECT COUNT(*) FROM personne")->fetchColumn();
        }

        $like = '%' . $q . '%';
        $stmt = $pdo->prepare("SELECT COUNT(*)
                            FROM personne
                            WHERE nom LIKE :q OR prenom LIKE :q OR pseudo LIKE :q OR email LIKE :q");
        $stmt->execute([':q' => $like]);
        return (int)$stmt->fetchColumn();
    }

    public static function promoteToAdmin(int $idPersonne): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE personne SET role = 'admin' WHERE id_personne = :id");
        $stmt->execute([':id' => $idPersonne]);
    }

    public static function deleteById(int $idPersonne): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM personne WHERE id_personne = :id");
        $stmt->execute([':id' => $idPersonne]);
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id_personne, nom, prenom, pseudo, email, role, adresse, avatar
                            FROM personne WHERE id_personne = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        return $u ?: null;
    }
}

// Met à jour le mot de passe d'un utilisateur
public static function updatePassword(int $id, string $hashedPassword): bool
{
    $pdo = Database::getConnection();

    $sql = "UPDATE personne SET mdp = :mdp WHERE id_personne = :id";
    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        ':mdp' => $hashedPassword,
        ':id'  => $id,
    ]);
}