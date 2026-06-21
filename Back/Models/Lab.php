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

    public function get_location_num($id_sch){
        $query = "SELECT LAB.id_lab, count(*) as 'Free_Place', SCH.start_time from allocation as ALO inner join schedule SCH on SCH.id_schedule = ALO.id_schedule right join lab as LAB on LAB.id_lab = ALO.id_lab where SCH.id_schedule= :id_sch group by LAB.id_lab;";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_sch", $id_sch);
        $stmt->execute();

        return $stmt;
    }
}

?>