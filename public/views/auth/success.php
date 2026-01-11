<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion réussie - BuyMatch</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #4caf50;
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
        }
        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 28px;
        }
        .welcome-text {
            color: #666;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .user-name {
            color: #667eea;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 30px;
        }
        .redirect-text {
            color: #888;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .countdown {
            color: #667eea;
            font-weight: bold;
            font-size: 18px;
        }
        .progress-bar {
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin: 20px 0;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            width: 0%;
            animation: progress 3s linear forwards;
        }
        @keyframes progress {
            to { width: 100%; }
        }
        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #f5f5f5;
            color: #333;
        }
        .btn-secondary:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>Connexion réussie !</h1>
        <p class="welcome-text">Bienvenue,</p>
        <p class="user-name">
            <?= htmlspecialchars(trim(($_SESSION['user_prenom'] ?? '') . ' ' . ($_SESSION['user_nom'] ?? ''))) ?>
        </p>
        <p class="redirect-text">
            Redirection vers votre tableau de bord dans 
            <span class="countdown" id="countdown">3</span> secondes...
        </p>
        
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        
        <div class="buttons">
            <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="btn btn-primary">
                Aller au tableau de bord
            </a>
            <a href="?route=auth&action=logout" class="btn btn-secondary">
                Se déconnecter
            </a>
        </div>
    </div>

    <script>
        let countdown = 3;
        const countdownEl = document.getElementById('countdown');
        const dashboardUrl = '<?= htmlspecialchars($dashboardUrl) ?>';
        
        const timer = setInterval(function() {
            countdown--;
            countdownEl.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = dashboardUrl;
            }
        }, 1000);
    </script>
</body>
</html>
