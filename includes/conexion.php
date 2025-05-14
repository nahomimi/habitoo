<?php
class Conexion
{
    private $host = 'localhost';
    private $dbname = 'habitoo';
    private $username = 'root';
    private $password = '';

    public function conectar()
    {
        try {

            $pdo = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Mejor manejo de errores.
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch como arreglo asociativo.
                PDO::ATTR_EMULATE_PREPARES => false, // Deshabilitar emulación de consultas preparadas.
            ];
            // Creación de la instancia PDO
            return new PDO($pdo, $this->username, $this->password, $options);

        } catch (\PDOException $e) { // Específico para capturar errores de PDO
            echo "Error de conexión: " . $e->getMessage(); // Muestra el mensaje de error si hay alguno.
            exit(); // Detiene el script.
        }
    }
}
