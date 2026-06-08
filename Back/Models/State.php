<?php

class State{
    //Conexion
    private $conn;
    private $table_name="State";

    //Propiedades de tabla
    public $id_state;
    public $state_name;

    public function __construct($db){
        $this->conn =$db;
    }

    public function get_All_States(){

        //Query
        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function get_By_Id($id_to_find){

        $query = "SELECT * FROM " . $this->table_name . " WHERE id_state = :id_state LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_state",$id_to_find);
        $stmt-> execute();
        return $stmt;
    }
    
}

?>