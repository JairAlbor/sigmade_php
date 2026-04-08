<?php
include('conexion.php');

$id = $_GET['id'];

// Verificar si el usuario tiene préstamos activos
$sql_check = "SELECT COUNT(*) as total FROM prestamo WHERE usuario_id = $id AND estado_general IN ('Activo', 'Prestado')";
$result = mysqli_query($conn, $sql_check);
$row = mysqli_fetch_assoc($result);

if ($row['total'] > 0) {
    header("Location: ../index.php?msg=usuario_con_prestamos");
} else {
    $sql = "DELETE FROM usuario WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../index.php?msg=usuario_eliminado");
    } else {
        header("Location: ../index.php?msg=error");
    }
}
?>