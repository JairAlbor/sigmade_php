<?php
header('Content-Type: application/json');
include('conexion.php');

$id = intval($_GET['id']);

$sql = "SELECT 
    p.id AS prestamo_id,
    u.nombre AS usuario_nombre,
    u.apellidos AS usuario_apellidos,
    p.fecha_solicitud,
    p.fecha_limite,
    p.estado_general
FROM prestamo p
JOIN usuario u ON p.usuario_id = u.id
WHERE p.id = $id";

$result = mysqli_query($conn, $sql);
$prestamo = mysqli_fetch_assoc($result);

if ($prestamo) {
    $sql_detalles = "SELECT dp.codigo_articulo, dp.nombre_articulo, m.nombre as mat_nombre, m.codigo_material 
                     FROM detalle_prestamo dp 
                     LEFT JOIN material m ON dp.material_id = m.id 
                     WHERE dp.prestamo_id = $id";
    $res_detalles = mysqli_query($conn, $sql_detalles);
    $materiales = [];
    $materiales_html = [];
    while ($row = mysqli_fetch_assoc($res_detalles)) {
        $codigo = $row['codigo_articulo'] ?: $row['codigo_material'];
        $nombre = $row['nombre_articulo'] ?: $row['mat_nombre'];
        $materiales[] =  ($codigo ? "[$codigo] " : "") . $nombre;
        $materiales_html[] = "<li><b>".htmlspecialchars($codigo)."</b> - ".htmlspecialchars($nombre)."</li>";
    }
    $prestamo['materiales_lista'] = $materiales;
    $prestamo['materiales_html'] = "<ul style='margin:0; padding-left:20px;'>" . implode('', $materiales_html) . "</ul>";
    $prestamo['materiales'] = implode(', ', $materiales); // Fallback compatible
    echo json_encode($prestamo);
} else {
    echo json_encode(["error" => "No se encontró el préstamo"]);
}
?>