<?php

class Student{
    //Atributos conexion de datos
    private $conn;
    private $table_name= "Student";

    //Propiedades Student
    public $no_boleta;
    public $name;
    public $id_user;
    public $last_name_P;
    public $last_name_M;
    public $birth_date;
    public $gender;
    public $id_state_origin;
    public $id_school;
    public $other_school_name;
    public $curp;

    //COnstructor de db
    public function __construct($db){
        $this->conn =$db;
    }

    //Metodo para insertar alumno en la base de datos
    public function creat_Student(){

        //Query para insert
        if($this->id_school==22){
            $query= "INSERT INTO " . $this->table_name . "
            (no_boleta, id_user, name, last_name_P, last_name_M, birth_date, gender, id_state_origin, id_school, other_school_name, curp_user)
            VALUES
            (:no_boleta, :id_user, :name, :last_name_P, :last_name_M, :birth_date, :gender, :id_state_origin, :id_school, :other_school_name, :curp)";
        } else {
            $query= "INSERT INTO " . $this->table_name . "
            (no_boleta, id_user, name, last_name_P, last_name_M, birth_date, gender, id_state_origin, id_school, curp_user)
            VALUES
            (:no_boleta, :id_user, :name, :last_name_P, :last_name_M, :birth_date, :gender, :id_state_origin, :id_school, :curp)";
        } 

        $stmt = $this->conn->prepare($query);

        //Se vincula
        $stmt->bindParam(":no_boleta",        $this->no_boleta);
        $stmt->bindParam(":id_user",          $this->id_user);
        $stmt->bindParam(":name",             $this->name);
        $stmt->bindParam(":last_name_P",      $this->last_name_P);
        $stmt->bindParam(":last_name_M",      $this->last_name_M);
        $stmt->bindParam(":birth_date",       $this->birth_date);
        $stmt->bindParam(":gender",           $this->gender);
        $stmt->bindParam(":id_state_origin",  $this->id_state_origin);
        $stmt->bindParam(":id_school",        $this->id_school);
        if($this->id_school==22){
            $stmt->bindParam(":other_school_name",$this->other_school_name);
        } 
        $stmt->bindParam(":curp",             $this->curp);

        //Ejecuta la consulta
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    public function get_Student($boleta_to_find){

        //Consulta SQL
        $query = "SELECT no_boleta, id_user, name, last_name_P, last_name_M, birth_date, gender, id_state_origin, id_school, other_school_name
                FROM " . $this->table_name . "
                WHERE no_boleta = :no_boleta LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":no_boleta",$boleta_to_find);

        $stmt->execute();

        return $stmt;
    }

    public function get_Student_By_CURP($curp){

        //Consulta SQL
        $query = "SELECT no_boleta FROM " . $this->table_name . "
                WHERE curp_user = :curp LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":curp",$curp);

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

    public function get_All_Info_Students(){
       $sql = "
            SELECT 
                s.no_boleta, 
                s.name, 
                s.last_name_P, 
                s.last_name_M, 
                s.curp_user AS curp,
                s.birth_date,
                s.gender,
                s.other_school_name AS other_school,
                u.email_user AS email,
                st.state_name AS estado,
                sc.school_name AS escuela_catalogo,
                l.name AS laboratorio,
                CONCAT(sch.exam_date, ' ', sch.start_time, ' a ', sch.end_time) AS horario
            FROM 
                student s
            INNER JOIN 
                user u ON s.id_user = u.id_user
            LEFT JOIN 
                state st ON s.id_state_origin = st.id_state
            LEFT JOIN 
                school sc ON s.id_school = sc.id_school
            LEFT JOIN 
                allocation a ON s.no_boleta = a.no_boleta
            LEFT JOIN 
                lab l ON a.id_lab = l.id_lab
            LEFT JOIN 
                schedule sch ON a.id_schedule = sch.id_schedule
            ORDER BY 
                s.last_name_P ASC, s.last_name_M ASC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>