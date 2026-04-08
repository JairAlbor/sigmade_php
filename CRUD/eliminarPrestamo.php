<?php
include('conexion.php');

$id = $_GET['id'];

// Eliminar detalles primero
$sql_detalle = "DELETE FROM detalle_prestamo WHERE prestamo_id = $id";
mysqli_query($conn, $sql_detalle);

// Eliminar préstamo
$sql = "DELETE FROM prestamo WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    header("Location: ../index.php?msg=eliminado");
} else {
    header("Location: ../index.php?msg=error");
}
?>