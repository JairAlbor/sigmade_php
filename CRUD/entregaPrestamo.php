<?php
include('conexion.php');

$id = $_GET['id'];

// Actualizar estado del préstamo a 'Entregado'
$sql = "UPDATE prestamo SET estado_general = 'Entregado', fecha_entrega = CURDATE() WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    // Redirigir de vuelta con mensaje de éxito
    header("Location: ../index.php?msg=entregado");
} else {
    header("Location: ../index.php?msg=error");
}
?>