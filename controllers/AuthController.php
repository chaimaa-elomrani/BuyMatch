<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    public function login() {
        $errors = [];
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validation
            if (empty($email)) {
                $errors[] = "L'email est requis.";
            } elseif (empty($password)) {
                $errors[] = "Le mot de passe est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide.";
            } else {
                // Try to login
                try {
                    $user = new User();
                    $loginResult = $user->login($email, $password);
                    
                    if ($loginResult === true) {
                        // Login successful - check role and redirect accordingly
                        $role = $_SESSION['user_role'] ?? 'user';
                        
                        // Redirect directly to appropriate dashboard based on role
                        switch($role) {
                            case 'admin':
                                header("Location: ?route=admin&action=dashboard");
                                exit();
                            case 'organizer':
                                header("Location: ?route=organizer&action=dashboard");
                                exit();
                            case 'user':
                            default:
                                header("Location: ?route=auth&action=success");
                                exit();
                        }
                    } else {
                        $errors[] = "Email ou mot de passe incorrect.";
                    }
                } catch (Exception $e) {
                    $errors[] = "Erreur: " . $e->getMessage();
                    error_log("Login error: " . $e->getMessage());
                }
            }
        }

        // Display login form
        require_once __DIR__ . '/../public/views/auth/login.php';
    }

    public function register() {
        $errors = [];
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';

            // Validation
            if (empty($nom)) {
                $errors[] = "Le nom est requis.";
            } elseif (empty($prenom)) {
                $errors[] = "Le prénom est requis.";
            } elseif (empty($email)) {
                $errors[] = "L'email est requis.";
            } elseif (empty($password)) {
                $errors[] = "Le mot de passe est requis.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide.";
            } elseif (strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            } elseif (!in_array($role, ['user', 'organizer'])) {
                $errors[] = "Type de compte invalide.";
            } else {
                // Try to register
                try {
                    $user = new User();
                    $registerResult = $user->register($email, $password, $nom, $prenom, $role);
                    
                    if ($registerResult === true) {
                        // Registration successful - redirect to login
                        header("Location: ?route=auth&action=login&registered=1");
                        exit();
                    } else {
                        $errors[] = "Erreur lors de l'inscription.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        // Display register form
        require_once __DIR__ . '/../public/views/auth/register.php';
    }

    public function success() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            header("Location: ?route=auth&action=login");
            exit();
        }
        
        // Determine dashboard URL based on role
        $role = $_SESSION['user_role'];
        $dashboardUrl = '?route=user&action=dashboard';
        
        if ($role === 'admin') {
            $dashboardUrl = '?route=admin&action=dashboard';
        } elseif ($role === 'organizer') {
            $dashboardUrl = '?route=organizer&action=dashboard';
        }
        
        // Display success page with role-specific message
        require_once __DIR__ . '/../public/views/auth/success.php';
    }

    public function logout() {
        // Destroy session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Redirect to matches page (public home)
        header("Location: ?route=match&action=index");
        exit();
    }
}
