<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        h1 { color: #333; margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 10px; text-align: center; }
        .stat-card h3 { font-size: 36px; margin-bottom: 10px; }
        .stat-card p { font-size: 16px; opacity: 0.9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-pending { background: #ffc107; color: #000; }
        .status-validated { background: #17a2b8; color: white; }
        .status-published { background: #28a745; color: white; }
        .status-rejected { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-menu">
            <ul>
                <li><a href="?route=organizer&action=dashboard">Tableau de bord</a></li>
                <li><a href="?route=organizer&action=createMatch">Créer un match</a></li>
                <li><a href="?route=organizer&action=statistics">Statistiques</a></li>
                <li><a href="?route=organizer&action=reviews">Avis</a></li>
                <li><a href="?route=auth&action=logout">Déconnexion</a></li>
            </ul>
        </nav>

        <h1>Statistiques</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= htmlspecialchars($stats['total_matchs'] ?? 0) ?></h3>
                <p>Total matchs créés</p>
            </div>
            <div class="stat-card">
                <h3><?= htmlspecialchars($stats['total_billets_vendus'] ?? 0) ?></h3>
                <p>Total billets vendus</p>
            </div>
            <div class="stat-card">
                <h3><?= htmlspecialchars(number_format($stats['chiffre_affaires_total'] ?? 0, 2)) ?> DH</h3>
                <p>Chiffre d'affaires total</p>
            </div>
            <div class="stat-card">
                <h3><?= htmlspecialchars(number_format($stats['note_moyenne'] ?? 0, 1)) ?>/5</h3>
                <p>Note moyenne</p>
            </div>
        </div>

        <h2>Détails par match</h2>
        <?php if (empty($stats['matches_details'])): ?>
            <p>Aucune statistique disponible.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Match</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Billets vendus</th>
                        <th>Revenus</th>
                        <th>Note moyenne</th>
                        <th>Nombre d'avis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['matches_details'] as $match): ?>
                        <tr>
                            <td><?= htmlspecialchars($match['match_name']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($match['date_match']))) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($match['statut']) ?>">
                                    <?= htmlspecialchars(ucfirst($match['statut'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($match['billets_vendus']) ?></td>
                            <td><?= htmlspecialchars(number_format($match['revenus'], 2)) ?> DH</td>
                            <td><?= htmlspecialchars(number_format($match['note_moyenne'], 1)) ?>/5</td>
                            <td><?= htmlspecialchars($match['nombre_avis']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
