<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    
    public function dashboard() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?route=auth&action=login");
            exit();
        }
        
        // Load user
        $user = new User();
        $loaded = $user->loadById($_SESSION['user_id']);
        
        if (!$loaded) {
            session_destroy();
            header("Location: ?route=auth&action=login");
            exit();
        }
        
        // Get user tickets
        $tickets = $user->getTickets();
        
        // Display dashboard
        require_once __DIR__ . '/../public/views/user/dashboard.php';
    }
}
