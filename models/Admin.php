<?php
class Admin extends AbstractUser {

    public function __construct($id){
        parent::__construct();
        $this->role = 'admin';
        if($id !== null){
        $this->loadById($id);
        }
    }


    public function validateMatch($matchId){
        return $this->updateMatchStatus($matchId, 'validated');
    }

    public function rejectMatch($matchId){
        return $this->updateMatchStatus($matchId, 'rejected');
    }

    public function publishMatch($matchId){
        return $this->updateMatchStatus($matchId, 'published');
    }

    private function updateMatchStatus($matchId, $status){
        $allowed = ['validated', 'rejected', 'published'];
        if(!in_array($status, $allowed)){
            throw new Exception("Statut invalide");
        }
        $stmt = $this->db->prepare("UPDATE matchs SET statut = ? WHERE id = ?");
        return $stmt->execute([
            $status , 
            $matchId
        ]);
    }


}