<?php
require_once __DIR__ . '/../core/Database.php';

class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstancia()->getConexion();
    }

    // Registrar nuevo usuario
    public function registrar(string $email, string $usuario, string $password): bool
    {
        // Validaciones básicas
        if (empty($email) || empty($usuario) || empty($password)) {
            throw new Exception("Campos incompletos.");
        }

        // Verificar si ya existe
        $check = $this->db->prepare("SELECT 1 FROM usuarios WHERE email = :email OR usuario = :usuario");
        $check->execute([':email' => $email, ':usuario' => $usuario]);
        if ($check->fetch()) {
            throw new Exception("El usuario o email ya existen.");
        }

        // Crear hash seguro
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Insertar nuevo usuario
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (email, usuario, password, nivel)
            VALUES (:email, :usuario, :password, 1)
        ");
        return $stmt->execute([
            ':email' => $email,
            ':usuario' => $usuario,
            ':password' => $hash
        ]);
    }

    // Iniciar sesión
    public function login(string $usuario, string $password): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1");
        $stmt->execute([':usuario' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user; // retorna todos los datos del usuario
        }
        return false;
    }

    // Obtener nivel del usuario
    public function obtenerNivel(string $usuario): ?int
    {
        $stmt = $this->db->prepare("SELECT nivel FROM usuarios WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $usuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['nivel'] : null;
    }
}
