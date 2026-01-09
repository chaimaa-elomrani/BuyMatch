<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../models/Organizer.php';

// Login organisateur (ex. Charlie, id 3)
$_SESSION['user_id'] = 3;
$_SESSION['user_role'] = 'organizer';

require_once __DIR__ . '/../models/Organizer.php';

$organizer = new Organizer(3);

try {
    $matchId = $organizer->createMatch(
    team1Id: 1,
    team2Id: 2,
    dateMatch: '2026-02-01 20:00:00',
    lieu: 'Stade Test',
    capacity: 1800,
    organizerId: $organizer->getId()  // ou directement 3
);

    if ($matchId) {
        echo "<h2>Succès ! Match créé avec ID : $matchId (statut pending)</h2>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Erreur : " . $e->getMessage() . "</p>";
}

// Vérification
$matches = $organizer->getMatches();
echo "<pre>";
print_r($matches);
echo "</pre>";