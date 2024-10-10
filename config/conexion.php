<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Asegúrate de requerir el autoload de Composer

use Dotenv\Dotenv;

class Conexion
{
    private static $instance = null; // Instancia única
    private $conn;

    // Constructor privado para evitar instanciación externa
    private function __construct()
    {
        // Cargar variables de entorno desde el archivo .env
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
        $dotenv->load();

        $servidor = $_ENV['DB_HOST'];
        $usuario = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $base_datos = $_ENV['DB_NAME'];

        // Crear conexión
        $this->conn = new mysqli($servidor, $usuario, $password, $base_datos);

        // Verificar conexión
        if ($this->conn->connect_error) {
            die("Conexión fallida - ERROR de conexión: " . $this->conn->connect_error);
        }
    }

    // Método para obtener la conexión
    public static function conectar()
    {
        if (self::$instance === null) {
            self::$instance = new Conexion();
        }
        return self::$instance->conn; // Retornar la conexión
    }
}
