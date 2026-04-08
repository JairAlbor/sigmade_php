<?php
include('c:/xampp/htdocs/sigmade_php/CRUD/conexion.php');
$res = mysqli_query($conn, 'SHOW COLUMNS FROM disciplina');
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
