<?php
include('CRUD/conexion.php');
$result = mysqli_query($conn, "DESCRIBE usuario");
while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
?>
