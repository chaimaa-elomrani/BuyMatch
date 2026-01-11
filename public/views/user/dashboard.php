<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - BuyMatch</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-menu {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .nav-menu ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
            transition: all 0.3s;
        }
        .nav-menu a:hover {
            background: rgba(255,255,255,0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #667eea;
        }
        h2 {
            color: #555;
            margin: 30px 0 20px 0;
        }
        .user-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-card h3 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .no-tickets {
            color: #888;
            font-style: italic;
            padding: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Espace Acheteur - <?= htmlspecialchars($user->getFullname()) ?></h1>
        
        <nav class="nav-menu">
            <ul>
                <li><a href="?route=user&action=dashboard">Tableau de bord</a></li>
                <li><a href="?route=user&action=profile">Mon profil</a></li>
                <li><a href="?route=user&action=matches">Matchs disponibles</a></li>
                <li><a href="?route=user&action=tickets">Mes billets</a></li>
                <li><a href="?route=user&action=reviews">Mes avis</a></li>
                <li><a href="?route=auth&action=logout">Déconnexion</a></li>
            </ul>
        </nav>
        
        <div class="user-info">
            <p><strong>Email:</strong> <?= htmlspecialchars($user->getEmail()) ?></p>
            <p><strong>Rôle:</strong> <?= htmlspecialchars($user->getRole()) ?></p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <h3><?= count($tickets) ?></h3>
                <p>Billets achetés</p>
            </div>
        </div>

        <h2>Mes billets récents</h2>
        
        <?php if (empty($tickets)): ?>
            <p class="no-tickets">Aucun billet acheté pour l'instant.</p>
            <p style="text-align: center; margin-top: 20px;">
                <a href="?route=user&action=matches" class="btn">Voir les matchs disponibles</a>
            </p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Match</th>
                        <th>Place</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($tickets, 0, 5) as $ticket): ?>
                        <tr>
                            <td><?= htmlspecialchars($ticket['team1_name'] . ' vs ' . $ticket['team2_name']) ?></td>
                            <td><?= htmlspecialchars($ticket['place_number']) ?></td>
                            <td><?= htmlspecialchars($ticket['category_name']) ?></td>
                            <td><?= htmlspecialchars($ticket['prix']) ?> DH</td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($ticket['date_match']))) ?></td>
                            <td>
                                <a href="?route=user&action=downloadTicket&id=<?= $ticket['id'] ?>" class="btn">Télécharger PDF</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (count($tickets) > 5): ?>
                <p style="text-align: center; margin-top: 20px;">
                    <a href="?route=user&action=tickets" class="btn">Voir tous mes billets</a>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
