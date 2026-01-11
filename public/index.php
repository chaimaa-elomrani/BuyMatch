<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Get route and action from URL
$route = $_GET['route'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Simple router
try {
    switch ($route) {
        case 'auth':
            require_once __DIR__ . '/../controllers/AuthController.php';
            $controller = new AuthController();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                die("Error: Action '$action' not found in AuthController");
            }
            break;
            
        case 'user':
            require_once __DIR__ . '/../controllers/UserController.php';
            $controller = new UserController();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                die("Error: Action '$action' not found in UserController");
            }
            break;

        case 'organizer':
            require_once __DIR__ . '/../controllers/OrganizerController.php';
            $controller = new OrganizerController();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                die("Error: Action '$action' not found in OrganizerController");
            }
            break;

        case 'admin':
            require_once __DIR__ . '/../controllers/AdminController.php';
            $controller = new AdminController();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                die("Error: Action '$action' not found in AdminController");
            }
            break;
        
        default:
            echo "<h1>Bienvenue sur BuyMatch</h1>";
            echo "<p><a href='?route=auth&action=login'>Se connecter</a> | <a href='?route=auth&action=register'>S'inscrire</a></p>";
            break;
    }
} catch (Exception $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}
