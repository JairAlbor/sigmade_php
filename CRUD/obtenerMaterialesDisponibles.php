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

$sql = "SELECT m.id, m.nombre, m.estado, m.foto_url,
               d.nombre AS disciplina, m.disciplina_id
        FROM material m
        LEFT JOIN disciplina d ON m.disciplina_id = d.id
        WHERE LOWER(m.disponible) = 'libre'
        ORDER BY d.nombre, m.nombre";
$result = mysqli_query($conn, $sql);
$materiales = [];

while ($row = mysqli_fetch_assoc($result)) {
    $row['codigo_material'] = generarCodigoMaterial($row['disciplina'], $row['nombre'], $row['id']);
    $materiales[] = $row;
}

echo json_encode($materiales);
?>