<?php
// test_connection.php - Page pour tester la connexion BDD, .htaccess et 404
session_start();

// Configuration
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/conx.php';
// Test 1 : Connexion à la base de données
echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test de Configuration - Billetterie Sportive</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
    <style>
        .test-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 20px;
        }
        .success { border-left-color: #198754; }
        .error { border-left-color: #dc3545; }
        .warning { border-left-color: #ffc107; }
    </style>
</head>
<body class='bg-light'>
    <div class='container mt-5'>
        <h1 class='text-center mb-5'>
            <i class='fas fa-cog'></i> Test de Configuration
        </h1>";

// ============================================
// TEST 1 : Connexion à la base de données
// ============================================
echo "<div class='card test-card";

    try {
    $db = Database::getInstance()->getConnection();
    
    echo " success'>";
    echo "<div class='card-header bg-success text-white'>
            <h4><i class='fas fa-check-circle'></i> Test 1 : Connexion Base de Données</h4>
          </div>";
    echo "<div class='card-body'>";
    echo "<p class='text-success'><strong>✅ SUCCÈS :</strong> Connexion à la base de données réussie !</p>";
    
    // Tester la base de données
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "<p><strong>Base de données connectée :</strong> " . htmlspecialchars($result['db_name']) . "</p>";
    
    // Vérifier les tables
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<p><strong>Tables trouvées (" . count($tables) . ") :</strong></p>";
        echo "<ul class='list-group'>";
        foreach ($tables as $table) {
            echo "<li class='list-group-item'><i class='fas fa-table text-primary'></i> " . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
     
        
        // Compter les utilisateurs (si la table existe)
        if (in_array('users', $tables, true)) {
            $stmt = $db->query("SELECT COUNT(*) as total FROM users");
            $count = $stmt->fetch();
            echo "<p class='mt-3'><strong>Utilisateurs dans la base :</strong> " . ($count['total'] ?? 0) . "</p>";
        }

        // Compter les matchs (vérifie deux orthographes courantes)
        if (in_array('matchs', $tables, true) || in_array('matches', $tables, true)) {
            $tableName = in_array('matchs', $tables, true) ? 'matchs' : 'matches';
            $stmt = $db->query("SELECT COUNT(*) as total FROM " . $tableName);
            $count = $stmt->fetch();
            echo "<p><strong>Matchs dans la base :</strong> " . ($count['total'] ?? 0) . "</p>";
        }
        
    } else {
        echo "<div class='alert alert-warning mt-3'>
                <i class='fas fa-exclamation-triangle'></i> 
                <strong>Attention :</strong> La base de données est vide. 
                Exécutez le fichier <code>database/schema.sql</code> pour créer les tables.
              </div>";
        echo "<pre class='bg-dark text-white p-3 rounded'>mysql -u root -p billetterie_sportive < database/schema.sql</pre>";
    }
    
} catch (Exception $e) {
    echo " error'>";
    echo "<div class='card-header bg-danger text-white'>
            <h4><i class='fas fa-times-circle'></i> Test 1 : Connexion Base de Données</h4>
          </div>";
    echo "<div class='card-body'>";
    echo "<p class='text-danger'><strong>❌ ERREUR :</strong> Impossible de se connecter à la base de données</p>";
    echo "<div class='alert alert-danger'>";
    echo "<strong>Message d'erreur :</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
    echo "<h5>Solutions possibles :</h5>";
    echo "<ol>
            <li>Vérifiez que MySQL est démarré (XAMPP/WAMP/MAMP)</li>
            <li>Vérifiez les identifiants dans <code>config/conx.php</code></li>
            <li>Créez la base de données :
                <pre class='bg-dark text-white p-2 rounded mt-2'>CREATE DATABASE billetterie_sportive;</pre>
            </li>
            <li>Vérifiez le nom d'utilisateur et le mot de passe MySQL</li>
          </ol>";
}

echo "</div></div>";

// ============================================
// TEST 2 : Configuration .htaccess
// ============================================
echo "<div class='card test-card";

if (file_exists(BASE_PATH . '/.htaccess')) {
    echo " success'>";
    echo "<div class='card-header bg-success text-white'>
            <h4><i class='fas fa-check-circle'></i> Test 2 : Fichier .htaccess</h4>
          </div>";
    echo "<div class='card-body'>";
    echo "<p class='text-success'><strong>✅ SUCCÈS :</strong> Le fichier .htaccess existe</p>";
    
    // Vérifier si mod_rewrite est activé
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (in_array('mod_rewrite', $modules)) {
            echo "<p class='text-success'><strong>✅ mod_rewrite :</strong> Activé</p>";
        } else {
            echo "<p class='text-warning'><strong>⚠️ mod_rewrite :</strong> Non détecté (peut être activé mais non détectable)</p>";
        }
    } else {
        echo "<p class='text-info'><strong>ℹ️ mod_rewrite :</strong> Impossible de vérifier (fonction non disponible)</p>";
    }
    
    // Afficher le contenu du .htaccess
    $htaccess_content = file_get_contents(BASE_PATH . '/.htaccess');
    echo "<div class='mt-3'>
            <strong>Contenu du .htaccess :</strong>
            <pre class='bg-dark text-white p-3 rounded mt-2' style='max-height: 300px; overflow-y: auto;'>" 
            . htmlspecialchars($htaccess_content) . 
          "</pre>
          </div>";
    
    echo "<div class='alert alert-info mt-3'>
            <strong>Test manuel :</strong> Essayez d'accéder à une page inexistante pour tester le 404 :
            <br><a href='page-qui-existe-pas' class='btn btn-sm btn-primary mt-2' target='_blank'>
                <i class='fas fa-external-link-alt'></i> Tester la page 404
            </a>
          </div>";
    
} else {
    echo " error'>";
    echo "<div class='card-header bg-danger text-white'>
            <h4><i class='fas fa-times-circle'></i> Test 2 : Fichier .htaccess</h4>
          </div>";
    echo "<div class='card-body'>";
    echo "<p class='text-danger'><strong>❌ ERREUR :</strong> Le fichier .htaccess n'existe pas</p>";
    echo "<div class='alert alert-danger'>
            Créez un fichier <code>.htaccess</code> à la racine du projet avec le contenu fourni.
          </div>";
}

echo "</div></div>";

// ============================================
// TEST 3 : Page 404
// ============================================
echo "<div class='card test-card";

if (file_exists(BASE_PATH . '/views/errors/404.php')) {
    echo " success'>";
    echo "<div class='card-header bg-success text-white'>
            <h4><i class='fas fa-check-circle'></i> Test 3 : Page 404</h4>
          </div>";
    echo "<div class='card-body'>";
    echo "<p class='text-success'><strong>✅ SUCCÈS :</strong> Le fichier 404.php existe</p>";
    echo "<p><strong>Emplacement :</strong> <code>/views/errors/404.php</code></p>";
    
    echo "<div class='alert alert-info'>
            <strong>Test de la page 404 :</strong>
            <ol class='mb-0'>
                <li>Cliquez sur le bouton ci-dessous pour ouvrir une page inexistante</li>
                <li>Vous devriez voir la page 404 personnalisée</li>
            </ol>
            <a href='cette-page-nexiste-pas-123' class='btn btn-primary mt-2' target='_blank'>
                <i class='fas fa-external-link-alt'></i> Tester la page 404
            </a>
          </div>";
    
} else {
    echo " error'>";
    echo "<div class='card-header bg-danger text-white'>
            <h4><i class='fas fa-times-circle'></i> Test 3 : Page 404</h4>
          </div>";
    echo "<div class='card-body'>";
    echo "<p class='text-danger'><strong>❌ ERREUR :</strong> Le fichier 404.php n'existe pas</p>";
    echo "<div class='alert alert-danger'>
            Créez le dossier <code>views/errors/</code> et le fichier <code>404.php</code> dedans.
          </div>";
}

echo "</div></div>";

// ============================================
// TEST 4 : Structure des dossiers
// ============================================
echo "<div class='card test-card";

$required_folders = [
    'config',
    'models',
    'controllers',
    'views',
    'views/auth',
    'views/matches',
    'views/tickets',
    'views/user',
    'views/organizer',
    'views/admin',
    'views/partials',
    'views/errors',
    'database'
];

$missing_folders = [];
foreach ($required_folders as $folder) {
    if (!is_dir(BASE_PATH . '/' . $folder)) {
        $missing_folders[] = $folder;
    }
}

if (empty($missing_folders)) {
    echo " success'>";
    echo "<div class='card-header bg-success text-white'>
            <h4><i class='fas fa-check-circle'></i> Test 4 : Structure des Dossiers</h4>
          </div>";
    echo "<div class='card-body'>";
    echo "<p class='text-success'><strong>✅ SUCCÈS :</strong> Tous les dossiers requis existent</p>";
    echo "<ul class='list-group'>";
    foreach ($required_folders as $folder) {
        echo "<li class='list-group-item'><i class='fas fa-folder text-warning'></i> " . htmlspecialchars($folder) . "</li>";
    }
    echo "</ul>";
} else {
    echo " warning'>";
    echo "<div class='card-header bg-warning text-dark'>
            <h4><i class='fas fa-exclamation-triangle'></i> Test 4 : Structure des Dossiers</h4>
          </div>";
    echo "<div class='card-body'>";
    echo "<p class='text-warning'><strong>⚠️ ATTENTION :</strong> Certains dossiers sont manquants</p>";
    echo "<p><strong>Dossiers manquants :</strong></p>";
    echo "<ul class='list-group'>";
    foreach ($missing_folders as $folder) {
        echo "<li class='list-group-item list-group-item-warning'>
                <i class='fas fa-folder-open'></i> " . htmlspecialchars($folder) . "
              </li>";
    }
    echo "</ul>";
    echo "<div class='alert alert-info mt-3'>
            Créez ces dossiers pour que l'application fonctionne correctement.
          </div>";
}

echo "</div></div>";

// ============================================
// Résumé et Actions
// ============================================
echo "<div class='card border-primary'>
        <div class='card-header bg-primary text-white'>
            <h4><i class='fas fa-list-check'></i> Résumé et Prochaines Étapes</h4>
        </div>
        <div class='card-body'>";

if (empty($missing_folders)) {
    echo "<h5 class='text-success'><i class='fas fa-check-circle'></i> Configuration complète !</h5>";
    echo "<p>Tous les tests sont passés. Vous pouvez maintenant :</p>";
    echo "<ol>
            <li>Accéder à la page d'accueil : 
                <a href='index.php?action=home' class='btn btn-sm btn-primary'>
                    <i class='fas fa-home'></i> Accueil
                </a>
            </li>
            <li>Tester la connexion : 
                <a href='index.php?action=login' class='btn btn-sm btn-info'>
                    <i class='fas fa-sign-in-alt'></i> Connexion
                </a>
            </li>
            <li>Créer un compte : 
                <a href='index.php?action=register' class='btn btn-sm btn-success'>
                    <i class='fas fa-user-plus'></i> Inscription
                </a>
            </li>
          </ol>";
          
    echo "<div class='alert alert-success mt-3'>
            <strong>Comptes de test :</strong>
            <ul class='mb-0'>
                <li><strong>Admin :</strong> admin@billetterie.com / password</li>
                <li><strong>Organisateur :</strong> organisateur@test.com / password</li>
                <li><strong>Utilisateur :</strong> user@test.com / password</li>
            </ul>
          </div>";
} else {
    echo "<h5 class='text-warning'><i class='fas fa-exclamation-triangle'></i> Configuration incomplète</h5>";
    echo "<p>Veuillez corriger les problèmes identifiés ci-dessus avant de continuer.</p>";
}

echo "</div></div>";

// Bouton pour rafraîchir les tests
echo "<div class='text-center mt-4 mb-5'>
        <a href='test_connection.php' class='btn btn-lg btn-primary'>
            <i class='fas fa-sync-alt'></i> Relancer les tests
        </a>
        <a href='index.php' class='btn btn-lg btn-success'>
            <i class='fas fa-home'></i> Aller à l'accueil
        </a>
      </div>";

echo "</div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>