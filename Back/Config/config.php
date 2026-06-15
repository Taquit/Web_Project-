<?php

class Database{

    //Definimos credenciales
    private $host= getenv('DB_HOST');
    private $db_name= getenv('DB_NAME');
    private $user=getenv('DB_USER');
    private $password= getenv('DB_PASSWORD');
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
