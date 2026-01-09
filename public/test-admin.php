<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

// Charger les modèles nécessaires
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Matchs.php'; // pour créer un match de test

echo "<h1>Test Admin - Validation des matchs</h1>";

// === 1. Créer un match de test en pending (comme si un organisateur l’avait fait) ===
$matchs = new Matchs();
$matchId = $matchs->createMatch(
    team1Id: 1,               // Red Tigers
    team2Id: 2,               // Blue Sharks
    dateMatch: '2026-02-15 20:00:00',
    lieu: 'Stade Mohammed V',
    capacity: 1800,
    organizerId: 3            // Charlie l'organisateur
);

if ($matchId) {
    echo "<p style='color:green;'>Match de test créé en pending - ID : $matchId</p>";
} else {
    die("<p style='color:red;'>Échec création match de test</p>");
}

// === 2. Instancier Admin (Ethan id 5) ===
$admin = new Admin(5);  // ID de l'admin dans tes données de test

if (!$admin->getId()) {
    die("<p style='color:red;'>Échec chargement admin ID 5</p>");
}

echo "<p>Admin chargé : " . $admin->getFullname() . " (role: " . $admin->getRole() . ")</p>";

// === 3. Lister les matchs en attente ===
$pending = $admin->getPendingMatches();
echo "<h3>Matchs en attente (pending) :</h3>";
if (empty($pending)) {
    echo "<p>Aucun match pending.</p>";
} else {
    echo "<pre>";
    print_r($pending);
    echo "</pre>";
}

// === 4. Valider le match qu’on vient de créer ===
try {
    $validated = $admin->validateMatch($matchId);
    if ($validated) {
        echo "<p style='color:green;'>Match $matchId → validé avec succès</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Erreur validation : " . $e->getMessage() . "</p>";
}

// === 5. Publier directement (facultatif - test complet) ===
try {
    $published = $admin->publishMatch($matchId);
    if ($published) {
        echo "<p style='color:green;'>Match $matchId → publié avec succès</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Erreur publication : " . $e->getMessage() . "</p>";
}

// === 6. Vérifier le statut final dans la base (optionnel mais très utile) ===
$matchsModel = new Matchs();
$matchDetails = $matchsModel->getMatchById($matchId);
$finalStatus = $matchDetails['statut'] ?? 'unknown';
echo "<p>Statut final du match $matchId : <strong>$finalStatus</strong></p>";

// === 7. Tester les stats globales (si procédure existe) ===
try {
    $stats = $admin->getGlobalStats();
    echo "<h3>Stats globales :</h3><pre>";
    print_r($stats);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:orange;'>Procédure get_global_stats() non disponible ou erreur : " . $e->getMessage() . "</p>";
}