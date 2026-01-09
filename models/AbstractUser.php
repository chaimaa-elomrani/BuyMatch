<?php
require_once 'C:\laragon\www\BuyMatch\config\conx.php';
abstract class AbstractUser {

    protected $id;
    protected $nom;
    protected $prenom;
    protected $password;
    protected $email;
    protected $role ;
    protected $status;
    protected $db;


    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        ?string $prenom = null,
        ?string $email = null,
        ?string $password = null,
        string $role = 'user',
        string $status = 'active'
    ) {
        $this->db = Database::getInstance()->getConnection();

        $this->id       = $id;
        $this->nom      = $nom ?? '';
        $this->prenom   = $prenom ?? '';
        $this->email    = $email ?? '';
        $this->role     = $role;
        $this->status   = $status;

        // Si mot de passe fourni, on le hash immédiatement (utile pour register)
        if ($password !== null && $password !== '') {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
        }
    }

    public function getId(){
        return $this->id;
    }
    
    public function getFullname(){
        return trim($this->nom . ' ' . $this->prenom);
    }

    public function getEmail(){
        return $this->email;
    }
    public function getRole(){
        return $this->role;
    }

    public function setNom($nom){
        $this->nom = $nom;
    }
    public function setPrenom($prenom){
        $this->prenom = $prenom;
    }
  
    public function setEmail($email){
        $this->email = $email;
    }

    public function setPassword($password){
        $this->password = password_hash($password, PASSWORD_DEFAULT);
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
    $stmt = $this->db->prepare("SELECT id, nom, prenom, email, role, password FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $this->id = $user['id'];
        $this->nom = $user['nom'];
        $this->prenom = $user['prenom'];
        $this->email = $user['email'];
        $this->role = $user['role'];
        $this->password = $user['password']; // optionnel
        return true;
    }
    return false;
}
    
    

}