<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $estatus = mysqli_real_escape_string($conn, $_POST['estatus']);

    // Actualizamos en la base de datos
    $consulta = "UPDATE usuario SET estatus = '$estatus' WHERE id = $id";

    if (mysqli_query($conn, $consulta)) {
        // REDIRECCIÓN: Esto es lo que hace que parezca que "nada pasó"
        // Cambia 'administracion.php' por el nombre de tu archivo principal
        header("Location: ../administacion.php?status=updated");
        exit();
    } else {
        echo "Error al actualizar: " . mysqli_error($conn);
    }
}
?>