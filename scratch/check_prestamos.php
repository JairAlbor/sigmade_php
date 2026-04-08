<?php
include('c:/xampp/htdocs/sigmade_php/CRUD/conexion.php');
$res = mysqli_query($conn, "SELECT id, estado_general FROM prestamo");
while($row = mysqli_fetch_assoc($res)) {
    echo json_encode($row) . "\n";
}
?>
