<?php

class AuthController {

    public function login($email, $password) {
        $errors = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if(empty($email) || empty($password)){
                $errors[] = "Email et mot de passe requis.";
            }else{
                $user = new User();
                if($user->login($email, $password)){
                    $role = $_SESSION['user_role'] ?? 'user';
                    header("Location: ?route=$role&action=dashboard");
                    exit;
                }else{
                    $errors[] = "Email ou mot de passe incorrect.";    
            }
        }
    }
    require_once __DIR__ . '/../views/auth/login.php';
}

public function register(){
    $errors = [];
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if(empty($nom) || empty($prenom) || empty($email) || empty($password)){
            $errors[] = "Tous les champs sont requis.";
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[] = "Email invalide.";
        }else{
            // this condition means all inputs are valid
            $user = new User();
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setEmail($email);
            $user->setPassword($password); // sera hashé dans le constructeur

            try{
                if($user->register()){
                    echo "<p style='color:green;'>Inscription réussie ! Vous pouvez maintenant vous connecter.</p>";
                    echo "<a href='?route=auth&action=login'>Se connecter</a>";
                }
            }catch(Exception $e){
                $errors[] = $e->getMessage();
            }
        }
    }
    require_once __DIR__ . '/../views/auth/register.php';
}

public function logout(){
    session_destroy();
    header("Location: ?route=auth&action=login");
    exit;
}

}