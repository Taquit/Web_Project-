<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../Models/Student.php';
require_once '../Config/config.php';

try{
    
    $db = new Database();
    $conn = $db->getConnection();

    $studentModel = new Student($conn);

    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        $listaAlumnos = $studentModel->get_All_Info_Students();
        echo json_encode($listaAlumnos);
        
    }else{
        http_response_code(405); // Código HTTP de "Método no permitido"
        echo json_encode([
            "error" => true, 
            "mensaje" => "Método no permitido. Se esperaba una petición GET."
        ]);
    }
}catch(Exception $e){
    http_response_code(500); 
    echo json_encode([
        "error" => true, 
        "mensaje" => "Error en el servidor: " . $e->getMessage()
    ]);

}


?>