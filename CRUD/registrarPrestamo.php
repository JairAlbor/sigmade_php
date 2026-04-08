<?php
header('Content-Type: application/json');
include('conexion.php');

$usuario_id = $_POST['usuario_id'];
$materiales = $_POST['materiales'];
$fecha_limite = $_POST['fecha_limite'];

// Validar datos
if (!$usuario_id || empty($materiales) || !$fecha_limite) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Sanitizar entradas
$usuario_id = mysqli_real_escape_string($conn, $usuario_id);
$fecha_limite = mysqli_real_escape_string($conn, $fecha_limite);

// Verificar que todos los materiales estén disponibles antes de insertar
foreach ($materiales as $material_id) {
    $mid = mysqli_real_escape_string($conn, $material_id);
    $check = mysqli_query($conn, "SELECT id FROM material WHERE id = $mid AND LOWER(disponible) = 'libre'");
    if (mysqli_num_rows($check) === 0) {
        echo json_encode(['success' => false, 'message' => "El material #$mid ya no está disponible"]);
        exit;
    }
}

// Insertar préstamo
$sql = "INSERT INTO prestamo (usuario_id, fecha_solicitud, fecha_limite, estado_general) 
        VALUES ($usuario_id, CURDATE(), '$fecha_limite', 'Activo')";

if (mysqli_query($conn, $sql)) {
    $prestamo_id = mysqli_insert_id($conn);
    $success = true;

    foreach ($materiales as $material_id) {
        $mid = mysqli_real_escape_string($conn, $material_id);

        // Insertar detalle del préstamo
        $sql_detalle = "INSERT INTO detalle_prestamo (prestamo_id, material_id) 
                        VALUES ($prestamo_id, $mid)";
        if (!mysqli_query($conn, $sql_detalle)) {
            $success = false;
            break;
        }

        // Marcar material como Prestado
        $sql_update = "UPDATE material SET disponible = 'Prestado' WHERE id = $mid";
        mysqli_query($conn, $sql_update);
    }

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Préstamo registrado exitosamente']);
    } else {
        // Si algo falló, revertir el préstamo
        mysqli_query($conn, "DELETE FROM detalle_prestamo WHERE prestamo_id = $prestamo_id");
        mysqli_query($conn, "DELETE FROM prestamo WHERE id = $prestamo_id");
        echo json_encode(['success' => false, 'message' => 'Error al registrar detalles del préstamo']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
}
?>