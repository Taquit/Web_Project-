<?php

require_once '../Config/config.php';
require_once '../Models/User.php';
require_once '../Models/Student.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Conexion a la DB
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $student = new Student($db);
    
    //Usuario
    $user->email_user = $_POST['correo'];
    $user->password_user = $_POST['contrasena'];

    //Estudiante
    $student->name = $_POST['nombre'];
    $student->last_name_P = $_POST['apellidopaterno'];
    $student->last_name_M = $_POST['apellidomaterno'];
    $student->birth_date = $_POST['fecha-nacimiento'];
    $student->gender = $_POST['genero'];
    $student->no_boleta = $_POST['numbol'];
    $student->id_state_origin = $_POST['estado-origen'];
    $student->id_school = $_POST['escuela-procedencia'];
    $student->other_school_name = $_POST['nombre-escuela'];
    $student->curp = $_POST['CURP'];

    echo "Datos recibidos: <br>"
    . "Email: " . $user->email_user . "<br>"
    . "Contraseña: " . $user->password_user . "<br>"
    . "Nombre: " . $student->name . "<br>"
    . "Apellido Paterno: " . $student->last_name_P . "<br>"
    . "Apellido Materno: " . $student->last_name_M . "<br>"
    . "Fecha de Nacimiento: " . $student->birth_date . "<br>"
    . "Género: " . $student->gender . "<br>"
    . "No. Boleta: " . $student->no_boleta . "<br>"
    . "ID Estado de Origen: " . $student->id_state_origin . "<br>"
    . "ID Escuela: " . $student->id_school . "<br>"
    . "Otro Nombre Escuela: " . $student->other_school_name;

    if($student->gender == "Hombre") $student->gender = 1;
    else $student->gender = 0;
    
    //Verificamos que no este registrado su correo, curp o boleta
    $stmt1 = $user->get_id_By_Email($user->email_user);
    $stmt2 = $student->get_Student($student->no_boleta);
    $stmt3 = $student->get_Student_By_CURP($student->curp);

    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
    $boleta = $stmt2->fetch(PDO::FETCH_ASSOC);
    $curp = $stmt3->fetch(PDO::FETCH_ASSOC);

    if($row){
        header("Location: ../../Front/Home_page/index.html");
    } else if($boleta){
        header("Location: ../../Front/Home_page/index.html");
    } else if($curp){
        header("Location: ../../Front/Home_page/index.html");
    }
    
    //Conseguimos un lugar para asignarselo
    
    echo "Todo bien jaja";
}
?>
