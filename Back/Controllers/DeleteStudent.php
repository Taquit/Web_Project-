<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: DELETE, POST, OPTIONS');

require_once '../Config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["error" => true, "mensaje" => "Método no permitido."]);
    exit();
}

// Acepta tanto form-data (POST) como JSON raw (DELETE)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
} else {
    $data = $_POST;
}

$boleta = trim($data['no_boleta'] ?? '');

if (empty($boleta)) {
    http_response_code(400);
    echo json_encode(["error" => true, "mensaje" => "La boleta es obligatoria."]);
    exit();
}

try {
    $db   = new Database();
    $conn = $db->getConnection();

    // Obtener id_user antes de borrar el estudiante
    $stmtGetUser = $conn->prepare("SELECT id_user FROM Student WHERE no_boleta = :no_boleta LIMIT 1");
    $stmtGetUser->bindParam(':no_boleta', $boleta);
    $stmtGetUser->execute();
    $row = $stmtGetUser->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(["error" => true, "mensaje" => "Estudiante no encontrado."]);
        exit();
    }

    $idUser = $row['id_user'];

    // 1. Eliminar asignación (Allocation) — FK a Student
    $conn->prepare("DELETE FROM Allocation WHERE no_boleta = :no_boleta")
         ->execute([':no_boleta' => $boleta]);

    // 2. Eliminar estudiante (Student) — FK a User
    $conn->prepare("DELETE FROM Student WHERE no_boleta = :no_boleta")
         ->execute([':no_boleta' => $boleta]);

    // 3. Eliminar usuario
    $conn->prepare("DELETE FROM User WHERE id_user = :id_user")
         ->execute([':id_user' => $idUser]);

    echo json_encode(["error" => false, "mensaje" => "Estudiante eliminado correctamente."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => true, "mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>
