<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes avis - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }
        textarea { min-height: 100px; }
        button { padding: 12px 30px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .reviews-list { margin-top: 40px; }
        .review-item { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 15px; }
        .stars { color: #ffc107; font-size: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-menu">
            <ul>
                <li><a href="?route=user&action=dashboard">Tableau de bord</a></li>
                <li><a href="?route=user&action=tickets">Mes billets</a></li>
                <li><a href="?route=user&action=reviews">Mes avis</a></li>
                <li><a href="?route=auth&action=logout">Déconnexion</a></li>
            </ul>
        </nav>

        <h1>Donner un avis</h1>

        <?php if ($success): ?>
            <div class="success">Avis ajouté avec succès !</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Match:</label>
                <select name="match_id" required>
                    <option value="">Sélectionner un match</option>
                    <?php 
                    $matchModel = new Matchs();
                    foreach ($matchIds as $mid): 
                        $m = $matchModel->getPublishedMatchById($mid);
                        if ($m):
                    ?>
                        <option value="<?= $m['id'] ?>">
                            <?= htmlspecialchars($m['team1_name'] . ' vs ' . $m['team2_name']) ?>
                        </option>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Note (1-5):</label>
                <select name="rating" required>
                    <option value="">Sélectionner une note</option>
                    <option value="1">1 étoile</option>
                    <option value="2">2 étoiles</option>
                    <option value="3">3 étoiles</option>
                    <option value="4">4 étoiles</option>
                    <option value="5">5 étoiles</option>
                </select>
            </div>
            <div class="form-group">
                <label>Commentaire:</label>
                <textarea name="comment" required placeholder="Votre avis sur le match..."></textarea>
            </div>
            <button type="submit">Publier l'avis</button>
        </form>

        <div class="reviews-list">
            <h2>Mes avis</h2>
            <?php if (empty($reviews)): ?>
                <p>Aucun avis donné.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="stars"><?= str_repeat('★', $review['note']) ?><?= str_repeat('☆', 5 - $review['note']) ?></div>
                        <p><strong>Match:</strong> <?= htmlspecialchars($review['team1_name'] . ' vs ' . $review['team2_name']) ?></p>
                        <p><?= htmlspecialchars($review['comment']) ?></p>
                        <small><?= htmlspecialchars(date('d/m/Y', strtotime($review['created_at']))) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
