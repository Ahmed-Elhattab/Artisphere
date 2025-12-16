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
}