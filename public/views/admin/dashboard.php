<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin - BuyMatch</title>
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
        .pending-alert { background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .pending-alert strong { display: block; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #dc3545; color: white; }
        .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-pending { background: #ffc107; color: #000; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Espace Administrateur</h1>
        
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
                <p>Chiffre d'affaires</p>
            </div>
            <div class="stat-card">
                <h3><?= htmlspecialchars(number_format($stats['note_moyenne_generale'] ?? 0, 1)) ?></h3>
                <p>Note moyenne</p>
            </div>
        </div>

        <?php if (!empty($pendingMatches)): ?>
            <div class="pending-alert">
                <strong>⚠️ Matchs en attente de validation</strong>
                Vous avez <?= count($pendingMatches) ?> match(s) en attente de validation.
                <a href="?route=admin&action=validateMatches" style="color: #856404; font-weight: bold; margin-left: 10px;">Voir les matchs →</a>
            </div>
        <?php endif; ?>

        <h2>Matchs en attente</h2>
        <?php if (empty($pendingMatches)): ?>
            <p>Aucun match en attente de validation.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Match</th>
                        <th>Organisateur</th>
                        <th>Date</th>
                        <th>Lieu</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($pendingMatches, 0, 5) as $match): ?>
                        <tr>
                            <td><?= htmlspecialchars($match['team1_name'] . ' vs ' . $match['team2_name']) ?></td>
                            <td><?= htmlspecialchars($match['org_prenom'] . ' ' . $match['org_nom']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($match['date_match']))) ?></td>
                            <td><?= htmlspecialchars($match['lieu']) ?></td>
                            <td>
                                <a href="?route=admin&action=validateMatches" style="color: #dc3545; font-weight: bold;">Valider →</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
