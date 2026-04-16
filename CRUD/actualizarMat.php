
<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombreArticulo'];
    $disciplina = $_POST['disciplinaAct'];
    $estado = $_POST['estadoAct'];
    $tipoMaterial = $_POST['tipoMaterialAct'];
    $disponible = $_POST['disponibilidadAct'];
    // Procesar la foto opcional
    $foto_update = "";
    if (isset($_FILES['imagenAct']) && $_FILES['imagenAct']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $tmp_name = $_FILES['imagenAct']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['imagenAct']['name']);
        $dest_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($tmp_name, $dest_path)) {
            $foto_url = "uploads/" . mysqli_real_escape_string($conn, $file_name);
            $foto_update = ", foto_url = '$foto_url'";
        }
    }

    $consulta = "UPDATE material SET nombre = '$nombre', disciplina_id = '$disciplina', tipoMaterial = '$tipoMaterial',
                estado = '$estado',
                disponible = '$disponible'
                $foto_update
                WHERE id = $id";
    
    if (mysqli_query($conn, $consulta)) {
        echo "<script>alert('Material actualizado correctamente.');</script>";
        header("Location: ../catalogo.php?success=1");
    } else {
        echo "<script>alert('Error al actualizar el material.');</script>";
        header("Location: ../catalogo.php?error=1");
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
