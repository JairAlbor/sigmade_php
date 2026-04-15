<?php
header('Content-Type: application/json');
include('conexion.php');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data && !empty($_POST)) {
    $data = $_POST;
}

$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Falta el id del evento']);
    exit;
}

$id = mysqli_real_escape_string($conn, $id);

$sql = "UPDATE evento SET estatus = 'Inactivo' WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Evento eliminado.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar evento: ' . mysqli_error($conn)]);
}
?>
