<?php
header('Content-Type: application/json');
include('conexion.php');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$observaciones = $data['observaciones'] ?? '';

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos (id)']);
    exit;
}

$id = mysqli_real_escape_string($conn, $id);
$observaciones = mysqli_real_escape_string($conn, $observaciones);

mysqli_begin_transaction($conn);

try {
    $sql_prestamo = "UPDATE prestamo SET estado_general = 'Finalizado', observaciones = '$observaciones' WHERE id = $id";
    mysqli_query($conn, $sql_prestamo);
    $sql_detalle_prestamo = "UPDATE detalle_prestamo SET fecha_entrega_real = CURDATE() WHERE prestamo_id = $id";
    mysqli_query($conn, $sql_detalle_prestamo);

    $sql_materiales = "UPDATE material m 
                       JOIN detalle_prestamo dp ON m.id = dp.material_id 
                       SET m.disponible = 'Libre' 
                       WHERE dp.prestamo_id = $id";
    mysqli_query($conn, $sql_materiales);

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Préstamo finalizado exitosamente y materiales liberados']);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => "Error al finalizar préstamo: " . $e->getMessage()]);
}
?>
