<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECYTE - Sistema de Reportes</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="stylesII.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed: 80px;
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --text-color: #ecf0f1;
            --hover-color: #1abc9c;
        }
        
        .main-container {
            display: flex;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        /* Sidebar mejorado */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            color: var(--text-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 3px 0 15px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }
        
        .sidebar-header {
            padding: 20px 15px;
            background-color: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .logo-name {
            opacity: 0;
            width: 0;
        }
        
        #btn-toggle {
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
            background: rgba(255,255,255,0.1);
            padding: 8px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        #btn-toggle:hover {
            background: var(--accent-color);
            transform: rotate(90deg);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .nav-item {
            list-style: none;
            margin: 5px 0;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            border-left: 4px solid transparent;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255,255,255,0.1);
            border-left-color: var(--hover-color);
            color: white;
        }
        
        .nav-link i {
            font-size: 1.3rem;
            min-width: 40px;
            text-align: center;
        }
        
        .link-text {
            margin-left: 10px;
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }
        
        .sidebar.collapsed .link-text {
            opacity: 0;
            width: 0;
        }
        
        .tooltip {
            position: absolute;
            left: calc(var(--sidebar-collapsed) + 10px);
            background: var(--primary-color);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1001;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .sidebar.collapsed .nav-link:hover .tooltip {
            opacity: 1;
            visibility: visible;
        }
        
        .user-section {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 15px;
            background: rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .user-link {
            display: flex;
            align-items: center;
            color: var(--text-color);
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .user-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        /* Contenido principal */
        .content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            background: #f8f9fa;
        }
        
        .sidebar.collapsed ~ .content-wrapper {
            margin-left: var(--sidebar-collapsed);
        }
        
        /* Header fijo */
        .main-header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        /* Contenido de la reseña histórica */
        .historia-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .historia-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .historia-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .historia-content {
            line-height: 1.8;
            color: #555;
            text-align: justify;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: var(--sidebar-collapsed);
            }
            
            .sidebar:not(.collapsed) {
                width: var(--sidebar-width);
            }
            
            .content-wrapper {
                margin-left: var(--sidebar-collapsed);
            }
            
            .sidebar:not(.collapsed) ~ .content-wrapper {
                margin-left: var(--sidebar-width);
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Sidebar mejorado -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-name">SISTEMA DE REPORTES CECYTE_SC </div>
                    <i class='bx bx-menu' id="btn-toggle"></i>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <!-- Barra de búsqueda -->
                <li class="nav-item">
                    <div class="nav-link search-box">
                        <i class='bx bx-search'></i>
                        <input type="text" class="form-control" placeholder="Buscar..." id="sidebar-search">
                        <span class="tooltip">Buscar en el sistema</span>
                    </div>
                </li>
                
                <!-- Menú principal -->
                <li class="nav-item">
                    <a href="main.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'main.php' ? 'active' : ''; ?>">
                        <i class='bx bx-home-alt-2'></i>
                        <span class="link-text">Inicio</span>
                        <span class="tooltip">Inicio</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="registro.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'registro.php' ? 'active' : ''; ?>">
                        <i class='bx bx-file'></i>
                        <span class="link-text">Registro de Información</span>
                        <span class="tooltip">Registro de Información</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="reportes.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reportes.php' ? 'active' : ''; ?>">
                        <i class='bx bx-pencil'></i>
                        <span class="link-text">Generar Reportes</span>
                        <span class="tooltip">Generar Reportes</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="estadisticas.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'estadisticas.php' ? 'active' : ''; ?>">
                        <i class='bx bx-chart'></i>
                        <span class="link-text">Estadísticas</span>
                        <span class="tooltip">Ver Estadísticas</span>
                    </a>
                </li>


		<li class="nav-item">
                    <a href="qr_asistencia.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'qr_asistencia.php' ? 'active' : ''; ?>">
                        <i class='bx bx-folder'></i>
                        <span class="link-text">Asistencia QR</span>
                        <span class="tooltip">Subir/Descargar Archivos</span>
                    </a>
                </li>

                
                <li class="nav-item">
                    <a href="updo.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'updo.php' ? 'active' : ''; ?>">
                        <i class='bx bx-folder'></i>
                        <span class="link-text">Archivos</span>
                        <span class="tooltip">Subir/Descargar Archivos</span>
                    </a>
                </li>

                
		<li class="nav-item">
                    <a href="qr_asistencia.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'qr_asistencia.php' ? 'active' : ''; ?>">
                        <i class='bx bx-folder'></i>
                        <span class="link-text">Asistencia QR</span>
                        <span class="tooltip">Subir/Descargar Archivos</span>
                    </a>
                </li>

                
                <li class="nav-item">
                      <a href="asignar_qr.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'asignar_qr.php' ? 'active' : ''; ?>">
        <i class='bx bx-qr'></i>
        <span class="link-text">Asignar QR</span>
        <span class="tooltip">Asignar código QR para alumnos</span>
                      </a>
                </li>
                
                <!-- Separador -->
                <li class="nav-item my-4">
                    <hr style="border-color: rgba(255,255,255,0.1); margin: 0 20px;">
                </li>
                
                <!-- Opciones adicionales si las necesitas -->
                <li class="nav-item">
                    <a href="configuracion.php" class="nav-link">
                        <i class='bx bx-cog'></i>
                        <span class="link-text">Configuración</span>
                        <span class="tooltip">Configuración del Sistema</span>
                    </a>
                </li>
                
                
                <li class="nav-item">
                    <a href="perfil.php" class="nav-link">
                        <i class='bx bx-user'></i>
                        <span class="link-text">Mi Perfil</span>
                        <span class="tooltip">Mi Perfil de Usuario</span>
                    </a>
                </li>
            </ul>
            
            <!-- Sección de usuario -->
                <a href="logout.php" class="user-link">
                    <i class='bx bx-log-out-circle' style="font-size: 1.5rem;"></i>
                    <span class="link-text">Cerrar Sesión</span>
                    <span class="tooltip">Cerrar Sesión</span>
                </a>
        </aside>
        
        <!-- Contenido principal -->
        <div class="content-wrapper">
            <!-- Header -->
            <header class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bienvenido, <?php echo $_SESSION['username'] ?? 'Usuario'; ?></h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-3">CECyTE Santa Catarina N.L.</span>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class='bx bx-user-circle'></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="perfil.php">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="configuracion.php">Configuración</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Contenido de la página -->
            <main class="container-fluid py-4">
                <div class="historia-container">
                    <div class="historia-header">
                        <h2 class="historia-title">Reseña Histórica - CECyTE Nuevo León</h2>
                    </div>
                    <div class="historia-content">
                        <p>
                            El Colegio de Estudios Científicos y Tecnológicos del Estado de Nuevo León (CECyTE NL) se creó el 18 de agosto de 1993, a través de un acuerdo de colaboración entre la secretaría de educación pública del gobierno federal, en representación del Dr. Ernesto Zedillo Ponce de León, y el Gobierno de Nuevo León, en representación del Lic. Sócrates Uauhtémoc Rizzo García, presidente del Tribunal Constitucional.
                        </p>
                        <p>
                            Este acuerdo se ratificó mediante el decreto de creación 287, emitido el 11 de mayo de 1994, y modificado con el decreto 340 el 19 de mayo de 2003. En sus comienzos, el CECyTE NL se fundó como una nueva alternativa de educación de nivel medio superior en la región, proporcionando servicios en cuatro establecimientos situados en los municipios de Apodaca, García, Linares y Marín.
                        </p>
                        <p>
                            El programa educativo local contemplaba tres profesiones técnicas: administración, electrónica y programación. El Colegio implementó el bachillerato general conocido como Educación Media Superior a Distancia (EMSAD), comenzando con un establecimiento en el municipio de Lampazos de Naranjo, N.L.
                        </p>
                        <p>
                            En la actualidad, el CECyTE NL dispone de 17 establecimientos que imparten el Bachillerato Tecnológico con 20 áreas técnicas autorizadas, junto con 17 Centros EMSAD que ofrecen educación remota. El Colegio se ha enfocado en potencializar sus índices estadísticos fundamentales, tales como la eficiencia para terminal con el abandono escolar y la reprobación, además de reforzar la formación de los profesores, el trabajo en equipo, la educación dual, la electromovilidad, la inclusión, el crecimiento socioemocional y la salud integral de la comunidad educativa.
                        </p>
                    </div>
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="bg-success text-white text-center py-3 mt-4">
                <div class="container">
                    <p class="mb-1">CECyTE SANTA CATARINA N.L.</p>
                    <p class="mb-0">© <?php echo date("Y"); ?> Sistema de Reportes. Todos los derechos reservados.</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Toggle del sidebar
        document.getElementById('btn-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            
            // Cambiar icono
            const icon = this;
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('bx-menu');
                icon.classList.add('bx-menu-alt-right');
            } else {
                icon.classList.remove('bx-menu-alt-right');
                icon.classList.add('bx-menu');
            }
        });
        
        // Resaltar elemento activo
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.nav-link').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
        
        // Buscar en el sidebar
        document.getElementById('sidebar-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.nav-item').forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm) || searchTerm === '') {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Auto-colapsar en móviles
        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                document.getElementById('btn-toggle').classList.remove('bx-menu');
                document.getElementById('btn-toggle').classList.add('bx-menu-alt-right');
            } else {
                sidebar.classList.remove('collapsed');
                document.getElementById('btn-toggle').classList.remove('bx-menu-alt-right');
                document.getElementById('btn-toggle').classList.add('bx-menu');
            }
        }
        
        window.addEventListener('resize', handleResize);
        window.addEventListener('load', handleResize);
    </script>
</body>
</html>