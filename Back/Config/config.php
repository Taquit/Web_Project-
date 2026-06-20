<?php

$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    die("Error: No se encontró el archivo .env");
}

// Parsear el archivo y guardar las variables en un arreglo
$env = parse_ini_file($envPath);

// 2. Asignar las variables
// 2. Definir CONSTANTES globales de PHP
define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASSWORD', $env['DB_PASSWORD']);

class Database{

    //Definimos credenciales
    private $host= DB_HOST;
    private $db_name= DB_NAME;
    private $user=DB_USER;
    private $password= DB_PASSWORD;
    public $conn;

    //Funcion que realiza la conexion
    public function getConnection(){
        $this->conn = null;
        try{
            //cadena de conexion
            $dsn ="mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            //PDO
            $this->conn = new PDO($dsn,$this->user,$this->password);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $exception){
            echo json_encode(array("error" => "Error de conexión a la base de datos: " . $exception->getMessage()));
            exit();
        }

        return $this->conn;
    }
}

?>
