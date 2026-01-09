<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

// Login organisateur (Charlie id 3)
$_SESSION['user_id'] = 3;
$_SESSION['user_role'] = 'organizer';

require_once __DIR__ . '/../models/Organizer.php';
require_once __DIR__ . '/../models/Matchs.php';

$organizer = new Organizer(3);
echo "Test loadById...<br>";
if (!$organizer->getId()) {
    die("Erreur : Impossible de charger l'organisateur ID 3. Vérifie la table users.");
}
echo "Organisateur chargé : " . $organizer->getFullname() . "<br>";

// 1. Créer un match de test
$matchs = new Matchs();
$matchId = $matchs->createMatch(1, 2, '2026-03-01 20:00:00', 'Stade Test 2', 1500, 3);

echo "<h2>Match créé ID : $matchId</h2>";

// 2. Ajouter 3 catégories
try {
    $organizer->addCategoryToMatch($matchId, 'VIP', 150.00);
    $organizer->addCategoryToMatch($matchId, 'Standard', 80.00);
    $organizer->addCategoryToMatch($matchId, 'Économie', 40.00);
    echo "<p>3 catégories ajoutées avec succès</p>";

    // Tenter une 4e → doit échouer (trigger BD)
    $organizer->addCategoryToMatch($matchId, 'Tribune', 100.00);
} catch (Exception $e) {
    echo "<p style='color:green;'>Erreur attendue (max 3) : " . $e->getMessage() . "</p>";
}

// Vérification
$category = new Category();
$categories = $category->getByMatch($matchId);
echo "<pre>";
print_r($categories);
echo "</pre>";