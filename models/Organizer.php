<?php
require_once 'models/AbstractUser.php';
require_once 'models/Matchs.php';

class organizer extends AbstractUser{
   public function __construct($id = null) {
        parent::__construct();
        $this->role = 'organizer';
        if ($id) {
            $this->loadById($id);
        }
    }   

    public function register(){
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$this->email]);
        if ($stmt->fetch()) {
            return false;
        }
         $stmt = $this->db->prepare("
            INSERT INTO users (email, password, fullname, role) 
            VALUES (?, ?, ?, 'organizer')
        ");

        return $stmt->execute([
            $this->email,
            $this->fullname,
            $this->password,
            $this->role
        ]);
    }


    public function createMatch($equipe, $lieu, $date_match, $duration){
        $match = new Matchs();
        return $match->createMatch($equipe, $lieu, $date_match, $duration, $this->id);
    }


    public function getMatches(){
        $stmt = $this->db->prepare("SELECT * FROM matchs WHERE organizer_id = ? ORDER BY date_match DESC");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

   





}