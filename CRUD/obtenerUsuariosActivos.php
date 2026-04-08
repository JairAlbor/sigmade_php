<?php
header('Content-Type: application/json');
include('conexion.php');

$sql = "SELECT id, CONCAT(nombre, ' ', apellidos) AS nombre FROM usuario WHERE estatus = 'Activo' ORDER BY nombre";
$result = mysqli_query($conn, $sql);
$usuarios = [];

while ($row = mysqli_fetch_assoc($result)) {
    $usuarios[] = $row;
}

echo json_encode($usuarios);
?>