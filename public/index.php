<?php 
// ----------------------------------------------------------
// ✅ CONFIGURACIÓN BÁSICA
// ----------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ruta base (AJUSTALA si cambia la carpeta del proyecto)
define('URL_BASE', 'http://localhost/Draftosaurus/public/');

// ----------------------------------------------------------
// ✅ ENRUTADOR PRINCIPAL
// ----------------------------------------------------------
$ruta = $_GET['ruta'] ?? 'home';

switch ($ruta) {
    // --- AUTENTICACIÓN ---
    case 'login':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case 'register':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        (new AuthController())->register();
        break;

    // --- JUEGO ---
    case 'play':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->play();
        break;

    case 'newGame':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->newGame();
        break;

    case 'join':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->join();
        break;
        
    case 'game':
    require_once __DIR__ . '/../app/controllers/GameController.php';
    (new GameController())->game();
    break;

    case 'players':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->players();
        break;

    // --- HOME ---
    case 'home':
    default:
        require_once __DIR__ . '/../app/controllers/HomeController.php';
        (new HomeController())->index();
        break;
}

