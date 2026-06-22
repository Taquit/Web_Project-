<?php
session_start();
header('Content-Type: application/json');

// CORRECCIÓN: Validar directamente si existe el arreglo 'Registro' en la sesión
if (isset($_SESSION['Registro']) && !empty($_SESSION['Registro'])) {
    
    // Devolver los datos estructurados en formato JSON directamente
    echo json_encode($_SESSION['Registro'], JSON_UNESCAPED_UNICODE);
    exit();

} else {
    // CORRECCIÓN: En lugar de usar header() en un Fetch, enviamos un estado de error 
    // para que JavaScript maneje la redirección en el navegador de manera limpia.
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Sesión no válida o expirada."
    ]);
    exit();
}
?>