<?php
header('Content-Type: application/json');
include('conexion.php');

$sql = "SELECT id, titulo, descripcion, fecha, hora, ubicacion FROM evento WHERE estatus = 'Activo' ORDER BY fecha DESC, hora DESC";
$result = mysqli_query($conn, $sql);
$eventos = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $eventos[] = $row;
    }
}

echo json_encode($eventos);
?>
