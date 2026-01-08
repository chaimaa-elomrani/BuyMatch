<?php

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
    
   
}