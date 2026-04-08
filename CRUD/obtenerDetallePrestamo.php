<?php
header('Content-Type: application/json');
include('conexion.php');

$id = $_GET['id'];

$sql = "SELECT 
    p.id AS prestamo_id,
    u.nombre AS usuario_nombre,
    u.apellidos AS usuario_apellidos,
    GROUP_CONCAT(m.nombre SEPARATOR ', ') AS materiales,
    p.fecha_solicitud,
    p.fecha_limite,
    p.estado_general,
    p.fecha_entrega
FROM prestamo p
JOIN usuario u ON p.usuario_id = u.id
JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
JOIN material m ON dp.material_id = m.id
WHERE p.id = $id
GROUP BY p.id";

$result = mysqli_query($conn, $sql);
$prestamo = mysqli_fetch_assoc($result);

echo json_encode($prestamo);
?>