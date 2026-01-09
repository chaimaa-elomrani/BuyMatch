<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

// 1. Login un utilisateur de test (Alice - id 1)
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'user';

// 2. Instancier User (acheteur)
require_once __DIR__ . '/../models/User.php'; // ajuste le chemin
$user = new User(1); // Pass user ID to constructor

// 3. Acheter un billet (exemple avec données valides de ta BD)
try {
    $ticketId = $user->buyTicket(
        matchId: 1,         // un match existant
        categoryId: 1,      // une catégorie existante pour ce match
        placeNumber: 'TEST-A1',
        quantité: 1         // tu gères déjà la limite à 4
    );

    if ($ticketId) {
        echo "<h2>Succès ! Billet créé avec ID : $ticketId</h2>";
        echo "<p>Vérifie ta boîte mail (ou les logs si credentials incorrects)</p>";
        echo "<p>Le PDF a été généré et attaché.</p>";
    } else {
        echo "<h2>Échec création billet</h2>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Erreur : " . $e->getMessage() . "</p>";
}

// 4. Afficher les billets de l'utilisateur pour vérification
$tickets = $user->getTickets();
echo "<h3>Historique des billets de l'utilisateur :</h3>";
echo "<pre>";
print_r($tickets);
echo "</pre>";

