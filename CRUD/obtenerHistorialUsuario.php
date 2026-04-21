<?php
header('Content-Type: application/json');
include('conexion.php');

$usuario_id = $_GET['usuario_id'] ?? null;

if (!$usuario_id) {
    echo json_encode(['success' => false, 'message' => 'Falta el id del usuario']);
    exit;
}

$usuario_id = mysqli_real_escape_string($conn, $usuario_id);

$sql = "SELECT 
    p.id AS prestamo_id,
    GROUP_CONCAT(m.nombre SEPARATOR ', ') AS materiales,
    DATE(p.fecha_solicitud) AS fecha_solicitud,
    DATE(p.fecha_inicio) AS fecha_inicio,
    DATE(p.fecha_limite) AS fecha_limite,
    p.estado_general
FROM prestamo p
LEFT JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
LEFT JOIN material m ON dp.material_id = m.id
WHERE p.usuario_id = $usuario_id
GROUP BY p.id
ORDER BY p.fecha_solicitud DESC";

$result = mysqli_query($conn, $sql);
$prestamos = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $prestamos[] = $row;
    }
}

echo json_encode($prestamos);
?>
