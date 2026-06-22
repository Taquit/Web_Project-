<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once '../Config/config.php';
require_once '../Models/User.php';
require_once '../Models/Student.php';
require_once '../Models/Allocation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => true, "mensaje" => "Método no permitido."]);
    exit();
}



$boleta     = trim($_POST['no_boleta']   ?? '');
$nombre     = trim($_POST['name']        ?? '');
$apPaterno  = trim($_POST['last_name_P'] ?? '');
$apMaterno  = trim($_POST['last_name_M'] ?? '');
$email      = trim($_POST['email']       ?? '');
$curp       = trim($_POST['curp']        ?? '');
$genero     = trim($_POST['gender']      ?? '');
$nacimiento = trim($_POST['birth_date']  ?? '');
$estado     = trim($_POST['estado']      ?? '');
$escuela    = trim($_POST['escuela']     ?? '');
$otraEscuela = trim($_POST['otra_escuela'] ?? '');
$lab        = trim($_POST['lab']         ?? '');
$horario    = trim($_POST['horario']     ?? '');
$promedio   = trim($_POST['promedio']    ?? '');
$telefono   = trim($_POST['telefono']    ?? '');

if (empty($boleta) || empty($email) || empty($curp)) {
    http_response_code(400);
    echo json_encode(["error" => true, "mensaje" => "Faltan datos obligatorios (Boleta, Correo, CURP)."]);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $student = new Student($db);
    $allo = new Allocation($db);

    // Verificar existencia
    $stmt1 = $user->get_By_Email($email);
    if ($stmt1->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(["error" => true, "mensaje" => "El correo ya está registrado."]);
        exit();
    }

    $stmt2 = $student->get_Student($boleta);
    if ($stmt2->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(["error" => true, "mensaje" => "La boleta ya está registrada."]);
        exit();
    }

    $stmt3 = $student->get_Student_By_CURP($curp);
    if ($stmt3->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(["error" => true, "mensaje" => "La CURP ya está registrada."]);
        exit();
    }

    // Resolver ID de Estado (el frontend ya envía el id_state numérico)
    $id_state = is_numeric($estado) ? intval($estado) : 7;

    // Resolver ID de Escuela (el frontend ya envía el id_school numérico)
    $id_school = is_numeric($escuela) ? intval($escuela) : 22;
    $other_school = ($id_school == 22 && !empty($otraEscuela)) ? $otraEscuela : (($id_school == 22) ? 'N/A' : 'N/A');

    // Iniciar transacción para evitar datos huérfanos
    $db->beginTransaction();

    // 1. Crear Usuario
    $user->email_user = $email;
    $user->password_user = $curp; // Contraseña por defecto es la CURP
    $user->id_rol = 1;

    if (!$user->creat_User()) {
        throw new Exception("Error al crear la cuenta de usuario.");
    }

    // 2. Crear Estudiante
    $student->no_boleta = $boleta;
    $student->id_user = $user->id_user; // Obtenido con lastInsertId() en creat_User()
    $student->name = $nombre;
    $student->last_name_P = $apPaterno;
    $student->last_name_M = $apMaterno;
    $student->birth_date = $nacimiento;
    $student->gender = $genero;
    $student->id_state_origin = $id_state;
    $student->id_school = $id_school;
    $student->other_school_name = $other_school;
    $student->curp = $curp;
    $student->avarage = $promedio;  // ESTA LÍNEA FALTABA
    $student->num_phone = ''; 
    $student->num_phone = $telefono;

    if (!$student->creat_Student()) {
        throw new Exception("Error al guardar la información del estudiante.");
    }

    // 3. Crear Asignación (Solo si eligió Lab y Horario)
    if (!empty($lab) && !empty($horario)) {
        // Resolver ID de Laboratorio (el frontend envía el nombre del lab)
        $stmtLab = $db->prepare("SELECT id_lab FROM lab WHERE name = :lab LIMIT 1");
        $stmtLab->bindParam(':lab', $lab);
        $stmtLab->execute();
        $rowLab = $stmtLab->fetch(PDO::FETCH_ASSOC);
        $id_lab = $rowLab ? $rowLab['id_lab'] : null;

        // Resolver ID de Horario (el frontend ya envía el id_schedule numérico)
        $id_horario = is_numeric($horario) ? intval($horario) : null;

        if ($id_lab && $id_horario) {
            // Verificar cupo antes de asignar
            $stmtCount = $db->prepare("SELECT COUNT(*) as ocupados FROM allocation WHERE id_lab = :lab AND id_schedule = :horario");
            $stmtCount->bindParam(':lab', $id_lab);
            $stmtCount->bindParam(':horario', $id_horario);
            $stmtCount->execute();
            $rowCount = $stmtCount->fetch(PDO::FETCH_ASSOC);

            if ($rowCount && $rowCount['ocupados'] >= 30) {
                throw new Exception("El laboratorio seleccionado ya no tiene cupo en este horario (Límite de 30 lugares alcanzado).");
            }

            $allo->no_boleta = $boleta;
            $allo->id_lab = $id_lab;
            $allo->id_schedule = $id_horario;
            
            if (!$allo->create_Allocation()) {
                throw new Exception("Error al guardar la asignación del laboratorio.");
            }
        }
    }

    // Confirmar cambios
    $db->commit();
    echo json_encode(["error" => false, "mensaje" => "Alumno registrado exitosamente."]);

} catch (Exception $e) {
    // Revertir si hubo error
    if (isset($db)) {
        $db->rollBack();
    }
    http_response_code(500);
    error_log("CreateStudent Error - lab: '$lab', horario: '$horario', escuela: '$escuela', estado: '$estado'");
    echo json_encode(["error" => true, "mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>
