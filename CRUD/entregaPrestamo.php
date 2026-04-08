<?php
include('conexion.php');

$id = $_GET['id'];

// Actualizar estado del préstamo a 'Finalizado'
$sql = "UPDATE prestamo SET estado_general = 'Finalizado', fecha_entrega = CURDATE() WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    // Redirigir de vuelta con mensaje de éxito
    header("Location: ../administacion.php?msg=entregado");
} else {
    header("Location: ../administacion.php?msg=error");
}
?>