<?php
header('Content-Type: application/json');
include('conexion.php');
$sql = "SELECT id, nombre, apellidos FROM usuario WHERE rol IN ('Docente', 'Entrenador')";
$result = mysqli_query($conn, $sql);
$entrenadores = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $entrenadores[] = $row;
    }
}
echo json_encode($entrenadores);
?>
