<?php
require_once '../Config/config.php';
require_once '../Models/User.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $user->email_user = $_POST['email'];
    $password_input   = $_POST['password'];

    $stmt = $user->get_By_Email($user->email_user);
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password_input, $row['password'])) {

        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['id_rol']  = $row['id_rol'];
        $_SESSION['email']   = $row['email_user'];

        if ($row['id_rol'] == 2) {
            header('Location: ../../Front/Admin_page/AdminPanel.php');
        } else if ($row['id_rol'] == 1) {
            header('Location: ../../Front/Cuenta/Cuenta.php');
        } else {
            session_destroy();
            header("Location: ../../Front/Home_page/index.php");
        }
        exit();

    } else {
        // Detectar desde qué formulario vino para redirigir al lugar correcto
        $email = $_POST['email'] ?? '';
        if (str_contains($email, '@alumno.ipn.mx')) {
            header("Location: ../../Front/Account_page/Account.php?error=admin1");
        } else {
            header("Location: ../../Front/Admin_page/Admin.php?error=admin1");
        }
        exit();
    }

} else {
    header("Location: ../../Front/Home_page/index.php");
    exit();
}
?>