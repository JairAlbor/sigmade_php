<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="css/logoSigmade.png">
    <title>Perfil</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/navBar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/profile.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="js/theme.js?v=<?php echo time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body class="sg">
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
$confiabilidad = $user['confiabilidad'] ?? 100;
$confColorProfile = $confiabilidad >= 80 ? '#34d399' : ($confiabilidad >= 50 ? '#fbbf24' : '#f87171');

$query_activos = "SELECT COUNT(*) as activos FROM prestamo WHERE usuario_id = $userId AND estado_general IN ('Activo', 'Prestado', 'Pendiente')";
$res_activos = mysqli_query($conn, $query_activos);
$prestamos_activos = mysqli_fetch_assoc($res_activos)['activos'];
?>


    <nav class="navbar">
      <div class="logo"><img src="css/logoSigmade.png" alt="Logo SIGMADE" width="100px" height="90px"></div>

      <ul class="nav-menu">
        <li class="nav-item" onclick="window.location.href='index.php'">Inicio</li>
        <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador'): ?>
            <li class="nav-item" onclick="window.location.href='administacion.php'">Administración</li>
        <?php else: ?>
            <li class="nav-item" onclick="window.location.href='Dashboard.php'">Préstamo</li>
        <?php endif; ?>
        <li class="nav-item" onclick="window.location.href='catalogo.php'">Catálogo</li>
        <li class="nav-item active">Perfil</li>
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
                <button class="btn-edit" onclick="document.getElementById('modalEditarPerfil').classList.remove('hidden'); document.getElementById('modalEditarPerfil').style.display='flex';">
                  <i class="fa-solid fa-pen-to-square" style="margin-right:6px;"></i>Editar Perfil
                </button>
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
                <i data-lucide="calendar"></i>
                <span>Fecha de nacimiento: 15 de Marzo, 1995</span>
              </div>
            </div>
          </section>

           <aside class="profile-stats">
            <div class="stat-card" style="border-top: 4px solid <?php echo $confColorProfile; ?>">
              <div class="stat-header">
                <i data-lucide="shield-check" style="color: <?php echo $confColorProfile; ?>"></i>
                <span>Confiabilidad</span>
              </div>
              <span class="stat-value" style="color: <?php echo $confColorProfile; ?>"><?php echo $confiabilidad; ?>%</span>
            </div>

            <div class="stat-card">
              <div class="stat-header">
                <i data-lucide="credit-card"></i>
                <span>Préstamos activos</span>
              </div>
              <span class="stat-value"><?php echo $prestamos_activos; ?></span>
            </div>


            <div class="stat-card">
              <div class="stat-header">
                <i data-lucide="medal"></i>
                <span>Prestamos realizados</span>
              </div>
              <span class="stat-value"><?php 
              include("CRUD/conexion.php");
              //Codigo para imprimir el numero total de prestamos realizados por el usuario
              $query_prestamos = "SELECT COUNT(*) as total FROM prestamo WHERE usuario_id = $userId";
              $res_prestamos = mysqli_query($conn, $query_prestamos);

              $total_prestamos = mysqli_fetch_assoc($res_prestamos)['total'];
              echo $total_prestamos;
              ?></span>
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
                    // Definir color estilo pill modo oscuro premium
                    $bg_color = 'rgba(var(--bg-primary-rgb), 0.8)';
                    $text_color = 'rgba(var(--text-primary-rgb), 0.8)';
                    $border_color = 'rgba(var(--text-primary-rgb), 0.2)';
                    
                    if (in_array($estado, ['Activo', 'Prestado', 'Aprobado'])) {
                        $bg_color = 'rgba(59, 130, 246, 0.15)'; // Azul translúcido
                        $text_color = '#60a5fa';
                        $border_color = 'rgba(59, 130, 246, 0.3)';
                    } elseif (in_array($estado, ['Entregado', 'Finalizado', 'Devuelto'])) {
                        $bg_color = 'rgba(16, 185, 129, 0.15)'; // Verde translúcido
                        $text_color = '#34d399';
                        $border_color = 'rgba(16, 185, 129, 0.3)';
                    } elseif ($estado == 'Pendiente') {
                        $bg_color = 'rgba(139, 92, 246, 0.15)'; // Morado translúcido
                        $text_color = '#a78bfa';
                        $border_color = 'rgba(139, 92, 246, 0.3)';
                    }

                    echo '<div style="display: flex; justify-content: space-between; align-items: center; padding: 1.2rem; background: rgba(var(--bg-primary-rgb), 0.5); border-radius: 0.8rem; border: 1px solid rgba(139, 26, 43, 0.2); transition: all 0.3s ease;" onmouseover="this.style.borderColor=\'var(--crimson-light)\'; this.style.transform=\'translateX(5px)\';" onmouseout="this.style.borderColor=\'rgba(139, 26, 43, 0.2)\'; this.style.transform=\'none\';">';
                    echo '  <div style="display: flex; flex-direction: column; gap: 0.4rem;">';
                    echo '    <strong style="color: var(--off-white); font-size: 1.1rem; letter-spacing: 0.5px;">' . $materiales . '</strong>';
                    echo '    <span style="color: rgba(var(--text-primary-rgb), 0.6); font-size: 0.85rem;"><i data-lucide="calendar" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: text-bottom; color: var(--crimson-light);"></i> ' . $fecha . '</span>';
                    echo '  </div>';
                    echo '  <div>';
                    echo '    <span style="background: ' . $bg_color . '; color: ' . $text_color . '; border: 1px solid ' . $border_color . '; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">' . $estado . '</span>';
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

  <!-- MODAL EDITAR PERFIL -->
  <div id="modalEditarPerfil" class="modal-overlay hidden">
    <div class="modal-content" style="max-width:540px; width:95%;">
      <header class="modal-header">
        <h2><i class="fa-solid fa-user-pen"></i> Editar Mi Perfil</h2>
        <button class="close-modal-btn" onclick="document.getElementById('modalEditarPerfil').classList.add('hidden'); document.getElementById('modalEditarPerfil').style.display='none';">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </header>
      <div class="modal-body">
        <form id="formEditarPerfil" class="form-diseno">

          <fieldset class="profile-form-section">
            <legend>Información Personal</legend>

            <div class="form-group">
              <label><i class="fa-solid fa-user"></i> Nombre(s)</label>
              <input type="text" id="ep_nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </div>

            <div class="form-group">
              <label><i class="fa-solid fa-user"></i> Apellidos</label>
              <input type="text" id="ep_apellidos" name="apellidos" value="<?php echo htmlspecialchars($apellidos); ?>" required>
            </div>
          </fieldset>

          <fieldset class="profile-form-section">
            <legend>Contacto</legend>

            <div class="form-group">
              <label><i class="fa-solid fa-envelope"></i> Correo Electrónico</label>
              <input type="email" id="ep_email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
              <label><i class="fa-solid fa-phone"></i> Teléfono</label>
              <input type="tel" id="ep_telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" pattern="[0-9]{10}">
            </div>
          </fieldset>

          <fieldset class="profile-form-section">
            <legend>Seguridad</legend>

            <div class="form-group">
              <label><i class="fa-solid fa-lock"></i> Nueva Contraseña <span style="font-size:0.8rem; color:#888;">(Déjalo en blanco para no cambiarla)</span></label>
              <input type="password" id="ep_password" name="password" placeholder="Mínimo 8 caracteres" minlength="8">
            </div>
          </fieldset>

          <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="document.getElementById('modalEditarPerfil').classList.add('hidden'); document.getElementById('modalEditarPerfil').style.display='none';">
              <i class="fa-solid fa-xmark"></i> Cancelar
            </button>
            <button type="submit" class="btn-guinda">
              <i class="fa-solid fa-check"></i> Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

    <script>
      lucide.createIcons();

      // Manejar submit del form de editar perfil
      document.getElementById('formEditarPerfil').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = new URLSearchParams(new FormData(this));
        fetch('CRUD/actualizarPerfil.php', {
          method: 'POST',
          body: data
        })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            Swal.fire({ icon: 'success', title: '¡Guardado!', text: 'Tu perfil ha sido actualizado.', confirmButtonColor: '#8B1A2B' })
              .then(() => location.reload());
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'No se pudo actualizar', confirmButtonColor: '#8B1A2B' });
          }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error de red', confirmButtonColor: '#8B1A2B' }));
      });
    </script>
  </body>
</html>
