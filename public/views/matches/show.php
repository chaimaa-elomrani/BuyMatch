<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($match['team1_name'] . ' vs ' . $match['team2_name']) ?> - BuyMatch</title>
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
            max-width: 900px;
            margin: 0 auto;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .match-detail {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .match-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 3px solid #667eea;
        }
        .match-title {
            font-size: 32px;
            color: #333;
            margin-bottom: 30px;
        }
        .teams {
            display: flex;
            align-items: center;
            justify-content: space-around;
            margin: 30px 0;
        }
        .team {
            text-align: center;
            flex: 1;
        }
        .team-logo {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            font-weight: bold;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .team-name {
            font-weight: bold;
            font-size: 24px;
            color: #333;
        }
        .vs {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin: 0 20px;
        }
        .match-info {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .info-row {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #667eea;
            min-width: 150px;
        }
        .info-value {
            color: #333;
            flex: 1;
        }
        .categories-section {
            margin: 40px 0;
        }
        .section-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .category-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }
        .category-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .category-price {
            font-size: 32px;
            font-weight: bold;
        }
        .buy-section {
            margin-top: 40px;
            text-align: center;
            padding: 30px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .buy-btn {
            display: inline-block;
            padding: 15px 40px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .buy-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .login-required {
            color: #666;
            margin-top: 15px;
        }
        .login-required a {
            color: #667eea;
            text-decoration: none;
        }
        .login-required a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="?route=match&action=index" class="back-link">← Retour aux matchs</a>

        <div class="match-detail">
            <div class="match-header">
                <h1 class="match-title">Détails du match</h1>
                
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
            </div>

            <div class="match-info">
                <div class="info-row">
                    <span class="info-label">📅 Date et heure:</span>
                    <span class="info-value"><?= htmlspecialchars(date('d/m/Y à H:i', strtotime($match['date_match']))) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">📍 Lieu:</span>
                    <span class="info-value"><?= htmlspecialchars($match['lieu']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">⏱️ Durée:</span>
                    <span class="info-value"><?= htmlspecialchars($match['duration']) ?> minutes</span>
                </div>
                <div class="info-row">
                    <span class="info-label">👥 Capacité:</span>
                    <span class="info-value"><?= htmlspecialchars($match['capacity']) ?> places</span>
                </div>
                <div class="info-row">
                    <span class="info-label">📊 Statut:</span>
                    <span class="info-value">
                        <span style="background: #4caf50; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px;">
                            Publié
                        </span>
                    </span>
                </div>
            </div>

            <?php if (!empty($match['categories'])): ?>
                <div class="categories-section">
                    <h2 class="section-title">Catégories de billets disponibles</h2>
                    <div class="categories-grid">
                        <?php foreach ($match['categories'] as $category): ?>
                            <div class="category-card">
                                <div class="category-name"><?= htmlspecialchars($category['nom']) ?></div>
                                <div class="category-price"><?= htmlspecialchars($category['prix']) ?> DH</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="categories-section">
                    <p style="color: #999; text-align: center; padding: 20px;">
                        Aucune catégorie disponible pour ce match.
                    </p>
                </div>
            <?php endif; ?>

            <div class="buy-section">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <p style="margin-bottom: 20px; color: #666;">
                        Connecté en tant que: <strong><?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?></strong>
                    </p>
                    <a href="?route=user&action=dashboard" class="buy-btn">
                        Acheter un billet
                    </a>
                    <p class="login-required" style="margin-top: 15px;">
                        <a href="?route=user&action=dashboard">Accéder à votre tableau de bord</a> pour acheter des billets
                    </p>
                <?php else: ?>
                    <p style="margin-bottom: 20px; color: #666;">
                        Vous devez être connecté pour acheter des billets
                    </p>
                    <a href="?route=auth&action=login" class="buy-btn">
                        Se connecter pour acheter
                    </a>
                    <p class="login-required">
                        Pas encore de compte ? <a href="?route=auth&action=register">Inscrivez-vous</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
