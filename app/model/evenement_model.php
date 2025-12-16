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
}