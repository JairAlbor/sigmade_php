<?php
header('Content-Type: application/json');
include('conexion.php');

$sql = "
    SELECT d.id, d.nombre as disciplina, u.nombre as entrenador 
    FROM disciplina d 
    LEFT JOIN usuario u ON d.entrenador_id = u.id
";
$result = mysqli_query($conn, $sql);
$disciplinas = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $disciplinas[] = $row;
    }
}
echo json_encode($disciplinas);
?>
