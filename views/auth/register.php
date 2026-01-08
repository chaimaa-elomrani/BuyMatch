<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Billetterie Sportive</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Inscription</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success) ?>
                                <a href="index.php?action=login">Se connecter maintenant</a>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="index.php?action=register">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom *</label>
                                    <input type="text" name="nom" id="nom" class="form-control" 
                                           value="<?= $_POST['nom'] ?? '' ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label">Prénom *</label>
                                    <input type="text" name="prenom" id="prenom" class="form-control" 
                                           value="<?= $_POST['prenom'] ?? '' ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" name="email" id="email" class="form-control" 
                                       value="<?= $_POST['email'] ?? '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" name="telephone" id="telephone" class="form-control" 
                                       value="<?= $_POST['telephone'] ?? '' ?>">
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Mot de passe *</label>
                                    <input type="password" name="password" id="password" class="form-control" 
                                           minlength="6" required>
                                    <small class="text-muted">Minimum 6 caractères</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Confirmer le mot de passe *</label>
                                    <input type="password" name="confirm_password" id="confirm_password" 
                                           class="form-control" minlength="6" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Type de compte</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="role" 
                                               id="role_user" value="user" 
                                               <?= (!isset($_POST['role']) || $_POST['role'] === 'user') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="role_user">
                                            Utilisateur (Acheteur de billets)
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="role" 
                                               id="role_organizer" value="organizer"
                                               <?= (isset($_POST['role']) && $_POST['role'] === 'organizer') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="role_organizer">
                                            Organisateur d'événements
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                        </form>
                        
                        <hr>
                        <p class="text-center">
                            Déjà inscrit ? 
                            <a href="index.php?action=login">Se connecter</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>