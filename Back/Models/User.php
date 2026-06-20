<?php

class User{
    //Atributos conexion de datos
    private $conn;
    private $table_name= "User";

    //Propiedades Student
    public $id_user;
    public $email_user;
    public $password_user;
    public $id_rol;

    //COnstructor de db
    public function __construct($db){
        $this->conn =$db;
    }

    //Metodo para insertar alumno en la base de datos
    public function creat_User(){

        //Query para insert
        $query= "INSERT INTO " . $this->table_name . "
            (email_user, password, id_rol)
            VALUES
            (:email_user, :password, :id_rol)";

        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($this->password_user, PASSWORD_BCRYPT);

        //Se vincula
        $stmt->bindParam(":email_user",$this->email_user);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id_rol",$this->id_rol);
        
        //Ejecuta la consulta
        if($stmt->execute()){
            //Obtenemos el id generado automaticamente 
            $this->id_user = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function get_By_id($id_to_find){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_user = :id_user LIMIT 0,1 ";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_user",$id_to_find);

        $stmt->execute();

        return $stmt;
    }
    
    public function get_By_Email($email_to_find){
        $query = "SELECT * FROM " . $this->table_name . " WHERE email_user = :email_user LIMIT 0,1 ";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email_user",$email_to_find);

        $stmt->execute();

        return $stmt;
    }

    public function get_All(){
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    public function get_Id_By_Email($email) {
    $query = "SELECT id_user FROM " . $this->table_name . " WHERE email_user = :email LIMIT 1";
    $stmt = $this->conn->prepare($query);
    
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    return $stmt; 
}
}

?>