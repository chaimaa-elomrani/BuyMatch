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
}