
<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombreArticulo'];
    $disciplina = $_POST['disciplinaAct'];
    $estado = $_POST['estadoAct'];
    $tipoMaterial = $_POST['tipoMaterialAct'];
    $disponible = $_POST['disponibilidadAct'];
    
    $consulta = "UPDATE material SET nombre = '$nombre', disciplina_id = '$disciplina', tipoMaterial = '$tipoMaterial',
                estado = '$estado',
                disponible = '$disponible'
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
