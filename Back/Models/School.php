<?php

class School{

    //Atributos conexion
    private $conn;
    private $table_name = "School";

    //Propiedades de School
    public $id_school;
    public $school_name;

    public function __construct($db){
        $this->conn = $db;
    }

    public function get_All_School(){
        $query = "SELECT * FROM ". $this->table_name ;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function get_By_id($id_to_find){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_school = :id_school LIMIT 0,1 ";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_school",$id_to_find);

        $stmt->execute();

        return $stmt;
    }
}

?>