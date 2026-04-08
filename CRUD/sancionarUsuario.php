<?php
include('conexion.php');

$prestamo_id = $_GET['prestamo_id'];
$motivo = mysqli_real_escape_string($conn, $_GET['motivo']);

// Obtener usuario_id del préstamo
$sql_usuario = "SELECT usuario_id FROM prestamo WHERE id = $prestamo_id";
$result = mysqli_query($conn, $sql_usuario);
$row = mysqli_fetch_assoc($result);
$usuario_id = $row['usuario_id'];

// Actualizar estatus del usuario
$sql = "UPDATE usuario SET estatus = 'Sancionado', motivo_sancion = '$motivo' WHERE id = $usuario_id";

if (mysqli_query($conn, $sql)) {
    header("Location: ../index.php?msg=sancionado");
} else {
    header("Location: ../index.php?msg=error");
}
?>