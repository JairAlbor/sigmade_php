<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="css/logoSigmade.png">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/navBar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/cssAdmin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="js/theme.js?v=<?php echo time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body class="sg">
    <?php session_start();
    if (!isset($_SESSION['usuario_nombre'])) {
        // Si el usuario no ha iniciado sesión, redirigir al login
        header("Location: login.php");
        exit();
    }
    ?>
 <nav class="navbar">
  <div class="logo"><img src="css/logoSigmade.png" alt="SIGMADE" width="100px" height="90px""></div>

  <ul class="nav-menu">
    <li class="nav-item" onclick="window.location.href='index.php'">Inicio</li>
    <li class="nav-item active">Préstamo</li>
    <li class="nav-item" onclick="window.location.href='catalogo.php'">Catálogo</li>
    <li class="nav-item" onclick="window.location.href='profile.php'">Perfil</li>
  </ul>

  <div class="top-bar-user">
      <div class="user-pill">
        <div class="user-avatar">
          <i data-lucide="user" class="icon-user"></i>
        </div>
        <span class="user-name">Hola, <?php echo $_SESSION['usuario_nombre']; ?></span>
      </div>
      <a href="extras/logout.php" class="btn-logout" title="Cerrar Sesión">
        <i data-lucide="log-out" class="icon-logout"></i>
      </a>
      <button class="theme-toggle-btn" title="Alternar Tema"><i data-lucide="sun"></i></button>
    </div>
</nav>

   <section class="form-container">

    <div class="dashboard-container">
      <h2 class="dashboard-title">Dashboard</h2>

      <div class="dashboard-grid">
        <article class="adeudos-card" id="adeudosCard">
          <i class="fa-solid fa-award check-icon" id="adeudosIcon" style="color: #ffffff;"></i>
          <div class="adeudos-content">
            <h3 class="adeudos-title" id="adeudosTitle">Sin adeudos pendientes</h3>
            <p class="adeudos-subtext" id="adeudosSubtext">Tu cuenta se encuentra al día.</p>
          </div>
        </article>

      <div class="cards-grid">
  
  <button class="dashboard-card" onclick="openModalPrestamo()">
    <div class="card-icon">
      <i data-lucide="hand-coins" class="icon-svg"></i>
    </div>
    <h3 class="card-title">Nuevo Préstamo</h3>
  </button>

  <button class="dashboard-card" onclick="openModalReserva()">
    <div class="card-icon">
      <i data-lucide="layout-grid" class="icon-svg"></i>
    </div>
    <h3 class="card-title">Reservar Cancha</h3>
  </button>

  <button class="dashboard-card" onclick="openModalEventos()">
    <div class="card-icon">
      <i data-lucide="calendar-days" class="icon-svg"></i>
    </div>
    <h3 class="card-title">Mis Eventos</h3>
  </button>

  <button class="dashboard-card" onclick="openModalHistorial()">
    <div class="card-icon">
      <i data-lucide="history" class="icon-svg"></i>
    </div>
    <h3 class="card-title">Historial</h3>
  </button>

</div>

    <!-- Script para manejar la navegación activa (opcional) -->
    <script>
      // Pequeño script para cambiar la clase active al hacer clic en los items del menú
      document.querySelectorAll(".nav-item").forEach((item) => {
        item.addEventListener("click", function () {
          document.querySelectorAll(".nav-item").forEach((navItem) => {
            navItem.classList.remove("active");
          });
          this.classList.add("active");
        });
      });
    </script>
    <script>
  lucide.createIcons();
</script>
<!-- MODAL PRESTAMO -->
<div id="modalPrestamo" class="modal-overlay hidden">
  <div class="modal-content modal-medium">
    <header class="modal-header">
      <h2><i class="fa-solid fa-hand-holding"></i> Nuevo Préstamo de Material</h2>
      <button class="close-modal-btn" onclick="closeModalPrestamo()">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </header>
    <div class="modal-body">
      <form id="formPrestamo" class="form-diseno" onsubmit="event.preventDefault(); registrarPrestamo();">

        <!-- BARRA DE BÚSQL NUEVA -->
        <div class="form-group">
          <label><i class="fa-solid fa-magnifying-glass"></i> Buscar Material:</label>
          <div class="search-input-wrapper" style="position:relative;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:rgba(var(--text-primary-rgb),0.4);"></i>
            <input type="text" id="searchModalMaterial" placeholder="Nombre, disciplina..." 
              style="width:100%; padding: 10px 10px 10px 38px; border-radius:8px; border:1px solid rgba(var(--text-primary-rgb),0.15); background:rgba(var(--bg-primary-rgb),0.7); color:var(--off-white); font-family:inherit;"
              oninput="filtrarMaterialesEnModal(this.value)">
          </div>
        </div>

        <!-- LISTA DE MATERIALES -->
        <div class="form-group custom-dropdown-group">
          <label><i class="fa-solid fa-boxes-stacked"></i> Materiales Disponibles:</label>
          <div class="custom-dropdown-wrapper">
            <div class="custom-dropdown-trigger" onclick="this.nextElementSibling.classList.toggle('hidden-dropdown')">
              <span>Desplegar Catálogo de Materiales...</span>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div id="listaMaterialesDisponibles" class="material-grid drop-version custom-dropdown-menu hidden-dropdown">
              Cargando...
            </div>
          </div>
        </div>

        <!-- FECHAS -->
        <div class="form-grid" style="grid-template-columns:1fr 1fr;">
          <div class="form-group">
            <label><i class="fa-solid fa-calendar-plus"></i> Fecha y Hora Inicio:</label>
            <input type="datetime-local" id="fechaInicioPrestamo" required style="color:var(--off-white);">
          </div>
          <div class="form-group">
            <label><i class="fa-solid fa-calendar-xmark"></i> Fecha Límite:</label>
            <input type="datetime-local" id="fechaLimitePrestamo" required style="color:var(--off-white);">
          </div>
        </div>

        <div class="form-actions-edit">
          <button type="submit" class="btn-guinda"><i class="fa-solid fa-check"></i> Solicitar Préstamo</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL RESERVAR CANCHA -->
<div id="modalReserva" class="modal-overlay hidden">
  <div class="modal-content modal-medium">
    <header class="modal-header">
      <h2><i class="fa-solid fa-calendar-check"></i> Reservar Cancha</h2>
      <button class="close-modal-btn" onclick="closeModalReserva()">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </header>
    <div class="modal-body">
      <form id="formReserva" class="form-diseno" onsubmit="event.preventDefault(); registrarReserva();">
        <div class="form-group custom-dropdown-group">
          <label>Cancha a Reservar:</label>
          <div class="custom-dropdown-wrapper">
            <div class="custom-dropdown-trigger" onclick="this.nextElementSibling.classList.toggle('hidden-dropdown')">
              <span>Desplegar Opciones de Canchas...</span>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div id="listaEspaciosDisponibles" class="material-grid drop-version custom-dropdown-menu hidden-dropdown">
              Cargando...
            </div>
          </div>
        </div>
        <div class="form-grid">
          <div class="form-group">
            <label>Fecha y Hora Inicio:</label>
            <input type="datetime-local" id="fechaInicioReserva" required>
          </div>
          <div class="form-group">
            <label>Fecha y Hora Fin:</label>
            <input type="datetime-local" id="fechaFinReserva" required>
          </div>
        </div>
        <div class="form-group">
          <label>Motivo:</label>
          <input type="text" id="motivoReserva" required placeholder="Motivo de la reserva">
        </div>
        <div class="form-actions-edit">
          <button type="submit" class="btn-guinda"><i class="fa-solid fa-check"></i> Solicitar Reserva</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL MIS EVENTOS -->
<div id="modalEventosDashboard" class="modal-overlay hidden">
  <div class="modal-content modal-large">
    <header class="modal-header">
      <h2><i class="fa-solid fa-calendar-days"></i> Eventos Activos</h2>
      <button class="close-modal-btn" onclick="closeModalEventos()">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </header>
    <div class="modal-body">
      <div id="listaEventos" class="event-list-container">
        Cargando eventos...
      </div>
    </div>
  </div>
</div>

<!-- MODAL HISTORIAL -->
<div id="modalHistorial" class="modal-overlay hidden">
  <div class="modal-content modal-large">
    <header class="modal-header">
      <h2><i class="fa-solid fa-history"></i> Mi Historial de Préstamos</h2>
      <button class="close-modal-btn" onclick="closeModalHistorial()">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </header>
    <div class="modal-body">
      <div class="table-responsive">
        <table id="tablaHistorialUsuario" class="custom-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Materiales</th>
              <th>Solicitud</th>
              <th>Inicio</th>
              <th>Límite</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Dynamic content -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  const currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
</script>
<script src="modal-js/modal-dashboard.js?v=<?php echo time(); ?>"></script>
</section>
  </body>
</html>
