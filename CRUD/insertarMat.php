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
    // Procesar imagen si fue enviada
    $foto_url_sql = 'NULL';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $tmp_name = $_FILES['imagen']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['imagen']['name']);
        $dest_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($tmp_name, $dest_path)) {
            $foto_relativa = "uploads/" . mysqli_real_escape_string($conn, $file_name);
            $foto_url_sql = "'$foto_relativa'";
        }
    }

    $sql = "INSERT INTO material (nombre, disciplina_id, tipoMaterial, estado, disponible, foto_url) VALUES ('$nombre', '$disciplina', '$tipoMaterial', '$estado', '$disponibilidad', $foto_url_sql)";

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