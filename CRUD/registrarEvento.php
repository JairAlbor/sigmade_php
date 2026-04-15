<?php
header('Content-Type: application/json');
include('conexion.php');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data && !empty($_POST)) {
    $data = $_POST;
}

$titulo = $data['titulo'] ?? null;
$descripcion = $data['descripcion'] ?? null;
$fecha = $data['fecha'] ?? null;
$hora = $data['hora'] ?? null;
$ubicacion = $data['ubicacion'] ?? null;

if (!$titulo || !$fecha) {
    echo json_encode(['success' => false, 'message' => 'El título y la fecha son obligatorios']);
    exit;
}

$titulo = mysqli_real_escape_string($conn, $titulo);
$descripcion = mysqli_real_escape_string($conn, $descripcion);
$fecha = mysqli_real_escape_string($conn, $fecha);
$hora = mysqli_real_escape_string($conn, $hora);
$ubicacion = mysqli_real_escape_string($conn, $ubicacion);

$sql = "INSERT INTO evento (titulo, descripcion, fecha, hora, ubicacion) 
        VALUES ('$titulo', '$descripcion', '$fecha', '$hora', '$ubicacion')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Evento registrado correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar evento: ' . mysqli_error($conn)]);
}
?>
