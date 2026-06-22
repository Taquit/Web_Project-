<?php

require_once '../Config/config.php';
require_once '../Models/User.php';
require_once '../Models/Student.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Conexion a la DB
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);

    $user->email_user = $_POST['email'];
    $password_input   = $_POST['password'];

    $stmt = $user->get_By_Email($user->email_user);
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password_input, $row['password'])) {

        // Guardar datos en sesion
        $_SESSION['id_user']  = $row['id_user'];
        $_SESSION['id_rol']   = $row['id_rol'];
        $_SESSION['email']    = $row['email_user'];

        // Redirigir segun rol 
        if ($row['id_rol'] == 2) {
            header('Location: ../../Front/Admin_page/AdminPanel.html');
        } else if($row['id_rol']==1) {
            header('Location: ../../Front/Cuenta/Cuenta.html');
        }else{
            alert("Credenciales no encontradas");
        }
        exit();

    } else {
        // Credenciales incorrectas
        //http_response_code(401);
        //echo json_encode(array("error" => "Correo o contraseña incorrectos."));
        header("Location: ../../Front/Admin_page/admin.html?error=admin1");
        exit();
    }
} else {
    header("Location: ../../Front/Home_page/index.html");
    exit();
}
?>
