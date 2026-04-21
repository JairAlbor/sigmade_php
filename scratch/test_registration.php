<?php
$_POST = [
    'rol' => 'Alumno',
    'nombre' => 'Test',
    'apellidos' => 'Simulado',
    'iden' => 'SIM123',
    'tel' => '1234567890',
    'email' => 'sim@test.com',
    'password' => '123456'
];
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

// Cambiar el directorio de trabajo para que conexion.php funcione si usa rutas relativas
chdir('c:/xampp/htdocs/sigmade_php/CRUD');
include('registrarUsuario.php');
?>
