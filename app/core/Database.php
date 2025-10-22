<?php
/**
 * conexion.php
 * Conexión segura a MySQL usando PDO y Singleton.
 * Compatible con tu base Draftosaurus en phpMyAdmin (XAMPP).
 */

define('SERVERNAME', 'localhost');   // igual que tu $host
define('USERNAME', 'root');          // igual que tu $user
define('PASSWORD', '');              // igual que tu $pass
define('DBNAME', 'Draftosaurus');    // igual que tu $dbname
define('CHARSET', 'utf8mb4');        // versión mejorada de utf8

class Database
{
    // Guarda la única instancia de la clase (Singleton)
    private static ?Database $instancia = null;

    // Objeto PDO (conexión a MySQL)
    private PDO $conexion;

    // Constructor privado → impide crear objetos con new Database()
    private function __construct()
    {
        $host = SERVERNAME;
        $db   = DBNAME;
        $user = USERNAME;
        $pass = PASSWORD;
        $charset = CHARSET;

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        try {
            $this->conexion = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lanza errores controlables
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // devuelve arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false                   // evita SQL Injection
            ]);
        } catch (PDOException $e) {
            // No mostrar errores técnicos al usuario (solo registrar)
            error_log("[DB ERROR] " . $e->getMessage(), 3, __DIR__ . "/db_errors.log");
            die("❌ Error de conexión a la base de datos. Intentalo más tarde.");
        }
    }

    // Método público para obtener la instancia
    public static function getInstancia(): Database
    {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }
        return self::$instancia;
    }

    // Devuelve la conexión PDO
    public function getConexion(): PDO
    {
        return $this->conexion;
    }
}
?>
