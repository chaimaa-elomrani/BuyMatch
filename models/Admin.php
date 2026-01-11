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
        $stmt = $this->db->prepare(
            "SELECT m.*, 
                   t1.nom AS team1_name, t2.nom AS team2_name,
                   u.nom AS org_nom, u.prenom AS org_prenom
            FROM matchs m
            JOIN equipes t1 ON m.team1_id = t1.id
            JOIN equipes t2 ON m.team2_id = t2.id
            JOIN users u ON m.organizer_id = u.id
            ORDER BY m.date_match DESC
            "
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getGlobalStats()
    {
        try {
            $stmt = $this->db->prepare("CALL get_global_stats()");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result ? $result : [
                'total_matchs_published' => 0,
                'total_billets_vendus' => 0,
                'chiffre_affaires_total' => 0,
                'note_moyenne_generale' => 0
            ];
        } catch (PDOException $e) {
            error_log("getGlobalStats error: " . $e->getMessage());
            return [
                'total_matchs_published' => 0,
                'total_billets_vendus' => 0,
                'chiffre_affaires_total' => 0,
                'note_moyenne_generale' => 0
            ];
        }
    }

    public function getAllUsers()
    {
        $stmt = $this->db->prepare(
            "SELECT id, nom, prenom, email, role, status, created_at 
            FROM users 
            ORDER BY created_at DESC
            "
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function activateUser($userId)
    {
        $stmt = $this->db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function deactivateUser($userId)
    {
        $stmt = $this->db->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function getAllReviews()
    {
        $stmt = $this->db->prepare(
            "SELECT 
                r.*,
                m.id AS match_id,
                CONCAT(t1.nom, ' vs ', t2.nom) AS match_name,
                m.date_match,
                u.nom AS user_nom,
                u.prenom AS user_prenom
            FROM reviews r
            JOIN matchs m ON r.match_id = m.id
            JOIN equipes t1 ON m.team1_id = t1.id
            JOIN equipes t2 ON m.team2_id = t2.id
            JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
            "
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}