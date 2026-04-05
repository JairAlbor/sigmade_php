<?php
include("conexion.php");

//con este codigo podremos insertar un nuevo material a la base de datos, los datos se reciben por POST desde el formulario de agregar material en catalogo.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //recibimos el nombre del material
    $nombre = mysqli_real_escape_string($conn, $_POST['nombreArticulo']);
    $disciplina = mysqli_real_escape_string($conn, $_POST['disciplina']);
    $estado = mysqli_real_escape_string($conn, $_POST['estado']);
    $tipoMaterial = mysqli_real_escape_string($conn, $_POST['tipoMaterial']);
    $disponibilidad = 'Libre'; // Por defecto, el nuevo material estará disponible

    //validamos que los datos no estén vacíos
    if (empty($nombre) || empty($disciplina) || empty($estado) || empty($tipoMaterial)) {
        echo "<script>alert('Por favor, completa todos los campos.');</script>";
        header("Location: ../catalogo.php?error=1");
        exit();
    }

    $sql = "INSERT INTO material (nombre, disciplina_id, tipoMaterial, estado, disponible) VALUES ('$nombre', '$disciplina', '$tipoMaterial', '$estado', '$disponibilidad')";

    if (mysqli_query($conn, $sql)) {
        // Redirigimos y pasamos "success=1" como señal
        echo "<script>alert('Material agregado correctamente.');</script>";
        header("Location: ../catalogo.php?success=1");
        exit();
    } else {
        echo "<script>alert('Error al agregar el material.');</script>";
        header("Location: ../catalogo.php?error=1");
        exit();
    }
}
?>