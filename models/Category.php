<?php
require_once __DIR__ . '/../config/conx.php';
class Category
{
    private $id;
    private $match_id;
    private $nom;
    private $prix;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($matchId, $nom, $prix)
    {
        $stmt = $this->db->prepare("
            INSERT INTO categories (match_id, nom, prix) 
            VALUES (?, ?, ?)
        ");

        $result = $stmt->execute([$matchId, $nom, $prix]);

        if ($result) {
            return $this->db->lastInsertId();
        }
        return false; // Le trigger BD lèvera une erreur si >3
    }

    public function getByMatch($matchId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM categories WHERE match_id = ? ORDER BY prix DESC
        ");
        $stmt->execute([$matchId]);
        return $stmt->fetchAll();
    }
}