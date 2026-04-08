<?php
header('Content-Type: application/json');
include('conexion.php');

$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? intval($data['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

if ($id > 0) {
    // Al remover un entrenador, le asignamos el rol 'Alumno'
    $sql = "UPDATE usuario SET rol = 'Alumno' WHERE id = $id AND rol IN ('Docente', 'Entrenador')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Entrenador regresado al rol de Alumno"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al degradar de rol: " . mysqli_error($conn)]);
    }
} else {
    echo json_encode(["success" => false, "message" => "ID inválido"]);
}
?>
