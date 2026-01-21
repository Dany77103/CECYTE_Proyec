<?php
// Conexión a la base de datos usando PDO
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="styles.css">
    
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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
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
        
        /* Estilos para las tarjetas de reportes */
        .reports-container {
            padding: 30px;
        }
        
        .page-title {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            border-bottom: 3px solid var(--accent-color);
            padding-bottom: 15px;
        }
        
        .card-report {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            height: 100%;
            border-top: 5px solid;
        }
        
        .card-report:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .card-report .card-body {
            padding: 25px;
            text-align: center;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .card-text {
            color: #6c757d;
            margin-bottom: 20px;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .btn-report {
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            min-width: 140px;
        }
        
        .btn-report:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* Colores específicos para cada tarjeta */
        .card-alumnos {
            border-top-color: #3498db;
        }
        
        .card-maestros {
            border-top-color: #2ecc71;
        }
        
        .card-calificaciones {
            border-top-color: #95a5a6;
        }
        
        .card-academico-maestros {
            border-top-color: #f39c12;
        }
        
        .card-horarios {
            border-top-color: #2c3e50;
        }
        
        .card-asistencias {
            border-top-color: #ecf0f1;
        }
        
        .card-qr {
            border-top-color: #9b59b6;
        }
        
        .card-materias {
            border-top-color: #e74c3c;
        }
        
        .card-fotos {
            border-top-color: #1abc9c;
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
            
            .reports-container {
                padding: 15px;
            }
            
            .card-title {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn-report {
                min-width: 120px;
                padding: 8px 20px;
            }
        }
        
        /* Efecto para íconos en tarjetas */
        .card-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            display: block;
        }
        
        .card-alumnos .card-icon {
            color: #3498db;
        }
        
        .card-maestros .card-icon {
            color: #2ecc71;
        }
        
        .card-calificaciones .card-icon {
            color: #95a5a6;
        }
        
        .card-academico-maestros .card-icon {
            color: #f39c12;
        }
        
        .card-horarios .card-icon {
            color: #2c3e50;
        }
        
        .card-asistencias .card-icon {
            color: #7f8c8d;
        }
        
        .card-qr .card-icon {
            color: #9b59b6;
        }
        
        .card-materias .card-icon {
            color: #e74c3c;
        }
        
        .card-fotos .card-icon {
            color: #1abc9c;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-name">SISTEMA DE REPORTES</div>
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
                    <a href="reportes.php" class="nav-link active">
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
                
                <!-- Separador -->
                <li class="nav-item my-4">
                    <hr style="border-color: rgba(255,255,255,0.1); margin: 0 20px;">
                </li>
                
                <!-- Opciones adicionales -->
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
            <div class="user-section">
                <a href="logout.php" class="user-link">
                    <i class='bx bx-log-out-circle' style="font-size: 1.5rem;"></i>
                    <span class="link-text">Cerrar Sesión</span>
                    <span class="tooltip">Cerrar Sesión</span>
                </a>
            </div>
        </aside>
        
        <!-- Contenido principal -->
        <div class="content-wrapper">
            <!-- Header -->
            <header class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sistema de Reportes - CECyTE</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-3">CECyTE Santa Catarina N.L.</span>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class='bx bx-user-circle'></i> <?php echo $_SESSION['username'] ?? 'Usuario'; ?>
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
            <main class="reports-container">
                <h1 class="page-title">
                    <i class='bx bx-pencil'></i> Sistema de Reportes
                </h1>
                
                <div class="row g-4">
                    <!-- Tarjeta para Reporte de Alumnos -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-report card-alumnos">
                            <div class="card-body">
                                <i class="fas fa-users card-icon"></i>
                                <h5 class="card-title">
                                    <i class="fas fa-users"></i> Reporte de Alumnos
                                </h5>
                                <p class="card-text">Genera un reporte detallado de los alumnos con información completa y filtros avanzados.</p>
                                <button type="button" class="btn btn-primary btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteAlumnos">
                                    Ver Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta para Reporte de Maestros -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-report card-maestros">
                            <div class="card-body">
                                <i class="fas fa-chalkboard-teacher card-icon"></i>
                                <h5 class="card-title">
                                    <i class="fas fa-chalkboard-teacher"></i> Reporte de Maestros
                                </h5>
                                <p class="card-text">Genera un reporte detallado de los maestros con su información profesional y académica.</p>
                                <button type="button" class="btn btn-success btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteMaestros">
                                    Ver Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta para Reporte de Calificaciones -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-report card-calificaciones">
                            <div class="card-body">
                                <i class="fas fa-check-circle card-icon"></i>
                                <h5 class="card-title">
                                    <i class="fas fa-check-circle"></i> Reporte de Calificaciones
                                </h5>
                                <p class="card-text">Genera un reporte detallado de las calificaciones por grupo, materia y periodo.</p>
                                <button type="button" class="btn btn-secondary btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteCalificaciones">
                                    Ver Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta para Reporte Academico de Maestros -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-report card-academico-maestros">
                            <div class="card-body">
                                <i class="fas fa-briefcase card-icon"></i>
                                <h5 class="card-title">
                                    <i class="fas fa-briefcase"></i> Reporte Académico de Maestros
                                </h5>
                                <p class="card-text">Genera un reporte detallado académico de maestros con su formación y especialidades.</p>
                                <button type="button" class="btn btn-warning btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteDatosAcademicosMaestros">
                                    Ver Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta para Reporte de Horarios -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-report card-horarios">
                            <div class="card-body">
                                <i class="fas fa-calendar-alt card-icon"></i>
                                <h5 class="card-title">
                                    <i class="fas fa-calendar-alt"></i> Reporte de Horarios
                                </h5>
                                <p class="card-text">Genera un reporte detallado de los horarios de clases por grupo y maestro.</p>
                                <button type="button" class="btn btn-dark btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteHorarios">
                                    Ver Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta para Reporte de Asistencias -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-report card-asistencias">
                            <div class="card-body">
                                <i class="fas fa-clipboard-list card-icon"></i>
                                <h5 class="card-title">
                                    <i class="fas fa-clipboard-list"></i> Reporte de Asistencias
                                </h5>
                                <p class="card-text">Genera un reporte detallado de asistencias de alumnos y maestros por periodo.</p>
                                <button type="button" class="btn btn-light btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteAsistencias">
                                    Ver Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta para Sistema de Asistencia QR -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-report card-qr">
                            <div class="card-body">
                                <i class="fas fa-qrcode card-icon"></i>
                                <h5 class="card-title">
                                    <i class="fas fa-qrcode"></i> Sistema Asistencia QR
                                </h5>
                                <p class="card-text">Registro de asistencia mediante códigos QR para alumnos con entrada y salida automática.</p>
                                <a href="qr_asistencia.php" class="btn btn-primary btn-report">
                                    <i class="fas fa-external-link-alt me-2"></i> Acceder al Sistema
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta para Reporte de Materias -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-report card-materias">
                            <div class="card-body">
                                <i class="fas fa-book card-icon"></i>
                                <h5 class="card-title">
                                    <i class="fas fa-book"></i> Reporte de Materias
                                </h5>
                                <p class="card-text">Genera un reporte detallado de materias por carrera, semestre y horas crédito.</p>
                                <button type="button" class="btn btn-danger btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteMaterias">
                                    Ver Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarjeta para Reporte de Fotos Alumnos -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card card-report card-fotos">
                            <div class="card-body">
                                <i class="fas fa-camera card-icon"></i>
                                <h5 class="card-title">
                                    <i class="fas fa-camera"></i> Reporte de Fotos Alumnos
                                </h5>
                                <p class="card-text">Genera un reporte detallado de fotos de alumnos con información visual y datos.</p>
                                <button type="button" class="btn btn-success btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteFotoAlumno">
                                    Ver Reporte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="bg-success text-white text-center py-3 mt-5">
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        
        // Resaltar elemento activo en sidebar
        document.querySelectorAll('.sidebar-menu .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-menu .nav-link').forEach(item => {
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
        
        // Efecto hover mejorado para tarjetas
        document.querySelectorAll('.card-report').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 15px 35px rgba(0,0,0,0.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.08)';
            });
        });
        
        // Validación básica para formularios en modales
        document.addEventListener('DOMContentLoaded', function() {
            // Esta función se ejecutará cuando se carguen los modales desde modales_reportes.php
            // Puedes agregar validaciones específicas para cada modal aquí
            console.log('Reportes cargados correctamente');
        });
    </script>
    
    <!-- Incluir modales de reportes -->
    <?php include 'modales_reportes.php'; ?>
</body>
</html>