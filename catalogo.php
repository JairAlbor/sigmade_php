<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catálogo de Material</title>
    <link rel="stylesheet" href="css/nav-bar.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/ncssCat.css"/>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://unpkg.com/lucide@latest"></script>

</head>

<body>
    <?php session_start(); 
    if (!isset($_SESSION['usuario_nombre'])) {
        // Si el usuario no ha iniciado sesión, redirigir al login
        header("Location: login.php");
        exit();
    }
    
    ?>
    <nav class="navbar">
        <div class="logo">SIGMADE</div>

        <ul class="nav-menu">
            <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') { ?>
                <li class="nav-item" onclick="window.location.href = 'administacion.php'">
                    Inicio
                </li>
            <?php } else { ?>
                <li class="nav-item" onclick="window.location.href = 'Dashboard.php'">
                    Inicio
                </li>
            <?php } ?>

            <li class="nav-item active">Catalogo</li>
            <li class="nav-item" onclick="window.location.href = 'profile.php'">
                Perfil
            </li>
        </ul>

        <div class="top-bar-user">
            <div class="notification-wrapper">
                <i data-lucide="bell" class="icon-bell"></i>
                <span class="notification-dot"></span>
            </div>

            <div class="user-pill">
                <div class="user-avatar">
                    <i data-lucide="user" class="icon-user"></i>
                </div>
                <!-- Aquí mostramos el nombre del usuario desde la sesión, o "Usuario" si no está definido -->
                <span id="userName" class="user-name"><?php echo isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Usuario'; ?></span>
            </div>
        </div>
    </nav>

    <div class="form-container">
        <div class="header-catalogo">
            <h1>Catálogo de Material</h1>

        </div>
        <div id="modal-Nuevo" class="form-card hidden">
            <h3>Nuevo Material</h3>
            <div class="form-grid">
                <form id="formArticulo" action="CRUD/insertarMat.php" method="post">
                    <input type="text" name="nombreArticulo" id="nombreArticulo" placeholder="Nombre del artículo" required />

                    <?php
                    //haremos un select para obtener las disciplinas de la base de datos y mostrarlas en el formulario
                    include("CRUD/conexion.php");
                    $consulta = "SELECT id, nombre FROM disciplina";
                    $resultado = mysqli_query($conn, $consulta);
                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        echo '<select id="disciplina" name="disciplina" required>';
                        echo '<option value="">Seleccionar disciplina</option>';
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            echo '<option value="' . $fila['id'] . '">' . $fila['nombre'] . '</option>';
                        }
                        echo '</select>';
                    } else {
                        echo '<p>No se encontraron disciplinas.</p>';
                    }
                    ?>



                    <select name="estado" id="estado" required>
                        <option value="">Seleccionar estado</option>
                        <option value="Nuevo">Nuevo</option>
                        <option value="Bueno">Bueno</option>
                        <option value="Regular">Regular</option>
                        <option value="Muy-desgastado">Muy Desgastado</option>
                        <option value="Roto">Roto</option>
                    </select>

                    <select name="tipoMaterial" id="tipoMaterial" required>
                        <option value="">Seleccionar tipo de material</option>
                        <option value="Material deportivo">Material deportivo</option>
                        <option value="Cancha">Cancha</option>
                    </select>

                    <div class="stat-label">
                        <label for="imagen">Ingresa una imagen del artículo</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*" />
                    </div>
            </div>

            <div class="form-actions">
                <button type="button" id="btn-cancelar" class="btn-secundario">Cancelar</button>
                <button type="submit" id="btn-exito" class="btn-exito">Guardar Material</button>
            </div>
            </form>
        </div>

        <!--codigo para el formulario de actualizar-->
        <div id="modal-Actualizar" class="form-card hidden">
            <h3>Actualizar Material</h3>
            <div class="form-grid">
                <form id="formArticuloAct" action="CRUD/actualizarMat.php" method="post">

                <input type="hidden" name="id" id="idMaterialAct">

                    <input type="text" name="nombreArticulo" id="nombreArticuloAct" placeholder="Nombre del artículo" required />

                     <?php
                    //haremos un select para obtener las disciplinas de la base de datos y mostrarlas en el formulario
                    include("CRUD/conexion.php");
                    $consulta = "SELECT id, nombre FROM disciplina";
                    $resultado = mysqli_query($conn, $consulta);
                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        echo '<select id="disciplinaAct" name="disciplinaAct" required>';
                        echo '<option value="">Seleccionar disciplina</option>';
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            echo '<option value="' . $fila['id'] . '">' . $fila['nombre'] . '</option>';
                        }
                        echo '</select>';
                    } else {
                        echo '<p>No se encontraron disciplinas.</p>';
                    }
                    ?>

                    <select name="estadoAct" id="estadoAct" required>
                        <option value="">Seleccionar estado</option>
                        <option value="Nuevo">Nuevo</option>
                        <option value="Bueno">Bueno</option>
                        <option value="Regular">Regular</option>
                        <option value="Muy-desgastado">Muy Desgastado</option>
                        <option value="Roto">Roto</option>
                    </select>

                    <select name="tipoMaterialAct" id="tipoMaterialAct" required>
                        <option value="">Seleccionar tipo de material</option>
                        <option value="Material deportivo">Material deportivo</option>
                        <option value="Cancha">Cancha</option>
                    </select>

                    <select name="disponibilidadAct" id="disponibilidadAct" required>
                        <option value="Libre">Libre</option>
                        <option value="Reservado">Reservado</option>
                        <option value="Ocupado">Ocupado</option>
                    </select>
                    
            </div>

            <div class="form-actions">
                <button type="button" id="btn-cancelar" class="btn-secundario">Cancelar</button>
                <button type="submit" id="btn-guardar-actualizacion" class="btn-exito">Guardar Cambios</button>
            </div>
            </form>
        </div>



        <div class="tabla-container">

            <div class="controls-container">
                <div class="search-bar-container">
                    <div class="search-input-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Buscar material..." id="searchMaterial">
                    </div>
                </div>
                <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') { ?>
                    <button id="btn-abrir-formulario" class="btn-principal">
                        + Agregar Material
                    </button>
                <?php } ?>

                <div class="stats-cards">
                    <div class="stat-card total">

                        <span class="stat-label">Total artículos</span>
                        <span class="stat-number" id="articulos">
                            <?php include("CRUD/conexion.php");

                            // 1. Definir la consulta
                            $consulta = 'SELECT COUNT(*) AS total FROM material';

                            // 2. Ejecutar consulta
                            $resultado = mysqli_query($conn, $consulta);
                            // 3. Verificar si hay resultados y mostrar los datos
                            if ($resultado && mysqli_num_rows($resultado) > 0) {
                                $datos = mysqli_fetch_assoc($resultado);
                                echo $datos['total'];
                            } else {
                                echo "0";
                            }
                            ?></span>

                    </div>


                    <div class="stat-card available">

                        <span class="stat-label">Disponibles</span>
                        <span class="stat-number" id="disponibles"><?php
                                                                    //imprintamos el numero de articulos disponibles
                                                                    include("CRUD/conexion.php");
                                                                    // 1. Definir la consulta
                                                                    $consulta = 'SELECT COUNT(*) AS disponibles FROM material WHERE disponible = "Libre"';
                                                                    // 2. Ejecutar consulta
                                                                    $resultado = mysqli_query($conn, $consulta);
                                                                    // 3. Verificar si hay resultados y mostrar los datos
                                                                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                                                                        $datos = mysqli_fetch_assoc($resultado);
                                                                        echo $datos['disponibles'];
                                                                    } else {
                                                                        echo "0";
                                                                    }
                                                                    ?></span>

                    </div>

                    <div class="stat-card reserved">
                        <span class="stat-label">Reservados</span>
                        <span class="stat-number" id="reservados">
                            <?php
                            include("CRUD/conexion.php");

                            // Filtramos por los que NO están libres (o podrías usar WHERE disponible = 'Prestado')
                            $consulta = 'SELECT COUNT(*) AS reservados FROM material WHERE disponible != "Libre"';

                            $resultado = mysqli_query($conn, $consulta);

                            if ($resultado && mysqli_num_rows($resultado) > 0) {
                                $datos = mysqli_fetch_assoc($resultado);
                                echo $datos['reservados'];
                            } else {
                                echo "0";
                            }
                            ?>
                        </span>
                    </div>

                </div>
            </div>

            <div class="table-responsive">
                <table class="material-table">
                    <thead>
                        <tr>
                            <th>Artículo</th>
                            <th>Categoría</th>
                            <th>Tipo Material</th>
                            <th>Estado</th>
                            <th>Disponible</th>
                            <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') { ?>
                                <th>Acciones</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="tabla-cuerpo">
                       <?php
include("CRUD/conexion.php");

// 1. Definir la consulta (Asegúrate de traer m.disciplina_id para el modal)
$consulta = 'SELECT m.id, m.nombre AS nombre_material, d.nombre AS nombre_disciplina, m.disciplina_id, m.tipoMaterial, m.estado, m.disponible 
             FROM material m 
             JOIN disciplina d ON m.disciplina_id = d.id';

// 2. Ejecutar consulta
$resultado = mysqli_query($conn, $consulta);

// 3. Verificar si hay resultados
if ($resultado && mysqli_num_rows($resultado) > 0) {
    while ($fila = mysqli_fetch_assoc($resultado)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($fila['nombre_material']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['nombre_disciplina']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['tipoMaterial']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['estado']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['disponible']) . "</td>";

        if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') {
            echo '<td class="actions">';
            
            // Preparamos los datos para evitar errores de comillas en JS
            $id = $fila['id'];
            $nombre = addslashes(htmlspecialchars($fila['nombre_material'], ENT_QUOTES));
            $id_dis = (int)$fila['disciplina_id'];
            $estado = addslashes($fila['estado']);
            $tipo = addslashes($fila['tipoMaterial']);
            $disp = addslashes($fila['disponible']);

            // Botón Editar con concatenación limpia
            echo "<button class='btn-icon edit' onclick=\"abrirModalEdicion($id, '$nombre', $id_dis, '$estado', '$tipo', '$disp')\">
                    <i class='fa-regular fa-pen-to-square'></i>
                  </button>";

            // Botón Eliminar
            echo ' <a href="CRUD/eliminarMat.php?id=' . $id . '" 
                       class="btn-icon delete" 
                       onclick="return confirm(\'¿Estás seguro de que deseas eliminar este material?\')">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>';
            
            echo '</td>';
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No se encontraron materiales.</td></tr>";
}
?>
                    </tbody>
                </table>
            </div>




        </div>
    </div>





    <script>
        lucide.createIcons();
    </script>

    <script src="modal-js//modal-catalogo.js"></script>
</body>

</html>