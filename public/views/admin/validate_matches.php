<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valider les matchs - BuyMatch</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-menu { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .nav-menu ul { list-style: none; display: flex; flex-wrap: wrap; gap: 15px; }
        .nav-menu a { color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .nav-menu a:hover { background: rgba(255,255,255,0.3); }
        h1 { color: #333; margin-bottom: 30px; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .alert-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #dc3545; color: white; }
        .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; }
        .status-pending { background: #ffc107; color: #000; }
        .status-validated { background: #17a2b8; color: white; }
        .status-published { background: #28a745; color: white; }
        .status-rejected { background: #dc3545; color: white; }
        .btn { padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; margin-right: 5px; }
        .btn-validate { background: #28a745; color: white; }
        .btn-validate:hover { background: #218838; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-reject:hover { background: #c82333; }
        .btn-publish { background: #17a2b8; color: white; }
        .btn-publish:hover { background: #138496; }
        form { display: inline; }
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd; }
        .tab { padding: 10px 20px; background: #f8f9fa; border: none; cursor: pointer; border-radius: 5px 5px 0 0; }
        .tab.active { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Valider les matchs</h1>
        
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

        <?php if ($success): ?>
            <div class="alert alert-success">
                Action effectuée avec succès !
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul style="margin-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab active" onclick="showTab('pending')">En attente (<?= count($pendingMatches) ?>)</button>
            <button class="tab" onclick="showTab('all')">Tous les matchs</button>
        </div>

        <div id="pending-tab">
            <h2>Matchs en attente de validation</h2>
            <?php if (empty($pendingMatches)): ?>
                <p>Aucun match en attente de validation.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Match</th>
                            <th>Organisateur</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Capacité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingMatches as $match): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($match['team1_name'] . ' vs ' . $match['team2_name']) ?></strong></td>
                                <td><?= htmlspecialchars($match['org_prenom'] . ' ' . $match['org_nom']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($match['date_match']))) ?></td>
                                <td><?= htmlspecialchars($match['lieu']) ?></td>
                                <td><?= htmlspecialchars($match['capacity']) ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="match_id" value="<?= htmlspecialchars($match['id']) ?>">
                                        <input type="hidden" name="action" value="validate">
                                        <button type="submit" class="btn btn-validate" onclick="return confirm('Valider ce match ?')">Valider</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="match_id" value="<?= htmlspecialchars($match['id']) ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn btn-reject" onclick="return confirm('Rejeter ce match ?')">Rejeter</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div id="all-tab" style="display: none;">
            <h2>Tous les matchs</h2>
            <?php if (empty($allMatches)): ?>
                <p>Aucun match.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Match</th>
                            <th>Organisateur</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allMatches as $match): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($match['team1_name'] . ' vs ' . $match['team2_name']) ?></strong></td>
                                <td><?= htmlspecialchars($match['org_prenom'] . ' ' . $match['org_nom']) ?></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($match['date_match']))) ?></td>
                                <td><?= htmlspecialchars($match['lieu']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= htmlspecialchars($match['statut']) ?>">
                                        <?= htmlspecialchars(ucfirst($match['statut'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($match['statut'] === 'validated'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="match_id" value="<?= htmlspecialchars($match['id']) ?>">
                                            <input type="hidden" name="action" value="publish">
                                            <button type="submit" class="btn btn-publish" onclick="return confirm('Publier ce match ?')">Publier</button>
                                        </form>
                                    <?php elseif ($match['statut'] === 'pending'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="match_id" value="<?= htmlspecialchars($match['id']) ?>">
                                            <input type="hidden" name="action" value="validate">
                                            <button type="submit" class="btn btn-validate">Valider</button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="match_id" value="<?= htmlspecialchars($match['id']) ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-reject">Rejeter</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showTab(tab) {
            document.getElementById('pending-tab').style.display = tab === 'pending' ? 'block' : 'none';
            document.getElementById('all-tab').style.display = tab === 'all' ? 'block' : 'none';
            
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach((t, index) => {
                if ((tab === 'pending' && index === 0) || (tab === 'all' && index === 1)) {
                    t.classList.add('active');
                } else {
                    t.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>
