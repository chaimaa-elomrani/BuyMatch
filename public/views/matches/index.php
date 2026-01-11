<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchs disponibles - BuyMatch</title>
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
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        .auth-links {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .matches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .match-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .match-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .match-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .teams {
            display: flex;
            align-items: center;
            justify-content: space-around;
            margin: 20px 0;
        }
        .team {
            text-align: center;
            flex: 1;
        }
        .team-logo {
            width: 80px;
            height: 80px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        .team-name {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        .vs {
            font-size: 24px;
            font-weight: bold;
            color: #999;
            margin: 0 15px;
        }
        .match-info {
            margin: 20px 0;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #666;
        }
        .info-item strong {
            color: #333;
            margin-right: 10px;
            min-width: 100px;
        }
        .categories {
            margin: 20px 0;
        }
        .categories-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .category-badge {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
        .category-price {
            font-weight: bold;
            margin-left: 5px;
        }
        .view-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .view-btn:hover {
            background: #5568d3;
        }
        .no-matches {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            color: #999;
        }
        .no-matches h2 {
            color: #666;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏆 BuyMatch</h1>
            <p>Découvrez les matchs disponibles et réservez vos billets</p>
        </div>

        <div class="auth-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?route=user&action=dashboard">Mon tableau de bord</a>
                <a href="?route=auth&action=logout">Déconnexion</a>
            <?php else: ?>
                <a href="?route=auth&action=login">Se connecter</a>
                <a href="?route=auth&action=register">S'inscrire</a>
            <?php endif; ?>
        </div>

        <?php if (empty($matches)): ?>
            <div class="no-matches">
                <h2>Aucun match disponible</h2>
                <p>Il n'y a actuellement aucun match publié. Revenez plus tard !</p>
            </div>
        <?php else: ?>
            <div class="matches-grid">
                <?php foreach ($matches as $match): ?>
                    <div class="match-card">
                        <div class="match-header">
                            <h3 style="color: #667eea; margin-bottom: 15px;">Match #<?= htmlspecialchars($match['id']) ?></h3>
                        </div>

                        <div class="teams">
                            <div class="team">
                                <div class="team-logo">
                                    <?= htmlspecialchars(substr($match['team1_name'], 0, 2)) ?>
                                </div>
                                <div class="team-name"><?= htmlspecialchars($match['team1_name']) ?></div>
                            </div>
                            <div class="vs">VS</div>
                            <div class="team">
                                <div class="team-logo">
                                    <?= htmlspecialchars(substr($match['team2_name'], 0, 2)) ?>
                                </div>
                                <div class="team-name"><?= htmlspecialchars($match['team2_name']) ?></div>
                            </div>
                        </div>

                        <div class="match-info">
                            <div class="info-item">
                                <strong>📅 Date:</strong>
                                <span><?= htmlspecialchars(date('d/m/Y à H:i', strtotime($match['date_match']))) ?></span>
                            </div>
                            <div class="info-item">
                                <strong>📍 Lieu:</strong>
                                <span><?= htmlspecialchars($match['lieu']) ?></span>
                            </div>
                            <div class="info-item">
                                <strong>👥 Capacité:</strong>
                                <span><?= htmlspecialchars($match['capacity']) ?> places</span>
                            </div>
                        </div>

                        <?php if (!empty($match['categories'])): ?>
                            <div class="categories">
                                <div class="categories-title">Catégories disponibles:</div>
                                <div class="category-list">
                                    <?php foreach ($match['categories'] as $category): ?>
                                        <span class="category-badge">
                                            <?= htmlspecialchars($category['nom']) ?>
                                            <span class="category-price"><?= htmlspecialchars($category['prix']) ?> DH</span>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <a href="?route=match&action=show&id=<?= htmlspecialchars($match['id']) ?>" class="view-btn">
                            Voir les détails
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
