<?php
require_once __DIR__ . '/../config/conx.php';
require_once 'AbstractUser.php';
class Admin extends AbstractUser
{

    public function __construct($id)
    {
        parent::__construct();
        $this->role = 'admin';
        if ($id !== null) {
            $this->loadById($id);
        }
    }

    // Exemple pour Organizer.php
    public function register()
    {
        throw new Exception("Les comptes administrateurs ne peuvent pas s'inscrire via cette méthode");
    }

    public function validateMatch($matchId)
    {
        return $this->updateMatchStatus($matchId, 'validated');
    }

    public function rejectMatch($matchId)
    {
        return $this->updateMatchStatus($matchId, 'rejected');
    }

    public function publishMatch($matchId)
    {
        return $this->updateMatchStatus($matchId, 'published');
    }

    private function updateMatchStatus($matchId, $status)
    {
        $allowed = ['validated', 'rejected', 'published'];
        if (!in_array($status, $allowed)) {
            throw new Exception("Statut invalide");
        }
        $stmt = $this->db->prepare("UPDATE matchs SET statut = ? WHERE id = ?");
        return $stmt->execute([
            $status,
            $matchId
        ]);
    }

    public function getPendingMatches()
    {
        $stmt = $this->db->prepare(
            "SELECT m.*, 
                   t1.nom AS team1_name, t2.nom AS team2_name,
                   u.nom AS org_nom, u.prenom AS org_prenom
            FROM matchs m
            JOIN equipes t1 ON m.team1_id = t1.id
            JOIN equipes t2 ON m.team2_id = t2.id
            JOIN users u ON m.organizer_id = u.id
            WHERE m.statut = 'pending'
            ORDER BY m.date_match
            "
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllMatches()
    {
        $stmt = $this->db->prepare("CALL getAllMatches()");
        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function getGlobalStats()
    {
        $stmt = $this->db->prepare("CALL get_global_stats()");
        $stmt->execute();
        return $stmt->fetch();
    }
}