<?php
include('c:/xampp/htdocs/sigmade_php/CRUD/conexion.php');
$sql = "ALTER TABLE disciplina ADD COLUMN entrenador_id INT(11) NULL";
if(mysqli_query($conn, $sql)) {
    echo "Added column entrenador_id.\n";
} else {
    echo "Error adding column: " . mysqli_error($conn) . "\n";
}

$sql2 = "ALTER TABLE disciplina ADD CONSTRAINT fk_entrenador FOREIGN KEY (entrenador_id) REFERENCES usuario(id) ON DELETE SET NULL";
if(mysqli_query($conn, $sql2)) {
    echo "Added foreign key constraint.\n";
} else {
    echo "Error adding constraint: " . mysqli_error($conn) . "\n";
}
?>
