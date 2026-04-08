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

$query_activos = "SELECT COUNT(*) as activos FROM prestamo WHERE usuario_id = $userId AND estado_general IN ('Activo', 'Prestado', 'Pendiente')";
$res_activos = mysqli_query($conn, $query_activos);
$prestamos_activos = mysqli_fetch_assoc($res_activos)['activos'];
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
              <span class="stat-value"><?php echo $prestamos_activos; ?></span>
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
          <div class="activity-placeholder">
            <?php
            $query_historial = "
                SELECT 
                    p.id, 
                    p.fecha_solicitud, 
                    p.estado_general, 
                    GROUP_CONCAT(m.nombre SEPARATOR ', ') AS materiales
                FROM prestamo p
                JOIN detalle_prestamo dp ON p.id = dp.prestamo_id
                JOIN material m ON dp.material_id = m.id
                WHERE p.usuario_id = $userId
                GROUP BY p.id
                ORDER BY p.fecha_solicitud DESC
                LIMIT 5
            ";
            $res_historial = mysqli_query($conn, $query_historial);

            if (mysqli_num_rows($res_historial) > 0) {
                echo '<div style="display: flex; flex-direction: column; gap: 1rem;">';
                while ($prestamo = mysqli_fetch_assoc($res_historial)) {
                    $estado = $prestamo['estado_general'];
                    $fecha = date("d/m/Y", strtotime($prestamo['fecha_solicitud']));
                    $materiales = htmlspecialchars($prestamo['materiales']);
                    
                    // Definir color estilo pill
                    $bg_color = '#f3f4f6';
                    $text_color = '#4b5563';
                    if (in_array($estado, ['Activo', 'Prestado', 'Aprobado'])) {
                        $bg_color = '#e0e7ff';
                        $text_color = '#4f46e5';
                    } elseif (in_array($estado, ['Entregado', 'Finalizado', 'Devuelto'])) {
                        $bg_color = '#dcfce7';
                        $text_color = '#16a34a';
                    } elseif ($estado == 'Pendiente') {
                        $bg_color = '#fef3c7';
                        $text_color = '#d97706';
                    }

                    echo '<div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background-color: #f9fafb; border-radius: 0.5rem; border: 1px solid #e5e7eb;">';
                    echo '  <div style="display: flex; flex-direction: column; gap: 0.25rem;">';
                    echo '    <strong style="color: #1f2937; font-size: 1rem;">' . $materiales . '</strong>';
                    echo '    <span style="color: #6b7280; font-size: 0.85rem;"><i data-lucide="calendar" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: text-bottom;"></i> ' . $fecha . '</span>';
                    echo '  </div>';
                    echo '  <div>';
                    echo '    <span style="background-color: ' . $bg_color . '; color: ' . $text_color . '; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 500;">' . $estado . '</span>';
                    echo '  </div>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p style="color: #6b7280; text-align: center; padding: 2rem 0;">No hay actividad reciente de préstamos.</p>';
            }
            ?>
          </div>
        </section>
      </main>
    </section>
    <script>
      lucide.createIcons();
    </script>
  </body>
</html>
