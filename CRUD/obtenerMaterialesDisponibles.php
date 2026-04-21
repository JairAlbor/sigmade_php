<?php
header('Content-Type: application/json');
include('conexion.php');

function generarCodigoMaterial($disciplina, $nombre, $id) {
    $disPart = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $disciplina), 0, 3));
    $nomPart = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nombre), 0, 3));
    $idPart  = str_pad($id, 5, '0', STR_PAD_LEFT);
    if (empty($disPart)) $disPart = 'GEN';
    if (empty($nomPart)) $nomPart = 'MAT';
    return "$disPart-$nomPart-$idPart";
}

$inicio = isset($_GET['inicio']) ? mysqli_real_escape_string($conn, $_GET['inicio']) : '';
$limite = isset($_GET['limite']) ? mysqli_real_escape_string($conn, $_GET['limite']) : '';
$excludeCanchas = isset($_GET['excludeCanchas']) && $_GET['excludeCanchas'] == 'true';

// Base query for physically functional materials
$sql = "SELECT m.id, m.nombre, m.estado, m.foto_url,
               d.nombre AS disciplina, m.disciplina_id, m.codigo_material
        FROM material m
        LEFT JOIN disciplina d ON m.disciplina_id = d.id
        WHERE m.estado != 'Roto' AND m.estado != 'Mantenimiento'";

if ($excludeCanchas) {
    $sql .= " AND (m.tipoMaterial != 'Cancha' OR m.tipoMaterial IS NULL)";
}

// Agregamos la subconsulta si enviaron fechas
if (!empty($inicio) && !empty($limite)) {
    $sql .= " AND m.id NOT IN (
                SELECT dp.material_id 
                FROM prestamo p
                JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
                WHERE p.estado_general IN ('Activo', 'Prestado', 'Pendiente', 'Aprobado')
                AND ('$inicio' < p.fecha_limite AND '$limite' > p.fecha_inicio)
              )";
}

$sql .= " ORDER BY d.nombre, m.nombre";
$result = mysqli_query($conn, $sql);
$materiales = [];

while ($row = mysqli_fetch_assoc($result)) {
    $row['codigo_material'] = generarCodigoMaterial($row['disciplina'], $row['nombre'], $row['id']);
    $materiales[] = $row;
}

echo json_encode($materiales);
?>