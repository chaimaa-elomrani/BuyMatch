<?php
require_once __DIR__ . '/../models/Admin.php';

class AdminController {
    
    private function checkAuth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: ?route=auth&action=login");
            exit();
        }
    }
    
    public function dashboard() {
        $this->checkAuth();
        
        $admin = new Admin($_SESSION['user_id']);
        $stats = $admin->getGlobalStats();
        $pendingMatches = $admin->getPendingMatches();
        
        require_once __DIR__ . '/../public/views/admin/dashboard.php';
    }

    public function validateMatches() {
        $this->checkAuth();
        
        $admin = new Admin($_SESSION['user_id']);
        $errors = [];
        $success = false;
        
        // Handle POST request (validate/reject match)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matchId = $_POST['match_id'] ?? null;
            $action = $_POST['action'] ?? '';
            
            if (empty($matchId)) {
                $errors[] = "ID de match invalide.";
            } elseif ($action === 'validate') {
                try {
                    if ($admin->validateMatch($matchId)) {
                        $success = true;
                    } else {
                        $errors[] = "Erreur lors de la validation.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            } elseif ($action === 'reject') {
                try {
                    if ($admin->rejectMatch($matchId)) {
                        $success = true;
                    } else {
                        $errors[] = "Erreur lors du rejet.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            } elseif ($action === 'publish') {
                try {
                    if ($admin->publishMatch($matchId)) {
                        $success = true;
                    } else {
                        $errors[] = "Erreur lors de la publication.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        $pendingMatches = $admin->getPendingMatches();
        $allMatches = $admin->getAllMatches();
        
        require_once __DIR__ . '/../public/views/admin/validate_matches.php';
    }

    public function manageUsers() {
        $this->checkAuth();
        
        $admin = new Admin($_SESSION['user_id']);
        $errors = [];
        $success = false;
        
        // Handle POST request (activate/deactivate user)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $action = $_POST['action'] ?? '';
            
            if (empty($userId)) {
                $errors[] = "ID utilisateur invalide.";
            } elseif ($userId == $_SESSION['user_id']) {
                $errors[] = "Vous ne pouvez pas modifier votre propre compte.";
            } elseif ($action === 'activate') {
                try {
                    if ($admin->activateUser($userId)) {
                        $success = true;
                    } else {
                        $errors[] = "Erreur lors de l'activation.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            } elseif ($action === 'deactivate') {
                try {
                    if ($admin->deactivateUser($userId)) {
                        $success = true;
                    } else {
                        $errors[] = "Erreur lors de la désactivation.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        $users = $admin->getAllUsers();
        
        require_once __DIR__ . '/../public/views/admin/manage_users.php';
    }

    public function statistics() {
        $this->checkAuth();
        
        $admin = new Admin($_SESSION['user_id']);
        $stats = $admin->getGlobalStats();
        
        require_once __DIR__ . '/../public/views/admin/statistics.php';
    }

    public function reviews() {
        $this->checkAuth();
        
        $admin = new Admin($_SESSION['user_id']);
        $reviews = $admin->getAllReviews();
        
        require_once __DIR__ . '/../public/views/admin/reviews.php';
    }
}
