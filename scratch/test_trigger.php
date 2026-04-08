<?php
include('c:/xampp/htdocs/sigmade_php/CRUD/conexion.php');
$sql = "UPDATE prestamo SET estado_general = 'Finalizado' WHERE id = 5";
if (mysqli_query($conn, $sql)) {
    echo "Update OK";
} else {
    echo "Update Error: " . mysqli_error($conn);
}
?>
