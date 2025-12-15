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
            ':nom'     => $data['nom'],
            ':prenom'  => $data['prenom'],
            ':pseudo'  => $data['pseudo'],
            ':email'   => $data['email'],
            ':mdp'     => $data['mdp_hash'],
            ':role'    => $role,
            ':adresse' => $adresse,
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
}