<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="css/logoSigmade.png">
    <title>SIGMADE - Acceso y Registro</title>
    
    <link rel="stylesheet" href="css/cssNuevo.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/login.css?v=<?php echo time(); ?>">
    
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="js/theme.js?v=<?php echo time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="sg sg-login-page">

    <div class="sg-login-bg"></div>
    
    <a href="index.php" class="sg-back-btn" title="Volver al inicio">
        <i data-lucide="home"></i>
        <span>Inicio</span>
    </a>

    <nav class="sg-nav-login">
        <div class="sg-nav-logo" style="display:flex; align-items:center; position: relative;">
            <img src="css/logoSigmade.png" alt="SIGMADE" width="70" height="60" style="margin-right: 15px; filter: drop-shadow(0 0 5px rgba(168,32,53,0.5));">
            SIG<span>MADE</span>
            <button class="theme-toggle-btn" title="Alternar Tema" style="margin-left: 20px;"><i data-lucide="sun"></i></button>
        </div>
    </nav>

    <input type="checkbox" id="flip-toggle">

    <main class="sg-card-wrapper">
        <div class="sg-flip-card-inner">
            
            <!-- CONTENEDOR DE INICIAR SESIÓN -->
            <section class="sg-flip-front">
                <h2 class="sg-form-title">Bienvenido</h2>
                <form action="CRUD/procesarLogin.php" method="post" id="formLogin">
                    <div class="sg-form-group">
                        <label for="loginMatri">Ingresa tu Matricula</label>
                        <input type="text" id="loginMatri" name="email" placeholder="ejemplo@correo.com" required />
                    </div>

                    <div class="sg-form-group">
                        <label for="loginPassword">Contraseña</label>
                        <input type="password" id="loginPassword" name="password" placeholder="••••••••" required />
                    </div>
                    
                    <button id="login" type="submit" class="sg-btn-p btn-submit-full" style="background: linear-gradient(135deg, var(--crimson) 0%, var(--crimson-light) 100%); color: var(--off-white); border: none; padding: 14px; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; margin-top: 2rem; width: 100%; box-shadow: 0 4px 10px rgba(168, 32, 53, 0.3);">Iniciar Sesión</button>
                    
                    <?php if (isset($_GET['error'])): ?>
                        <div style="margin-top: 15px; color: #ef4444; font-size: 14px; background: rgba(220, 38, 38, 0.1); padding: 10px; border-radius: 6px; border: 1px solid rgba(220, 38, 38, 0.3);">
                            Credenciales incorrectas.
                        </div>
                    <?php endif; ?>
                </form>
                
                <div class="sg-switch-text">
                    ¿Aún no te registras? <label for="flip-toggle">Regístrate aquí</label>
                </div>
            </section>

            <!-- CONTENEDOR DE REGISTRO -->
            <section class="sg-flip-back">
                <h2 class="sg-form-title">Registro</h2>
                <form id="formUsuarios">
                    <div class="sg-scroll-form">
                        <div class="sg-form-group">
                            <label for="rol">Tipo de usuario</label>
                            <select id="rol" name="rol" required>
                                <option value="">Selecciona</option>
                                <option value="Alumno">Alumno</option>
                                <option value="Docente">Entrenador</option>
                                <option value="Admin">Administrador</option>
                                <option value="Operador">Operador</option>
                            </select>
                        </div>
                        
                        <div class="sg-form-row">
                            <div class="sg-form-group">
                                <label for="nombre">Nombre(s)</label>
                                <input type="text" id="nombre" name="nombre" placeholder="Nombres" required />
                            </div>
                            <div class="sg-form-group">
                                <label for="apellidos">Apellidos</label>
                                <input type="text" id="apellidos" name="apellidos" placeholder="Apellidos" required />
                            </div>
                        </div>

                        <div class="sg-form-group">
                            <label for="iden">Matrícula / Nómina</label>
                            <input type="text" id="iden" name="iden" placeholder="Matricula/Número de trabajador" required />
                        </div>

                        <div class="sg-form-group">
                            <label for="tel">Número telefónico</label>
                            <input type="tel" id="tel" name="tel" placeholder="10 dígitos" required />
                        </div>

                        <div class="sg-form-group">
                            <label for="correo">Correo electrónico</label>
                            <input type="email" id="correo" name="email" placeholder="correo@utm.mx" required />
                        </div>

                        <div class="sg-form-group">
                            <label for="passwordUs">Contraseña</label>
                            <input type="password" id="passwordUs" name="password" placeholder="Crea una contraseña" required />
                        </div>
                    </div>

                    <button id="Registrar" type="submit" onclick="guardarUsuario(event)" class="sg-btn-p btn-submit-full" style="background: linear-gradient(135deg, var(--crimson) 0%, var(--crimson-light) 100%); color: var(--off-white); border: none; padding: 14px; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; margin-top: 1.5rem; width: 100%; box-shadow: 0 4px 10px rgba(168, 32, 53, 0.3);">Realizar registro</button>
                </form>
                <div class="sg-switch-text">
                    ¿Ya tienes cuenta? <label for="flip-toggle">Inicia sesión</label>
                </div>
            </section>

        </div>
    </main>

    <!-- Script original o inyectado para guardar usuarios si el JS que lo lanza estuviera fallando en carga -->
    <script src="modal-js/alertas.js"></script>
    <script src="modal-js/usuarios.js"></script>
    <script>
        lucide.createIcons();
    </script>

</body>
</html>