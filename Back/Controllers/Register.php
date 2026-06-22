<?php

require_once '../Config/config.php';
require_once '../Models/User.php';
require_once '../Models/Student.php';
require_once '../Models/Lab.php';
require_once '../Models/Allocation.php';
require_once '../Models/State.php';
require_once '../Models/School.php';
require_once '../Models/Schedule.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Conexion a la DB
    $database = new Database();
    $db = $database->getConnection();

    //Objetos
    $user = new User($db);
    $student = new Student($db);
    $lab = new lab($db);
    $allo = new Allocation($db);
    $stat = new State($db);
    $schol = new School($db);
    
    //Usuario
    $user->email_user = $_POST['correo'];
    $user->password_user = $_POST['contrasena'];
    $user->id_rol = 1;

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
    $student->avarage = $_POST['promedio'];
    $student->num_phone = $_POST['telefono'];
    
    /*
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
    
    */
    
    //Verificamos que no este registrado su correo, curp o boleta
    $stmt1 = $user->get_id_By_Email($user->email_user);
    $stmt2 = $student->get_Student($student->no_boleta);
    $stmt3 = $student->get_Student_By_CURP($student->curp);

    $email = $stmt1->fetch(PDO::FETCH_ASSOC);
    $boleta = $stmt2->fetch(PDO::FETCH_ASSOC);
    $curp = $stmt3->fetch(PDO::FETCH_ASSOC);

    if($email){
        //El correo ya existe
        //echo "<br>El correo ya existe";
        header("Location: ../../Front/Registro_page/registro.html?error=email");
        exit();
    } else if($boleta){
        //La boleta ya existe
        //echo "<br>La boleta ya existe";
        header("Location: ../../Front/Registro_page/registro.html?error=boleta");
        exit();
    } else if($curp){
        //La curp ya existe
        //echo "<br>La curp ya existe";
        header("Location: ../../Front/Registro_page/registro.html?error=curp");
        exit();
    }
    
    //Conseguimos un lugar para asignarselo
    $stmt4 = $lab->get_location_num(1);
    $stmt5 = $lab->get_location_num(2);

    $mat = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    $ves = $stmt5->fetchAll(PDO::FETCH_ASSOC);

    $allo->id_lab = 0;//id_labotorio
    $allo->id_schedule = 0;//id horario

    if($mat){
        foreach($mat as $f){
            if(!$f['Free_Place'] or $f['Free_Place'] < 30){
                $allo->id_lab = $f['id_lab'];
                $allo->id_schedule = 1;
                break;
            }
        }
    } else {
        $allo->id_lab = 1;
        $allo->id_schedule = 1;
    }

    if(!$allo->id_lab){
        if($ves){
            foreach($ves as $f){
                if(!$f['Free_Place'] or $f['Free_Place'] < 30){
                    $allo->id_lab = $f['id_lab'];
                    $allo->id_schedule = 2;
                    break;
                }
            }
        } else {
            $allo->id_lab = 1;
            $allo->id_schedule = 2;
        }
    }

    if(!$allo->id_lab and !$allo->id_schedule){
        //Ya no hay lugares
        //echo "<br>No hay lugares";
        header("Location: ../../Front/Registro_page/registro.html?error=full");
        exit();
    } else {
        //echo "<br>Todo bien jaja<br>";
        echo "Laboratorio: ".$allo->id_lab.", Horario: ".$allo->id_schedule;

        $lab->no_boleta = $student->no_boleta;

        if($user->creat_User()){
            $student->id_user = $user->id_user;
            if($student->creat_Student()){
                $allo->no_boleta = $student->no_boleta;
                if($allo->create_Allocation()){
                    
                    //echo "<br><span style='color:green; font-weight: bold;'>FELCIDDADES, NACIO HOMOSEXUAL, quiero decir, registro exitoso!!!</span>";
                    $usuario = $user->get_By_id($user->id_user)->fetch(PDO::FETCH_ASSOC);
                    $estudiante = $student->get_Student($student->no_boleta)->fetch(PDO::FETCH_ASSOC);
                    $horario_exam = $allo->get_By_Boleta($student->no_boleta)->fetch(PDO::FETCH_ASSOC);

                    $_SESSION['Registro']= [
                        "correo" => $usuario['email_user'],
                        "no_boleta" => $estudiante['no_boleta'],
                        "curp" => $estudiante['curp_user'],
                        "name" => $estudiante['name'],
                        "last_name_P" => $estudiante['last_name_P'],
                        "last_name_M" => $estudiante['last_name_M'],
                        "birth_date" => $estudiante['birth_date'],
                        "genero" => $estudiante['gender'],
                        "state" => $stat->get_By_Id($estudiante['id_state_origin'])->fetch(PDO::FETCH_ASSOC)['state_name'],
                        "school" => ($estudiante['id_school'] == 22)?$estudiante['other_school_name']: $schol->get_By_id($estudiante['id_school'])->fetch(PDO::FETCH_ASSOC)['school_name'],
                        "promedio" => $estudiante['avarage'],
                        "hora_ini" => $horario_exam['start_time'],
                        "hora_fin" => $horario_exam['end_time'],
                        "fecha" => $horario_exam['exam_date'],
                        "laboratorio" => $horario_exam['lab_name'],
                        "telefono" => $horario_exam['num_phone']
                    ];

                    header("Location: ../../Front/Inicio_page/inicio.html");
                    exit();
                } else {
                    //echo "<br>No se pudo registrar su Asignacion de examen, que chango";
                    $user->delete_By_Id($user->id_user);
                    header("Location: ../../Front/Registro_page/registro.html?error=bd1");
                    exit();
                }
            } else {
                //echo "<br>No se pudo registrar su estudiante, que chango";
                $user->delete_By_Id($user->id_user);
                header("Location: ../../Front/Registro_page/registro.html?error=bd2");
                exit();
            }
        } else {
            //echo "<br>No se pudo registrar su usuario, que chango";
            header("Location: ../../Front/Registro_page/registro.html?error=bd3");
            exit();
        }
    }
    
} else {
    header("Location: ../../Front/Home_page/index.html");
    exit();
}
?>
