<?php
require_once __DIR__ . '/../core/Database.php';

class Partida {
    private PDO $db;

    public function __construct() {
        // Usamos siempre la misma instancia de conexión
        $this->db = Database::getInstancia()->getConexion();
    }

    /* ======================================================
       🦖 CREAR NUEVA PARTIDA
       ====================================================== */
    public function crear(string $creador, int $jugadores, string $tipo, string $nombre, ?string $password = null): int {
        $stmt = $this->db->prepare("
            INSERT INTO partidas (creador, jugadores, tipo, nombre, contraseña, estado)
            VALUES (:creador, :jugadores, :tipo, :nombre, :password, 'pendiente')
        ");
        $stmt->execute([
            ':creador'   => $creador,
            ':jugadores' => $jugadores,
            ':tipo'      => $tipo,
            ':nombre'    => $nombre,
            ':password'  => $password
        ]);

        // 🔹 Obtenemos el ID de la partida recién creada
        $id = (int)$this->db->lastInsertId();

        // 🔹 Si es una partida online, agregamos al creador como jugador
        if ($tipo === 'online') {
            $this->agregarJugador($id, $creador);
        }

        return $id;
    }

    /* ======================================================
       🚪 UNIRSE A UNA PARTIDA EXISTENTE
       ====================================================== */
    public function unirse(string $nombre, string $usuario, ?string $password = null): int {
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

        $partidaId = (int)$partida['id'];

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
       👥 AGREGAR JUGADOR A UNA PARTIDA
       ====================================================== */
    public function agregarJugador(int $partidaId, string $usuario): bool {
        $stmt = $this->db->prepare("
            INSERT INTO jugadores_partida (partida_id, usuario)
            VALUES (:partida_id, :usuario)
        ");
        return $stmt->execute([
            ':partida_id' => $partidaId,
            ':usuario'    => $usuario
        ]);
    }

    /* ======================================================
       🦕 CREAR JUGADORES LOCALES (modo seguimiento)
       ====================================================== */
    public function crearSeguimiento(int $partidaId, array $nombres): void {
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
       🔄 ACTUALIZAR ESTADO DE PARTIDA
       ====================================================== */
    public function actualizarEstado(int $partidaId): void {
        $stmt = $this->db->prepare("SELECT jugadores, estado FROM partidas WHERE id = :id");
        $stmt->execute([':id' => $partidaId]);
        $partida = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$partida) return;

        $jugadoresNecesarios = (int)$partida['jugadores'];
        $estadoActual = $partida['estado'];

        $stmt2 = $this->db->prepare("SELECT COUNT(*) AS total FROM jugadores_partida WHERE partida_id = :id");
        $stmt2->execute([':id' => $partidaId]);
        $jugadoresActuales = (int)$stmt2->fetch(PDO::FETCH_ASSOC)['total'];

        // Cambiamos el estado cuando se completa la cantidad de jugadores
        if ($jugadoresActuales >= $jugadoresNecesarios && $estadoActual === 'pendiente') {
            $update = $this->db->prepare("UPDATE partidas SET estado = 'en_curso' WHERE id = :id");
            $update->execute([':id' => $partidaId]);
        }
    }

    /* ======================================================
       🔍 OBTENER PARTIDA POR ID
       ====================================================== */
    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM partidas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $partida = $stmt->fetch(PDO::FETCH_ASSOC);

        // ✅ Devolvemos los datos de la partida o null si no existe
        return $partida ?: null;
    }

    /* ======================================================
       🧩 OBTENER JUGADORES DE UNA PARTIDA
       ====================================================== */
    public function obtenerJugadores(int $partidaId): array {
        $stmt = $this->db->prepare("SELECT usuario FROM jugadores_partida WHERE partida_id = :id");
        $stmt->execute([':id' => $partidaId]);
        $jugadores = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // ✅ Si no hay jugadores, devolvemos un array vacío
        return $jugadores ?: [];
    }

    public function colocarDinosaurio($jugador, $dinoNombre, $zona) {
    // Ejemplo simple de registro en BD
    $query = "INSERT INTO jugadas (jugador, dinosaurio, zona) VALUES (?, ?, ?)";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$jugador, $dinoNombre, $zona]);
}

}
