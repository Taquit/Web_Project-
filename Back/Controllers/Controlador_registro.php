<?php
session_start();
header('Content-Type: application/json');
if($_SESSION){
    $respuesta = [
    "status" => "vacio"
    ];

    if (isset($_SESSION['Registro'])) {
    $respuesta = $_SESSION['Registro'];
    unset($_SESSION['Registro']); 
    } 

    echo json_encode($respuesta);
    exit();
} else {
    header("Location: ../../Front/Home_page/index.html");
    exit();
}
