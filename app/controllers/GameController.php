<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Partida.php';
require_once __DIR__ . '/../core/Database.php'; // si no estaba ya incluido

class GameController
{
    // Verifica que el usuario esté logueado
    private function requireLogin()
    {
        if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
            header("Location: index.php?ruta=login");
            exit;
        }
    }

    // Menú principal (Play)
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

    // Crear nueva partida
    public function newGame()
    {
        $this->requireLogin();
        $mensaje = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jugadores = (int) ($_POST['jugadores'] ?? 0);
            $tipo = $_POST['tipo'] ?? '';
            $creador = $_SESSION['usuario'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $contrasena = $_POST['password'] ?? null;

            try {
                $partidaModel = new Partida();
                $partidaId = $partidaModel->crear($creador, $jugadores, $tipo, $nombre, $contrasena);

                // Si es modo seguimiento, registrar los jugadores ingresados manualmente
                if ($tipo === 'seguimiento') {
                    $nombres = [];
                    for ($i = 1; $i <= 5; $i++) {
                        $n = trim($_POST["nombre_jugador_$i"] ?? '');
                        if ($n !== '')
                            $nombres[] = $n;
                    }
                    if (!empty($nombres)) {
                        $partidaModel->crearSeguimiento($partidaId, $nombres);
                    }
                }

                header("Location: index.php?ruta=game&id=" . (int) $partidaId);
                exit;
            } catch (Exception $e) {
                error_log("[NEWGAME ERROR] " . $e->getMessage());
                $mensaje = "Error al crear la partida. Intentalo de nuevo.";
            }
        }

        require __DIR__ . '/../views/newGame.view.php';
    }

    // Unirse a partida
    public function join()
    {
        $this->requireLogin();
        $mensaje = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $password = $_POST['password'] ?? null;
            $usuario = $_SESSION['usuario'] ?? '';

            try {
                $partidaModel = new Partida();
                $partidaId = $partidaModel->unirse($nombre, $usuario, $password);
                header("Location: index.php?ruta=game&id=" . (int) $partidaId);
                exit;
            } catch (Exception $e) {
                $mensaje = $e->getMessage();
            }
        }

        require __DIR__ . '/../views/join.view.php';
    }

    // Ver partida
    public function game()
    {
        $this->requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
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

            // Cargar dinosaurios y repartir fichas (solo la primera vez)
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
                if ($indiceJugador === false)
                    $indiceJugador = 0;
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

    // Guardar colocación individual de dinosaurios (no usado ahora)
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

    // Guardar todos los movimientos (para ranking / historial)
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

    // Consultar estado y jugadores de la partida (para refresco en tiempo real)
    public function estadoPartida()
    {
        header('Content-Type: application/json');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        try {
            $partidaModel = new Partida();
            $partida = $partidaModel->obtenerPorId($id);
            if (!$partida) {
                echo json_encode(['error' => 'Partida no encontrada']);
                return;
            }

            $jugadores = $partidaModel->obtenerJugadores($id);

            echo json_encode([
                'estado' => $partida['estado'],
                'jugadores' => $jugadores,
                'turno_actual' => $partida['turno_actual'] ?? null,
                'dado_zona' => $partida['dado_zona'] ?? null, // obligatorio para el front
            ]);

        } catch (Exception $e) {
            error_log("[estadoPartida ERROR] " . $e->getMessage());
            echo json_encode(['error' => 'Error interno']);
        }
    }

    // Tirar dado (llamado por fetch desde JS)
    public function tirarDado()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $id = (int) ($_POST['idPartida'] ?? 0);
        $usuario = $_SESSION['usuario'] ?? '';
        if ($id <= 0 || !$usuario) {
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        $numero = random_int(1, 6);

        $partidaModel = new Partida();
        $partidaModel->registrarDado($id, $usuario, $numero);

        echo json_encode(['ok' => true, 'numero' => $numero]);
    }

    // Verificar si todos ya tiraron
    public function estadoDados()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        $partidaModel = new Partida();
        $dados = $partidaModel->obtenerDados($id);

        $faltan = array_filter($dados, fn($j) => $j['dado_inicial'] === null);
        $todosListos = count($faltan) === 0;

        if (!$todosListos) {
            echo json_encode([
                'todosListos' => false,
                'dados' => $dados
            ]);
            return;
        }

        // Verificar si hay empates en el valor máximo
        $valores = array_column($dados, 'dado_inicial');
        $max = max($valores);
        $empatados = array_filter($dados, fn($j) => (int) $j['dado_inicial'] === (int) $max);

        if (count($empatados) > 1) {
            // Empate → resetear solo esos jugadores
            $usuariosEmpatados = array_column($empatados, 'usuario');
            $db = Database::getInstancia()->getConexion();
            $stmt = $db->prepare("UPDATE jugadores_partida SET dado_inicial = NULL WHERE partida_id = :id AND usuario = :usuario");

            foreach ($usuariosEmpatados as $u) {
                $stmt->execute([':id' => $id, ':usuario' => $u]);
            }

            echo json_encode([
                'todosListos' => false,
                'empate' => true,
                'jugadoresEmpatados' => $usuariosEmpatados,
                'dados' => $dados,
                'mensaje' => 'Empate detectado: los jugadores empatados deben volver a tirar.'
            ]);
            return;
        }

        // Si no hay empate → continuar normalmente
        usort($dados, fn($a, $b) => $b['dado_inicial'] <=> $a['dado_inicial']);
        $primerJugador = $dados[0]['usuario'];
        $partidaModel->setTurnoActual($id, $primerJugador);

        $db = Database::getInstancia()->getConexion();
        $db->prepare("UPDATE partidas SET estado='en_curso' WHERE id=?")->execute([$id]);

        echo json_encode([
            'todosListos' => true,
            'dados' => $dados,
            'primerJugador' => $primerJugador
        ]);
    }

    // Tirar dado de Draftosaurus (solo quien tiene el turno)
    public function tirarDadoZona()
    {
        header('Content-Type: application/json');

        $idPartida = $_POST['idPartida'] ?? null;
        $jugador = $_SESSION['usuario'] ?? null;

        if (!$idPartida || !$jugador) {
            echo json_encode(['ok' => false, 'error' => 'Datos inválidos.']);
            return;
        }

        $partidaModel = new Partida();
        $resultado = $partidaModel->tirarDadoZona($idPartida, $jugador);

        if (!$resultado) {
            echo json_encode(['ok' => false, 'error' => 'Error al tirar dado.']);
            return;
        }

        echo json_encode(['ok' => true, 'resultado' => $resultado]);
    }

    // Consultar resultado actual del dado
    public function estadoDadoZona()
    {
        header('Content-Type: application/json');
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        $partidaModel = new Partida();
        $zona = $partidaModel->obtenerDadoZona($id);
        echo json_encode(['zona' => $zona]);
    }

    // Colocar ficha y pasar turno (valida turno y dado_zona)
    public function marcarColoco($idPartida, $jugador)
    {
        $conexion = Database::getInstancia()->getConexion();
        $stmt = $conexion->prepare("
        UPDATE jugadores_partida
        SET coloco_en_ronda = 1
        WHERE partida_id = :id AND usuario = :jugador
    ");
        $stmt->execute([':id' => $idPartida, ':jugador' => $jugador]);
    }

    public function verificarTodosColocaron($idPartida)
    {
        $conexion = Database::getInstancia()->getConexion();
        $stmt = $conexion->prepare("
        SELECT COUNT(*) AS faltan
        FROM jugadores_partida
        WHERE partida_id = :id AND coloco_en_ronda = 0
    ");
        $stmt->execute([':id' => $idPartida]);
        $row = $stmt->fetch();
        return $row && $row['faltan'] == 0;
    }

    public function pasarTurno($idPartida)
    {
        $conexion = Database::getInstancia()->getConexion();

        // Obtener lista de jugadores en orden
        $stmt = $conexion->prepare("SELECT usuario FROM jugadores_partida WHERE partida_id = :id ORDER BY id ASC");
        $stmt->execute([':id' => $idPartida]);
        $jugadores = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($jugadores) === 0) {
            throw new Exception("No hay jugadores en la partida.");
        }

        // Turno actual
        $stmt = $conexion->prepare("SELECT turno_actual FROM partidas WHERE id = :id");
        $stmt->execute([':id' => $idPartida]);
        $turnoActual = $stmt->fetchColumn();

        // Si por algún motivo no hay turno actual, empieza el primero
        if (!$turnoActual) {
            $siguiente = $jugadores[0];
        } else {
            $index = array_search($turnoActual, $jugadores);
            if ($index === false)
                $index = -1;
            $siguiente = $jugadores[($index + 1) % count($jugadores)];
        }

        // Limpiar dado y pasar turno
        $stmt = $conexion->prepare("
        UPDATE partidas
        SET dado_zona = NULL, turno_actual = :siguiente
        WHERE id = :id
    ");
        $stmt->execute([':siguiente' => $siguiente, ':id' => $idPartida]);

        return $siguiente;
    }

    /**
     *  Valida si la zona elegida cumple las condiciones del dado.
     */
    private function validarZonaPorDado(string $caraDado, string $zona, int $idPartida): bool
    {
        // Normalizar
        $caraDado = strtoupper(trim($caraDado));
        $zona = strtoupper(trim($zona));

        //  Mapear zonas permitidas por cara del dado
        return match ($caraDado) {
            'EL_BOSQUE' => in_array($zona, ['RIVER', 'FOREST', 'PLAINS']), // zonas 1-2-3
            'LLANURA' => in_array($zona, ['MOUNTAINS', 'CAFETERIA', 'RESTROOMS']), // zonas 4-5-6
            'BAÑOS' => in_array($zona, ['FOREST', 'MOUNTAINS', 'RESTROOMS']), // 2-4-6 izquierda del río
            'CAFETERIA' => in_array($zona, ['RIVER', 'PLAINS', 'CAFETERIA']), // 1-3-5 derecha del río
            'RECINTO_VACIO' => $this->zonaEstaVacia($idPartida, $zona),
            'CUIDADO_T_REX' => !$this->zonaTieneTrex($idPartida, $zona),
            default => true
        };
    }

    /**
     * Verifica si una zona está vacía (sin dinosaurios colocados)
     */
    private function zonaEstaVacia(int $idPartida, string $zona): bool
    {
        $db = Database::getInstancia()->getConexion();
        $stmt = $db->prepare("SELECT COUNT(*) FROM movimientos WHERE id_partida = :id AND zona = :zona");
        $stmt->execute([':id' => $idPartida, ':zona' => $zona]);
        $cantidad = (int) $stmt->fetchColumn();
        return $cantidad === 0;
    }

    /**
     * Verifica si una zona contiene un T-Rex
     */
    private function zonaTieneTrex(int $idPartida, string $zona): bool
    {
        $db = Database::getInstancia()->getConexion();
        $stmt = $db->prepare("SELECT COUNT(*) FROM movimientos WHERE id_partida = :id AND zona = :zona AND color = 'TREX'");
        $stmt->execute([':id' => $idPartida, ':zona' => $zona]);
        $cantidad = (int) $stmt->fetchColumn();
        return $cantidad > 0;
    }

    public function colocarYPasarTurno()
    {
        header('Content-Type: application/json');

        try {
            $idPartida = $_POST['idPartida'] ?? null;
            $jugador = $_SESSION['usuario'] ?? null;
            if (!$idPartida || !$jugador) {
                throw new Exception('Datos inválidos.');
            }

            $zona = $_POST['zona'] ?? null;
            $especie = $_POST['especie'] ?? null;

            $partidaModel = new Partida();

            // Registrar jugada (si querés guardar detalle)
            if ($especie && $zona) {
                $partidaModel->colocarDinosaurio($jugador, $especie, $zona, $idPartida);
            }

            // Marcar al jugador como que ya colocó
            $partidaModel->marcarColocacion($idPartida, $jugador);

            // Verificar si todos colocaron
            $todosColocaron = $partidaModel->todosColocaron($idPartida);

            if ($todosColocaron) {
                $siguiente = $partidaModel->avanzarTurno($idPartida);
                echo json_encode(['ok' => true, 'todos_colocaron' => true, 'siguiente' => $siguiente]);
            } else {
                echo json_encode(['ok' => true, 'todos_colocaron' => false]);
            }
        } catch (Exception $e) {
            error_log("[colocarYPasarTurno] " . $e->getMessage());
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }


}

