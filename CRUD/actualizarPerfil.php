<?php
header('Content-Type: application/json');
include("conexion.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit();
}

$id        = $_SESSION['user_id'];
$nombre    = $_POST['nombre']   ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$email     = $_POST['email']    ?? '';
$telefono  = $_POST['telefono'] ?? '';
$password  = $_POST['password'] ?? '';

if (empty($nombre) || empty($apellidos) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Campos obligatorios faltantes']);
    exit();
}

// Si el usuario envió contraseña, también la actualizamos
if (!empty($password)) {
    $sql  = "UPDATE usuario SET nombre = ?, apellidos = ?, email = ?, telefono = ?, pass = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $apellidos, $email, $telefono, $password, $id);
} else {
    $sql  = "UPDATE usuario SET nombre = ?, apellidos = ?, email = ?, telefono = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $apellidos, $email, $telefono, $id);
}

if ($stmt->execute()) {
    $_SESSION['usuario_nombre'] = $nombre;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
