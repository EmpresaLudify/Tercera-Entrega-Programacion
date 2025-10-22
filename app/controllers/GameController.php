<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Partida.php';
require_once __DIR__ . '/../core/Database.php'; // si no estaba ya incluido

class GameController
{
    // 🔒 Verifica que el usuario esté logueado
    private function requireLogin()
    {
        if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
            header("Location: index.php?ruta=login");
            exit;
        }
    }

    // 🦖 Menú principal (Play)
    public function play()
    {
        error_log("🚀 Entrando a GameController::play()", 3, __DIR__ . "/../../debug.log");
        $this->requireLogin();

        $usuarioNombre = $_SESSION['usuario'] ?? null;
        if (!$usuarioNombre) {
            header("Location: index.php?ruta=login");
            exit;
        }

        try {
            $usuarioModel = new Usuario();
            $lvl = $usuarioModel->obtenerNivel($usuarioNombre);
        } catch (Exception $e) {
            error_log("[PLAY ERROR] " . $e->getMessage());
            $lvl = 1; // fallback
        }

        require __DIR__ . '/../views/play.view.php';
    }

    // 🆕 Crear nueva partida
    public function newGame()
    {
        $this->requireLogin();
        $mensaje = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jugadores  = (int)($_POST['jugadores'] ?? 0);
            $tipo       = $_POST['tipo'] ?? '';
            $creador    = $_SESSION['usuario'] ?? '';
            $nombre     = $_POST['nombre'] ?? '';
            $contrasena = $_POST['password'] ?? null;

            try {
                $partidaModel = new Partida();
                $partidaId = $partidaModel->crear($creador, $jugadores, $tipo, $nombre, $contrasena);

                // Si es modo seguimiento, registrar los jugadores ingresados manualmente
                if ($tipo === 'seguimiento') {
                    $nombres = [];
                    for ($i = 1; $i <= 5; $i++) {
                        $n = trim($_POST["nombre_jugador_$i"] ?? '');
                        if ($n !== '') $nombres[] = $n;
                    }
                    if (!empty($nombres)) {
                        $partidaModel->crearSeguimiento($partidaId, $nombres);
                    }
                }

                header("Location: index.php?ruta=game&id=" . (int)$partidaId);
                exit;
            } catch (Exception $e) {
                error_log("[NEWGAME ERROR] " . $e->getMessage());
                $mensaje = "Error al crear la partida. Intentalo de nuevo.";
            }
        }

        require __DIR__ . '/../views/newGame.view.php';
    }

    // 🚪 Unirse a partida
    public function join()
    {
        $this->requireLogin();
        $mensaje = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = $_POST['nombre'] ?? '';
            $password = $_POST['password'] ?? null;
            $usuario  = $_SESSION['usuario'] ?? '';

            try {
                $partidaModel = new Partida();
                $partidaId = $partidaModel->unirse($nombre, $usuario, $password);
                header("Location: index.php?ruta=game&id=" . (int)$partidaId);
                exit;
            } catch (Exception $e) {
                $mensaje = $e->getMessage();
            }
        }

        require __DIR__ . '/../views/join.view.php';
    }

    // 🎮 Ver partida
    public function game()
    {
        $this->requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: " . URL_BASE . "index.php?ruta=play");
            exit;
        }

        try {
            $partidaModel = new Partida();
            $partida = $partidaModel->obtenerPorId($id);

            if (!$partida) {
                require __DIR__ . '/../views/error.view.php';
                exit;
            }

            // Jugadores registrados
            $jugadores = $partidaModel->obtenerJugadores($id) ?? [];
            $totalJugadores = count($jugadores);
            if ($totalJugadores < 2 || $totalJugadores > 5) {
                $totalJugadores = 2; // fallback
            }

            // Datos del jugador actual
            $usuarioNombre = $_SESSION['usuario'] ?? 'Invitado';
            $lvl = $_SESSION['lvl'] ?? 1;

            // 🦕 Cargar dinosaurios y repartir fichas (solo la primera vez)
            require_once __DIR__ . '/../models/Dinosaurio.php';

            if (!isset($_SESSION['manos'][$id])) {
                $bolsa = DinoFactory::bolsaPorJugadores($totalJugadores);
                $manos = [];
                for ($i = 0; $i < $totalJugadores; $i++) {
                    $manos[$i] = DinoFactory::robarMano($bolsa, 6);
                }
                $_SESSION['manos'][$id] = $manos;
            }

            $manos = $_SESSION['manos'][$id];

            // Determinar mano del jugador actual
            $indiceJugador = 0;
            if (!empty($jugadores)) {
                $indiceJugador = array_search($usuarioNombre, $jugadores);
                if ($indiceJugador === false) $indiceJugador = 0;
            }

            $mano = $manos[$indiceJugador] ?? [];
            $colores = Especie::COLOR;

            // Renderizar vista
            require __DIR__ . '/../views/game.view.php';

        } catch (Exception $e) {
            error_log("[GAME ERROR] " . $e->getMessage());
            require __DIR__ . '/../views/error.view.php';
            exit;
        }
    }

    // 🦖 Guardar colocación individual de dinosaurios (no usado ahora)
    public function colocarDino()
    {
        $datos = json_decode(file_get_contents("php://input"), true);
        $nombre = $datos['nombre'] ?? '';
        $jugador = $datos['jugador'] ?? '';
        $zona = $datos['zona'] ?? '';

        if ($nombre && $jugador && $zona) {
            $partida = new Partida();
            $partida->colocarDinosaurio($jugador, $nombre, $zona);
        }
    }

    // 🗄️ Guardar todos los movimientos (para ranking / historial)
    public function guardarMovimientos()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['idPartida']) || !isset($input['movimientos'])) {
            echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $idPartida = $input['idPartida'];
        $movimientos = $input['movimientos'];

        try {
            $db = Database::getInstancia()->getConexion();
            $stmt = $db->prepare("INSERT INTO movimientos 
                (id_partida, jugador, color, zona, turno, puntos, fecha)
                VALUES (:id_partida, :jugador, :color, :zona, :turno, :puntos, :fecha)");

            foreach ($movimientos as $m) {
                $stmt->execute([
                    ':id_partida' => $idPartida,
                    ':jugador' => $m['jugador'],
                    ':color' => $m['color'],
                    ':zona' => $m['zona'],
                    ':turno' => $m['turno'],
                    ':puntos' => $m['puntos'],
                    ':fecha' => $m['fecha']
                ]);
            }

            echo json_encode(['ok' => true]);
        } catch (Exception $e) {
            error_log("[ERROR guardarMovimientos] " . $e->getMessage());
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }
}

