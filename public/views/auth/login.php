<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../models/AbstractUser.php';
require_once __DIR__ . '/../../../models/User.php';

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Email et mot de passe requis.";
    } else {
        $user = new User();
        if ($user->login($email, $password)) {
            $successMessage = "✓ Connexion réussie ! Redirection en cours...";
            $role = $_SESSION['user_role'] ?? 'user';
            $base = '../../';
            switch ($role) {
                case 'admin':
                    header("Location: {$base}?route=dashboard&section=admin");
                    break;
                case 'organizer':
                    header("Location: {$base}?route=dashboard&section=organizer");
                    break;
                default:
                    header("Location: {$base}?route=dashboard&section=user");
                    break;
            }
            exit;
        } else {
            $errors[] = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - BuyMatch</title>
</head>
<body>
    <h1>Se connecter</h1>

    <?php if ($successMessage): ?>
        <div style="color:green; font-size:1.4em; font-weight:bold; margin:20px 0; padding:15px; border:2px solid green; background:#e6ffe6;">
            <?= $successMessage ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div style="color:red; margin:15px 0; padding:10px; border:1px solid red;">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div style="margin-bottom:15px;">
            <label>Email :</label><br>
            <input type="email" name="email" required style="width:320px;padding:10px;">
        </div>

        <div style="margin-bottom:20px;">
            <label>Mot de passe :</label><br>
            <input type="password" name="password" required style="width:320px;padding:10px;">
        </div>

        <button type="submit" style="padding:12px 25px; font-size:1.1em;">Se connecter</button>
    </form>

    <p style="margin-top:25px;">Pas de compte ? <a href="?route=register">S'inscrire</a></p>
</body>
</html>