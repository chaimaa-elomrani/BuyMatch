<?php
require_once __DIR__ . '/AbstractUser.php';
require_once __DIR__ . '/Review.php';
require_once __DIR__ . '/Tickets.php';

class User extends AbstractUser {

    public function __construct($fullname = null, $email = null, $role = 'user'){
        parent::__construct();
        if ($fullname) {
            $this->fullname = $fullname;
        }
        if ($email) {
            $this->email = $email;
        }
        $this->role = $role;
    }

    public function register($email, $password, $nom, $prenom, $role = 'user'){
        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception("Email already registered.");
            }

            // Insert new user with hashed password
            $stmt = $this->db->prepare("INSERT INTO users (nom, prenom, email, password, role) 
                VALUES (?, ?, ?, ?, ?)");

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $result = $stmt->execute([
                $nom,
                $prenom, 
                $email, 
                $hashedPassword, 
                $role
            ]);
            
            if ($result) {
                // Load the newly created user data
                $this->id = $this->db->lastInsertId();
                $this->email = $email;
                $this->nom = $nom;
                $this->prenom = $prenom;
                $this->role = $role;
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            throw new Exception("Registration failed: " . $e->getMessage());
        }
    }

    public function login($email, $password) {
        return parent::login($email, $password);
    }

    public function loadById($id) {
        return parent::loadById($id);
    }

    public function getTickets(){
        if (!$this->id) {
            return [];
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    t.*, 
                    m.lieu, 
                    m.date_match, 
                    m.duration, 
                    c.nom AS category_name, 
                    c.prix,
                    t1.nom AS team1_name,
                    t2.nom AS team2_name,
                    t1.logo AS team1_logo,
                    t2.logo AS team2_logo,
                    t.place_number 
                FROM tickets t 
                JOIN matchs m ON t.match_id = m.id 
                JOIN categories c ON t.category_id = c.id 
                JOIN equipes t1 ON m.team1_id = t1.id
                JOIN equipes t2 ON m.team2_id = t2.id
                WHERE t.user_id = ?
                ORDER BY m.date_match DESC
            ");

            $stmt->execute([$this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getTickets error: " . $e->getMessage());
            return [];
        }
    }

    public function buyTicket($matchId, $categoryId, $placeNumber, $quantité){
        if (!$this->id) {
            throw new Exception("User not logged in.");
        }
        
        try {
            // Check ticket limit per user per match
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND match_id = ?");
            $stmt->execute([$this->id, $matchId]);
            if ($stmt->fetchColumn() >= 4) {
                throw new Exception("You can't buy more than 4 tickets per match.");
            }

            // Check if place is already taken
            $stmt = $this->db->prepare("SELECT * FROM tickets 
                WHERE match_id = ? AND category_id = ? AND place_number = ? LIMIT 1");
            $stmt->execute([$matchId, $categoryId, $placeNumber]);
            if ($stmt->fetch()) {
                throw new Exception("This place is already taken.");
            }

            // Create ticket
            $ticket = new Tickets();
            $qrCode = bin2hex(random_bytes(16)); // Generate a unique QR code
            return $ticket->create($this->id, $matchId, $categoryId, $placeNumber, $qrCode);
        } catch (PDOException $e) {
            error_log("buyTicket error: " . $e->getMessage());
            throw new Exception("Failed to buy ticket: " . $e->getMessage());
        }
    }

    public function addReview($matchId, $rating, $comment){
        if (!$this->id) {
            throw new Exception("User not logged in.");
        }
        
        try {
            $review = new Review(); 
            return $review->create($this->id, $matchId, $rating, $comment);
        } catch (Exception $e) {
            error_log("addReview error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getHistory(){
        if (!$this->id) {
            return [];
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT r.*, m.lieu, m.date_match, e.nom AS equipe_nom 
                FROM reviews r 
                JOIN matchs m ON r.match_id = m.id 
                JOIN equipes e ON m.equipe = e.id 
                WHERE r.user_id = ? 
                ORDER BY m.date_match DESC
            ");

            $stmt->execute([$this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getHistory error: " . $e->getMessage());
            return [];
        }
    }
}
