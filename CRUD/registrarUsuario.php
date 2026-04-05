<?php
include("conexion.php"); // Tu archivo de conexión que hicimos antes

//con el siguiente codigo podremos insertar un nuevo usuario a la base de datos, tomando los datos del formulario de registro. Luego de insertar, redirigimos al login para que el nuevo usuario pueda iniciar sesión con sus credenciales
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $identificador = $_POST['iden'];
    $telefono = $_POST['tel'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    $estatus = 'Activo'; // Establecer estatus por defecto

    $fechaCreacion = date("Y-m-d"); // Fecha de creación


    //validamos que no haya campos vacíos
    if (empty($nombre) || empty($apellidos) || empty($identificador) || empty($telefono) || empty($email) || empty($password) || empty($rol)) {
        // Si hay campos vacíos, redirigimos al registro con un error
        header("Location: ../login.php?error=1");
        //mandamos un alert con javascript para que el usuario sepa que debe llenar todos los campos
        echo "<script>alert('Por favor, llena todos los campos.');</script>";
        exit();
    }

    //validamos que el correo o el identificador no estén ya registrados en la base de datos
    $consultaVerificacion = "SELECT * FROM usuarios WHERE email = '$email' OR identificador = '$identificador'";
    $resultadoVerificacion = mysqli_query($conexion, $consultaVerificacion);

    if (mysqli_num_rows($resultadoVerificacion) > 0) {
        // Si ya existe un usuario con ese correo o identificador, redirigimos al registro con un error
        header("Location: ../login.php?error=2");
        //mandamos un alert con javascript para que el usuario sepa que el correo o identificador ya están registrados
        echo "<script>alert('El correo o identificador ya están registrados. Por favor, elige otro.');</script>";
        exit();
    }

    // Si no hay duplicados, procedemos a insertar el nuevo usuario


    $consulta = "INSERT INTO usuarios (identificador, nombre, apellidos, email, pass, telefono, estatus, created_at) VALUES ('$nombre', '$apellidos', '$identificador', '$telefono', '$email', '$password', '$estatus', '$fechaCreacion')";

    if (mysqli_query($conexion, $consulta)) {
        //mandamos un alert con javascript para que el usuario sepa que el registro fue exitoso
        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.');</script>";
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}


?>

