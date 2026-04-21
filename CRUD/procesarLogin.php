<?php
header('Content-Type: application/json');
include("conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    // Paso 1: Verificar si el usuario existe (por email o identificador)
    $consulta_user = "SELECT * FROM usuario WHERE email = '$user' OR identificador = '$user'";
    $resultado_user = mysqli_query($conn, $consulta_user);

    if (mysqli_num_rows($resultado_user) === 0) {
        // Usuario no existe en el sistema
        echo json_encode(['status' => 'error', 'message' => 'El usuario ingresado no existe.']);
        exit();
    }

    $datos_usuario = mysqli_fetch_array($resultado_user);

    // Paso 2: Verificar si la contraseña es correcta
    if ($datos_usuario['pass'] !== $pass) {
        echo json_encode(['status' => 'error', 'message' => 'La contraseña es incorrecta.']);
        exit();
    }

    // Credenciales correctas: guardar sesión
    $_SESSION['usuario_nombre'] = $datos_usuario['nombre'];
    $_SESSION['rol']            = $datos_usuario['rol'];
    $_SESSION['user_id']        = $datos_usuario['id'];

    echo json_encode(['status' => 'success', 'message' => '¡Bienvenido de nuevo!']);
    exit();
}
?>
