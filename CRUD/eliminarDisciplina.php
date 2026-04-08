<?php
header('Content-Type: application/json');
include('conexion.php');
$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? intval($data['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

if ($id > 0) {
    $sql = "DELETE FROM disciplina WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Disciplina eliminada"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar: " . mysqli_error($conn)]);
    }
} else {
    echo json_encode(["success" => false, "message" => "ID inválido"]);
}
?>
