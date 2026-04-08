<?php
header('Content-Type: application/json');
include('conexion.php');

// Estadísticas de usuarios
$sql_usuarios = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estatus = 'Activo' THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN estatus != 'Activo' THEN 1 ELSE 0 END) as inactivos
FROM usuario";
$result_usuarios = mysqli_query($conn, $sql_usuarios);
$usuarios = mysqli_fetch_assoc($result_usuarios);

// Estadísticas de préstamos
$sql_prestamos = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado_general = 'Activo' OR estado_general = 'Prestado' THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN estado_general = 'Vencido' THEN 1 ELSE 0 END) as vencidos,
    SUM(CASE WHEN estado_general = 'Entregado' THEN 1 ELSE 0 END) as entregados
FROM prestamo";
$result_prestamos = mysqli_query($conn, $sql_prestamos);
$prestamos = mysqli_fetch_assoc($result_prestamos);

echo json_encode([
    'usuarios' => $usuarios,
    'prestamos' => $prestamos
]);
?>