<?php
// models/AbstractUser.php - Classe abstraite pour tous les utilisateurs
require_once __DIR__ . '/../config/conx.php';

abstract class AbstractUser {
    protected $id;
    protected $email;
    protected $password;
    protected $nom;
    protected $prenom;
    protected $telephone;
    protected $role;
    protected $isActive;
    protected $createdAt;
    
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getNom() {
        return $this->nom;
    }
    
    public function getPrenom() {
        return $this->prenom;
    }
    
    public function getTelephone() {
        return $this->telephone;
    }
    
    public function getRole() {
        return $this->role;
    }
    
    public function getIsActive() {
        return $this->isActive;
    }
    
    public function getFullname() {
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? ''));
    }
    
    // Setters
    public function setEmail($email) {
        $this->email = filter_var($email, FILTER_SANITIZE_EMAIL);
    }
    
    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function setNom($nom) {
        $this->nom = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');
    }
    
    public function setPrenom($prenom) {
        $this->prenom = htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8');
    }
    
    public function setTelephone($telephone) {
        $this->telephone = htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8');
    }
    
    public function setIsActive($isActive) {
        $this->isActive = (bool)$isActive;
    }
    
    // Méthode concrète commune pour login
    public function login($email, $password) {
        // Trim and normalize email for comparison
        $email = trim(strtolower($email));
        
        if (empty($email) || empty($password)) {
            return false;
        }
        
        try {
            // Use LOWER() in SQL for case-insensitive email comparison
            $stmt = $this->db->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Check if user is active (check status field)
                if (isset($user['status']) && $user['status'] === 'inactive') {
                    return false;
                }
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_nom'] = $user['nom'] ?? '';
                $_SESSION['user_prenom'] = $user['prenom'] ?? '';
                $_SESSION['user_email'] = $user['email'];
                
                // Load user data into object
                $this->id = $user['id'];
                $this->email = $user['email'];
                $this->nom = $user['nom'] ?? '';
                $this->prenom = $user['prenom'] ?? '';
                $this->role = $user['role'];
                $this->isActive = (isset($user['status']) && $user['status'] === 'active') ? 1 : 0;
                
                return true;
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
        
        return false;
    }
    
    // Méthode pour mettre à jour le profil
    public function updateProfile($data) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET nom = ?, prenom = ?, telephone = ? 
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['telephone'],
            $this->id
        ]);
    }
    
    // Charger les données d'un utilisateur par ID
    public function loadById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($data) {
                $this->id = $data['id'];
                $this->email = $data['email'];
                $this->nom = $data['nom'] ?? '';
                $this->prenom = $data['prenom'] ?? '';
                $this->telephone = $data['telephone'] ?? '';
                $this->role = $data['role'] ?? 'user';
                $this->isActive = (isset($data['status']) && $data['status'] === 'active') ? 1 : 0;
                $this->createdAt = $data['created_at'] ?? null;
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("loadById error: " . $e->getMessage());
            return false;
        }
    }
}
