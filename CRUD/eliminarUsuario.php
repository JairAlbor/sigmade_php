<?php

//codigo para eliminar un usuario de la base de datos, se recibe el id del usuario a eliminar por POST desde el archivo administacion.php, luego se ejecuta la consulta para eliminarlo y se redirige de vuelta a la página de administración
include("conexion.php");
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sqlDelete = "DELETE FROM usuario WHERE id = '$id'";

    mysqli_query($conn, $sqlDelete);

    // Redirigir de vuelta a la página de administración
    header("Location: ../administacion.php");
    exit();
}