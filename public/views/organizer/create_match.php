<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un match - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        h1 { color: #333; margin-bottom: 30px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        select, input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        .categories-section { background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .category-item { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .category-item input { flex: 1; }
        .btn-add { padding: 8px 15px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn-remove { padding: 8px 15px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button[type="submit"] { padding: 12px 30px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        button:hover { opacity: 0.9; }
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

        <h1>Créer un nouveau match</h1>

        <?php if ($success): ?>
            <div class="success">Match créé avec succès ! Il sera soumis à validation par l'administrateur.</div>
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

        <form method="POST" id="matchForm">
            <div class="form-group">
                <label>Équipe 1:</label>
                <select name="team1_id" required>
                    <option value="">Sélectionner une équipe</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Équipe 2:</label>
                <select name="team2_id" required>
                    <option value="">Sélectionner une équipe</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Date et heure du match:</label>
                <input type="datetime-local" name="date_match" required>
            </div>

            <div class="form-group">
                <label>Lieu:</label>
                <input type="text" name="lieu" required placeholder="Ex: Stade Casablanca">
            </div>

            <div class="form-group">
                <label>Capacité (max 2000):</label>
                <input type="number" name="capacity" required min="1" max="2000" placeholder="Nombre de places">
            </div>

            <div class="categories-section">
                <h3>Catégories de billets (1 à 3 catégories)</h3>
                <div id="categoriesContainer">
                    <div class="category-item">
                        <input type="text" name="categories[0][nom]" placeholder="Nom (ex: VIP)" required>
                        <input type="number" name="categories[0][prix]" placeholder="Prix (DH)" step="0.01" min="0" required>
                        <button type="button" class="btn-add" onclick="addCategory()">+</button>
                    </div>
                </div>
            </div>

            <button type="submit">Créer le match</button>
        </form>
    </div>

    <script>
        let categoryCount = 1;
        
        function addCategory() {
            if (categoryCount >= 3) {
                alert('Maximum 3 catégories autorisées');
                return;
            }
            
            const container = document.getElementById('categoriesContainer');
            const newCategory = document.createElement('div');
            newCategory.className = 'category-item';
            newCategory.innerHTML = `
                <input type="text" name="categories[${categoryCount}][nom]" placeholder="Nom (ex: Standard)" required>
                <input type="number" name="categories[${categoryCount}][prix]" placeholder="Prix (DH)" step="0.01" min="0" required>
                <button type="button" class="btn-remove" onclick="removeCategory(this)">-</button>
            `;
            container.appendChild(newCategory);
            categoryCount++;
        }
        
        function removeCategory(btn) {
            if (document.querySelectorAll('.category-item').length <= 1) {
                alert('Au moins une catégorie est requise');
                return;
            }
            btn.parentElement.remove();
        }
    </script>
</body>
</html>
