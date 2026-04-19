<?php
include("conexion.php"); // Tu archivo de conexión que hicimos antes
session_start(); // ¡Importante! Esto permite que el sistema "recuerde" al usuario

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['email'];
    $pass = $_POST['password'];

    // Buscamos al usuario en la base de datos
    // Nota: Aquí asumo que tu tabla se llama 'usuarios' y tiene campos 'email' y 'password'
    $consulta = "SELECT * FROM usuario WHERE email = '$user' OR identificador = '$user' AND pass = '$pass'";
    $resultado = mysqli_query($conn, $consulta);

//si hay una coincidencia, extraemos nombre y rol del usuario y los guardamos en la sesión, luego redirigimos al panel principal. Si no, lo mandamos de vuelta al login con un error
    if (mysqli_num_rows($resultado) > 0) {
        // Si hay una coincidencia, extraemos los datos
        $datos_usuario = mysqli_fetch_array($resultado);
        
        // Guardamos su nombre y rol en la "Sesión"
        $_SESSION['usuario_nombre'] = $datos_usuario['nombre'];
        $_SESSION['rol'] = $datos_usuario['rol'];
        $_SESSION['user_id'] = $datos_usuario['id'];

        // Redirigimos a la página de inicio para que el usuario pueda visualizar la landing page
        header("Location: ../index.php");
        exit();
    } else {
        // Si falló, lo mandamos de vuelta con un error
        header("Location: login.php?error=1");
    }

   
}
?>