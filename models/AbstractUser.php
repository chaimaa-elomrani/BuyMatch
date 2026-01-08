<?php
abstract class AbstractUser {

    protected $id;
    protected $fullname;
    protected $password;
    protected $email;
    protected $role ;
    protected $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId(){
        return $this->id;
    }

    public function getFullname(){
        return $this->fullname;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getRole(){
        return $this->role;
    }

    public function setFullname($fullname){
        $this->fullname = $fullname;
    }
    public function setEmail($email){
        $this->email = $email;
    }
    public function setRole($role){
        $this->role = $role;
    }
    
    abstract public function register();
    public function login($email, $password){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_fullname'] = $user['nom'] . ' ' . $user['prenom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role']; 
            return true;
        }
        return false;

    }


    protected function loadById($id){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            $this->id = $user['id'];
            $this->fullname = $user['nom'] . ' ' . $user['prenom'];
            $this->email = $user['email'];
            $this->role = $user['role'];
        }
        return $user !== false;
    }
    
    

}