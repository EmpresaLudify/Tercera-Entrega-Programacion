<?php
// ----------------------------------------------------------
//  CONFIGURACIÓN GLOBAL DE CORS (DEBE SER LO PRIMERO DE TODO)
// ----------------------------------------------------------
$allowedOrigins = [
    'http://172.20.10.4',       // backend (sin puerto)
    'http://172.20.10.4:8080',  // frontend (puerto 8080)
    'http://localhost:8080',     // local
    'http://localhost'           // fallback
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ----------------------------------------------------------
//  CONFIGURACIÓN BÁSICA
// ----------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ruta base
define('URL_BASE', 'http://172.20.10.4:8080/');


// ----------------------------------------------------------
//  ENRUTADOR PRINCIPAL
// ----------------------------------------------------------
$ruta = $_GET['ruta'] ?? 'home';

switch ($ruta) {
    case 'login':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case 'register':
        require_once __DIR__ . '/../app/controllers/AuthController.php';
        (new AuthController())->register();
        break;

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

    case 'estadoPartida':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->estadoPartida();
        break;

    case 'tirarDado':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->tirarDado();
        break;

    case 'estadoDados':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->estadoDados();
        break;

    case 'tirarDadoZona':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->tirarDadoZona();
        break;

    case 'estadoDadoZona':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->estadoDadoZona();
        break;

    case 'colocarYPasarTurno':
        require_once __DIR__ . '/../app/controllers/GameController.php';
        (new GameController())->colocarYPasarTurno();
        break;

    default:
        require_once __DIR__ . '/../app/controllers/HomeController.php';
        (new HomeController())->index();
        break;
}
