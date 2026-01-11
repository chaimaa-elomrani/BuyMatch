<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        h1 { color: #333; margin-bottom: 30px; }
        .review-item { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #667eea; }
        .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .review-match { font-weight: bold; color: #667eea; }
        .review-user { color: #666; font-size: 14px; }
        .stars { color: #ffc107; font-size: 20px; margin: 10px 0; }
        .review-comment { color: #333; line-height: 1.6; }
        .review-date { color: #999; font-size: 12px; margin-top: 10px; }
        .no-reviews { text-align: center; padding: 40px; color: #999; }
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

        <h1>Avis sur mes matchs</h1>

        <?php if (empty($reviews)): ?>
            <div class="no-reviews">
                <p>Aucun avis pour le moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div>
                            <div class="review-match"><?= htmlspecialchars($review['match_name']) ?></div>
                            <div class="review-user">Par <?= htmlspecialchars($review['user_prenom'] . ' ' . $review['user_nom']) ?></div>
                        </div>
                        <div class="stars">
                            <?= str_repeat('★', $review['note']) ?><?= str_repeat('☆', 5 - $review['note']) ?>
                        </div>
                    </div>
                    <div class="review-comment">
                        <?= nl2br(htmlspecialchars($review['comment'])) ?>
                    </div>
                    <div class="review-date">
                        <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($review['created_at']))) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
