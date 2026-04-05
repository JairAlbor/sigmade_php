<?php
include("conexion.php");

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "DELETE FROM material WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        // Redirigimos y pasamos "success=1" como señal
        echo "<script>alert('Material eliminado correctamente.');</script>";
        header("Location: ../catalogo.php");
        exit();
    } else {
        echo "<script>alert('Error al eliminar el material.');</script>";
        header("Location: ../catalogo.php");
        exit();
    }
}
?>