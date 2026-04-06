<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/nav-bar.css">
    <link rel="stylesheet" href="css/profile.css">
    <script src="https://unpkg.com/lucide@latest"></script>
  </head>
  <body>
    <?php 
    include("CRUD/conexion.php");
        //iniciación de sesión
        session_start();

        // Verificar si el usuario ha iniciado sesión
        if (!isset($_SESSION['user_id'])) {
            // Si no ha iniciado sesión, redirigir al formulario de inicio de sesión
            header('Location: login.php');
            exit();
        }
    
    //so todo esta correcto, extremos la información del usuario de la base de datos usando el ID almacenado en la sesión
    $userId = $_SESSION['user_id'];
    // Aquí deberías conectar a tu base de datos y obtener la información del usuario usando $userId
    $query = "SELECT * FROM usuario WHERE id = $userId";
    // Ejecuta la consulta y almacena los resultados en variables para mostrar en el perfil
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    $identificador = $user['identificador'];
    $nombre = $user['nombre'];
    $apellidos = $user['apellidos'];
    $email = $user['email'];
$telefono = $user['telefono'];
$creado_en = $user['created_at'];

    ?>


    <nav class="navbar">
      <div class="logo">SIGMADE</div>

      <ul class="nav-menu">
        <?php 
        if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') {
            echo '<li class="nav-item" onclick="window.location.href=\'administacion.php\'">Inicio</li>';
        }else{
            echo '<li class="nav-item" onclick="window.location.href=\'Dashboard.php\'">Inicio</li>';
        }
        ?>
        <li class="nav-item" onclick="window.location.href='catalogo.php'">Catalogo</li>
        <li class="nav-item active">Perfil</li>
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
          <span id="userName" class="user-name">Hola, <?php echo $_SESSION['usuario_nombre']; ?></span>
        </div>
      </div>
    </nav>

    <section class="form-container">
      <main class="dashboard-container profile-page">
        <h1 class="dashboard-title">Mi Perfil</h1>

        <div class="profile-grid">
          <section class="profile-main-card">
            <div class="profile-header">
              <div class="profile-avatar-large">
                <i data-lucide="user"></i>
              </div>
              <div class="profile-header-info">
                <h2><?php echo $nombre; ?> <?php echo $apellidos; ?></h2>
                <p>Miembro desde <?php echo $creado_en; ?></p>
                <button class="btn-edit">Editar Perfil</button>
              </div>
            </div>

            <hr class="divider" />

            <div class="profile-details">
              <div class="detail-item">
                <i data-lucide="mail"></i>
                <span><?php echo $email; ?></span>
              </div>
              <div class="detail-item">
                <i data-lucide="phone"></i>
                <span><?php echo $telefono; ?></span>
              </div>
              <div class="detail-item">
                <i data-lucide="map-pin"></i>
                <span>Ciudad de México, México</span>
              </div>
              <div class="detail-item">
                <i data-lucide="calendar"></i>
                <span>Fecha de nacimiento: 15 de Marzo, 1995</span>
              </div>
            </div>
          </section>

          <aside class="profile-stats">
            <div class="stat-card">
              <div class="stat-header">
                <i data-lucide="credit-card"></i>
                <span>Préstamos activos</span>
              </div>
              <span class="stat-value">0</span>
            </div>

            <div class="stat-card">
              <div class="stat-header">
                <i data-lucide="calendar-check"></i>
                <span>Eventos asistidos</span>
              </div>
              <span class="stat-value"></span>
            </div>

            <div class="stat-card">
              <div class="stat-header">
                <i data-lucide="medal"></i>
                <span>Puntos acumulados</span>
              </div>
              <span class="stat-value">350</span>
            </div>
          </aside>
        </div>

        <section class="activity-section">
          <h3>Actividad Reciente</h3>
          <div class="activity-placeholder"></div>
        </section>
      </main>
    </section>
    <script>
      lucide.createIcons();
    </script>
  </body>
</html>
