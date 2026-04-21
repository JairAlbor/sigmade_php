<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="css/logoSigmade.png">
    <title>Catálogo de Material</title>
    <link rel="stylesheet" href="css/navBar.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/cssdisenCat.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="js/theme.js?v=<?php echo time(); ?>"></script>
</head>

<body class="sg">
    <?php 
    session_start(); 
    include("CRUD/conexion.php");
    ?>
    

    <nav class="navbar">
        <div class="logo"><img src="css/logoSigmade.png" alt="Logo SIGMADE" width="100px" height="90px"></div>
        <ul class="nav-menu">
            <li class="nav-item" onclick="window.location.href = 'index.php'">Inicio</li>
            
            <?php if (isset($_SESSION['rol'])): ?>
                <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador'): ?>
                    <li class="nav-item" onclick="window.location.href = 'administacion.php'">Administración</li>
                <?php else: ?>
                    <li class="nav-item" onclick="window.location.href = 'Dashboard.php'">Préstamo</li>
                <?php endif; ?>
            <?php endif; ?>
            
            <li class="nav-item active">Catálogo</li>
            
            <?php if (isset($_SESSION['rol'])): ?>
                <li class="nav-item" onclick="window.location.href = 'profile.php'">Perfil</li>
            <?php endif; ?>
        </ul>

        <!-- Right Side: User Menu / Login -->
        <div class="top-bar-user">
            <?php if (isset($_SESSION['usuario_nombre'])) { ?>
            <!-- Pill de usuario -->
            <div class="user-pill">
                <div class="user-avatar">
                    <i data-lucide="user" class="icon-user"></i>
                </div>
                <span id="userName" class="user-name">Hola, <?php echo $_SESSION['usuario_nombre']; ?></span>
            </div>
            <a href="extras/logout.php" class="btn-logout" title="Cerrar Sesión">
                <i data-lucide="log-out" class="icon-logout"></i>
            </a>
            <button class="theme-toggle-btn" title="Alternar Tema"><i data-lucide="sun"></i></button>
            <?php } else { ?>
            <button class="sg-btn-p" onclick="window.location.href='login.php'" style="background: linear-gradient(135deg, var(--crimson) 0%, var(--crimson-light) 100%); color: var(--off-white); border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; text-transform: uppercase; font-size:12px; letter-spacing:1px; white-space:nowrap;">Iniciar Sesión</button>
            <button class="theme-toggle-btn" title="Alternar Tema"><i data-lucide="sun"></i></button>
            <?php } ?>
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
                    
                    <input type="number" name="cantidad" id="cantidad" placeholder="Cantidad" min="1" value="1" required />
                    
                    <textarea name="descripcion" id="descripcion" placeholder="Descripción del material" rows="2" style="grid-column: 1 / -1; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; resize: vertical;" required></textarea>

                    <div class="file-input-wrapper" style="grid-column: 1 / -1;">
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
            <form id="formArticuloAct" action="CRUD/actualizarMat.php" method="post" enctype="multipart/form-data">
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

                    <div class="file-input-wrapper" style="grid-column: 1 / -1; margin-top: 10px;">
                        <label for="imagenAct"><i class="fa-solid fa-image"></i> Cambiar imagen (Opcional)</label>
                        <input type="file" id="imagenAct" name="imagenAct" accept="image/*" />
                    </div>
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
                
                <!-- FILTROS RÁPIDOS -->
                <div class="filtros-rapidos">
                    <select id="filtroDisponibilidad" class="filtro-select">
                        <option value="todos"><i class="fa-solid fa-layer-group"></i> Todos los materiales</option>
                        <option value="Libre"> Disponibles (Libres)</option>
                        <option value="Reservado"> Reservados</option>
                        <option value="Ocupado"> Ocupados</option>
                    </select>

                    <select id="filtroEstado" class="filtro-select">
                        <option value="todos"> Todos los estados</option>
                        <option value="Nuevo"> Nuevo</option>
                        <option value="Bueno"> Bueno</option>
                        <option value="Regular"> Regular</option>
                        <option value="Muy-desgastado"> Muy desgastado</option>
                        <option value="Roto"> Roto</option>
                    </select>

                    <!-- FILTRO POR DISCIPLINA (NUEVO) -->
                    <select id="filtroDisciplina" class="filtro-select">
                        <option value="todos">Todas las disciplinas</option>
                        <?php
                        $qDis = mysqli_query($conn, "SELECT id, nombre FROM disciplina ORDER BY nombre");
                        while ($d = mysqli_fetch_assoc($qDis)) {
                            echo '<option value="' . htmlspecialchars($d['nombre']) . '">' . htmlspecialchars($d['nombre']) . '</option>';
                        }
                        ?>
                    </select>

                    <button class="btn-limpiar-filtros" onclick="limpiarFiltrosCatalogo()">
                        <i class="fa-solid fa-eraser"></i> Limpiar
                    </button>
                </div>

                <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador')) { ?>
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

            <div class="table-responsive">
                <table class="custom-table" id="tablaMateriales">
                    <thead>
                        <tr>
                            <th style="text-align: center; width:90px;"><i class="fa-solid fa-image"></i> Imagen</th>
                            <th><i class="fa-solid fa-barcode"></i> Código</th>
                            <th><i class="fa-solid fa-box"></i> Artículo</th>
                            <th><i class="fa-solid fa-tag"></i> Categoría</th>
                            <th><i class="fa-solid fa-layer-group"></i> Tipo</th>
                            <th><i class="fa-solid fa-align-left"></i> Descripción</th>
                            <th><i class="fa-solid fa-circle-check"></i> Estado</th>
                            <th><i class="fa-solid fa-circle-dot"></i> Disponible</th>
                            <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador')) { ?>
                                <th><i class="fa-solid fa-gears"></i> Acciones</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody id="tabla-cuerpo">
                        <?php
                        /**
                         * Genera un código legible para el material:
                         * Formato: [DIS]-[NOM]-[ID]
                         * Ejemplo: FUT-BAL-00003 (Fútbol, Balón, ID 3)
                         */
                        function generarCodigoMaterial($disciplina, $nombre, $id) {
                            $disPart = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $disciplina), 0, 3));
                            $nomPart = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nombre), 0, 3));
                            $idPart  = str_pad($id, 5, '0', STR_PAD_LEFT);
                            if (empty($disPart)) $disPart = 'GEN';
                            if (empty($nomPart)) $nomPart = 'MAT';
                            return "$disPart-$nomPart-$idPart";
                        }

                        $consulta = 'SELECT m.id, m.nombre AS nombre_material, d.nombre AS nombre_disciplina, 
                                    m.disciplina_id, m.tipoMaterial, m.estado, m.disponible, m.foto_url, m.descripcion
                                    FROM material m 
                                    LEFT JOIN disciplina d ON m.disciplina_id = d.id
                                    ORDER BY d.nombre, m.nombre';
                        $resultado = mysqli_query($conn, $consulta);

                        if ($resultado && mysqli_num_rows($resultado) > 0) {
                            while ($fila = mysqli_fetch_assoc($resultado)) {
                                $claseDisponible = $fila['disponible'] == 'Libre' ? 'disponible-libre' : 'disponible-no-libre';
                                $claseEstado = strtolower(str_replace(' ', '-', $fila['estado']));
                                $fotoUrl = !empty($fila['foto_url']) ? $fila['foto_url'] : 'css/logoSigmade.png';
                                $codigo = generarCodigoMaterial($fila['nombre_disciplina'], $fila['nombre_material'], $fila['id']);
                                
                                echo "<tr data-nombre='" . htmlspecialchars($fila['nombre_material']) . "' 
                                          data-disciplina='" . htmlspecialchars($fila['nombre_disciplina']) . "'
                                          data-tipo='" . htmlspecialchars($fila['tipoMaterial']) . "'>";
                                          
                                echo "<td style='text-align:center;'><img src='" . htmlspecialchars($fotoUrl) . "' style='width: 72px; height: 62px; object-fit: cover; border-radius: 8px;' onerror='this.src=\"css/logoSigmade.png\"'></td>";
                                echo "<td><span style='font-family: monospace; font-size: 0.8rem; background: rgba(139,26,43,0.15); color: var(--crimson-light); padding: 2px 8px; border-radius: 4px; letter-spacing:1px;'>$codigo</span></td>";
                                echo "<td>" . htmlspecialchars($fila['nombre_material']) . "</td>";
                                echo "<td>" . htmlspecialchars($fila['nombre_disciplina']) . "</td>";
                                echo "<td>" . htmlspecialchars($fila['tipoMaterial']) . "</td>";
                                echo "<td style='max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;' title='" . htmlspecialchars($fila['descripcion']) . "'>" . htmlspecialchars($fila['descripcion'] ? $fila['descripcion'] : 'Sin descripción') . "</td>";
                                echo "<td><span class='badge-estado $claseEstado'>" . htmlspecialchars($fila['estado']) . "</span></td>";
                                echo "<td><span class='badge-disponible $claseDisponible'>" . htmlspecialchars($fila['disponible']) . "</span></td>";

                                if (isset($_SESSION['rol']) && ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador')) {
                                    echo '<td class="actions">';
                                    $id = $fila['id'];
                                    $nombre = addslashes(htmlspecialchars($fila['nombre_material'], ENT_QUOTES));
                                    $id_dis = (int)$fila['disciplina_id'];
                                    $estado = addslashes($fila['estado']);
                                    $tipo = addslashes($fila['tipoMaterial']);
                                    $disp = addslashes($fila['disponible']);

                                    echo "<button class='btn-icon edit' title='Editar' onclick='abrirModalEdicion($id, \"$nombre\", $id_dis, \"$estado\", \"$tipo\", \"$disp\")'>
                                            <i class='fa-regular fa-pen-to-square'></i>
                                          </button>";
                                    echo "<button class='btn-icon delete' title='Eliminar' onclick='eliminarMaterial($id)'>
                                            <i class='fa-regular fa-trash-can'></i>
                                          </button>";
                                    echo '</td>';
                                }
                                echo "</tr>";
                            }
                        } else {
                            $colspan = (isset($_SESSION['rol']) && ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador')) ? 9 : 8;
                            echo "<tr><td colspan='$colspan' class='text-center'>No se encontraron materiales.</td></tr>";
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