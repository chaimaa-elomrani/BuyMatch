<?php
require_once __DIR__ . '/../models/Matchs.php';
require_once __DIR__ . '/../models/Category.php';

class MatchController {
    
    public function index() {
        // Public route - visitors can access
        // Get all published matches
        $matchModel = new Matchs();
        $matches = $matchModel->getPublishedMatches();
        
        // Get categories for each match
        $categoryModel = new Category();
        foreach ($matches as &$match) {
            $match['categories'] = $categoryModel->getByMatch($match['id']);
        }
        
        // Display matches page
        require_once __DIR__ . '/../public/views/matches/index.php';
    }

    public function show() {
        // Public route - visitors can access
        // Get match ID from URL
        $matchId = $_GET['id'] ?? null;
        
        if (!$matchId || !is_numeric($matchId)) {
            header("Location: ?route=match&action=index");
            exit();
        }
        
        // Get match details (only published matches)
        $matchModel = new Matchs();
        $match = $matchModel->getPublishedMatchById($matchId);
        
        if (!$match) {
            header("Location: ?route=match&action=index");
            exit();
        }
        
        // Get categories for this match
        $categoryModel = new Category();
        $categories = $categoryModel->getByMatch($matchId);
        $match['categories'] = $categories;
        
        // Display match details page
        require_once __DIR__ . '/../public/views/matches/show.php';
    }
}
