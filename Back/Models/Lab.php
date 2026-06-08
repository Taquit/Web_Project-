<?php

class Lab{
    //Atributos de conexion 
    private $conn;
    private $table_name= "Lab";

    //Propiedades Lab
    public $id_lab;
    public $name;
    public $capacity;

    //Constructor
    public function __construct($db){
        $this->conn=$db;
    }

    //Metodos
    public function get_All_Labs(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function get_By_Id($id_to_find){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_lab = :id_lab LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_lab", $id_to_find);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id_lab = $row['id_lab'];
            $this->name = $row['name'];
            $this->capacity = $row['capacity'];
            return true;
        }
        return false;
    }
}

?>