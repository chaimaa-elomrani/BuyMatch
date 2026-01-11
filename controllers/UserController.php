<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Matchs.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Tickets.php';
require_once __DIR__ . '/../models/Review.php';

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

    public function profile() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?route=auth&action=login");
            exit();
        }
        
        $user = new User();
        $user->loadById($_SESSION['user_id']);
        
        $errors = [];
        $success = false;
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            
            if (empty($nom) || empty($prenom)) {
                $errors[] = "Le nom et le prénom sont requis.";
            } else {
                try {
                    if ($user->updateProfileSimple($nom, $prenom, $telephone)) {
                        $success = true;
                        $_SESSION['user_nom'] = $nom;
                        $_SESSION['user_prenom'] = $prenom;
                    } else {
                        $errors[] = "Erreur lors de la mise à jour.";
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../public/views/user/profile.php';
    }

    public function matches() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?route=auth&action=login");
            exit();
        }
        
        $user = new User();
        $user->loadById($_SESSION['user_id']);
        
        // Get all published matches
        $matchModel = new Matchs();
        $matches = $matchModel->getPublishedMatches();
        
        // Get categories for each match
        $categoryModel = new Category();
        foreach ($matches as &$match) {
            $match['categories'] = $categoryModel->getByMatch($match['id']);
        }
        
        require_once __DIR__ . '/../public/views/user/matches.php';
    }

    public function buyTicket() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?route=auth&action=login");
            exit();
        }
        
        $user = new User();
        $user->loadById($_SESSION['user_id']);
        
        $errors = [];
        $matchId = $_GET['match_id'] ?? null;
        $categoryId = $_GET['category_id'] ?? null;
        
        // Get match details
        $matchModel = new Matchs();
        $match = null;
        $categories = [];
        $availableSeats = [];
        
        if ($matchId) {
            $match = $matchModel->getPublishedMatchById($matchId);
            if ($match) {
                $categoryModel = new Category();
                $categories = $categoryModel->getByMatch($matchId);
                $match['categories'] = $categories;
                
                if ($categoryId) {
                    $availableSeats = $user->getAvailableSeats($matchId, $categoryId);
                }
            }
        }
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matchId = $_POST['match_id'] ?? null;
            $categoryId = $_POST['category_id'] ?? null;
            $placeNumber = $_POST['place_number'] ?? null;
            
            if (empty($matchId) || empty($categoryId) || empty($placeNumber)) {
                $errors[] = "Tous les champs sont requis.";
            } else {
                try {
                    $ticketId = $user->buyTicket($matchId, $categoryId, $placeNumber, 1);
                    if ($ticketId) {
                        header("Location: ?route=user&action=tickets&purchased=1");
                        exit();
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        require_once __DIR__ . '/../public/views/user/buy_ticket.php';
    }

    public function tickets() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?route=auth&action=login");
            exit();
        }
        
        $user = new User();
        $user->loadById($_SESSION['user_id']);
        
        // Get user tickets
        $tickets = $user->getTickets();
        
        require_once __DIR__ . '/../public/views/user/tickets.php';
    }

    public function downloadTicket() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?route=auth&action=login");
            exit();
        }
        
        $ticketId = $_GET['id'] ?? null;
        
        if (!$ticketId) {
            header("Location: ?route=user&action=tickets");
            exit();
        }
        
        try {
            $ticket = new Tickets();
            $ticketData = $ticket->loadById($ticketId);
            
            // Verify ticket belongs to user
            if (!$ticketData || $ticketData['user_id'] != $_SESSION['user_id']) {
                header("Location: ?route=user&action=tickets");
                exit();
            }
            
            // Generate PDF
            $pdfContent = $ticket->generatePDF($ticketId);
            
            // Send PDF to browser
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="billet-' . $ticketId . '.pdf"');
            echo $pdfContent;
            exit();
        } catch (Exception $e) {
            error_log("Download ticket error: " . $e->getMessage());
            header("Location: ?route=user&action=tickets&error=1");
            exit();
        }
    }

    public function reviews() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?route=auth&action=login");
            exit();
        }
        
        $user = new User();
        $user->loadById($_SESSION['user_id']);
        
        $errors = [];
        $success = false;
        
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matchId = $_POST['match_id'] ?? null;
            $rating = $_POST['rating'] ?? null;
            $comment = trim($_POST['comment'] ?? '');
            
            if (empty($matchId) || empty($rating) || empty($comment)) {
                $errors[] = "Tous les champs sont requis.";
            } else {
                try {
                    $review = new Review();
                    $reviewId = $review->create($user->getId(), $matchId, $rating, $comment);
                    if ($reviewId) {
                        $success = true;
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        // Get user's reviews history
        $reviews = $user->getHistory();
        
        // Get matches user can review (matches they have tickets for)
        $tickets = $user->getTickets();
        $matchIds = array_unique(array_column($tickets, 'match_id'));
        
        require_once __DIR__ . '/../public/views/user/reviews.php';
    }
}
