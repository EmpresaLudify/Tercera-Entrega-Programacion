<?php
// ----------------------------------------------------------
// CONFIGURACIÓN DE CORS (Cross-Origin Resource Sharing)
// ----------------------------------------------------------

// Permitir solicitudes desde el frontend (ajustá si tenés dominio fijo)
$allowedOrigins = [
    'http://172.20.10.4',       // backend (sin puerto)
    'http://172.20.10.4:8080',  // frontend (puerto 8080)
    'http://localhost:8080',     // si trabajás local
    'http://localhost'           // fallback
];

// Detectar origen real
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Si el origen está permitido, aplicar los headers
if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
} else {
    header("Access-Control-Allow-Origin: http://172.20.10.4:8080"); // valor por defecto
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Responder de inmediato si es una preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

class HomeController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Variables base
        $sesion = isset($_SESSION['logueado']) && $_SESSION['logueado'] === true;
        $usuarioNombre = $sesion && isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
        $lvl = null;
        $mensaje = null;
        $mensaje2 = null;

        // Cerrar sesión
        if (isset($_POST['cerrar'])) {
            session_unset();
            session_destroy();
            header("Location: index.php?ruta=login");
            exit;
        }

        // Jugar (redirige según sesión)
        if (isset($_POST['jugar'])) {
            if ($sesion) {
                header("Location: index.php?ruta=play");
                exit;
            } else {
                // No hay sesión → mostrar mensaje
                $mensaje = "MensajeSesion.png";
                $mensaje2 = "IluminacionInicioSesion.png";
            }
        }

        // Obtener nivel solo si hay sesión
        if ($sesion && $usuarioNombre) {
            require_once __DIR__ . '/../models/Usuario.php';
            $usuarioModel = new Usuario();

            try {
                $lvl = $usuarioModel->obtenerNivel($usuarioNombre);
            } catch (Exception $e) {
                error_log("[HOME ERROR] " . $e->getMessage());
                $lvl = 1;
            }
        }

        // Cargar vista sin romper headers
        ob_start();
        include __DIR__ . '/../views/home.view.php';
        $contenido = ob_get_clean();
        echo $contenido;    
    }
}
?>