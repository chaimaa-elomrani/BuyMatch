<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes billets - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .btn { padding: 8px 15px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-menu">
            <ul>
                <li><a href="?route=user&action=dashboard">Tableau de bord</a></li>
                <li><a href="?route=user&action=matches">Matchs disponibles</a></li>
                <li><a href="?route=user&action=tickets">Mes billets</a></li>
                <li><a href="?route=auth&action=logout">Déconnexion</a></li>
            </ul>
        </nav>

        <h1>Mes billets</h1>

        <?php if (isset($_GET['purchased']) && $_GET['purchased'] == 1): ?>
            <div class="success">Billet acheté avec succès ! Un email vous a été envoyé avec votre billet en PDF.</div>
        <?php endif; ?>

        <?php if (empty($tickets)): ?>
            <p>Aucun billet acheté.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Match</th>
                        <th>Place</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Date du match</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
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
        <?php endif; ?>
    </div>
</body>
</html>
