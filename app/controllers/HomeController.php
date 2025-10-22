<?php
class HomeController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sesion = isset($_SESSION['logueado']) && $_SESSION['logueado'] === true;
        $usuarioNombre = $sesion && isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
        $lvl = null;
        $mensaje = null;
        $mensaje2 = null;

        // 🔹 Cerrar sesión
        if (isset($_POST['cerrar'])) {
            session_unset();
            session_destroy();
            header("Location: index.php?ruta=login");
            exit;
        }

        // 🔹 Botón “Jugar”
        if (isset($_POST['jugar'])) {
            if ($sesion) {
                header("Location: index.php?ruta=play");
                exit;
            } else {
                // Mostrar mensaje de error si no hay sesión
                $mensaje = "ErrorDebesIniciar.png";
                $mensaje2 = "Iluminacion.png";
            }
        }

        // 🔹 Obtener nivel del usuario si hay sesión
        if ($sesion && $usuarioNombre) {
            require_once __DIR__ . '/../models/Usuario.php';
            $usuarioModel = new Usuario();
            $lvl = $usuarioModel->obtenerNivel($usuarioNombre);
        }

        require __DIR__ . '/../views/home.view.php';
    }
}
?>

