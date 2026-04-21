<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="css/logoSigmade.png">
    <title>SIGMADE | Sistema de Préstamos</title>
    <link rel="stylesheet" href="css/cssNuevo.css?v=<?php echo time(); ?>">
    <?php if (isset($_SESSION['rol'])): ?>
        <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="css/navBar.css?v=<?php echo time(); ?>">
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600;700&family=Bebas+Neue&display=swap"
        rel="stylesheet">
    <!-- theme.js debe ir ANTES del body para evitar destellos de color no deseados -->
    <script src="js/theme.js?v=<?php echo time(); ?>"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Leaflet para Mapas -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body class="sg">
    <?php if (!isset($_SESSION['rol'])) { ?>
        <nav class="sg-nav">
            <div class="logo"><img src="css/logoSigmade.png" alt="Logo SIGMADE" width="100px" height="90px"></div>
            <div class="sg-nav-links">
                <a class="active" href="index.php">Inicio</a>
                <a href="catalogo.php">Catálogo</a>
            </div>
            <div style="display:flex; align-items:center;">
                <button class="sg-nav-btn" onclick="window.location.href='login.php'" style="white-space:nowrap;">Iniciar Sesión</button>
                <button class="theme-toggle-btn" title="Alternar Tema"><i data-lucide="sun"></i></button>
            </div>
        </nav>
    <?php } else { ?>
        <nav class="navbar">
            <div class="logo"><img src="css/logoSigmade.png" alt="Logo SIGMADE" width="100px" height="90px"></div>
            <ul class="nav-menu">
                <li class="nav-item active">Inicio</li>
                <?php if ($_SESSION['rol'] == 'Admin' || $_SESSION['rol'] == 'Operador') { ?>
                    <li class="nav-item" onclick="window.location.href='administacion.php'">Administración</li>
                <?php } else { ?>
                    <li class="nav-item" onclick="window.location.href='Dashboard.php'">Préstamo</li>
                <?php } ?>
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
    <?php } ?>

    <header class="sg-hero">
        <div class="sg-hero-bg"></div>
        <div class="sg-hero-left">
            <div class="sg-eyebrow">Sistema SIGMADE</div>
            <h1>Gestión inteligente de <em>equipamiento deportivo</em></h1>
            <p>Consulta la disponibilidad, reserva balones, redes o espacios, y gestiona tus préstamos de manera rápida y ordenada.</p>
            <div class="sg-btn-row">
                <?php if (!isset($_SESSION['rol'])): ?>
                    <button class="sg-btn-p" onclick="window.location.href='login.php'">Iniciar Sesión</button>
                <?php else: ?>
                    <button class="sg-btn-p" onclick="window.location.href='Dashboard.php'">Ir al Panel</button>
                <?php endif; ?>
                <button class="sg-btn-s" onclick="window.location.href='catalogo.php'">Ver Catálogo</button>
            </div>
        </div>
        <div class="sg-hero-right">
            <div class="sg-hero-info">
                <!-- Sección Clima -->
                <div class="sg-info-card weather" id="weather-widget">
                    <div class="sg-weather-main">
                        <img id="weather-icon" src="" alt="Clima" width="50">
                        <span id="temp">--°C</span>
                    </div>
                    <div class="sg-weather-details">
                        <span id="weather-desc">Cargando clima...</span>
                        <small id="weather-rec">Verificando condiciones...</small>
                    </div>
                    <div class="sg-status-dot pulse"></div>
                </div>

                <!-- Sección Ubicación y Mapa -->
                <div class="sg-info-card location">
                    <div class="sg-location-text">
                        <div class="sg-info-item">
                            <i data-lucide="map-pin"></i>
                            <span>Edificio de Deportes, UTM</span>
                        </div>
                        <div class="sg-info-item">
                            <i data-lucide="clock"></i>
                            <span>Lun - Vie: 8:00 - 16:00</span>
                        </div>
                    </div>
                    <div id="map-preview" class="sg-map-container"></div>
                </div>

                <!-- Botones de Contacto -->
                <div class="sg-info-actions">
                    <a href="https://wa.me/529511234567?text=Hola,%20tengo%20una%20duda%20sobre%20un%20préstamo%20de%20material" target="_blank" class="sg-btn-info wa">
                        <i data-lucide="message-circle"></i> WhatsApp
                    </a>
                    <a href="mailto:deportes@utm.mx" class="sg-btn-info mail">
                        <i data-lucide="mail"></i> Correo
                    </a>
                </div>
            </div>
        </div>
    </header>


    <section class="sg-section">
        <div class="sg-stag">¿Qué resolvemos?</div>
        <h2>Diseñado para el <em>Rendimiento</em></h2>
        <div class="sg-cards">
            <div class="sg-card">
                <div class="sg-card-icon"><i data-lucide="database"></i></div>
                <h3>Inventario en Tiempo Real</h3>
                <p>Sabe exactamente qué está "Libre" y qué está "Ocupado" antes de ir a pedirlo.</p>
            </div>
            <div class="sg-card">
                <div class="sg-card-icon"><i data-lucide="clipboard-list"></i></div>
                <h3>Préstamos Ágiles</h3>
                <p>Olvídate del papeleo; registra solicitudes y devoluciones al instante.</p>
            </div>
            <div class="sg-card">
                <div class="sg-card-icon"><i data-lucide="users"></i></div>
                <h3>Control de Disciplinas</h3>
                <p>Organiza torneos, equipos y asigna materiales a cada disciplina de forma eficiente.</p>
            </div>
            <div class="sg-card">
                <div class="sg-card-icon"><i data-lucide="smartphone"></i></div>
                <h3>Multidispositivo</h3>
                <p>Diseñado para usarse desde tu celular en cualquier lugar del campus.</p>
            </div>
        </div>
    </section>

    <section class="sg-section" style="background: var(--dark2);">
        <div class="sg-stag">Flujo de Trabajo</div>
        <h2>¿Cómo funciona <em>SIGMADE?</em></h2>
        <div class="sg-how">
            <div class="sg-step">
                <div class="sg-step-num">1</div>
                <h4>Ingresa</h4>
                <p>Inicia sesión con tu matrícula y contraseña oficial.</p>
            </div>
            <div class="sg-step">
                <div class="sg-step-num">2</div>
                <h4>Explora</h4>
                <p>Revisa el catálogo dinámico para encontrar el material que necesitas.</p>
            </div>
            <div class="sg-step">
                <div class="sg-step-num">3</div>
                <h4>Solicita</h4>
                <p>Acude con el administrador para confirmar tu préstamo y ¡listo!</p>
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
    <script>
        lucide.createIcons();

        /* Script de Clima - OpenWeatherMap */
        const weatherApiKey = '090347162e580d5356d0563cc892cf18';
        const lat = 19.726445;
        const lon = -101.162219;

        async function fetchWeather() {
            try {
                const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${weatherApiKey}&units=metric&lang=es`);
                const data = await response.json();
                
                if (data.cod === 200) {
                    document.getElementById('temp').innerText = `${Math.round(data.main.temp)}°C`;
                    const desc = data.weather[0].description;
                    document.getElementById('weather-desc').innerText = desc.charAt(0).toUpperCase() + desc.slice(1);
                    document.getElementById('weather-icon').src = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;
                    
                    const condition = data.weather[0].main;
                    const recText = (condition === 'Clear' || condition === 'Clouds') ? 'Óptimo para deportes exterior' : 'Recomendado interiores';
                    document.getElementById('weather-rec').innerText = recText;
                }
            } catch (error) {
                console.error('Error al obtener el clima:', error);
            }
        }

        /* Script de Mapa - Leaflet */
        function initMap() {
            const map = L.map('map-preview', {
                zoomControl: false,
                attributionControl: false
            }).setView([lat, lon], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            const utmIcon = L.icon({
                iconUrl: 'css/logoSigmade.png',
                iconSize: [32, 28],
                iconAnchor: [16, 14],
            });

            L.marker([lat, lon], {icon: utmIcon}).addTo(map)
                .bindPopup('<b>Almacén SIGMADE</b><br>Edificio de Deportes')
                .openPopup();
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchWeather();
            initMap();
        });
    </script>

</html>