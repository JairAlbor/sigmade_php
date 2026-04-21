<?php
header('Content-Type: application/json');
include('conexion.php');

$sql = "SELECT 
    p.id AS prestamo_id,
    u.nombre AS usuario_nombre,
    u.apellidos AS usuario_apellidos,
    GROUP_CONCAT(m.nombre SEPARATOR ', ') AS materiales,
    DATE(p.fecha_solicitud) AS fecha_solicitud,
    DATE(p.fecha_inicio) AS fecha_inicio,
    DATE(p.fecha_limite) AS fecha_limite,
    p.estado_general,
    DATEDIFF(p.fecha_limite, CURDATE()) AS dias_restantes
FROM prestamo p
JOIN usuario u ON p.usuario_id = u.id
JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
JOIN material m ON dp.material_id = m.id
GROUP BY p.id
ORDER BY p.fecha_solicitud DESC";

$result = mysqli_query($conn, $sql);
$prestamos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $prestamos[] = $row;
}

echo json_encode($prestamos);
?>