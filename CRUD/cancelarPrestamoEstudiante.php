<?php
header('Content-Type: application/json');
include('conexion.php');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data && !empty($_POST)) {
    $data = $_POST;
}

$prestamo_id = $data['prestamo_id'] ?? null;
$usuario_id = $data['usuario_id'] ?? null;

if (!$prestamo_id || !$usuario_id) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos para cancelar el préstamo']);
    exit;
}

$prestamo_id = mysqli_real_escape_string($conn, $prestamo_id);
$usuario_id = mysqli_real_escape_string($conn, $usuario_id);

// Verificar la existencia y el estado válido
$check = mysqli_query($conn, "SELECT estado_general FROM prestamo WHERE id = $prestamo_id AND usuario_id = $usuario_id");
if (mysqli_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Préstamo no encontrado o no pertenece al usuario activo']);
    exit;
}

$row = mysqli_fetch_assoc($check);
if ($row['estado_general'] !== 'Pendiente' && $row['estado_general'] !== 'Aprobado') {
    echo json_encode(['success' => false, 'message' => 'No puedes cancelar un préstamo que ya está en curso o finalizado']);
    exit;
}

// Iniciar transacción explícita si la base de datos lo soporta, o mediante queries secuenciales
mysqli_autocommit($conn, false);
$success = true;

// 1. Liberar materiales asociados
$sql_materiales = "SELECT material_id FROM detalle_prestamo WHERE prestamo_id = $prestamo_id";
$res_materiales = mysqli_query($conn, $sql_materiales);
while ($rowMat = mysqli_fetch_assoc($res_materiales)) {
    $mid = $rowMat['material_id'];
    if (!mysqli_query($conn, "UPDATE material SET disponible = 'Libre' WHERE id = $mid")) {
        $success = false;
        break;
    }
}

// 2. Eliminar detalle_prestamo
if ($success && !mysqli_query($conn, "DELETE FROM detalle_prestamo WHERE prestamo_id = $prestamo_id")) {
    $success = false;
}

// 3. Eliminar el registro del prestamo
if ($success && !mysqli_query($conn, "DELETE FROM prestamo WHERE id = $prestamo_id")) {
    $success = false;
}

if ($success) {
    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Préstamo cancelado correctamente. Materiales devueltos al catálogo.']);
} else {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Error de base de datos al intentar cancelar. ' . mysqli_error($conn)]);
}

// Retornar al modo auto-commit
mysqli_autocommit($conn, true);
?>
