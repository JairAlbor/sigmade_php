<?php
include('c:/xampp/htdocs/sigmade_php/CRUD/conexion.php');

// 1. Simular registro sin hash
$_POST = [
    'rol' => 'Alumno',
    'nombre' => 'Test Plain',
    'apellidos' => 'Text',
    'iden' => 'PLAIN456',
    'tel' => '0000000000',
    'email' => 'plain@test.com',
    'password' => 'mipassword123'
];
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
include('c:/xampp/htdocs/sigmade_php/CRUD/registrarUsuario.php');
echo "\nRegistro intentado para PLAIN456.\n";

// 2. Verificar en DB
$res = mysqli_query($conn, "SELECT pass FROM usuario WHERE identificador = 'PLAIN456'");
$row = mysqli_fetch_assoc($res);
echo "Password guardado en DB: " . $row['pass'] . " (Debería ser 'mipassword123')\n";

// 3. Simular Login
$_POST = [
    'email' => 'PLAIN456',
    'password' => 'mipassword123'
];
$_SERVER['REQUEST_METHOD'] = 'POST';
// Redirigir la salida de header() para que no de error en CLI
ob_start();
include('c:/xampp/htdocs/sigmade_php/CRUD/procesarLogin.php');
$output = ob_get_clean();

if (isset($_SESSION['usuario_nombre']) && $_SESSION['usuario_nombre'] == 'Test Plain') {
    echo "Login exitoso para el usuario sin hash.\n";
} else {
    echo "Login fallido para el usuario sin hash.\n";
}
?>
