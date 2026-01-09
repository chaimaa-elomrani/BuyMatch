<?php
require_once BASE_PATH . '/config/conx.php';
class Matchs{

    //CREATE TABLE matchs (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     equipe INT NOT NULL,
//     date_match DATETIME NOT NULL,
//     lieu VARCHAR(150) NOT NULL,
//     duration INT DEFAULT 90,
//     capacity INT NOT NULL CHECK (capacity <= 2000),
//     statut ENUM('pending', 'valid', 'rejected', 'published') DEFAULT 'pending',
//     organizer_id INT NOT NULL,
//     FOREIGN KEY (equipe) REFERENCES equipes(id) ON DELETE RESTRICT,
//     FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
// );

    private $id;
    private $equipe; 
    private $date_match;
    private $lieu;
    private $duration;
    private $capacity;
    private $statut;
    private $organizer_id;
    private $db;


    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function getOrganizerId(){
        return $this->organizer_id;
    }

    public function createMatch($equipe, $lieu, $date_match, $duration, $organizer_id){
        $stmt = $this->db->prepare("
            INSERT INTO matchs (equipe, lieu, date_match, duration, capacity, organizer_id) 
            VALUES (?, ?, ?, ?, 2000, ?)
        ");

        return $stmt->execute([
            $equipe,
            $lieu,
            $date_match,
            $duration,
            $organizer_id
        ]);
    }


    public function getAllMatches(){
        $stmt = $this->db->prepare("SELECT * FROM matchs ORDER BY date_match DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateMatchStatus($matchId, $status){
        $stmt = $this->db->prepare("UPDATE matchs SET statut = ? WHERE id = ?");
        return $stmt->execute([$status, $matchId]);
    }

    public function getMatchById($matchId){
        $stmt = $this->db->prepare("SELECT * FROM matchs WHERE id = ?");
        $stmt->execute([$matchId]); 
        return $stmt->fetch();
    }

    public function deleteMatch($matchId){
        $stmt = $this->db->prepare("DELETE FROM matchs WHERE id = ?");
        return $stmt->execute([$matchId]);
    }

    public function getMatchesByOrganizer($organizerId){
        $stmt = $this->db->prepare("SELECT * FROM matchs WHERE organizer_id = ? ORDER BY date_match DESC");
        $stmt->execute([$organizerId]);
        return $stmt->fetchAll();
    }
}