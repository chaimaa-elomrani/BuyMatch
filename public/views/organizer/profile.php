<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        h1 { color: #333; margin-bottom: 30px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        button { padding: 12px 30px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        button:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-menu">
            <ul>
                <li><a href="?route=organizer&action=dashboard">Tableau de bord</a></li>
                <li><a href="?route=organizer&action=profile">Mon profil</a></li>
                <li><a href="?route=organizer&action=createMatch">Créer un match</a></li>
                <li><a href="?route=organizer&action=statistics">Statistiques</a></li>
                <li><a href="?route=organizer&action=reviews">Avis</a></li>
                <li><a href="?route=auth&action=logout">Déconnexion</a></li>
            </ul>
        </nav>

        <h1>Mon profil</h1>

        <?php if ($success): ?>
            <div class="success">Profil mis à jour avec succès !</div>
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
                <label>Nom:</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($organizer->getNom()) ?>" required>
            </div>
            <div class="form-group">
                <label>Prénom:</label>
                <input type="text" name="prenom" value="<?= htmlspecialchars($organizer->getPrenom()) ?>" required>
            </div>
            <div class="form-group">
                <label>Téléphone:</label>
                <input type="tel" name="telephone" value="<?= htmlspecialchars($organizer->getTelephone() ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?= htmlspecialchars($organizer->getEmail()) ?>" disabled>
                <small style="color: #666;">L'email ne peut pas être modifié</small>
            </div>
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
