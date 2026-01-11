<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchs disponibles - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .nav-menu { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .matches-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
        .match-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .match-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.2); }
        .teams { display: flex; align-items: center; justify-content: space-around; margin: 20px 0; }
        .team { text-align: center; }
        .team-logo { width: 80px; height: 80px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-size: 24px; font-weight: bold; color: #667eea; }
        .vs { font-size: 24px; font-weight: bold; color: #999; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="container">
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

        <h1>Matchs disponibles</h1>

        <?php if (empty($matches)): ?>
            <p>Aucun match disponible.</p>
        <?php else: ?>
            <div class="matches-grid">
                <?php foreach ($matches as $match): ?>
                    <div class="match-card">
                        <h3><?= htmlspecialchars($match['team1_name'] . ' vs ' . $match['team2_name']) ?></h3>
                        <p><strong>Date:</strong> <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($match['date_match']))) ?></p>
                        <p><strong>Lieu:</strong> <?= htmlspecialchars($match['lieu']) ?></p>
                        <?php if (!empty($match['categories'])): ?>
                            <p><strong>Prix:</strong> À partir de <?= htmlspecialchars(min(array_column($match['categories'], 'prix'))) ?> DH</p>
                        <?php endif; ?>
                        <a href="?route=user&action=buyTicket&match_id=<?= $match['id'] ?>" class="btn">Acheter un billet</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
