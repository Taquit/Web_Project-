<?php

class Student{
    //Atributos conexion de datos
    private $conn;
    private $table_name= "Student";

    //Propiedades Student
    public $no_boleta;
    public $name_student;
    public $id_user;
    public $last_name_P;
    public $last_name_M;
    public $birth_date;
    public $id_gender;
    public $id_state_origin;
    public $id_school;
    public $curp;
    public $average;

    //COnstructor de db
    public function __construct($db){
        $this->conn =$db;
    }

    //Metodo para insertar alumno en la base de datos
    public function creat_Student(){

        //Query para insert
        $query= "INSERT INTO " . $this->table_name . "
            (no_boleta, id_user, name_student, last_name_P,last_name_M,birth_date,id_gender,id_state_origin,id_school,curp,average)
            VALUES
            (:no_boleta, :id_user, :name_student, :last_name_P,:last_name_M,:birth_date,:id_gender,:id_state_origin,:id_school,:curp,:average)";

        $stmt = $this->conn->prepare($query);

        //Se vincula
        $stmt->bindParam(":no_boleta",$this->no_boleta);
        $stmt->bindParam(":id_user",$this->id_user);
        $stmt->bindParam(":name_student", $this->name_student);
        $stmt->bindParam(":last_name_P",$this->last_name_P);
        $stmt->bindParam(":last_name_M",$this->last_name_M);
        $stmt->bindParam(":birth_date",$this->birth_date);
        $stmt->bindParam(":id_gender",$this->id_gender);
        $stmt->bindParam(":id_state_origin",$this->id_state_origin);
        $stmt->bindParam(":id_school",$this->id_school);
        $stmt->bindParam(":curp",$this->curp);
        $stmt->bindParam(":average",$this->average);

        //Ejecuta la consulta
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    public function get_Student($boleta_to_find){

        //Consulta SQL
        $query = "SELECT no_boleta, id_user, name_student, last_name_P,last_name_M,birth_date,id_gender,id_state_origin,id_school,curp,average
                FROM " . $this->table_name . "
                WHERE no_boleta = :no_boleta LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":no_boleta",$boleta_to_find);

        $stmt->execute();

        return $stmt;
    }
    
    public function get_All_Students(){
         
        //Consulta SQL
        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt -> execute();
        return $stmt;
    }
}

?>