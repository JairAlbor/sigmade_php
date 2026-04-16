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

// Verificar si el usuario ya tiene un préstamo activo
$check_active = mysqli_query($conn, "SELECT id FROM prestamo WHERE usuario_id = $usuario_id AND estado_general IN ('Activo', 'Prestado', 'Pendiente', 'Aprobado')");
if (mysqli_num_rows($check_active) > 0) {
    echo json_encode(['success' => false, 'message' => 'El usuario ya tiene un préstamo activo o pendiente. Debe finalizarlo antes de solicitar otro.']);
    exit;
}

// Verificar que todos los materiales estén disponibles antes de insertar
foreach ($materiales as $material_id) {
    $mid = mysqli_real_escape_string($conn, $material_id);
    $check = mysqli_query($conn, "SELECT id FROM material WHERE id = $mid AND LOWER(disponible) = 'libre'");
    if (mysqli_num_rows($check) === 0) {
        echo json_encode(['success' => false, 'message' => "El material #$mid ya no está disponible"]);
        exit;
    }
}

// Iniciar transacción explícita
mysqli_autocommit($conn, false);

// Insertar préstamo principal
$sql = "INSERT INTO prestamo (usuario_id, fecha_solicitud, fecha_limite, estado_general) 
        VALUES ($usuario_id, CURDATE(), '$fecha_limite', 'Pendiente')";

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

        // Marcar material como Prestado en el inventario temporalmente reservado
        $sql_update = "UPDATE material SET disponible = 'Prestado' WHERE id = $mid";
        if (!mysqli_query($conn, $sql_update)) {
            $success = false;
            break;
        }
    }

    if ($success) {
        // Asegurar transaccion de forma permanente
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Préstamo registrado exitosamente bajo alta disponibilidad.']);
    } else {
        // Ejecutar Rollback por prevención a corrupciones o desincronización
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'El Préstamo fue declinado debido a un fallo en el registro. Ningún objeto fue reservado (Haciendo Rollback).']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error Crítico: ' . mysqli_error($conn)]);
}

// Restaurar el comportamiento individual en base de datos
mysqli_autocommit($conn, true);
?>