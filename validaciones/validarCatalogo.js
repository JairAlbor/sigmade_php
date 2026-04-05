function eliminarMaterial(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este material?")) {
        // Redirige al archivo PHP pasando el ID por la URL (GET)
        window.location.href = "eliminarMat.php?id=" + id;
    }
}