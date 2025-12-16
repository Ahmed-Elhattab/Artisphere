<?php
class FaqModel
{
    public static function getAllGroupedByCategorie(): array
    {
        $pdo = Database::getConnection();

        $sql = "SELECT id_FAQ, categorie, question, reponse
                FROM faq
                ORDER BY categorie ASC, id_FAQ ASC";

        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($rows as $r) {
            $cat = trim((string)$r['categorie']);
            if ($cat === '') $cat = 'Autres';

            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [];
            }
            $grouped[$cat][] = $r;
        }
        return $grouped;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO faq (categorie, question, reponse)
                VALUES (:categorie, :question, :reponse)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':categorie' => $data['categorie'],
            ':question'  => $data['question'],
            ':reponse'   => $data['reponse'],
        ]);

        return (int)$pdo->lastInsertId();
    }
}