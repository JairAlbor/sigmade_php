<?php
header('Content-Type: application/json');
include('conexion.php');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data && !empty($_POST)) {
    $data = $_POST;
}

$espacio_id = $data['espacio_id'] ?? null;
$usuario_id = $data['usuario_id'] ?? null;
$inicio = $data['inicio'] ?? null;
$fin = $data['fin'] ?? null;
$motivo = $data['motivo'] ?? null;

if (!$espacio_id || !$usuario_id || !$inicio || !$fin || !$motivo) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos para la reserva']);
    exit;
}

$espacio_id = mysqli_real_escape_string($conn, $espacio_id);
$usuario_id = mysqli_real_escape_string($conn, $usuario_id);
$inicio = mysqli_real_escape_string($conn, $inicio);
$fin = mysqli_real_escape_string($conn, $fin);
$motivo = mysqli_real_escape_string($conn, $motivo);

// Verificar si el usuario ya tiene una reserva Activa o Pendiente
$check_user_reserva = mysqli_query($conn, "SELECT id FROM reserva WHERE usuario_id = $usuario_id AND estatus IN ('Pendiente', 'Confirmada')");
if (mysqli_num_rows($check_user_reserva) > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya tienes una solicitud de cancha pendiente o confirmada. Debes finalizarla primero.']);
    exit;
}

// Verificar superposición de reservas para el mismo espacio
// Solo checamos las que estén confirmadas o pendientes (evita dobles reservas)
$check = "SELECT id FROM reserva 
          WHERE espacio_id = $espacio_id 
          AND estatus IN ('Pendiente', 'Confirmada')
          AND (
            ('$inicio' BETWEEN inicio AND fin) OR 
            ('$fin' BETWEEN inicio AND fin) OR
            (inicio BETWEEN '$inicio' AND '$fin')
          )";

$resCheck = mysqli_query($conn, $check);
if (mysqli_num_rows($resCheck) > 0) {
    echo json_encode(['success' => false, 'message' => 'El horario solicitado choca con una reserva existente.']);
    exit;
}

$sql = "INSERT INTO reserva (espacio_id, usuario_id, inicio, fin, motivo, estatus) 
        VALUES ($espacio_id, $usuario_id, '$inicio', '$fin', '$motivo', 'Pendiente')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Reserva solicitada correctamente y en espera de aprobación.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar la reserva: ' . mysqli_error($conn)]);
}
?>
