<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acheter un billet - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .match-info { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        button { padding: 12px 30px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        button:hover { background: #5568d3; }
        .seats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); gap: 10px; margin-top: 10px; }
        .seat { padding: 10px; border: 2px solid #ddd; border-radius: 5px; text-align: center; cursor: pointer; }
        .seat.available { background: #d4edda; border-color: #28a745; }
        .seat.taken { background: #f8d7da; border-color: #dc3545; cursor: not-allowed; }
        .seat.selected { background: #667eea; color: white; border-color: #667eea; }
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

        <h1>Acheter un billet</h1>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($match): ?>
            <div class="match-info">
                <h2><?= htmlspecialchars($match['team1_name'] . ' vs ' . $match['team2_name']) ?></h2>
                <p><strong>Date:</strong> <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($match['date_match']))) ?></p>
                <p><strong>Lieu:</strong> <?= htmlspecialchars($match['lieu']) ?></p>
            </div>

            <form method="POST" id="buyForm">
                <input type="hidden" name="match_id" value="<?= htmlspecialchars($match['id']) ?>">
                
                <div class="form-group">
                    <label>Catégorie:</label>
                    <select name="category_id" id="categorySelect" required onchange="loadSeats()">
                        <option value="">Sélectionner une catégorie</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" data-price="<?= $cat['prix'] ?>">
                                <?= htmlspecialchars($cat['nom']) ?> - <?= htmlspecialchars($cat['prix']) ?> DH
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="seatsGroup" style="display: none;">
                    <label>Place disponible:</label>
                    <div class="seats-grid" id="seatsGrid"></div>
                    <input type="hidden" name="place_number" id="selectedSeat" required>
                </div>

                <button type="submit">Acheter le billet</button>
            </form>

            <script>
                function loadSeats() {
                    const categoryId = document.getElementById('categorySelect').value;
                    const matchId = <?= $match['id'] ?>;
                    
                    if (!categoryId) {
                        document.getElementById('seatsGroup').style.display = 'none';
                        return;
                    }
                    
                    // Reload page with category_id to load seats
                    window.location.href = '?route=user&action=buyTicket&match_id=' + matchId + '&category_id=' + categoryId;
                }
                
                // Auto-load seats if category_id is in URL
                window.onload = function() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const categoryId = urlParams.get('category_id');
                    if (categoryId) {
                        document.getElementById('categorySelect').value = categoryId;
                        <?php if (!empty($availableSeats)): ?>
                            const availableSeats = <?= json_encode($availableSeats) ?>;
                            const seatsGrid = document.getElementById('seatsGrid');
                            document.getElementById('seatsGroup').style.display = 'block';
                            seatsGrid.innerHTML = '';
                            
                            if (availableSeats.length === 0) {
                                seatsGrid.innerHTML = '<p>Aucune place disponible pour cette catégorie.</p>';
                            } else {
                                availableSeats.forEach(seat => {
                                    const seatDiv = document.createElement('div');
                                    seatDiv.className = 'seat available';
                                    seatDiv.textContent = seat;
                                    seatDiv.onclick = function() {
                                        document.querySelectorAll('.seat').forEach(s => s.classList.remove('selected'));
                                        this.classList.add('selected');
                                        document.getElementById('selectedSeat').value = seat;
                                    };
                                    seatsGrid.appendChild(seatDiv);
                                });
                            }
                        <?php endif; ?>
                    }
                };
            </script>
        <?php else: ?>
            <p>Match non trouvé. <a href="?route=user&action=matches">Retour aux matchs</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
