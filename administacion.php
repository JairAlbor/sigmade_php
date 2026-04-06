<!doctype html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard de Administración</title>
  <link rel="stylesheet" href="css/nav-bar.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/Admin.css" />
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

  // AL PRINCIPIO DEL ARCHIVO
  include("CRUD/conexion.php");

  // 1. Consultas para las estadísticas (Los cuadritos de colores)
  $resTotal = mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuario");
  $total = mysqli_fetch_assoc($resTotal)['total'];

  $resActivos = mysqli_query($conn, "SELECT COUNT(*) AS activos FROM usuario WHERE estatus = 'Activo'");
  $activos = mysqli_fetch_assoc($resActivos)['activos'];

  $inactivos = $total - $activos;

  // 2. Consulta para la lista de usuarios (La tabla)
  $consultaUsers = "SELECT * FROM usuario";
  $resultadoUsers = mysqli_query($conn, $consultaUsers);
  ?>


  <nav class="navbar">
    <div class="logo">SIGMADE</div>

    <ul class="nav-menu">
      <li class="nav-item active">Inicio</li>
      <li class="nav-item" onclick="window.location.href = 'catalogo.php'">
        Catalogo
      </li>
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
          <div class="big-number">12</div>
          <p class="subtitle">Préstamos en curso</p>

          <div class="status-list">
            <div class="status-item">Vencen hoy: 3</div>
            <div class="status-item">Vencidos: 1</div>
          </div>
        </div>
        <button id="openModalBtn" class="btn-guinda">
          Abrir Gestión de Eventos
        </button>
        <footer class="card-footer">Click para ver detalles →</footer>
      </section>

      <div class="secondary-grid">
        <button class="card-small" onclick="openDisciplines()">
          <i class="fa-solid fa-dumbbell"></i>
          <h3>Disciplinas</h3>
          <p class="count">8</p>
        </button>

        <button class="card-small" onclick="toggleModal('modalUsuarios')">
          <i class="fa-solid fa-users"></i>
          <h3>Usuarios</h3>
          <p class="count" id="totalUser">
            <?php
            include("CRUD/conexion.php");
            $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM usuario");
            $row = mysqli_fetch_assoc($result);
            echo $row['total'];
            ?>

          </p>
        </button>

        <button class="card-small">
          <i class="fa-solid fa-calendar-days"></i>
          <h3>Eventos</h3>
          <p class="count">5</p>
        </button>

        <button class="card-small">
          <i class="fa-solid fa-user-gear"></i>
          <h3>Entrenadores</h3>
          <p class="count">12</p>
        </button>
      </div>
    </div>
  </main>

  <div id="eventModalOverlay" class="modal-overlay hidden">
    <div class="modal-content">
      <header class="modal-header">
        <h2>Gestión de Eventos</h2>
        <div class="header-actions">
          <button class="btn-guinda" onclick="toggleFormEventos()">
            <i class="fa-solid fa-plus"></i> Registrar Evento
          </button>
          <button class="close-modal-btn" onclick="closeModal()">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </header>
      <div class="modal-body">
        <section
          id="addEventForm"
          class="add-form-container hidden">
          <h3>Nuevo Evento</h3>
          <div class="form-grid">
            <input type="text" placeholder="Título del evento" id="eventTitle">
            <input type="text" placeholder="Ubicación" id="eventLocation">
            <input type="date" id="eventDate">
            <input type="time" id="eventTime">
          </div>
          <textarea placeholder="Descripción del evento" rows="3" id="eventDesc"></textarea>
          <div class="form-actions">
            <button class="btn-cancel" onclick="toggleForm(false)">Cancelar</button>
            <button class="btn-save" onclick="saveEvent()">Guardar Evento</button>
          </div>
        </section>
        <div id="eventList" class="event-list-container"></div>
      </div>
      <footer class="modal-footer">
        <button class="btn-secondary" onclick="closeModal()">Cerrar</button>
      </footer>
    </div>
  </div>

  <div id="disciplineModalOverlay" class="modal-overlay hidden">
    <div class="modal-content">
      <header class="modal-header">
        <h2>Gestión de Disciplinas</h2>
        <div class="header-actions">
          <button class="btn-guinda" onclick="toggleFormDisciplinas()">
            <i class="fa-solid fa-plus"></i> Agregar
          </button>
        </div>
      </header>
      <!-- ********* Formulario para agregar nueva disciplina ********************* -->
      <div class="modal-body">
        <div id="disciplineForm" class="add-form-container hidden">
          <h3>Nueva Disciplina</h3>
          <div class="form-row">
            <input type="text" placeholder="Nombre" id="inputName" />
            <input type="text" placeholder="Entrenador" id="inputTrainer" />
            <button class="btn-save" onclick="saveDiscipline()">
              Guardar
            </button>
          </div>
        </div>
        <div id="disciplineGrid" class="disciplines-grid"></div>
      </div>
      <footer class="modal-footer">
        <button class="btn-secondary" onclick="closeDisciplines()">
          Cerrar
        </button>
      </footer>
    </div>
  </div>







  <script>
    lucide.createIcons();
  </script>
  <script src="modal-js/modal-admi.js"></script>



  <!-- Modal para gestión de usuarios -->
<div id="modalUsuarios" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fa-solid fa-users-gear"></i> Usuarios Registrados</h2>
            <button class="btn-cerrar-x" onclick="toggleModal('modalUsuarios')">&times;</button>
        </div>
        
        <div class="modal-body">
            <div class="search-bar-container">
                <input type="text" id="buscarUsuario" placeholder="Buscar por nombre, email o rol...">
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaUsuariosBody">
                        <?php
                        // Consulta de usuarios
                        $sqlUsers = "SELECT id, nombre, email, rol, estatus FROM usuario";
                        $resUsers = mysqli_query($conn, $sqlUsers);

                        while ($user = mysqli_fetch_assoc($resUsers)) {
                            $claseEstado = ($user['estatus'] == 'Activo') ? 'status-active' : 'status-inactive';
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($user['nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['rol']) . "</td>";
                            echo "<td><span class='status-pill $claseEstado'>" . $user['estatus'] . "</span></td>";
                            
                            // ACCIONES (Igual que en catálogo)
                            echo "<td class='actions'>";
                            echo "<button class='btn-icon edit' onclick=\"abrirEditarUsuario(" . $user['id'] . ")\">
                                    <i class='fa-solid fa-pen-to-square'></i>
                                  </button>";
                            echo "<button class='btn-icon delete' onclick=\"eliminarUsuario(" . $user['id'] . ")\">
                                    <i class='fa-solid fa-trash-can'></i>
                                  </button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-secundario btn-cerrar" onclick="toggleModal('modalUsuarios')">Cerrar</button>
        </div>
    </div>
</div>

</body>

</html>