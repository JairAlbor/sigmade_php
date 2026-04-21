<?php
include('conexion.php');

$id = $_POST['id_usuario'];
$nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$rol = mysqli_real_escape_string($conn, $_POST['rol']);
$confiabilidad = isset($_POST['confiabilidad']) ? (int)$_POST['confiabilidad'] : 100;

$sql = "UPDATE usuario SET nombre = '$nombre', email = '$email', rol = '$rol', confiabilidad = $confiabilidad WHERE id = $id";


if (mysqli_query($conn, $sql)) {
    header("Location: ../index.php?msg=usuario_actualizado");
} else {
    header("Location: ../index.php?msg=error");
}
?>