<?php
header('Content-Type: application/json');
include('conexion.php');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$estado = $data['estado'] ?? null;

if (!$id || !$estado) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos (id, estado)']);
    exit;
}

$id = mysqli_real_escape_string($conn, $id);
$estado = mysqli_real_escape_string($conn, $estado);

try {
    $sql = "UPDATE prestamo SET estado_general = '$estado' WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => "Estado actualizado a $estado"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error al actualizar estado: " . mysqli_error($conn)]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Excepción al actualizar estado: " . $e->getMessage()]);
}
?>
