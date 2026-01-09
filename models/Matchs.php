<?php
require_once __DIR__ . '/../config/conx.php';
class Matchs{
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

   public function createMatch($team1Id, $team2Id, $dateMatch, $lieu, $capacity, $organizerId)
{
    if ($capacity > 2000) {
        throw new Exception("Capacity cannot exceed 2000");
    }

    $stmt = $this->db->prepare("
        INSERT INTO matchs (team1_id, team2_id, date_match, lieu, capacity, organizer_id, statut)
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");

    $result = $stmt->execute([$team1Id, $team2Id, $dateMatch, $lieu, $capacity, $organizerId]);

    if ($result) {
        return $this->db->lastInsertId();
    }
    return false;
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