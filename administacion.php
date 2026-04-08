<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard de Administración</title>
  <link rel="stylesheet" href="css/nav-bar.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/cssAdmin.css" />
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

  // Estadísticas
  $resTotal = mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuario");
  $total = mysqli_fetch_assoc($resTotal)['total'];

  $resActivos = mysqli_query($conn, "SELECT COUNT(*) AS activos FROM usuario WHERE estatus = 'Activo'");
  $activos = mysqli_fetch_assoc($resActivos)['activos'];

  $inactivos = $total - $activos;

  $consultaUsers = "SELECT * FROM usuario";
  $resultadoUsers = mysqli_query($conn, $consultaUsers);
  ?>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="logo">SIGMADE</div>
    <ul class="nav-menu">
      <li class="nav-item active">Inicio</li>
      <li class="nav-item" onclick="window.location.href = 'catalogo.php'">Catalogo</li>
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

  <main class="form-container">
    <header class="dashboard-header">
      <h2 class="admin-title">Panel de Administración</h2>
    </header>

    <div class="main-grid">
      <section class="card-featured">
        <div class="card-header">
          <h3>Préstamos Activos</h3>
          <i class="fa-solid fa-book-open icon-large"></i>
        </div>
        <div class="card-body">
          <div class="big-number" id="prestamosActivosCount">0</div>
          <p class="subtitle">Préstamos en curso</p>
          <div class="status-list" id="estadisticasPrestamos">
            <div class="status-item">Cargando...</div>
          </div>
        </div>
        <button id="openModalBtn" class="btn-guinda" onclick="openPrestamosModal()">
          Abrir Gestión de Prestamos
        </button>
      </section>

      <div class="secondary-grid">
        <button class="card-small" onclick="toggleModal('modalDisciplinas')">
          <i class="fa-solid fa-dumbbell"></i>
          <h3>Disciplinas</h3>
          <p class="count">
            <?php
            $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM disciplina");
            $row = mysqli_fetch_assoc($result);
            echo $row['total'];
            ?>
          </p>
        </button>

        <button class="card-small" onclick="toggleModal('modalUsuarios')">
          <i class="fa-solid fa-users"></i>
          <h3>Usuarios</h3>
          <p class="count" id="totalUser">
            <?php
            $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuario");
            $row = mysqli_fetch_assoc($result);
            echo $row['total'];
            ?>
          </p>
        </button>

        <button class="card-small" onclick="toggleModal('modalEventos')">
          <i class="fa-solid fa-calendar-days"></i>
          <h3>Eventos</h3>
          <p class="count">5</p>
        </button>

        <button class="card-small" onclick="toggleModal('modalEntrenadores')">
          <i class="fa-solid fa-user-gear"></i>
          <h3>Entrenadores</h3>
          <p class="count">
            <?php
            $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuario WHERE rol = 'Docente'");
            $row = mysqli_fetch_assoc($result);
            echo $row['total'];
            ?>
          </p>
        </button>
      </div>
    </div>
  </main>

  <!-- MODAL PRESTAMOS COMPLETO -->
  <div id="modalPrestamos" class="modal-overlay hidden">
    <div class="modal-content modal-large">
      <header class="modal-header">
        <h2><i class="fa-solid fa-book-open"></i> Gestión de Préstamos</h2>
        <div class="header-actions">
          <button class="btn-guinda" onclick="mostrarFormularioPrestamo()">
            <i class="fa-solid fa-plus"></i> Nuevo Préstamo
          </button>
          <button class="close-modal-btn" onclick="toggleModal('modalPrestamos')">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </header>

      <div class="modal-body">
        <!-- Controles de filtrado y búsqueda -->
        <div class="controls-container">
          <div class="search-bar-container">
            <div class="search-input-wrapper">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="buscarPrestamo" placeholder="Buscar por usuario, materiales o estado...">
            </div>
          </div>
          <button class="btn-secondary" onclick="toggleFiltrosPrestamos()">
            <i class="fa-solid fa-filter"></i> Filtros
          </button>
        </div>

        <!-- Filtros avanzados -->
        <div id="filtrosPrestamos" class="filtros-avanzados hidden">
          <div class="filtros-header">
            <i class="fa-solid fa-sliders-h"></i>
            <span>Filtros Avanzados</span>
            <button class="btn-limpiar-filtros" onclick="limpiarFiltrosPrestamos()">
              <i class="fa-solid fa-eraser"></i> Limpiar filtros
            </button>
          </div>
          <div class="filtros-grid">
            <div class="filtro-group">
              <label class="filtro-label">
                <i class="fa-solid fa-flag"></i> Estado
              </label>
              <select id="filtroEstadoPrestamo" class="filtro-select" onchange="filtrarPrestamos()">
                <option value="">Todos</option>
                <option value="Activo">Activo</option>
                <option value="Vencido">Vencido</option>
                <option value="Devuelto">Devuelto</option>
                <option value="Renovado">Renovado</option>
              </select>
            </div>
            <div class="filtro-group">
              <label class="filtro-label">
                <i class="fa-solid fa-calendar"></i> Fecha inicio
              </label>
              <input type="date" id="filtroFechaInicio" class="filtro-select" onchange="filtrarPrestamos()">
            </div>
            <div class="filtro-group">
              <label class="filtro-label">
                <i class="fa-solid fa-calendar"></i> Fecha fin
              </label>
              <input type="date" id="filtroFechaFin" class="filtro-select" onchange="filtrarPrestamos()">
            </div>
          </div>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="stats-cards">
          <div class="stat-card total">
            <span class="stat-label">Total Préstamos</span>
            <span class="stat-number" id="totalPrestamos">0</span>
          </div>
          <div class="stat-card available">
            <span class="stat-label">Préstamos Activos</span>
            <span class="stat-number" id="prestamosActivos">0</span>
          </div>
          <div class="stat-card reserved">
            <span class="stat-label">Vencidos</span>
            <span class="stat-number" id="prestamosVencidos">0</span>
          </div>
        </div>

       <!-- Tabla de préstamos modificada -->
<div class="table-responsive">
  <table class="custom-table">
    <thead>
      <tr>
        <th>Usuario</th>
        <th>Materiales</th>
        <th>Fecha Solicitud</th>
        <th>Fecha Límite</th>
        <th>Estado</th>
        <th>Días Restantes</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody id="tablaPrestamosBody">
      <tr>
        <td colspan="7" style="text-align: center;">Cargando préstamos...</td>
      </tr>
    </tbody>
  </table>
</div>
      </div>

      <footer class="modal-footer">
        <button class="btn-secondary" onclick="toggleModal('modalPrestamos')">Cerrar</button>
      </footer>
    </div>
  </div>

 <!-- MODAL FORMULARIO NUEVO PRÉSTAMO -->
<div id="modalFormPrestamo" class="modal-overlay hidden">
  <div class="modal-content modal-medium">
    <header class="modal-header">
      <h2><i class="fa-solid fa-hand-holding"></i> Registrar Nuevo Préstamo</h2>
      <button class="close-modal-btn" onclick="toggleModal('modalFormPrestamo')">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </header>
    <div class="modal-body">
      <form id="formNuevoPrestamo" class="form-diseno">

        <div class="form-group">
          <label for="usuarioPrestamo">Usuario *</label>
          <select id="usuarioPrestamo" name="usuario_id" required>
            <option value="">Seleccione un usuario</option>
          </select>
        </div>

        <div class="form-group custom-dropdown-group">
          <label>Material(es) disponibles *</label>
          <div class="custom-dropdown" id="dropdownMateriales">
            <div class="custom-dropdown-header" onclick="toggleMaterialesDropdown(event)">
              <span id="dropdownMaterialesTexto">Seleccione materiales...</span>
              <i class="fa-solid fa-chevron-down toggle-icon"></i>
            </div>
            
            <div class="custom-dropdown-body hidden" id="dropdownMaterialesCuerpo">
              <div class="search-input-wrapper" style="margin: 8px;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="buscarMaterial" placeholder="Buscar material...">
              </div>
              
              <!-- Lista de checkboxes -->
              <div id="listaMateriales" class="lista-materiales-check drop-version">
                <p class="text-muted" style="padding:10px;">Cargando materiales...</p>
              </div>
            </div>
          </div>
          <!-- Resumen de seleccionados -->
          <div id="resumenSeleccion" class="resumen-seleccion hidden" style="margin-top: 8px;">
            <i class="fa-solid fa-circle-check"></i>
            <span id="textoResumen">0 materiales seleccionados</span>
          </div>
        </div>

        <div class="form-group">
          <label for="fechaLimite">Fecha Límite *</label>
          <input type="date" id="fechaLimite" name="fecha_limite" required>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-secondary" onclick="toggleModal('modalFormPrestamo')">Cancelar</button>
          <button type="submit" class="btn-guinda">
            <i class="fa-solid fa-check"></i> Registrar Préstamo
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

  <!-- MODAL EVENTOS -->
  <div id="modalEventos" class="modal-overlay hidden">
    <div class="modal-content">
      <header class="modal-header">
        <h2>Gestión de Eventos</h2>
        <div class="header-actions">
          <button class="btn-guinda" onclick="toggleFormEventos()">
            <i class="fa-solid fa-plus"></i> Registrar Evento
          </button>
          <button class="close-modal-btn" onclick="toggleModal('modalEventos')">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </header>
      <div class="modal-body">
        <section id="addEventForm" class="add-form-container hidden">
          <h3>Nuevo Evento</h3>
          <div class="form-grid">
            <input type="text" placeholder="Título del evento" id="eventTitle">
            <input type="text" placeholder="Ubicación" id="eventLocation">
            <input type="date" id="eventDate">
            <input type="time" id="eventTime">
          </div>
          <textarea placeholder="Descripción del evento" rows="3" id="eventDesc"></textarea>
          <div class="form-actions">
            <button class="btn-cancel" onclick="toggleFormEventos()">Cancelar</button>
            <button class="btn-save" onclick="saveEvent()">Guardar Evento</button>
          </div>
        </section>
        <div id="eventList" class="event-list-container"></div>
      </div>
      <footer class="modal-footer">
        <button class="btn-secondary" onclick="toggleModal('modalEventos')">Cerrar</button>
      </footer>
    </div>
  </div>

  <!-- MODAL USUARIOS -->
  <div id="modalUsuarios" class="modal-overlay hidden">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="tituloModalUsuarios"><i class="fa-solid fa-users-gear"></i> Usuarios Registrados</h2>
        <button class="btn-cerrar-x" onclick="toggleModal('modalUsuarios')">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="modal-body">
        <!-- Vista Tabla de Usuarios -->
        <div id="vistaTablaUsuarios">
          <div class="controls-container">
            <div class="search-bar-container">
              <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="buscarUsuario" placeholder="Buscar por nombre, email o rol...">
              </div>
            </div>
            <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') { ?>
              <button id="btn-abrir-formulario" class="btn-principal" onclick="mostrarFormularioRegistro()">
                + Agregar Usuario
              </button>
            <?php } ?>
          </div>

          <div class="stats-cards">
            <div class="stat-card total">
              <span class="stat-label">Total Usuarios registrados</span>
              <span class="stat-number" id="articulos">
                <?php
                $consulta = 'SELECT COUNT(*) AS total FROM usuario';
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
              <span class="stat-label">Activos</span>
              <span class="stat-number" id="userActivos">
                <?php
                $consulta = 'SELECT COUNT(*) AS disponibles FROM usuario WHERE estatus = "Activo"';
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
              <span class="stat-label">Sancionados</span>
              <span class="stat-number" id="userSancionados">
                <?php
                $consulta = 'SELECT COUNT(*) AS estado FROM usuario WHERE estatus != "Activo"';
                $resultado = mysqli_query($conn, $consulta);
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                  $datos = mysqli_fetch_assoc($resultado);
                  echo $datos['estado'];
                } else {
                  echo "0";
                }
                ?>
              </span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="custom-table">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Teléfono</th>
                  <th>Rol</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaUsuariosBody">
                <?php
                $sqlUsers = "SELECT id, nombre, email, rol, telefono, estatus FROM usuario";
                $resUsers = mysqli_query($conn, $sqlUsers);

                while ($user = mysqli_fetch_assoc($resUsers)) {
                  $claseEstatusActual = ($user['estatus'] == 'Activo') ? 'status-active' : 'status-inactive';

                  echo "<tr id='user-row-" . $user['id'] . "'>";
                  echo "<td>" . htmlspecialchars($user['nombre']) . "</td>";
                  echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                  echo "<td>" . htmlspecialchars($user['telefono']) . "</td>";
                  echo "<td class='user-rol-cell'>" . htmlspecialchars($user['rol']) . "</td>";
                  echo "<td>";
                  echo "<form action='CRUD/actualizarEstatusUsuario.php' method='POST' style='display:inline;'>";
                  echo "<input type='hidden' name='id' value='" . $user['id'] . "'>";
                  echo "<select name='estatus' class='status-pill $claseEstatusActual' onchange='this.form.submit()'>";
                  echo "<option value='Activo' " . ($user['estatus'] == 'Activo' ? 'selected' : '') . ">Activo</option>";
                  echo "<option value='Sancionado' " . ($user['estatus'] == 'Sancionado' ? 'selected' : '') . ">Sancionado</option>";
                  echo "</select>";
                  echo "</form>";
                  echo "</td>";
                  echo "<td class='actions'>";
                  echo "<button class='btn-icon edit' onclick=\"mostrarFormularioEditar('" . $user['id'] . "', '" . addslashes($user['nombre']) . "', '" . $user['email'] . "', '" . $user['rol'] . "')\">";
                  echo "<i class='fa-solid fa-pen-to-square'></i>";
                  echo "</button>";
                  echo "<button class='btn-icon delete' onclick=\"eliminarUsuario(" . $user['id'] . ")\">";
                  echo "<i class='fa-solid fa-trash-can'></i>";
                  echo "</button>";
                  echo "</td>";
                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Vista Formulario Edición -->
        <div id="vistaFormularioUsuario" class="hidden">
          <button class="btn-regresar" onclick="mostrarTablaUsuarios()">
            <i class="fa-solid fa-arrow-left"></i> Volver a la lista
          </button>

          <form id="formEditarUsuario" action="CRUD/actualizarUsuarioCompleto.php" method="POST" class="form-diseno">
            <input type="hidden" name="id_usuario" id="edit_id_usuario">

            <div class="form-group">
              <label>Nombre Completo</label>
              <input type="text" name="nombre" id="edit_nombre" required>
            </div>

            <div class="form-group">
              <label>Correo Electrónico</label>
              <input type="email" name="email" id="edit_email" required>
            </div>

            <div class="form-group">
              <label>Rol de Usuario</label>
              <select name="rol" id="edit_rol">
                <option value="Admin">Administrador</option>
                <option value="Operador">Operador</option>
                <option value="Alumno">Alumno</option>
                <option value="Docente">Entrenador</option>
              </select>
            </div>

            <div class="form-actions-edit">
              <button type="submit" class="btn-guardar-cambios">Guardar Cambios</button>
            </div>
          </form>
        </div>

        <!-- Vista Registro Usuario -->
        <div id="vistaRegistroUsuario" class="hidden">
          <button class="btn-regresar" onclick="mostrarTablaUsuarios()">
            <i class="fa-solid fa-arrow-left"></i> Volver a la lista
          </button>

          <h2><i class="fa-solid fa-user-plus"></i> Registrar Nuevo Usuario</h2>

          <form id="formUsuarios" action="CRUD/registrarUsuario.php" method="POST">
            <div class="form-group">
              <label for="rol">Tipo de usuario:</label>
              <select id="rol" name="rol" required>
                <option value="" disabled selected>Selecciona una opción</option>
                <option value="Alumno">Alumno</option>
                <option value="Docente">Entrenador</option>
                <option value="Admin">Administrador</option>
                <option value="Operador">Operador</option>
              </select>
            </div>

            <div class="form-group">
              <label for="nombre">Nombre(s)</label>
              <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Carlos" required />
            </div>

            <div class="form-group">
              <label for="apellidos">Apellidos</label>
              <input type="text" id="apellidos" name="apellidos" placeholder="Ej. Pérez García" required />
            </div>

            <div class="form-group">
              <label for="iden">ID Institucional</label>
              <input type="text" id="iden" name="iden" placeholder="Matrícula o número de trabajador" required />
            </div>

            <div class="form-group">
              <label for="tel">Número telefónico</label>
              <input type="tel" id="tel" name="tel" placeholder="10 dígitos (ej. 9511234567)" pattern="[0-9]{10}" required />
            </div>

            <div class="form-group">
              <label for="correo">Correo electrónico</label>
              <input type="email" id="correo" name="email" placeholder="usuario@utm.mx" required />
            </div>

            <div class="form-group">
              <label for="passwordUs">Contraseña</label>
              <input type="password" id="passwordUs" name="password" placeholder="Mínimo 8 caracteres" minlength="8" required />
            </div>

            <button id="Registrar" type="submit" class="btn-principal">
              <i class="fa-solid fa-check"></i> Realizar registro
            </button>
          </form>
        </div>
      </div>

      <div class="modal-footer">
        <button id="btnCerrarModalUser" class="btn-secondary" onclick="toggleModal('modalUsuarios')">Cerrar</button>
      </div>
    </div>
  </div>

  <!-- MODAL DISCIPLINAS -->
  <div id="modalDisciplinas" class="modal-overlay hidden">
    <div class="modal-content">
      <div class="modal-header">
        <h2><i class="fa-solid fa-trophy"></i> Gestión de Disciplinas</h2>
        <button class="btn-cerrar-x" onclick="toggleModal('modalDisciplinas')">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="controls-container">
          <div class="search-input-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="buscarDisciplina" placeholder="Buscar por disciplina o entrenador...">
          </div>
          <button class="btn-principal" onclick="mostrarFormularioDisciplina()">
            <i class="fa-solid fa-plus"></i> Agregar Disciplina
          </button>
        </div>

        <!-- Formulario para nueva disciplina -->
        <div id="formDisciplina" class="add-form-container hidden">
          <h3>Nueva Disciplina</h3>
          <div class="form-grid">
            <input type="text" id="disciplinaNombre" placeholder="Nombre de la disciplina">
            <select id="disciplinaEntrenador">
                <option value="">Seleccione Entrenador...</option>
            </select>
          </div>
          <div class="form-actions">
            <button class="btn-cancel" onclick="ocultarFormularioDisciplina()">Cancelar</button>
            <button class="btn-save" onclick="guardarDisciplina()">Guardar Disciplina</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="custom-table">
            <thead>
              <tr>
                <th>Disciplina</th>
                <th>Entrenador / Instructor</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tablaDisciplinasBody">
              <tr><td colspan="3" style="text-align: center;">Cargando disciplinas...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" onclick="toggleModal('modalDisciplinas')">Cerrar</button>
      </div>
    </div>
  </div>

  <!-- MODAL ENTRENADORES -->
  <div id="modalEntrenadores" class="modal-overlay hidden">
    <div class="modal-content">
      <div class="modal-header">
        <h2><i class="fa-solid fa-user-gear"></i> Gestión de Entrenadores</h2>
        <button class="btn-cerrar-x" onclick="toggleModal('modalEntrenadores')">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="modal-body">
        <div class="controls-container">
          <div class="search-bar-container">
            <div class="search-input-wrapper">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="buscarEntrenador" placeholder="Buscar entrenador...">
            </div>
          </div>
          <button class="btn-principal" onclick="mostrarFormularioEntrenador()">
            <i class="fa-solid fa-plus"></i> Agregar Entrenador
          </button>
        </div>

        <!-- Formulario para agregar entrenador -->
        <div id="formEntrenador" class="add-form-container hidden">
          <h3>Asignar Entrenador</h3>
          <div class="form-grid">
            <select id="selectUsuarioEntrenador">
                <option value="">Cargando candidatos...</option>
            </select>
          </div>
          <div class="form-actions">
            <button class="btn-cancel" onclick="ocultarFormularioEntrenador()">Cancelar</button>
            <button class="btn-save" onclick="guardarEntrenador()">Asignar Rol</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="custom-table">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Especialidad</th>
                <th>Contacto</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tablaEntrenadoresBody">
              <tr><td colspan="5" style="text-align: center;">Cargando entrenadores...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-secondary" onclick="toggleModal('modalEntrenadores')">Cerrar</button>
      </div>
    </div>
  </div>

  <!-- MODAL FINALIZAR PRÉSTAMO -->
  <div id="modalFinalizarPrestamo" class="modal-overlay hidden">
    <div class="modal-content modal-medium">
      <header class="modal-header">
        <h2><i class="fa-solid fa-check-double"></i> Finalizar Préstamo</h2>
        <button class="close-modal-btn" onclick="toggleModal('modalFinalizarPrestamo')">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </header>
      <div class="modal-body">
        <div class="form-diseno">
          <input type="hidden" id="inputFinalizarPrestamoId">
          <div class="form-group">
            <p>Por favor confirma la entrega del material. ¿Hay alguna observación sobre el estado del material devuelto (daños, faltantes, retrasos)?</p>
          </div>
          <div class="form-group">
            <label for="textareaObservaciones">Observaciones (Opcional)</label>
            <textarea id="textareaObservaciones" rows="4" placeholder="Todo en orden..."></textarea>
          </div>
          <div class="form-actions">
            <button class="btn-secondary" onclick="toggleModal('modalFinalizarPrestamo')">Cancelar</button>
            <button class="btn-guinda" onclick="finalizarPrestamo()">Confirmar y Finalizar</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
  </script>
  <script src="modal-js/modalAdmin.js"></script>
</body>

</html>