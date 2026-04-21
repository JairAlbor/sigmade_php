<?php
header('Content-Type: application/json');
include('conexion.php');

$usuario_id = $_POST['usuario_id'];
$materiales = $_POST['materiales'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_limite = $_POST['fecha_limite'];

// Validar datos
if (!$usuario_id || empty($materiales) || !$fecha_inicio || !$fecha_limite) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Sanitizar entradas
$usuario_id = mysqli_real_escape_string($conn, $usuario_id);
$fecha_inicio = mysqli_real_escape_string($conn, $fecha_inicio);
$fecha_limite = mysqli_real_escape_string($conn, $fecha_limite);

// La verificación de préstamo activo fue retirada para permitir el sistema de reservas y préstamos concurrentes (siempre que no choquen fechas)

// Verificar choques de fecha/hora para cada material
foreach ($materiales as $material_id) {
    $mid = mysqli_real_escape_string($conn, $material_id);
    
    // Primero validamos si el material tiene estado físico funcional
    $check_fisico = mysqli_query($conn, "SELECT id, nombre, estado, codigo_material FROM material WHERE id = $mid AND estado != 'Roto' AND estado != 'Mantenimiento'");
    if (mysqli_num_rows($check_fisico) === 0) {
        echo json_encode(['success' => false, 'message' => "El material #$mid no se encuentra en condiciones óptimas para préstamo"]);
        exit;
    }
    $mat_info = mysqli_fetch_assoc($check_fisico);
    
    // Ahora validamos si hay un préstamo cruzado
    $query_choque = "SELECT p.id FROM prestamo p 
                     JOIN detalle_prestamo dp ON p.id = dp.prestamo_id 
                     WHERE dp.material_id = $mid 
                     AND p.estado_general IN ('Activo', 'Prestado', 'Pendiente', 'Aprobado')
                     AND ('$fecha_inicio' < p.fecha_limite AND '$fecha_limite' > p.fecha_inicio)";
    $check_choque = mysqli_query($conn, $query_choque);
    if (mysqli_num_rows($check_choque) > 0) {
        echo json_encode(['success' => false, 'message' => "El material '" . $mat_info['nombre'] . "' ya tiene una reserva que choca con la fecha y hora seleccionadas."]);
        exit;
    }
}

// Iniciar transacción explícita
mysqli_autocommit($conn, false);

// Insertar préstamo principal con fecha de inicio
$sql = "INSERT INTO prestamo (usuario_id, fecha_solicitud, fecha_inicio, fecha_limite, estado_general) 
        VALUES ($usuario_id, CURDATE(), '$fecha_inicio', '$fecha_limite', 'Pendiente')";

if (mysqli_query($conn, $sql)) {
    $prestamo_id = mysqli_insert_id($conn);
    $success = true;

    foreach ($materiales as $material_id) {
        $mid = mysqli_real_escape_string($conn, $material_id);

        // Insertar detalle del préstamo con código y nombre (historización)
        // Volvemos a consultar o usamos caché. Es más seguro consultarlo rápido.
        $m_res = mysqli_query($conn, "SELECT nombre, codigo_material FROM material WHERE id = $mid");
        $m_data = mysqli_fetch_assoc($m_res);
        $nombre_mat = mysqli_real_escape_string($conn, $m_data['nombre']);
        $codigo_mat = mysqli_real_escape_string($conn, $m_data['codigo_material']);

        $sql_detalle = "INSERT INTO detalle_prestamo (prestamo_id, material_id, codigo_articulo, nombre_articulo) 
                        VALUES ($prestamo_id, $mid, '$codigo_mat', '$nombre_mat')";
        if (!mysqli_query($conn, $sql_detalle)) {
            $success = false;
            break;
        }

        // Nota: Ya no se marca el material físico como 'Prestado' de inmediato,
        // dependemos del choque de fechas (estado en la tabla prestamo) para la disponibilidad.
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