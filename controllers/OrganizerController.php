<?php
require_once __DIR__ . '/../models/Organizer.php';
require_once __DIR__ . '/../models/Matchs.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../config/conx.php';

class OrganizerController {
    
    private function checkAuth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
            header("Location: ?route=auth&action=login");
            exit();
        }
    }
    
    public function dashboard() {
        $this->checkAuth();
        
        $organizer = new Organizer();
        $organizer->loadById($_SESSION['user_id']);
        
        // Get basic stats
        $stats = $organizer->getStatistics();
        $matches = $organizer->getMatches();
        
        require_once __DIR__ . '/../public/views/organizer/dashboard.php';
    }

    public function profile() {
        $this->checkAuth();
        
        $organizer = new Organizer();
        $organizer->loadById($_SESSION['user_id']);
        
        $errors = [];
        $success = false;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            
            if (empty($nom) || empty($prenom)) {
                $errors[] = "Le nom et le prénom sont requis.";
            } else {
                try {
                    if ($organizer->updateProfileSimple($nom, $prenom, $telephone)) {
                        $success = true;
                        $_SESSION['user_nom'] = $nom;
                        $_SESSION['user_prenom'] = $prenom;
                    } else {
                        $errors[] = "Erreur lors de la mise à jour.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../public/views/organizer/profile.php';
    }

    public function createMatch() {
        $this->checkAuth();
        
        $organizer = new Organizer();
        $organizer->loadById($_SESSION['user_id']);
        
        $errors = [];
        $success = false;
        
        // Get all teams
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM equipes ORDER BY nom");
            $stmt->execute();
            $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $teams = [];
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $team1Id = $_POST['team1_id'] ?? null;
            $team2Id = $_POST['team2_id'] ?? null;
            $dateMatch = $_POST['date_match'] ?? null;
            $lieu = trim($_POST['lieu'] ?? '');
            $capacity = $_POST['capacity'] ?? null;
            $categories = $_POST['categories'] ?? [];
            
            // Validation
            if (empty($team1Id) || empty($team2Id)) {
                $errors[] = "Veuillez sélectionner deux équipes.";
            } elseif ($team1Id == $team2Id) {
                $errors[] = "Les deux équipes doivent être différentes.";
            } elseif (empty($dateMatch)) {
                $errors[] = "La date du match est requise.";
            } elseif (empty($lieu)) {
                $errors[] = "Le lieu est requis.";
            } elseif (empty($capacity) || $capacity < 1 || $capacity > 2000) {
                $errors[] = "La capacité doit être entre 1 et 2000.";
            } elseif (empty($categories) || count($categories) == 0) {
                $errors[] = "Veuillez ajouter au moins une catégorie.";
            } elseif (count($categories) > 3) {
                $errors[] = "Maximum 3 catégories autorisées.";
            } else {
                try {
                    // Create match
                    $matchId = $organizer->createMatch(
                        $team1Id,
                        $team2Id,
                        $dateMatch,
                        $lieu,
                        $capacity,
                        $_SESSION['user_id']
                    );
                    
                    if ($matchId) {
                        // Add categories
                        foreach ($categories as $cat) {
                            if (!empty($cat['nom']) && !empty($cat['prix'])) {
                                $organizer->addCategoryToMatch($matchId, $cat['nom'], $cat['prix']);
                            }
                        }
                        $success = true;
                    } else {
                        $errors[] = "Erreur lors de la création du match.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../public/views/organizer/create_match.php';
    }

    public function statistics() {
        $this->checkAuth();
        
        $organizer = new Organizer();
        $organizer->loadById($_SESSION['user_id']);
        
        $stats = $organizer->getStatistics();
        
        require_once __DIR__ . '/../public/views/organizer/statistics.php';
    }

    public function reviews() {
        $this->checkAuth();
        
        $organizer = new Organizer();
        $organizer->loadById($_SESSION['user_id']);
        
        $reviews = $organizer->getReviews();
        
        require_once __DIR__ . '/../public/views/organizer/reviews.php';
    }
}
