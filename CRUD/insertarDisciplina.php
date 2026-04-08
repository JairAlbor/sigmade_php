<?php
//codigo para insertar una nueva disciplina en la base de datos, se recibe el nombre de la disciplina y el id del entrenador por POST desde el archivo administacion.php, luego se ejecuta la consulta para insertarla y se redirige de vuelta a la página de administración
include("conexion.php");
if (isset($_POST['nombre_disciplina']) && isset($_POST['entrenador_id'])) {
    $nombre_disciplina = mysqli_real_escape_string($conn, $_POST['nombre_disciplina']);
    $entrenador_id = mysqli_real_escape_string($conn, $_POST['entrenador_id']);

    $sqlInsert = "INSERT INTO disciplina (nombre, entrenador_id) VALUES ('$nombre_disciplina', " . ($entrenador_id ? "'$entrenador_id'" : "NULL") . ")";
    if (mysqli_query($conn, $sqlInsert)) {
        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos"]);
}