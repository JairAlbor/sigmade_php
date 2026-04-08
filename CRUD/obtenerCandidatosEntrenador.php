<?php
header('Content-Type: application/json');
include('conexion.php');
// Obtener usuarios que NO son entrenadores
$sql = "SELECT id, nombre, apellidos FROM usuario WHERE rol NOT IN ('Docente', 'Entrenador')";
$result = mysqli_query($conn, $sql);
$candidatos = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $candidatos[] = $row;
    }
}
echo json_encode($candidatos);
?>
