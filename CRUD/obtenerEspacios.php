<?php
header('Content-Type: application/json');
include('conexion.php');

$sql = "SELECT id, nombre, capacidad FROM espacio";
$result = mysqli_query($conn, $sql);
$espacios = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $espacios[] = $row;
    }
}

echo json_encode($espacios);
?>
