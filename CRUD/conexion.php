<?php
/*codigo qye hace la conexion a la base de datos */


$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "basesilla";

// Crear conexión
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}