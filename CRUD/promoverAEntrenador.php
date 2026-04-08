<?php
header('Content-Type: application/json');
include('conexion.php');

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? intval($data['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
$especialidad = isset($data['especialidad']) ? mysqli_real_escape_string($conn, $data['especialidad']) : '';

if ($id > 0) {
    // Si quisieras guardar la especialidad tendrías que agregar columna a usuario. Dejamos como Docente.
    $sql = "UPDATE usuario SET rol = 'Docente' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Promovido a Entrenador exitosamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar rol: " . mysqli_error($conn)]);
    }
} else {
    echo json_encode(["success" => false, "message" => "ID inválido"]);
}
?>
