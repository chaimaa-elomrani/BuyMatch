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

// Define public routes (accessible without login)
$publicRoutes = ['match', 'auth'];
$publicAuthActions = ['login', 'register', 'success', 'logout'];

// Check if route requires authentication
$requiresAuth = !in_array($route, $publicRoutes);

// If route requires auth but user is not logged in, redirect to login
if ($requiresAuth && !isset($_SESSION['user_id'])) {
    header("Location: ?route=auth&action=login");
    exit();
}

// For auth route, check if action is public
if ($route === 'auth' && !in_array($action, $publicAuthActions)) {
    // Only allow public auth actions for visitors
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?route=auth&action=login");
        exit();
    }
}

// Simple router
try {
    switch ($route) {
        case 'auth':
            require_once __DIR__ . '/../controllers/AuthController.php';
            $controller = new AuthController();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                header("Location: ?route=match&action=index");
                exit();
            }
            break;
            
        case 'user':
            // Protected route - already checked above
            require_once __DIR__ . '/../controllers/UserController.php';
            $controller = new UserController();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                header("Location: ?route=match&action=index");
                exit();
            }
            break;

        case 'organizer':
            // Protected route - already checked above
            require_once __DIR__ . '/../controllers/OrganizerController.php';
            $controller = new OrganizerController();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                header("Location: ?route=match&action=index");
                exit();
            }
            break;

        case 'admin':
            // Protected route - already checked above
            require_once __DIR__ . '/../controllers/AdminController.php';
            $controller = new AdminController();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                header("Location: ?route=match&action=index");
                exit();
            }
            break;

        case 'match':
            // Public route - visitors can access
            require_once __DIR__ . '/../controllers/MatchController.php';
            $controller = new MatchController();
            if (method_exists($controller, $action)) {
                $controller->$action();
            } else {
                header("Location: ?route=match&action=index");
                exit();
            }
            break;
        
        default:
            // Redirect to matches page as home
            header("Location: ?route=match&action=index");
            exit();
            break;
    }
} catch (Exception $e) {
    error_log("Router error: " . $e->getMessage());
    header("Location: ?route=match&action=index");
    exit();
}
