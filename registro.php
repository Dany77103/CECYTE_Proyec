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
    <title>CECYTE - Sistema de Registro</title>
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
        
        /* Estilos para el sistema de registro */
        .registration-container {
            padding: 30px;
        }
        
        .page-title {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            border-bottom: 3px solid var(--accent-color);
            padding-bottom: 15px;
        }
        
        .registration-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .registration-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-top: 5px solid;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .registration-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .card-header-custom {
            padding: 25px 25px 15px;
            text-align: center;
            flex-grow: 1;
        }
        
        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
        
        .card-title-custom {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .card-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .card-footer-custom {
            padding: 20px 25px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }
        
        .btn-registration {
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            min-width: 160px;
        }
        
        .btn-registration:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* Colores específicos para cada tarjeta */
        .card-alumnos {
            border-top-color: #3498db;
        }
        
        .card-alumnos .card-icon {
            color: #3498db;
        }
        
        .card-maestros {
            border-top-color: #2ecc71;
        }
        
        .card-maestros .card-icon {
            color: #2ecc71;
        }
        
        .card-laborales {
            border-top-color: #17a2b8;
        }
        
        .card-laborales .card-icon {
            color: #17a2b8;
        }
        
        .card-academicos {
            border-top-color: #f39c12;
        }
        
        .card-academicos .card-icon {
            color: #f39c12;
        }
        
        .card-historial {
            border-top-color: #e74c3c;
        }
        
        .card-historial .card-icon {
            color: #e74c3c;
        }
        
        .card-calificaciones {
            border-top-color: #6c757d;
        }
        
        .card-calificaciones .card-icon {
            color: #6c757d;
        }
        
        .card-horarios {
            border-top-color: #2c3e50;
        }
        
        .card-horarios .card-icon {
            color: #2c3e50;
        }
        
        .card-fotos {
            border-top-color: #1abc9c;
        }
        
        .card-fotos .card-icon {
            color: #1abc9c;
        }
        
        /* Estadísticas de registro */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-alumnos .stat-number {
            color: #3498db;
        }
        
        .stat-maestros .stat-number {
            color: #2ecc71;
        }
        
        .stat-calificaciones .stat-number {
            color: #6c757d;
        }
        
        .stat-fotos .stat-number {
            color: #1abc9c;
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
            
            .registration-container {
                padding: 15px;
            }
            
            .registration-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 480px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="logo-name">SISTEMA DE REGISTRO</div>
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
                    <a href="registro.php" class="nav-link active">
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
                    <h5 class="mb-0">Sistema de Registro - CECyTE</h5>
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
            <main class="registration-container">
                <h1 class="page-title">
                    <i class='bx bx-file'></i> Sistema de Registro
                </h1>
                
                <!-- Estadísticas -->
                <div class="stats-container">
                    <?php
                    // Consultas para obtener estadísticas
                    try {
                        // Total de alumnos
                        $sql_alumnos = "SELECT COUNT(*) as total FROM alumnos";
                        $stmt_alumnos = $con->prepare($sql_alumnos);
                        $stmt_alumnos->execute();
                        $total_alumnos = $stmt_alumnos->fetch(PDO::FETCH_ASSOC)['total'];
                        
                        // Total de maestros
                        $sql_maestros = "SELECT COUNT(*) as total FROM maestros";
                        $stmt_maestros = $con->prepare($sql_maestros);
                        $stmt_maestros->execute();
                        $total_maestros = $stmt_maestros->fetch(PDO::FETCH_ASSOC)['total'];
                        
                        // Total de calificaciones
                        $sql_calificaciones = "SELECT COUNT(*) as total FROM calificaciones";
                        $stmt_calificaciones = $con->prepare($sql_calificaciones);
                        $stmt_calificaciones->execute();
                        $total_calificaciones = $stmt_calificaciones->fetch(PDO::FETCH_ASSOC)['total'];
                        
                        // Total de fotos
                        $sql_fotos = "SELECT COUNT(*) as total FROM fotos_alumnos";
                        $stmt_fotos = $con->prepare($sql_fotos);
                        $stmt_fotos->execute();
                        $total_fotos = $stmt_fotos->fetch(PDO::FETCH_ASSOC)['total'];
                    } catch (PDOException $e) {
                        // Si hay error, mostrar valores por defecto
                        $total_alumnos = 0;
                        $total_maestros = 0;
                        $total_calificaciones = 0;
                        $total_fotos = 0;
                    }
                    ?>
                    
                    <div class="stat-card stat-alumnos">
                        <div class="stat-number"><?php echo $total_alumnos; ?></div>
                        <div class="stat-label">Alumnos Registrados</div>
                    </div>
                    
                    <div class="stat-card stat-maestros">
                        <div class="stat-number"><?php echo $total_maestros; ?></div>
                        <div class="stat-label">Maestros Registrados</div>
                    </div>
                    
                    <div class="stat-card stat-calificaciones">
                        <div class="stat-number"><?php echo $total_calificaciones; ?></div>
                        <div class="stat-label">Calificaciones</div>
                    </div>
                    
                    <div class="stat-card stat-fotos">
                        <div class="stat-number"><?php echo $total_fotos; ?></div>
                        <div class="stat-label">Fotos Registradas</div>
                    </div>
                </div>
                
                <!-- Grid de opciones de registro -->
                <div class="registration-grid">
                    <!-- Alta de Alumnos -->
                    <div class="registration-card card-alumnos">
                        <div class="card-header-custom">
                            <i class="fas fa-user-graduate card-icon"></i>
                            <h5 class="card-title-custom">Alta de Alumnos</h5>
                            <p class="card-description">Registra nuevos alumnos con información personal, académica y de contacto.</p>
                        </div>
                        <div class="card-footer-custom">
                            <button type="button" class="btn btn-primary btn-registration" data-bs-toggle="modal" data-bs-target="#modalAlumnos">
                                <i class="fas fa-plus-circle me-2"></i> Registrar Alumno
                            </button>
                        </div>
                    </div>
                    
                    <!-- Alta de Maestros o Administrativos -->
                    <div class="registration-card card-maestros">
                        <div class="card-header-custom">
                            <i class="fas fa-chalkboard-teacher card-icon"></i>
                            <h5 class="card-title-custom">Alta de Maestros/Admvos.</h5>
                            <p class="card-description">Registra maestros y personal administrativo con información profesional.</p>
                        </div>
                        <div class="card-footer-custom">
                            <button type="button" class="btn btn-success btn-registration" data-bs-toggle="modal" data-bs-target="#modalMaestros">
                                <i class="fas fa-plus-circle me-2"></i> Registrar Personal
                            </button>
                        </div>
                    </div>
                    
                    <!-- Datos Laborales Maestros o Administrativos -->
                    <div class="registration-card card-laborales">
                        <div class="card-header-custom">
                            <i class="fas fa-briefcase card-icon"></i>
                            <h5 class="card-title-custom">Datos Laborales</h5>
                            <p class="card-description">Registra información laboral de maestros y personal administrativo.</p>
                        </div>
                        <div class="card-footer-custom">
                            <button type="button" class="btn btn-info btn-registration" data-bs-toggle="modal" data-bs-target="#modalDatosLaborales">
                                <i class="fas fa-briefcase me-2"></i> Registrar Datos
                            </button>
                        </div>
                    </div>
                    
                    <!-- Datos Académicos Maestros o Administrativos -->
                    <div class="registration-card card-academicos">
                        <div class="card-header-custom">
                            <i class="fas fa-graduation-cap card-icon"></i>
                            <h5 class="card-title-custom">Datos Académicos</h5>
                            <p class="card-description">Registra formación académica, certificaciones y especialidades del personal.</p>
                        </div>
                        <div class="card-footer-custom">
                            <button type="button" class="btn btn-warning btn-registration" data-bs-toggle="modal" data-bs-target="#modalDatosAcademicos">
                                <i class="fas fa-graduation-cap me-2"></i> Registrar Datos
                            </button>
                        </div>
                    </div>
                    
                    <!-- Historial Académico Alumnos -->
                    <div class="registration-card card-historial">
                        <div class="card-header-custom">
                            <i class="fas fa-history card-icon"></i>
                            <h5 class="card-title-custom">Historial Académico</h5>
                            <p class="card-description">Registra el historial académico completo de los alumnos.</p>
                        </div>
                        <div class="card-footer-custom">
                            <button type="button" class="btn btn-danger btn-registration" data-bs-toggle="modal" data-bs-target="#modalHistorialAcademico">
                                <i class="fas fa-history me-2"></i> Registrar Historial
                            </button>
                        </div>
                    </div>
                    
                    <!-- Calificaciones Alumnos -->
                    <div class="registration-card card-calificaciones">
                        <div class="card-header-custom">
                            <i class="fas fa-check-circle card-icon"></i>
                            <h5 class="card-title-custom">Calificaciones Alumnos</h5>
                            <p class="card-description">Registra calificaciones por materia, periodo y grupo.</p>
                        </div>
                        <div class="card-footer-custom">
                            <button type="button" class="btn btn-secondary btn-registration" data-bs-toggle="modal" data-bs-target="#modalCalificaciones">
                                <i class="fas fa-check-circle me-2"></i> Registrar Calificaciones
                            </button>
                        </div>
                    </div>
                    
                    <!-- Horarios Maestros -->
                    <div class="registration-card card-horarios">
                        <div class="card-header-custom">
                            <i class="fas fa-calendar-alt card-icon"></i>
                            <h5 class="card-title-custom">Horarios Maestros</h5>
                            <p class="card-description">Registra horarios de clases, asesorías y actividades extracurriculares.</p>
                        </div>
                        <div class="card-footer-custom">
                            <button type="button" class="btn btn-dark btn-registration" data-bs-toggle="modal" data-bs-target="#modalHorarios">
                                <i class="fas fa-calendar-alt me-2"></i> Registrar Horarios
                            </button>
                        </div>
                    </div>
                    
                    <!-- Fotos y Perfil de Alumnos -->
                    <div class="registration-card card-fotos">
                        <div class="card-header-custom">
                            <i class="fas fa-camera card-icon"></i>
                            <h5 class="card-title-custom">Fotos y Perfil</h5>
                            <p class="card-description">Registra fotos de perfil y datos adicionales de los alumnos.</p>
                        </div>
                        <div class="card-footer-custom">
                            <button type="button" class="btn btn-success btn-registration" data-bs-toggle="modal" data-bs-target="#modalSubirFoto">
                                <i class="fas fa-camera me-2"></i> Subir Foto
                            </button>
                        </div>
                    </div>
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="bg-success text-white text-center py-3 mt-5">
                <div class="container">
                    <p class="mb-1">CECyTE SANTA CATARINA N.L.</p>
                    <p class="mb-0">© <?php echo date("Y"); ?> Sistema de Registro. Todos los derechos reservados.</p>
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
        document.querySelectorAll('.registration-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 15px 35px rgba(0,0,0,0.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.08)';
            });
        });
        
        // Efecto hover para estadísticas
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
            });
        });
        
        // Validación de formularios en modales
        document.addEventListener('DOMContentLoaded', function() {
            // Esta función se ejecutará cuando se carguen los modales desde modales.php
            console.log('Sistema de registro cargado correctamente');
            
            // Agregar validación a todos los formularios dentro de modales
            document.querySelectorAll('.modal form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const inputs = this.querySelectorAll('input[required], select[required], textarea[required]');
                    let isValid = true;
                    
                    inputs.forEach(input => {
                        if (input.value.trim() === '') {
                            isValid = false;
                            input.classList.add('is-invalid');
                            
                            // Agregar mensaje de error si no existe
                            if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = 'Este campo es requerido';
                                input.parentNode.appendChild(errorDiv);
                            }
                        } else {
                            input.classList.remove('is-invalid');
                            
                            // Remover mensaje de error si existe
                            if (input.nextElementSibling && input.nextElementSibling.classList.contains('invalid-feedback')) {
                                input.nextElementSibling.remove();
                            }
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Mostrar alerta
                        const modal = this.closest('.modal');
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            <strong>Error:</strong> Por favor, complete todos los campos requeridos.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        
                        const modalBody = modal.querySelector('.modal-body');
                        modalBody.insertBefore(alertDiv, modalBody.firstChild);
                        
                        // Auto-eliminar alerta después de 5 segundos
                        setTimeout(() => {
                            if (alertDiv.parentNode) {
                                alertDiv.remove();
                            }
                        }, 5000);
                    }
                });
            });
        });
        
        // Actualizar estadísticas automáticamente cada 30 segundos
        function actualizarEstadisticas() {
            $.ajax({
                url: 'actualizar_estadisticas.php',
                type: 'GET',
                success: function(response) {
                    try {
                        const stats = JSON.parse(response);
                        if (stats.alumnos !== undefined) {
                            document.querySelector('.stat-alumnos .stat-number').textContent = stats.alumnos;
                        }
                        if (stats.maestros !== undefined) {
                            document.querySelector('.stat-maestros .stat-number').textContent = stats.maestros;
                        }
                        if (stats.calificaciones !== undefined) {
                            document.querySelector('.stat-calificaciones .stat-number').textContent = stats.calificaciones;
                        }
                        if (stats.fotos !== undefined) {
                            document.querySelector('.stat-fotos .stat-number').textContent = stats.fotos;
                        }
                    } catch (e) {
                        console.error('Error al actualizar estadísticas:', e);
                    }
                },
                error: function() {
                    console.error('Error al conectar con el servidor para actualizar estadísticas');
                }
            });
        }
        
        // Actualizar cada 30 segundos si la página está activa
        setInterval(actualizarEstadisticas, 30000);
        
        // También actualizar cuando se vuelve a la página
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                actualizarEstadisticas();
            }
        });
    </script>
    
    <!-- Incluir modales de registro -->
    <?php include 'modales.php'; ?>
</body>
</html>