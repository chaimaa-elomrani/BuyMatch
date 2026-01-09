<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
$route = $_GET['route'] ?? 'home';
$action = $_GET['action'] ?? 'index';

switch ($route) {
    case 'auth':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->$action();
        break;
    case 'user':
        require __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->$action();
        break;

    case 'organizer':
        require __DIR__ . '/controllers/OrganizerController.php';
        $controller = new OrganizerController();
        $controller->$action();
        break;

    case 'admin':
        require __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        $controller->$action();
        break;


    default:
        echo "<h1>Bienvenue sur BuyMatch</h1>";
        echo "<p><a href='?route=auth&action=login'>Se connecter</a> | <a href='?route=auth&action=register'>S'inscrire</a></p>";
        break;
}