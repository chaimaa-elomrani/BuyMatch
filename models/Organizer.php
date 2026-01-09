<?php
require_once 'AbstractUser.php';
require_once 'Matchs.php';
require_once 'Category.php';

class organizer extends AbstractUser{
   public function __construct($id = null) {
        parent::__construct();
        $this->role = 'organizer';
        if ($id) {
            $this->loadById($id);
        }
    }   

    // Exemple pour Organizer.php
public function register()
{
    $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
    
    $stmt->execute([':email' => $this->email]);
    if ($stmt->fetch()) {
        throw new Exception("Email déjà utilisé");
    }

    $stmt = $this->db->prepare("
        INSERT INTO users (nom, prenom, email, password, role) 
        VALUES (?, ?, ?, ?, 'organizer')
    ");

    $success = $stmt->execute([
        $this->nom ?? 'Organisateur',   
        $this->prenom ?? '',
        $this->email,
        password_hash($this->password, PASSWORD_DEFAULT)
    ]);

    if ($success) {
        $this->id = $this->db->lastInsertId();
        return true;
    }
    return false;
}

   public function createMatch($team1Id, $team2Id, $dateMatch, $lieu, $capacity, $organizerId)
{
    $match = new Matchs();
    return $match->createMatch($team1Id, $team2Id, $dateMatch, $lieu, $capacity, $organizerId);
}


    public function getMatches(){
        $stmt = $this->db->prepare("SELECT * FROM matchs WHERE organizer_id = ? ORDER BY date_match DESC");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

   

public function addCategoryToMatch($matchId, $nom, $prix)
{
    // Optionnel : vérification que le match appartient bien à cet organisateur
    $stmt = $this->db->prepare("SELECT organizer_id FROM matchs WHERE id = ?");
    $stmt->execute([$matchId]);
    $match = $stmt->fetch();

    if (!$match || $match['organizer_id'] != $this->getId()) {
        throw new Exception("Match non autorisé");
    }

    $category = new Category();
    return $category->create($matchId, $nom, $prix);
}



}