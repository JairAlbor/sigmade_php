<?php
include(__DIR__ . '/../CRUD/conexion.php');
echo "Iniciando migración...\n";

// 1. Alterar tabla usuario
$sql = "ALTER TABLE usuario ADD COLUMN confiabilidad INT DEFAULT 100";
if (mysqli_query($conn, $sql)) {
    echo "Columna confiabilidad agregada a usuario.\n";
} else {
    echo "Nota: " . mysqli_error($conn) . "\n";
}

// 2. Alterar tabla detalle_prestamo
$sql = "ALTER TABLE detalle_prestamo ADD COLUMN codigo_articulo VARCHAR(50) DEFAULT NULL";
if (mysqli_query($conn, $sql)) {
    echo "Columna codigo_articulo agregada a detalle_prestamo.\n";
} else {
    echo "Nota: " . mysqli_error($conn) . "\n";
}

$sql = "ALTER TABLE detalle_prestamo ADD COLUMN nombre_articulo VARCHAR(100) DEFAULT NULL";
if (mysqli_query($conn, $sql)) {
    echo "Columna nombre_articulo agregada a detalle_prestamo.\n";
} else {
    echo "Nota: " . mysqli_error($conn) . "\n";
}

// 3. Alterar tabla material
$sql = "ALTER TABLE material ADD COLUMN codigo_material VARCHAR(50) DEFAULT NULL";
if (mysqli_query($conn, $sql)) {
    echo "Columna codigo_material agregada a material.\n";
} else {
    echo "Nota: " . mysqli_error($conn) . "\n";
}

// 4. Poblar códigos de materiales existentes
echo "Generando códigos para materiales existentes...\n";
$sql = "SELECT m.id, m.nombre, d.nombre as disciplina_nombre FROM material m LEFT JOIN disciplina d ON m.disciplina_id = d.id";
$res = mysqli_query($conn, $sql);

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $nom = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $row['nombre']), 0, 3));
        $disc = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $row['disciplina_nombre']), 0, 3));
        if (empty($disc)) $disc = "GEN";

        $codigo = $nom . "-" . $disc . "-" . str_pad($row['id'], 3, '0', STR_PAD_LEFT);
        
        $upd = "UPDATE material SET codigo_material = '$codigo' WHERE id = " . $row['id'];
        mysqli_query($conn, $upd);
        echo "Actualizado material ID " . $row['id'] . " a código $codigo\n";
    }
} else {
    echo "Error consultando materiales: " . mysqli_error($conn) . "\n";
}

echo "Migración completada.\n";
?>
