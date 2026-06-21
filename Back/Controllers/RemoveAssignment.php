<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');

require_once '../Config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => true, "mensaje" => "Método no permitido."]);
    exit();
}

$boleta = trim($_POST['no_boleta'] ?? '');

if (empty($boleta)) {
    http_response_code(400);
    echo json_encode(["error" => true, "mensaje" => "La boleta es obligatoria."]);
    exit();
}

try {
    $db   = new Database();
    $conn = $db->getConnection();

    // Eliminar solo la asignación (Allocation) - FK a Student
    $stmt = $conn->prepare("DELETE FROM Allocation WHERE no_boleta = :no_boleta");
    $stmt->execute([':no_boleta' => $boleta]);

    echo json_encode(["error" => false, "mensaje" => "Asignación eliminada correctamente."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => true, "mensaje" => "Error en el servidor: " . $e->getMessage()]);
}
?>
