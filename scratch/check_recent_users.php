<?php
include('c:/xampp/htdocs/sigmade_php/CRUD/conexion.php');
$res = mysqli_query($conn, 'SELECT * FROM usuario ORDER BY id DESC LIMIT 5');
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
