<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: PUT, POST, OPTIONS');

require_once '../Models/Student.php';
require_once '../Config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["error" => true, "mensaje" => "Método no permitido."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
} else {
    $data = $_POST;
}

$old_boleta = trim($data['old_boleta'] ?? '');
$boleta     = trim($data['no_boleta']    ?? '');
$nombre     = trim($data['name']         ?? '');
$apPaterno  = trim($data['last_name_P']  ?? '');
$apMaterno  = trim($data['last_name_M']  ?? '');
$email      = trim($data['email']        ?? '');
$curp       = trim($data['curp']         ?? '');
$genero     = trim($data['gender']       ?? '');
$nacimiento = trim($data['birth_date']   ?? '');
$estado     = trim($data['estado']       ?? '');
$escuela    = trim($data['escuela']      ?? '');
$lab        = trim($data['lab']          ?? '');
$horario    = trim($data['horario']      ?? '');

if (empty($old_boleta)) {
    $old_boleta = $boleta; // Fallback por si acaso
}

if (empty($boleta)) {
    http_response_code(400);
    echo json_encode(["error" => true, "mensaje" => "La boleta es obligatoria."]);
    exit();
}

try {
    $db   = new Database();
    $conn = $db->getConnection();

    // Actualizar tabla Student
    // Utilizamos subconsultas para mapear los textos a sus respectivos IDs
    $sqlStudent = "UPDATE student SET
                    no_boleta     = :no_boleta,
                    name          = :name,
                    last_name_P   = :last_name_P,
                    last_name_M   = :last_name_M,
                    birth_date    = :birth_date,
                    gender        = :gender,
                    curp_user     = :curp,
                    id_state_origin = COALESCE((SELECT id_state FROM state WHERE state_name = :estado LIMIT 1), id_state_origin),
                    id_school       = COALESCE((SELECT id_school FROM school WHERE school_name = :escuela LIMIT 1), 22),
                    other_school_name = IF((SELECT id_school FROM school WHERE school_name = :escuela LIMIT 1) IS NULL, :escuela, NULL)
                   WHERE no_boleta = :old_boleta";

    $stmtS = $conn->prepare($sqlStudent);
    $stmtS->bindParam(':no_boleta',   $boleta);
    $stmtS->bindParam(':name',        $nombre);
    $stmtS->bindParam(':last_name_P', $apPaterno);
    $stmtS->bindParam(':last_name_M', $apMaterno);
    $stmtS->bindParam(':birth_date',  $nacimiento);
    $stmtS->bindParam(':gender',      $genero);
    $stmtS->bindParam(':curp',        $curp);
    $stmtS->bindParam(':estado',      $estado);
    $stmtS->bindParam(':escuela',     $escuela);
    $stmtS->bindParam(':old_boleta',  $old_boleta);
    $stmtS->execute();

    // Actualizar email en tabla User (JOIN a través de Student)
    if (!empty($email)) {
        $sqlUser = "UPDATE user u
                    INNER JOIN student s ON s.id_user = u.id_user
                    SET u.email_user = :email
                    WHERE s.no_boleta = :no_boleta";

        $stmtU = $conn->prepare($sqlUser);
        $stmtU->bindParam(':email',     $email);
        $stmtU->bindParam(':no_boleta', $boleta);
        $stmtU->execute();
    }

    // Actualizar asignación (Allocation) si existe
    if (!empty($lab) && !empty($horario)) {
        // Encontrar el ID del horario basándonos en el string "HH:MM–HH:MM" 
        // El formato en la BD es "exam_date start_time a end_time", o puede ser solo horas. 
        // Vamos a usar LIKE para que coincida
        $horarioLike = '%' . str_replace('–', '%', $horario) . '%';
        $horarioLike2 = '%' . str_replace('-', '%', $horario) . '%';
        
        $sqlAlloc = "UPDATE allocation SET
                     id_lab = COALESCE((SELECT id_lab FROM lab WHERE name = :lab LIMIT 1), id_lab),
                     id_schedule = COALESCE((SELECT id_schedule FROM schedule WHERE CONCAT(start_time, '-', end_time) LIKE :h1 OR CONCAT(start_time, '-', end_time) LIKE :h2 LIMIT 1), id_schedule)
                     WHERE no_boleta = :no_boleta";
        $stmtA = $conn->prepare($sqlAlloc);
        $stmtA->bindParam(':lab', $lab);
        $stmtA->bindParam(':h1', $horarioLike);
        $stmtA->bindParam(':h2', $horarioLike2);
        $stmtA->bindParam(':no_boleta', $boleta);
        $stmtA->execute();
        
        // Si la fila no existía (no había asignación), podríamos intentar un INSERT, pero 
        // la función de editar asume que ya existe, o no modifica si no existe.
    }

    echo json_encode(["error" => false, "mensaje" => "Registro actualizado correctamente."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => true, "mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>
