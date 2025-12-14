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
}