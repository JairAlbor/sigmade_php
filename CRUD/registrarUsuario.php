<?php
include('conexion.php');

$rol = $_POST['rol'];
$nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
$apellidos = mysqli_real_escape_string($conn, $_POST['apellidos']);
$iden = mysqli_real_escape_string($conn, $_POST['iden']);
$telefono = mysqli_real_escape_string($conn, $_POST['tel']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO usuario (nombre, apellidos, identificacion, telefono, email, password, rol, estatus) 
        VALUES ('$nombre', '$apellidos', '$iden', '$telefono', '$email', '$password', '$rol', 'Activo')";

if (mysqli_query($conn, $sql)) {
    header("Location: ../index.php?msg=usuario_registrado");
} else {
    header("Location: ../index.php?msg=error");
}
?>