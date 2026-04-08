<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catálogo de Material</title>
    <link rel="stylesheet" href="css/nav-bar.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/cssdisenCat.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
    <?php 
    session_start(); 
    if (!isset($_SESSION['usuario_nombre'])) {
        header("Location: login.php");
        exit();
    }
    
    include("CRUD/conexion.php");
    ?>

    <nav class="navbar">
        <div class="logo">SIGMADE</div>
        <ul class="nav-menu">
            <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') { ?>
                <li class="nav-item" onclick="window.location.href = 'administacion.php'">Inicio</li>
            <?php } else { ?>
                <li class="nav-item" onclick="window.location.href = 'Dashboard.php'">Inicio</li>
            <?php } ?>
            <li class="nav-item active">Catalogo</li>
            <li class="nav-item" onclick="window.location.href = 'profile.php'">Perfil</li>
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
                <span id="userName" class="user-name"><?php echo isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Usuario'; ?></span>
            </div>
        </div>
    </nav>

    <div class="form-container">
        <div class="header-catalogo">
            <h1>Catálogo de Material</h1>
        </div>

        <!-- MODAL NUEVO MATERIAL -->
        <div id="modal-Nuevo" class="form-card hidden">
            <div class="modal-header-form">
                <h3><i class="fa-solid fa-plus-circle"></i> Nuevo Material</h3>
                <button type="button" class="btn-cerrar-modal" onclick="cerrarModales()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="formArticulo" action="CRUD/insertarMat.php" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <input type="text" name="nombreArticulo" id="nombreArticulo" placeholder="Nombre del artículo" required />
                    
                    <?php
                    $consulta = "SELECT id, nombre FROM disciplina";
                    $resultado = mysqli_query($conn, $consulta);
                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        echo '<select id="disciplina" name="disciplina" required>';
                        echo '<option value="">Seleccionar disciplina</option>';
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            echo '<option value="' . $fila['id'] . '">' . htmlspecialchars($fila['nombre']) . '</option>';
                        }
                        echo '</select>';
                    } else {
                        echo '<select disabled><option>No hay disciplinas disponibles</option></select>';
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

                    <div class="file-input-wrapper">
                        <label for="imagen"><i class="fa-solid fa-image"></i> Imagen del artículo</label>
                        <input type="file" id="imagen" name="imagen" accept="image/*" />
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secundario" onclick="cerrarModales()">Cancelar</button>
                    <button type="submit" class="btn-exito">Guardar Material</button>
                </div>
            </form>
        </div>

        <!-- MODAL ACTUALIZAR MATERIAL (SOLO UNA VEZ) -->
        <div id="modal-Actualizar" class="form-card hidden">
            <div class="modal-header-form">
                <h3><i class="fa-solid fa-pen-to-square"></i> Actualizar Material</h3>
                <button type="button" class="btn-cerrar-modal" onclick="cerrarModales()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="formArticuloAct" action="CRUD/actualizarMat.php" method="post">
                <input type="hidden" name="id" id="idMaterialAct">
                <div class="form-grid">
                    <input type="text" name="nombreArticulo" id="nombreArticuloAct" placeholder="Nombre del artículo" required />

                    <?php
                    $consulta = "SELECT id, nombre FROM disciplina";
                    $resultado = mysqli_query($conn, $consulta);
                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                        echo '<select id="disciplinaAct" name="disciplinaAct" required>';
                        echo '<option value="">Seleccionar disciplina</option>';
                        while ($fila = mysqli_fetch_assoc($resultado)) {
                            echo '<option value="' . $fila['id'] . '">' . htmlspecialchars($fila['nombre']) . '</option>';
                        }
                        echo '</select>';
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
                    <button type="button" class="btn-secundario" onclick="cerrarModales()">Cancelar</button>
                    <button type="submit" class="btn-exito">Guardar Cambios</button>
                </div>
            </form>
        </div>

        <!-- TABLA DE MATERIALES -->
        <div class="tabla-container">
            <div class="controls-container">
                <div class="search-bar-container">
                    <div class="search-input-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Buscar material..." id="searchMaterial">
                    </div>
                </div>
                
                <!-- FILTROS RÁPIDOS (AGREGADOS) -->
                <div class="filtros-rapidos">
                    <select id="filtroDisponibilidad" class="filtro-select">
                        <option value="todos">📋 Todos los materiales</option>
                        <option value="Libre">✅ Disponibles (Libres)</option>
                        <option value="Reservado">🔄 Reservados</option>
                        <option value="Ocupado">❌ Ocupados</option>
                    </select>
                    
                    <select id="filtroEstado" class="filtro-select">
                        <option value="todos"> Todos los estados</option>
                        <option value="Nuevo"> Nuevo</option>
                        <option value="Bueno"> Bueno</option>
                        <option value="Regular"> Regular</option>
                        <option value="Muy-desgastado">🔧 Muy desgastado</option>
                        <option value="Roto"> Roto</option>
                    </select>
                    
                    <button class="btn-limpiar-filtros" onclick="limpiarFiltrosCatalogo()">
                        <i class="fa-solid fa-eraser"></i> Limpiar
                    </button>
                </div>
                
                <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') { ?>
                    <button id="btn-abrir-formulario" class="btn-principal">
                        <i class="fa-solid fa-plus"></i> Agregar Material
                    </button>
                <?php } ?>
            </div>

            <!-- TARJETAS DE ESTADÍSTICAS -->
            <div class="stats-cards">
                <div class="stat-card total">
                    <span class="stat-label">Total artículos</span>
                    <span class="stat-number" id="articulos">
                        <?php
                        $consulta = 'SELECT COUNT(*) AS total FROM material';
                        $resultado = mysqli_query($conn, $consulta);
                        if ($resultado && mysqli_num_rows($resultado) > 0) {
                            $datos = mysqli_fetch_assoc($resultado);
                            echo $datos['total'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </span>
                </div>

                <div class="stat-card available">
                    <span class="stat-label">Disponibles</span>
                    <span class="stat-number" id="disponibles">
                        <?php
                        $consulta = 'SELECT COUNT(*) AS disponibles FROM material WHERE disponible = "Libre"';
                        $resultado = mysqli_query($conn, $consulta);
                        if ($resultado && mysqli_num_rows($resultado) > 0) {
                            $datos = mysqli_fetch_assoc($resultado);
                            echo $datos['disponibles'];
                        } else {
                            echo "0";
                        }
                        ?>
                    </span>
                </div>

                <div class="stat-card reserved">
                    <span class="stat-label">Reservados/Ocupados</span>
                    <span class="stat-number" id="reservados">
                        <?php
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

            <!-- CONTENEDOR DE BADGES DE FILTROS ACTIVOS -->
            <div class="filtros-activos-badge"></div>

            <!-- TABLA -->
            <div class="table-responsive">
                <table class="material-table" id="tablaMateriales">
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
                        $consulta = 'SELECT m.id, m.nombre AS nombre_material, d.nombre AS nombre_disciplina, 
                                    m.disciplina_id, m.tipoMaterial, m.estado, m.disponible 
                                    FROM material m 
                                    JOIN disciplina d ON m.disciplina_id = d.id';
                        $resultado = mysqli_query($conn, $consulta);

                        if ($resultado && mysqli_num_rows($resultado) > 0) {
                            while ($fila = mysqli_fetch_assoc($resultado)) {
                                $claseDisponible = $fila['disponible'] == 'Libre' ? 'disponible-libre' : 'disponible-no-libre';
                                $claseEstado = strtolower(str_replace(' ', '-', $fila['estado']));
                                echo "<tr data-nombre='" . htmlspecialchars($fila['nombre_material']) . "' 
                                          data-disciplina='" . htmlspecialchars($fila['nombre_disciplina']) . "'
                                          data-tipo='" . htmlspecialchars($fila['tipoMaterial']) . "'>";
                                echo "<td>" . htmlspecialchars($fila['nombre_material']) . "</td>";
                                echo "<td>" . htmlspecialchars($fila['nombre_disciplina']) . "</td>";
                                echo "<td>" . htmlspecialchars($fila['tipoMaterial']) . "</td>";
                                echo "<td><span class='badge-estado $claseEstado'>" . htmlspecialchars($fila['estado']) . "</span></td>";
                                echo "<td><span class='badge-disponible $claseDisponible'>" . htmlspecialchars($fila['disponible']) . "</span></td>";

                                if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') {
                                    echo '<td class="actions">';
                                    $id = $fila['id'];
                                    $nombre = addslashes(htmlspecialchars($fila['nombre_material'], ENT_QUOTES));
                                    $id_dis = (int)$fila['disciplina_id'];
                                    $estado = addslashes($fila['estado']);
                                    $tipo = addslashes($fila['tipoMaterial']);
                                    $disp = addslashes($fila['disponible']);

                                    echo "<button class='btn-icon edit' onclick='abrirModalEdicion($id, \"$nombre\", $id_dis, \"$estado\", \"$tipo\", \"$disp\")'>
                                            <i class='fa-regular fa-pen-to-square'></i>
                                          </button>";
                                    echo "<button class='btn-icon delete' onclick='eliminarMaterial($id)'>
                                            <i class='fa-regular fa-trash-can'></i>
                                          </button>";
                                    echo '</td>';
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No se encontraron materiales.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    <script src="modal-js/modal-catalogo.js"></script>
</body>

</html>