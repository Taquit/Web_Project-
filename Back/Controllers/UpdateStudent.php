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
$boleta     = trim($data['no_boleta']  ?? '');
$nombre     = trim($data['name']       ?? '');
$apPaterno  = trim($data['last_name_P']?? '');
$apMaterno  = trim($data['last_name_M']?? '');
$email      = trim($data['email']      ?? '');
$curp       = trim($data['curp']       ?? '');
$genero     = trim($data['gender']     ?? '');
$nacimiento = trim($data['birth_date'] ?? '');
$estado     = trim($data['estado']     ?? '');
$escuela    = trim($data['escuela']    ?? '');
$lab        = trim($data['lab']        ?? '');
$horario    = trim($data['horario']    ?? '');
$promedio   = trim($data['promedio']   ?? '');

if (empty($old_boleta)) {
    $old_boleta = $boleta;
}

if (empty($boleta)) {
    http_response_code(400);
    echo json_encode(["error" => true, "mensaje" => "La boleta es obligatoria."]);
    exit();
}

try {
    $db   = new Database();
    $conn = $db->getConnection();

    $sqlStudent = "UPDATE Student SET
                    no_boleta         = :no_boleta,
                    name              = :name,
                    last_name_P       = :last_name_P,
                    last_name_M       = :last_name_M,
                    birth_date        = :birth_date,
                    gender            = :gender,
                    curp_user         = :curp,
                    avarage           = :promedio,
                    id_state_origin   = COALESCE((SELECT id_state FROM State WHERE state_name = :estado LIMIT 1), id_state_origin),
                    id_school         = COALESCE(
                        (SELECT id_school FROM School WHERE school_name = :escuela LIMIT 1),
                        IF(:escuela REGEXP '^[0-9]+$', :escuela, 22)
                    ),
                    other_school_name = IF(
                        (SELECT id_school FROM School WHERE school_name = :escuela LIMIT 1) IS NULL AND NOT :escuela REGEXP '^[0-9]+$',
                        :escuela,
                        NULL
                    )
                   WHERE no_boleta = :old_boleta";

    $stmtS = $conn->prepare($sqlStudent);
    $stmtS->bindParam(':no_boleta',   $boleta);
    $stmtS->bindParam(':name',        $nombre);
    $stmtS->bindParam(':last_name_P', $apPaterno);
    $stmtS->bindParam(':last_name_M', $apMaterno);
    $stmtS->bindParam(':birth_date',  $nacimiento);
    $stmtS->bindParam(':gender',      $genero);
    $stmtS->bindParam(':curp',        $curp);
    $stmtS->bindParam(':promedio',    $promedio);
    $stmtS->bindParam(':estado',      $estado);
    $stmtS->bindParam(':escuela',     $escuela);
    $stmtS->bindParam(':old_boleta',  $old_boleta);
    $stmtS->execute();

    if (!empty($email)) {
        $sqlUser = "UPDATE User u
                    INNER JOIN Student s ON s.id_user = u.id_user
                    SET u.email_user = :email
                    WHERE s.no_boleta = :no_boleta";

        $stmtU = $conn->prepare($sqlUser);
        $stmtU->bindParam(':email',     $email);
        $stmtU->bindParam(':no_boleta', $boleta);
        $stmtU->execute();
    }

    if (!empty($lab) && !empty($horario)) {
        $horarioLike  = '%' . str_replace('–', '%', $horario) . '%';
        $horarioLike2 = '%' . str_replace('-', '%', $horario) . '%';

        // Primero obtenemos los IDs reales a los que se intenta asignar
        $stmtLab = $conn->prepare("SELECT id_lab FROM Lab WHERE name = :lab LIMIT 1");
        $stmtLab->bindParam(':lab', $lab);
        $stmtLab->execute();
        $id_lab_nuevo = $stmtLab->fetchColumn();

        $stmtHor = $conn->prepare("SELECT id_schedule FROM Schedule WHERE CONCAT(start_time, '-', end_time) LIKE :h1 OR CONCAT(start_time, '-', end_time) LIKE :h2 LIMIT 1");
        $stmtHor->bindParam(':h1', $horarioLike);
        $stmtHor->bindParam(':h2', $horarioLike2);
        $stmtHor->execute();
        $id_horario_nuevo = $stmtHor->fetchColumn();

        if ($id_lab_nuevo && $id_horario_nuevo) {
            // Verificamos si el alumno ya tiene asignado este mismo laboratorio y horario para no contarlo
            $stmtCurrent = $conn->prepare("SELECT id_lab, id_schedule FROM Allocation WHERE no_boleta = :no_boleta LIMIT 1");
            $stmtCurrent->bindParam(':no_boleta', $boleta);
            $stmtCurrent->execute();
            $currentAlloc = $stmtCurrent->fetch(PDO::FETCH_ASSOC);

            if (!$currentAlloc || $currentAlloc['id_lab'] != $id_lab_nuevo || $currentAlloc['id_schedule'] != $id_horario_nuevo) {
                // Solo validamos cupo si está intentando cambiarse a uno distinto
                $stmtCount = $conn->prepare("SELECT COUNT(*) FROM Allocation WHERE id_lab = :lab AND id_schedule = :horario");
                $stmtCount->bindParam(':lab', $id_lab_nuevo);
                $stmtCount->bindParam(':horario', $id_horario_nuevo);
                $stmtCount->execute();
                $ocupados = $stmtCount->fetchColumn();

                if ($ocupados >= 30) {
                    throw new Exception("El laboratorio seleccionado ya no tiene cupo en este horario (Límite de 30 lugares alcanzado).");
                }
            }
        }

        $sqlAlloc = "UPDATE Allocation SET
                     id_lab      = COALESCE(:lab_id, id_lab),
                     id_schedule = COALESCE(:horario_id, id_schedule)
                     WHERE no_boleta = :no_boleta";

        $stmtA = $conn->prepare($sqlAlloc);
        $stmtA->bindParam(':lab_id',       $id_lab_nuevo);
        $stmtA->bindParam(':horario_id',   $id_horario_nuevo);
        $stmtA->bindParam(':no_boleta',    $boleta);
        $stmtA->execute();
    }

    echo json_encode(["error" => false, "mensaje" => "Registro actualizado correctamente."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => true, "mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>