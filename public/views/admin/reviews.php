<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commentaires - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .nav-menu a:hover { background: rgba(255,255,255,0.3); }
        h1 { color: #333; margin-bottom: 30px; }
        .review-item { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #dc3545; }
        .review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; }
        .review-match { font-weight: bold; color: #dc3545; font-size: 18px; }
        .review-user { color: #666; font-size: 14px; margin-top: 5px; }
        .stars { color: #ffc107; font-size: 20px; margin: 10px 0; }
        .review-comment { color: #333; line-height: 1.6; background: white; padding: 15px; border-radius: 5px; margin-top: 10px; }
        .review-date { color: #999; font-size: 12px; margin-top: 10px; }
        .review-meta { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 10px; }
        .no-reviews { text-align: center; padding: 40px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Commentaires et avis</h1>
        
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

        <?php if (empty($reviews)): ?>
            <div class="no-reviews">
                <p>Aucun commentaire pour le moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div>
                            <div class="review-match"><?= htmlspecialchars($review['match_name']) ?></div>
                            <div class="review-user">Par <?= htmlspecialchars($review['user_prenom'] . ' ' . $review['user_nom']) ?></div>
                        </div>
                    </div>
                    <div class="stars">
                        <?php 
                        $rating = (int)($review['note'] ?? 0);
                        for ($i = 1; $i <= 5; $i++): 
                        ?>
                            <span><?= $i <= $rating ? '★' : '☆' ?></span>
                        <?php endfor; ?>
                        <span style="color: #333; font-size: 14px; margin-left: 10px;"><?= $rating ?>/5</span>
                    </div>
                    <div class="review-comment">
                        <?= nl2br(htmlspecialchars($review['comment'])) ?>
                    </div>
                    <div class="review-meta">
                        <div class="review-date">
                            Date du match: <?= htmlspecialchars(date('d/m/Y H:i', strtotime($review['date_match']))) ?>
                        </div>
                        <div class="review-date">
                            Commentaire publié le: <?= htmlspecialchars(date('d/m/Y H:i', strtotime($review['created_at']))) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
