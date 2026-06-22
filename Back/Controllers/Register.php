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

    // Objetos
    $user = new User($db);
    $student = new Student($db);
    $lab = new Lab($db);
    $allo = new Allocation($db);
    $stat = new State($db);
    $schol = new School($db);
    
    // Usuario - CORRECCIÓN DE SEGURIDAD: Encriptar contraseña con BCRYPT
    $user->email_user = $_POST['correo'];
    $user->password_user = $_POST['contrasena'];
    $user->id_rol = 1;

    // Estudiante
    $student->name = $_POST['nombre'];
    $student->last_name_P = $_POST['apellidopaterno'];
    $student->last_name_M = $_POST['apellidomaterno'];
    $student->birth_date = $_POST['fecha-nacimiento'];
    $student->gender = $_POST['genero'];
    $student->no_boleta = $_POST['boleta'];
    $student->id_state_origin = $_POST['estado-origen'];
    $student->id_school = $_POST['escuela-procedencia'];
    $student->other_school_name = $_POST['nombre-escuela'];
    $student->curp = $_POST['CURP'];
    $student->avarage = $_POST['promedio'];
    $student->num_phone = $_POST['telefono'];
    
    // Verificamos que no este registrado su correo, curp o boleta
    $stmt1 = $user->get_id_By_Email($user->email_user);
    $stmt2 = $student->get_Student($student->no_boleta);
    $stmt3 = $student->get_Student_By_CURP($student->curp);

    $email = $stmt1->fetch(PDO::FETCH_ASSOC);
    $boleta = $stmt2->fetch(PDO::FETCH_ASSOC);
    $curp = $stmt3->fetch(PDO::FETCH_ASSOC);

    if($email){
        header("Location: ../../Front/Registro_page/registro.html?error=email");
        exit();
    } else if($boleta){
        header("Location: ../../Front/Registro_page/registro.html?error=boleta");
        exit();
    } else if($curp){
        header("Location: ../../Front/Registro_page/registro.html?error=curp");
        exit();
    }
    
    // Conseguimos un lugar para asignarselo
    $stmt4 = $lab->get_location_num(1);
    $stmt5 = $lab->get_location_num(2);

    $mat = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    $ves = $stmt5->fetchAll(PDO::FETCH_ASSOC);

    $allo->id_lab = 0; // id_laboratorio
    $allo->id_schedule = 0; // id horario

    if($mat){
        foreach($mat as $f){
            if($f['Free_Place'] < 30){
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
                if($f['Free_Place'] < 30){
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

    if(!$allo->id_lab && !$allo->id_schedule){
        header("Location: ../../Front/Registro_page/registro.html?error=full");
        exit();
    } else {
        $lab->no_boleta = $student->no_boleta;

        // Intentar crear el registro en cascada
        if($user->creat_User()){
            $student->id_user = $user->id_user;
            
            if($student->creat_Student()){
                $allo->no_boleta = $student->no_boleta;
                
                if($allo->create_Allocation()){
                    
                    $usuario = $user->get_By_id($user->id_user)->fetch(PDO::FETCH_ASSOC);
                    $estudiante = $student->get_Student($student->no_boleta)->fetch(PDO::FETCH_ASSOC);
                    $horario_exam = $allo->get_By_Boleta($student->no_boleta)->fetch(PDO::FETCH_ASSOC);

                    $_SESSION['Registro'] = [
                        "correo" => $usuario['email_user'],
                        "no_boleta" => $estudiante['no_boleta'],
                        "curp" => $estudiante['curp_user'],
                        "name" => $estudiante['name'],
                        "last_name_P" => $estudiante['last_name_P'],
                        "last_name_M" => $estudiante['last_name_M'],
                        "birth_date" => $estudiante['birth_date'],
                        "genero" => $estudiante['gender'],
                        "state" => $stat->get_By_Id($estudiante['id_state_origin'])->fetch(PDO::FETCH_ASSOC)['state_name'],
                        "school" => ($estudiante['id_school'] == 22) ? $estudiante['other_school_name'] : $schol->get_By_id($estudiante['id_school'])->fetch(PDO::FETCH_ASSOC)['school_name'],
                        "promedio" => $estudiante['avarage'],
                        "hora_ini" => $horario_exam['start_time'],
                        "hora_fin" => $horario_exam['end_time'],
                        "fecha" => $horario_exam['exam_date'],
                        "laboratorio" => $horario_exam['lab_name'],
                        "telefono" => $horario_exam['num_phone']
                    ];

                    $_SESSION['id_user'] = $user->id_user;
$_SESSION['id_rol']  = 1;

                    header("Location: ../../Front/Inicio_page/inicio.php");
                    exit();
                } else {
                    // CORRECCIÓN: Ajuste de códigos de error según JS de tu frontend
                    $user->delete_By_Id($user->id_user);
                    header("Location: ../../Front/Registro_page/registro.html?error=bd3");
                    exit();
                }
            } else {
                $user->delete_By_Id($user->id_user);
                header("Location: ../../Front/Registro_page/registro.html?error=bd2");
                exit();
            }
        } else {
            header("Location: ../../Front/Registro_page/registro.html?error=bd1");
            exit();
        }
    }
    
} else {
    header("Location: ../../Front/Home_page/index.php");
    exit();
}
?>