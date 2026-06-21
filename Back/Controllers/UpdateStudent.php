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

// Acepta tanto form-data (POST) como JSON raw (PUT)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
} else {
    $data = $_POST;
}

$boleta     = trim($data['no_boleta']    ?? '');
$nombre     = trim($data['name']         ?? '');
$apPaterno  = trim($data['last_name_P']  ?? '');
$apMaterno  = trim($data['last_name_M']  ?? '');
$email      = trim($data['email']        ?? '');
$genero     = trim($data['gender']       ?? '');
$nacimiento = trim($data['birth_date']   ?? '');

if (empty($boleta)) {
    http_response_code(400);
    echo json_encode(["error" => true, "mensaje" => "La boleta es obligatoria."]);
    exit();
}

try {
    $db   = new Database();
    $conn = $db->getConnection();

    // Actualizar tabla Student
    $sqlStudent = "UPDATE Student SET
                    name          = :name,
                    last_name_P   = :last_name_P,
                    last_name_M   = :last_name_M,
                    birth_date    = :birth_date,
                    gender        = :gender
                   WHERE no_boleta = :no_boleta";

    $stmtS = $conn->prepare($sqlStudent);
    $stmtS->bindParam(':name',        $nombre);
    $stmtS->bindParam(':last_name_P', $apPaterno);
    $stmtS->bindParam(':last_name_M', $apMaterno);
    $stmtS->bindParam(':birth_date',  $nacimiento);
    $stmtS->bindParam(':gender',      $genero);
    $stmtS->bindParam(':no_boleta',   $boleta);
    $stmtS->execute();

    // Actualizar email en tabla User (JOIN a través de Student)
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

    echo json_encode(["error" => false, "mensaje" => "Registro actualizado correctamente."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => true, "mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>
