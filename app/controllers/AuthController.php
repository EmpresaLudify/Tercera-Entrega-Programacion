<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u = new Usuario();
            $usuario = trim($_POST['usuario'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($usuario === '' || $password === '') {
                $_SESSION['mensaje'] = "../Images/ErrorCamposVacios.png";
                $_SESSION['mensaje2'] = "../Images/Iluminacion.png";
                header("Location: index.php?ruta=login");
                exit;
            }

            $data = $u->login($usuario, $password);
            if ($data) {
                session_regenerate_id(true);
                $_SESSION['logueado'] = true;
                $_SESSION['usuario'] = $data['usuario'];
                header("Location: index.php?ruta=play");
                exit;
            } else {
                $_SESSION['mensaje'] = "../Images/ContraseñaIncorrecta.png";
                $_SESSION['mensaje2'] = "../Images/Iluminacion.png";
                header("Location: index.php?ruta=login");
                exit;
            }
        }

        // Si no viene por POST, mostrar el login vacío
        require __DIR__ . '/../views/login.view.php';
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $usuario = trim($_POST['usuario'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmar = trim($_POST['confirmar'] ?? '');

            if ($email === '' || $usuario === '' || $password === '' || $confirmar === '') {
                $_SESSION['mensaje'] = "../Images/ErrorCampos.png";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['mensaje'] = "../Images/ErrorCorreo.png";
            } elseif (strlen($usuario) < 3) {
                $_SESSION['mensaje'] = "../Images/ErrorUsuario.png";
            } elseif (strlen($password) < 6) {
                $_SESSION['mensaje'] = "../Images/ErrorPassCaracteres.png";
            } elseif ($password !== $confirmar) {
                $_SESSION['mensaje'] = "../Images/ErrorPassConfirmacion.png";
            } else {
                try {
                    $u = new Usuario();
                    $u->registrar($email, $usuario, $password);
                    header("Location: index.php?ruta=login");
                    exit;
                } catch (Exception $e) {
                    $_SESSION['mensaje'] = "../Images/ErrorRegistro.png";
                    error_log("[REGISTER ERROR] " . $e->getMessage());
                }
            }

            // Si hubo error, redirigir
            if (isset($_SESSION['mensaje'])) {
                $_SESSION['mensaje2'] = "../Images/Iluminacion.png";
                header("Location: index.php?ruta=register");
                exit;
            }
        }

        require __DIR__ . '/../views/register.view.php';
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: index.php?ruta=login");
        exit;
    }
}
