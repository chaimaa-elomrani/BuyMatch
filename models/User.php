<?php
require_once 'AbstractUser.php';
require_once 'Review.php';
require_once 'Tickets.php';
class User extends AbstractUser{

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

    public function register(){
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $this->email);
        if ($stmt->execute() && $stmt->fetch()) {
            throw new Exception("Email already registered.");
        }

        $stmt = $this->db->prepare("INSERT INTO users (nom, prenom, email, password, role) 
        VALUES (?, ?, ?, ?, ?)");

        return $stmt->execute([
            $this->fullname, 
            $this->email, 
            password_hash($this->password, PASSWORD_DEFAULT), 
            $this->role]);
    }

    public function login($email, $password)
    {
        return parent::login($email, $password);
    }


    public function getTickets(){
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
    return $stmt->fetchAll();
}

    public function buyTicket($matchId, $categoryId, $placeNumber, $quantité){
       $stmt = $this->db->prepare("SELECT COUNT(*) From tickets WHERE user_id =  ? And match_id = ?");
       $stmt->execute([$this->id, $matchId]);
       if ($stmt->fetchColumn() > 4) {
           throw new Exception("you can't buy more than 4 billets .");
       }

       $stmt = $this->db->prepare("SELECT * FROM tickets 
       WHERE match_id = ? AND category_id = ? AND place_number = ?");

       $stmt->execute([$matchId, $categoryId, $placeNumber]);
       if ($stmt->fetch()) {
           throw new Exception("This place is already taken.");
       }

       $ticket = new Tickets();
       $qrCode = bin2hex(random_bytes(16)); // Generate a unique QR code
       return $ticket->create($this->id, $matchId, $categoryId, $placeNumber, $qrCode);
    }


    public function addReview($matchId , $rating, $comment){
       $review = new Review(); 
       return $review->create($this->id, $matchId, $rating , $comment);
    }

    public function getHistory(){
        $stmt = $this->db->prepare("
        SELECT r.*, m.lieu, m.date_match, e.nom AS equipe_nom 
        FROM reviews r 
        JOIN matchs m ON r.match_id = m.id 
        JOIN equipes e ON m.equipe = e.id 
        WHERE r.user_id = ? 
        ORDER BY m.date_match DESC
        ");

        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }
}