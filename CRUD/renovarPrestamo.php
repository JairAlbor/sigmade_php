<?php
include('conexion.php');

$id = $_GET['id'];
$dias = $_GET['dias'];

$sql = "UPDATE prestamo SET fecha_limite = DATE_ADD(fecha_limite, INTERVAL $dias DAY), estado_general = 'Renovado' WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    header("Location: ../index.php?msg=renovado");
} else {
    header("Location: ../index.php?msg=error");
}
?> 