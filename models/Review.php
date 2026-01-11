<?php
require_once __DIR__ . '/../config/conx.php';

class Review {
    private $id;
    private $userId;
    private $matchId;
    private $rating;
    private $comment;
    private $createdAt;
    private $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId(){
        return $this->id;
    }

    public function getUserId(){
        return $this->userId;
    }

    public function getMatchId(){
        return $this->matchId;
    }

    public function getRating(){
        return $this->rating;
    }

    public function getComment(){
        return $this->comment;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }

    public function create($userId, $matchId, $rating, $comment){
        try {
            // Check if user already reviewed this match
            $stmt = $this->db->prepare("SELECT id FROM reviews WHERE user_id = ? AND match_id = ? LIMIT 1");
            $stmt->execute([$userId, $matchId]);
            if ($stmt->fetch()) {
                throw new Exception("Vous avez déjà donné un avis pour ce match.");
            }

            // Validate rating
            if ($rating < 1 || $rating > 5) {
                throw new Exception("La note doit être entre 1 et 5.");
            }

            $stmt = $this->db->prepare("
                INSERT INTO reviews (user_id, match_id, note, comment, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $result = $stmt->execute([$userId, $matchId, $rating, $comment]);
            
            if ($result) {
                $this->id = $this->db->lastInsertId();
                return $this->id;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Review creation error: " . $e->getMessage());
            throw new Exception("Erreur lors de la création de l'avis.");
        }
    }

    public function getByMatch($matchId){
        $stmt = $this->db->prepare("
            SELECT r.*, u.nom, u.prenom 
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.match_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$matchId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser($userId){
        $stmt = $this->db->prepare("
            SELECT r.*, m.date_match, t1.nom AS team1_name, t2.nom AS team2_name
            FROM reviews r
            JOIN matchs m ON r.match_id = m.id
            JOIN equipes t1 ON m.team1_id = t1.id
            JOIN equipes t2 ON m.team2_id = t2.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
