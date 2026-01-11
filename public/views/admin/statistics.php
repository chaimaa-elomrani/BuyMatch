<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques globales - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .nav-menu a:hover { background: rgba(255,255,255,0.3); }
        h1 { color: #333; margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 25px; border-radius: 10px; text-align: center; }
        .stat-card h3 { font-size: 36px; margin-bottom: 10px; }
        .stat-card p { font-size: 16px; opacity: 0.9; }
        .stat-section { margin-top: 40px; }
        .stat-section h2 { color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Statistiques globales</h1>
        
        <nav class="nav-menu">
            <ul>
                <li><a href="?route=admin&action=dashboard">Tableau de bord</a></li>
                <li><a href="?route=admin&action=validateMatches">Valider les matchs</a></li>
                <li><a href="?route=admin&action=manageUsers">Gérer les utilisateurs</a></li>
                <li><a href="?route=admin&action=statistics">Statistiques globales</a></li>
                <li><a href="?route=admin&action=reviews">Commentaires</a></li>
                <li><a href="?route=auth&action=logout">Déconnexion</a></li>
            </ul>
        </nav>

        <div class="stat-section">
            <h2>Vue d'ensemble</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?= htmlspecialchars($stats['total_matchs_published'] ?? 0) ?></h3>
                    <p>Matchs publiés</p>
                </div>
                <div class="stat-card">
                    <h3><?= htmlspecialchars($stats['total_billets_vendus'] ?? 0) ?></h3>
                    <p>Billets vendus</p>
                </div>
                <div class="stat-card">
                    <h3><?= htmlspecialchars(number_format($stats['chiffre_affaires_total'] ?? 0, 2)) ?> DH</h3>
                    <p>Chiffre d'affaires total</p>
                </div>
                <div class="stat-card">
                    <h3><?= htmlspecialchars(number_format($stats['note_moyenne_generale'] ?? 0, 1)) ?>/5</h3>
                    <p>Note moyenne générale</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
