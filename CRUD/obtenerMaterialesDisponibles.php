<?php
header('Content-Type: application/json');
include('conexion.php');

// Usamos LOWER() para ignorar mayúsculas/minúsculas en el valor de disponible
$sql = "SELECT id, nombre, estado FROM material WHERE LOWER(disponible) = 'libre'";
$result = mysqli_query($conn, $sql);
$materiales = [];

while ($row = mysqli_fetch_assoc($result)) {
    $materiales[] = $row;
}

echo json_encode($materiales);
?>