<?php 
class AdminController {
    
    public function dashboard() {
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: ?route=auth&action=login");
            exit;
        }
        
        echo "<h1>Espace Administrateur</h1>";
        echo "<p>Bienvenue, " . htmlspecialchars($_SESSION['user_nom'] . ' ' . $_SESSION['user_prenom']) . "</p>";
        echo "<a href='?route=auth&action=logout'>Déconnexion</a>";
    }
}
