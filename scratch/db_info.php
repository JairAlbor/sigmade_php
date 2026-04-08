<?php
include('c:/xampp/htdocs/sigmade_php/CRUD/conexion.php');
$res = mysqli_query($conn, "DESCRIBE prestamo");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
