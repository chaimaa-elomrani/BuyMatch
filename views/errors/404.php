<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__FILE__)));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable - 404</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include BASE_PATH . '/views/partials/navbar.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card shadow-lg">
                    <div class="card-body py-5">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 80px;"></i>
                        <h1 class="display-1 text-primary mt-4">404</h1>
                        <h2 class="mb-4">Page introuvable</h2>
                        <p class="lead text-muted mb-4">
                            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
                        </p>
                        <div class="d-grid gap-2 d-md-flex justify-content-center">
                            <a href="index.php?action=home" class="btn btn-primary btn-lg">
                                <i class="fas fa-home"></i> Retour à l'accueil
                            </a>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Page précédente
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include BASE_PATH . '/views/partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
