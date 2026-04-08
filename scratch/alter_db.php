<?php
include('c:/xampp/htdocs/sigmade_php/CRUD/conexion.php');
if (mysqli_query($conn, "ALTER TABLE prestamo MODIFY estado_general VARCHAR(50) DEFAULT 'Pendiente'")) {
    echo "estado_general modified successfully\n";
} else {
    echo "Error modifying estado_general: " . mysqli_error($conn) . "\n";
}
?>
