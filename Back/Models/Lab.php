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

        return $stmt;
    }
}

?>