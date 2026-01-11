<?php
require_once __DIR__ . '/AbstractUser.php';
require_once __DIR__ . '/Matchs.php';
require_once __DIR__ . '/Category.php';
require_once __DIR__ . '/Review.php';

class Organizer extends AbstractUser {
    
    public function __construct($id = null) {
        parent::__construct();
        $this->role = 'organizer';
        if ($id) {
            $this->loadById($id);
        }
    }

    public function getMatches(){
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    m.*,
                    t1.nom AS team1_name,
                    t2.nom AS team2_name
                FROM matchs m
                JOIN equipes t1 ON m.team1_id = t1.id
                JOIN equipes t2 ON m.team2_id = t2.id
                WHERE m.organizer_id = ?
                ORDER BY m.date_match DESC
            ");
            $stmt->execute([$this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getMatches error: " . $e->getMessage());
            return [];
        }
    }

    public function createMatch($team1Id, $team2Id, $dateMatch, $lieu, $capacity, $organizerId) {
        $match = new Matchs();
        return $match->createMatch($team1Id, $team2Id, $dateMatch, $lieu, $capacity, $organizerId);
    }

    public function addCategoryToMatch($matchId, $nom, $prix) {
        // Vérification que le match appartient bien à cet organisateur
        $stmt = $this->db->prepare("SELECT organizer_id FROM matchs WHERE id = ?");
        $stmt->execute([$matchId]);
        $match = $stmt->fetch();

        if (!$match || $match['organizer_id'] != $this->getId()) {
            throw new Exception("Match non autorisé");
        }

        $category = new Category();
        return $category->create($matchId, $nom, $prix);
    }

    public function getStatistics() {
        try {
            // Utiliser la vue SQL si elle existe, sinon calculer manuellement
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(DISTINCT m.id) AS total_matchs,
                    COUNT(t.id) AS total_billets_vendus,
                    COALESCE(SUM(c.prix), 0) AS chiffre_affaires_total,
                    COALESCE(AVG(r.note), 0) AS note_moyenne
                FROM matchs m
                LEFT JOIN tickets t ON t.match_id = m.id
                LEFT JOIN categories c ON t.category_id = c.id
                LEFT JOIN reviews r ON r.match_id = m.id
                WHERE m.organizer_id = ?
            ");
            $stmt->execute([$this->id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            // Stats par match
            $stmt = $this->db->prepare("
                SELECT 
                    m.id AS match_id,
                    CONCAT(t1.nom, ' vs ', t2.nom) AS match_name,
                    m.date_match,
                    m.statut,
                    COUNT(t.id) AS billets_vendus,
                    COALESCE(SUM(c.prix), 0) AS revenus,
                    COALESCE(AVG(r.note), 0) AS note_moyenne,
                    COUNT(r.id) AS nombre_avis
                FROM matchs m
                JOIN equipes t1 ON m.team1_id = t1.id
                JOIN equipes t2 ON m.team2_id = t2.id
                LEFT JOIN tickets t ON t.match_id = m.id
                LEFT JOIN categories c ON t.category_id = c.id
                LEFT JOIN reviews r ON r.match_id = m.id
                WHERE m.organizer_id = ?
                GROUP BY m.id
                ORDER BY m.date_match DESC
            ");
            $stmt->execute([$this->id]);
            $stats['matches_details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $stats;
        } catch (PDOException $e) {
            error_log("getStatistics error: " . $e->getMessage());
            return [
                'total_matchs' => 0,
                'total_billets_vendus' => 0,
                'chiffre_affaires_total' => 0,
                'note_moyenne' => 0,
                'matches_details' => []
            ];
        }
    }

    public function getReviews() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
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
                WHERE m.organizer_id = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getReviews error: " . $e->getMessage());
            return [];
        }
    }

    public function updateProfile($data) {
        return parent::updateProfile($data);
    }

    public function updateProfileSimple($nom, $prenom, $telephone = null) {
        return $this->updateProfile(['nom' => $nom, 'prenom' => $prenom, 'telephone' => $telephone]);
    }
}
