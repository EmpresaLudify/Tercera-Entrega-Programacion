<?php
require_once __DIR__ . '/../core/Database.php';

class Partida
{
    private PDO $db;

    public function __construct()
    {
        // Usamos siempre la misma instancia de conexión
        $this->db = Database::getInstancia()->getConexion();
    }

    /* ======================================================
        CREAR NUEVA PARTIDA
       ====================================================== */
    public function crear(string $creador, int $jugadores, string $tipo, string $nombre, ?string $password = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO partidas (creador, jugadores, tipo, nombre, contraseña, estado)
            VALUES (:creador, :jugadores, :tipo, :nombre, :password, 'pendiente')
        ");
        $stmt->execute([
            ':creador' => $creador,
            ':jugadores' => $jugadores,
            ':tipo' => $tipo,
            ':nombre' => $nombre,
            ':password' => $password
        ]);

        // Obtenemos el ID de la partida recién creada
        $id = (int) $this->db->lastInsertId();

        // Si es una partida online, agregamos al creador como jugador
        if ($tipo === 'online') {
            $this->agregarJugador($id, $creador);
        }

        return $id;
    }

    /* ======================================================
        UNIRSE A UNA PARTIDA EXISTENTE
       ====================================================== */
    public function unirse(string $nombre, string $usuario, ?string $password = null): int
    {
        $stmt = $this->db->prepare("SELECT * FROM partidas WHERE nombre = :nombre LIMIT 1");
        $stmt->execute([':nombre' => $nombre]);
        $partida = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$partida) {
            throw new Exception("La partida no existe.");
        }

        // Validamos contraseña si tiene
        if (!empty($partida['contraseña']) && $partida['contraseña'] !== $password) {
            throw new Exception("Contraseña incorrecta.");
        }

        $partidaId = (int) $partida['id'];

        // Verificamos si el usuario ya está en la partida
        $check = $this->db->prepare("SELECT 1 FROM jugadores_partida WHERE partida_id = :id AND usuario = :usuario");
        $check->execute([':id' => $partidaId, ':usuario' => $usuario]);
        if (!$check->fetch()) {
            $this->agregarJugador($partidaId, $usuario);
        }

        // Actualizamos el estado si ya se completó
        $this->actualizarEstado($partidaId);

        return $partidaId;
    }

    /* ======================================================
        AGREGAR JUGADOR A UNA PARTIDA
       ====================================================== */
    public function agregarJugador(int $partidaId, string $usuario): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO jugadores_partida (partida_id, usuario)
            VALUES (:partida_id, :usuario)
        ");
        return $stmt->execute([
            ':partida_id' => $partidaId,
            ':usuario' => $usuario
        ]);
    }

    /* ======================================================
        CREAR JUGADORES LOCALES (modo seguimiento)
       ====================================================== */
    public function crearSeguimiento(int $partidaId, array $nombres): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO jugadores_local 
            (partida_id, jugador1, jugador2, jugador3, jugador4, jugador5)
            VALUES (:partida_id, :j1, :j2, :j3, :j4, :j5)
        ");
        $stmt->execute([
            ':partida_id' => $partidaId,
            ':j1' => $nombres[0] ?? '',
            ':j2' => $nombres[1] ?? '',
            ':j3' => $nombres[2] ?? '',
            ':j4' => $nombres[3] ?? '',
            ':j5' => $nombres[4] ?? ''
        ]);
    }

    /* ======================================================
        ACTUALIZAR ESTADO DE PARTIDA
       ====================================================== */
    public function actualizarEstado(int $partidaId): void
    {
        $stmt = $this->db->prepare("SELECT jugadores, estado FROM partidas WHERE id = :id");
        $stmt->execute([':id' => $partidaId]);
        $partida = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$partida)
            return;

        $jugadoresNecesarios = (int) $partida['jugadores'];
        $estadoActual = $partida['estado'];

        $stmt2 = $this->db->prepare("SELECT COUNT(*) AS total FROM jugadores_partida WHERE partida_id = :id");
        $stmt2->execute([':id' => $partidaId]);
        $jugadoresActuales = (int) $stmt2->fetch(PDO::FETCH_ASSOC)['total'];

        // Cambiamos el estado cuando se completa la cantidad de jugadores
        if ($jugadoresActuales >= $jugadoresNecesarios && $estadoActual === 'pendiente') {
            $update = $this->db->prepare("UPDATE partidas SET estado = 'en_curso' WHERE id = :id");
            $update->execute([':id' => $partidaId]);
        }
    }

    /* ======================================================
        OBTENER PARTIDA POR ID
       ====================================================== */
    public function obtenerPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM partidas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $partida = $stmt->fetch(PDO::FETCH_ASSOC);

        //  Devolvemos los datos de la partida o null si no existe
        return $partida ?: null;
    }

    /* ======================================================
        OBTENER JUGADORES DE UNA PARTIDA
       ====================================================== */
    public function obtenerJugadores(int $partidaId): array
    {
        $stmt = $this->db->prepare("SELECT usuario FROM jugadores_partida WHERE partida_id = :id");
        $stmt->execute([':id' => $partidaId]);
        $jugadores = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Si no hay jugadores, devolvemos un array vacío
        return $jugadores ?: [];
    }

    public function colocarDinosaurio($jugador, $dinoNombre, $zona, $idPartida)
    {
        $query = "
        INSERT INTO movimientos (id_partida, jugador, color, zona, turno, puntos, fecha)
        VALUES (:id_partida, :jugador, :color, :zona, 0, 0, NOW())
    ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_partida' => $idPartida,
            ':jugador' => $jugador,
            ':color' => $dinoNombre, // acá guardamos el dinosaurio en la columna "color"
            ':zona' => $zona
        ]);
    }


    // Registrar el número del dado que sacó un jugador
    public function registrarDado(int $partidaId, string $usuario, int $numero)
    {
        $stmt = $this->db->prepare("
        UPDATE jugadores_partida
        SET dado_inicial = :numero
        WHERE partida_id = :id AND usuario = :usuario
    ");
        $stmt->execute([
            ':numero' => $numero,
            ':id' => $partidaId,
            ':usuario' => $usuario
        ]);
    }

    // Obtener resultados de los dados de todos los jugadores
    public function obtenerDados(int $partidaId): array
    {
        $stmt = $this->db->prepare("
        SELECT usuario, dado_inicial
        FROM jugadores_partida
        WHERE partida_id = :id
    ");
        $stmt->execute([':id' => $partidaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tira el dado de Draftosaurus (solo jugador en turno)
    public function tirarDadoZona($idPartida, $jugador)
    {
        $conexion = Database::getInstancia()->getConexion();

        // Posibles resultados del dado de zona
        $caras = ["EL_BOSQUE", "LLANURA", "BAÑOS", "CAFETERIA", "RECINTO_VACIO", "CUIDADO_T_REX"];
        $resultado = $caras[array_rand($caras)];

        try {
            $conexion->beginTransaction();

            // Guardar el resultado y el turno actual
            $stmt = $conexion->prepare("
            UPDATE partidas
            SET dado_zona = :resultado, turno_actual = :jugador
            WHERE id = :id
        ");
            $stmt->execute([
                ':resultado' => $resultado,
                ':jugador' => $jugador,
                ':id' => $idPartida
            ]);

            // Resetear colocaciones de todos los jugadores
            $conexion->prepare("
            UPDATE jugadores_partida
            SET coloco_en_ronda = 0
            WHERE partida_id = :id
        ")->execute([':id' => $idPartida]);

            $conexion->commit();
            return $resultado;
        } catch (Exception $e) {
            $conexion->rollBack();
            error_log("Error tirarDadoZona: " . $e->getMessage());
            return false;
        }
    }

    // Obtener el estado actual del dado
    public function obtenerDadoZona(int $idPartida): ?string
    {
        $stmt = $this->db->prepare("SELECT dado_zona FROM partidas WHERE id = :id");
        $stmt->execute([':id' => $idPartida]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['dado_zona'] : null;
    }
    // Guardar quién tiene el turno actual
    public function setTurnoActual(int $idPartida, string $usuario)
    {
        $stmt = $this->db->prepare("UPDATE partidas SET turno_actual = :usuario WHERE id = :id");
        $stmt->execute([':usuario' => $usuario, ':id' => $idPartida]);
    }

    // Obtener el turno actual
    public function getTurnoActual(int $idPartida): ?string
    {
        $stmt = $this->db->prepare("SELECT turno_actual FROM partidas WHERE id = :id");
        $stmt->execute([':id' => $idPartida]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['turno_actual'] : null;
    }

    // Pasar al siguiente jugador (cíclico) y limpiar el dado de zona
    public function avanzarTurno(int $partidaId): ?string
    {
        $jugadores = $this->obtenerJugadores($partidaId);
        if (empty($jugadores))
            return null;

        // Obtener el turno actual
        $stmt = $this->db->prepare("SELECT turno_actual FROM partidas WHERE id = :id");
        $stmt->execute([':id' => $partidaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $actual = $row ? $row['turno_actual'] : null;

        // Calcular siguiente jugador
        $idx = array_search($actual, $jugadores, true);
        if ($idx === false)
            $idx = -1;
        $next = $jugadores[($idx + 1) % count($jugadores)];

        // Guardar nuevo turno y limpiar dado
        $stmt2 = $this->db->prepare(
            "UPDATE partidas 
         SET turno_actual = :next, dado_zona = NULL 
         WHERE id = :id"
        );
        $stmt2->execute([':next' => $next, ':id' => $partidaId]);

        return $next;
    }
    // Limpiar dado de zona (por las dudas)
    public function limpiarDadoZona(int $partidaId): void
    {
        $stmt = $this->db->prepare("UPDATE partidas SET dado_zona = NULL WHERE id = :id");
        $stmt->execute([':id' => $partidaId]);
    }

    // Marcar que un jugador ya colocó su ficha en la ronda actual
    public function marcarColocacion(int $partidaId, string $usuario): void
    {
        $stmt = $this->db->prepare("
        UPDATE jugadores_partida
        SET coloco_en_ronda = 1
        WHERE partida_id = :id AND usuario = :usuario
    ");
        $stmt->execute([
            ':id' => $partidaId,
            ':usuario' => $usuario
        ]);
    }

    // Verificar si todos los jugadores ya colocaron su ficha
    public function todosColocaron(int $partidaId): bool
    {
        $stmt = $this->db->prepare("
        SELECT COUNT(*) AS faltan
        FROM jugadores_partida
        WHERE partida_id = :id AND coloco_en_ronda = 0
    ");
        $stmt->execute([':id' => $partidaId]);
        $faltan = (int) $stmt->fetch(PDO::FETCH_ASSOC)['faltan'];

        return $faltan === 0; // true si nadie falta
    }

    // Reiniciar el flag para todos los jugadores (nueva ronda)
    public function reiniciarColocaciones(int $partidaId): void
    {
        $stmt = $this->db->prepare("
        UPDATE jugadores_partida
        SET coloco_en_ronda = 0
        WHERE partida_id = :id
    ");
        $stmt->execute([':id' => $partidaId]);
    }


}
