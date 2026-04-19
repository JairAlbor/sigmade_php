<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="css/logoSigmade.png">
    <title>SIGMADE | Sistema de Préstamos</title>
    <link rel="stylesheet" href="css/cssNuevo.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600;700&family=Bebas+Neue&display=swap"
        rel="stylesheet">
    <!-- theme.js debe ir ANTES del body para evitar destellos de color no deseados -->
    <script src="js/theme.js?v=<?php echo time(); ?>"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="sg">
    <nav class="sg-nav">
       <!-- <div class="sg-nav-logo">SIG<span>MADE</span></div>-->
       <div class="logo"><img src="css/logoSigmade.png" alt="Logo SIGMADE" width="100px" height="90px"></div>
        <div class="sg-nav-links">
            <a class="active" href="index.php">Inicio</a>

            <?php if (!isset($_SESSION['rol'])) { ?>
                <a href="catalogo.php">Catálogo</a>
            <?php } else { ?>
                <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') { ?>
                    <a href="administacion.php">Administración</a>
                    <a href="catalogo.php">Catálogo</a>
                    <a href="profile.php">Perfil</a>
                <?php } else { ?>
                    <a href="Dashboard.php">Préstamos</a>
                    <a href="catalogo.php">Catálogo</a>
                    <a href="profile.php">Perfil</a>
                <?php } ?>
            <?php } ?>
        </div>

        <?php if (!isset($_SESSION['rol'])) { ?>
            <div style="display:flex; align-items:center;">
                <button class="sg-nav-btn" onclick="window.location.href='login.php'" style="white-space:nowrap;">Iniciar Sesión</button>
                <button class="theme-toggle-btn" title="Alternar Tema"><i data-lucide="sun"></i></button>
            </div>
        <?php } else { ?>
            <div style="display:flex; align-items:center; gap: 0.5rem;">
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
        <?php } ?>
    </nav>

    <header class="sg-hero">
        <div class="sg-hero-bg"></div>
        <div class="sg-hero-left">
            <div class="sg-eyebrow">Plataforma Oficial</div>
            <h1>Potencia tu <em>Entrenamiento</em></h1>
            <p>Gestiona, reserva y administra tu material deportivo con precisión. El sistema definitivo para la
                excelencia deportiva en tu institución.</p>
            <div class="sg-btn-row">
                <button class="sg-btn-p">Comenzar Ahora</button>
                <button class="sg-btn-s">Ver Catálogo</button>
            </div>
        </div>
        <div class="sg-hero-right">
            <div class="sg-item">
                <div class="sg-item-icon">🏀</div>
                <div class="sg-item-info">
                    <div class="sg-item-name">Basquetbol</div>
                    <div class="sg-item-sub">Balón / Cancha</div>
                </div>
            </div>
            <div class="sg-item">
                <div class="sg-item-icon">⚽</div>
                <div class="sg-item-info">
                    <div class="sg-item-name">Fútbol</div>
                    <div class="sg-item-sub">Balón / Cancha</div>
                </div>
            </div>
            <div class="sg-item">
                <div class="sg-item-icon">➕</div>
                <div class="sg-item-info">
                    <div class="sg-item-name">Y más disciplinas</div>
                    <div class="sg-item-sub">Voleibol / Ajedrez / Mterial Civico</div>
                </div>
            </div>
        </div>
    </header>


    <section class="sg-section">
        <div class="sg-stag">Características Principales</div>
        <h2>Diseñado para el <em>Rendimiento</em></h2>
        <div class="sg-cards">
            <div class="sg-card">
                <div class="sg-card-num">01</div>
                <h3>Control en Tiempo Real</h3>
                <p>Verifica la disponibilidad del material al instante. Nuestro sistema te mantiene actualizado sobre el
                    stock y la ubicación de cada artículo deportivo.</p>
            </div>
            <div class="sg-card">
                <div class="sg-card-num">02</div>
                <h3>Reservas Anticipadas</h3>
                <p>Asegura tu material para futuros entrenamientos o torneos con un sistema de reservas fácil de usar,
                    sin empalmes ni conflictos.</p>
            </div>
            <div class="sg-card">
                <div class="sg-card-num">03</div>
                <h3>Historial Completo</h3>
                <p>Mantén un registro detallado de tus préstamos anteriores. Consulta fechas, artículos y estatus con
                    total transparencia.</p>
            </div>
        </div>
    </section>

    <section class="sg-section" style="background: var(--dark2);">
        <div class="sg-stag">Flujo de Trabajo</div>
        <h2>¿Cómo funciona <em>SIGMADE?</em></h2>
        <div class="sg-how">
            <div class="sg-steps">
                <div class="sg-step">
                    <div class="sg-step-num">1</div>
                    <div class="sg-step-content">
                        <h4>Selecciona el Material</h4>
                        <p>Explora nuestro catálogo y elige el equipo que necesitas para tu sesión.</p>
                    </div>
                </div>
                <div class="sg-step">
                    <div class="sg-step-num">2</div>
                    <div class="sg-step-content">
                        <h4>Genera la Solicitud</h4>
                        <p>Confirma tus datos y selecciona la hora de uso requerida.</p>
                    </div>
                </div>
                <div class="sg-step">
                    <div class="sg-step-num">3</div>
                    <div class="sg-step-content">
                        <h4>Recibe la Aprobación</h4>
                        <p>El administrador de la instalación validará tu solicitud en minutos.</p>
                    </div>
                </div>
                <div class="sg-step">
                    <div class="sg-step-num">4</div>
                    <div class="sg-step-content">
                        <h4>Recoge y Entrena</h4>
                        <p>Acude a la zona de almacén, presenta tu matrícula y comienza tu entrenamiento.</p>
                    </div>
                </div>
            </div>
            <div class="sg-panel">
                <span class="sg-panel-title">Disponibilidad Actual por Disciplina</span>
                <div class="sg-prow">
                    <div class="sg-prow-head">
                        <span>Balones de Basquetbol</span>
                        <span class="sg-pct">75%</span>
                    </div>
                    <div class="sg-pbar">
                        <div class="sg-pfill" style="width: 75%"></div>
                    </div>
                </div>
                <div class="sg-prow">
                    <div class="sg-prow-head">
                        <span>Balones de Fútbol</span>
                        <span class="sg-pct">85%</span>
                    </div>
                    <div class="sg-pbar">
                        <div class="sg-pfill" style="width: 85%"></div>
                    </div>
                </div>
                <div class="sg-prow">
                    <div class="sg-prow-head">
                        <span>Juegos de Ajedrez</span>
                        <span class="sg-pct">90%</span>
                    </div>
                    <div class="sg-pbar">
                        <div class="sg-pfill" style="width: 90%"></div>
                    </div>
                </div>
                <div class="sg-prow">
                    <div class="sg-prow-head">
                        <span>Redes y Balones de Voleibol</span>
                        <span class="sg-pct">60%</span>
                    </div>
                    <div class="sg-pbar">
                        <div class="sg-pfill" style="width: 60%"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="sg-cta" style="margin-top: 30px;">
        <div class="sg-cta-content">
            <h2>Lleva tu deporte al <em>Siguiente Nivel</em></h2>
            <p>Únete a cientos de estudiantes y docentes que ya están maximizando su rendimiento deportivo con SIGMADE.
            </p>
        </div>
        <?php if (!isset($_SESSION['rol'])): ?>
            <button class="sg-btn-p" onclick="window.location.href='login.php'">Crear Cuenta</button>
        <?php else: ?>
            <button class="sg-btn-p" onclick="window.location.href='Dashboard.php'">Ir a Préstamos</button>
        <?php endif; ?>
    </div>

    <footer class="sg-footer">
        <div class="sg-footer-logo">SIG<span>MADE</span></div>
        <div class="sg-footer-copy">&copy; 2026 Sistema de Gestión de Material Deportivo. Todos los derechos reservados.
        </div>
    </footer>
</body>
    <script src="https://unpkg.com/lucide@latest"></script>
</html>