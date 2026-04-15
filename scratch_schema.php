<?php
include('CRUD/conexion.php');
$sql = "CREATE TABLE IF NOT EXISTS evento (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL,
    hora TIME,
    ubicacion VARCHAR(150),
    estatus ENUM('Activo', 'Inactivo') DEFAULT 'Activo'
)";
if(mysqli_query($conn, $sql)){
    echo "Tabla evento creada exitosamente.\n";
} else {
    echo "Error creando tabla evento: " . mysqli_error($conn) . "\n";
}
?>
