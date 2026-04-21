<?php
include('conexion.php');

// Detectar si es una solicitud AJAX
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

$rol = $_POST['rol'];
$nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
$apellidos = mysqli_real_escape_string($conn, $_POST['apellidos']);
$iden = mysqli_real_escape_string($conn, $_POST['iden']);
$telefono = mysqli_real_escape_string($conn, $_POST['tel']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password']; // Se eliminó el hash a petición del usuario

// La tabla 'usuario' usa 'identificador' y 'pass' según el esquema verificado
$sql = "INSERT INTO usuario (nombre, apellidos, identificador, telefono, email, pass, rol, estatus) 
        VALUES ('$nombre', '$apellidos', '$iden', '$telefono', '$email', '$password', '$rol', 'Activo')";

if (mysqli_query($conn, $sql)) {
    if ($is_ajax) {
        echo json_encode(['status' => 'success', 'message' => 'Usuario registrado correctamente']);
    } else {
        header("Location: ../index.php?msg=usuario_registrado");
    }
} else {
    $error = mysqli_error($conn);
    if ($is_ajax) {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar: ' . $error]);
    } else {
        header("Location: ../index.php?msg=error&detail=" . urlencode($error));
    }
}
?>