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
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #064e3b;    
            --secondary-color: #065f46;  
            --accent-color: #10b981;     
            --bg-light: #f0fdf4;         
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: #1e293b;
        }

        /* Header Consistente */
        .main-header {
            background: #fff;
            padding: 15px 40px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--accent-color);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            background: rgba(16, 185, 129, 0.1);
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-back:hover {
            background: var(--primary-color);
            color: white;
            transform: translateX(-5px);
        }

        .main-content {
            padding: 40px 20px;
            max-width: 1300px;
            margin: 0 auto;
        }

        .page-title {
            font-weight: 800;
            color: var(--primary-color);
            letter-spacing: -0.025em;
        }

        /* Tarjetas de Reportes Estilo Unificado */
        .card-report {
            background: white;
            border: none;
            border-radius: 24px;
            padding: 35px 25px;
            text-align: center;
            height: 100%;
            transition: var(--transition);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border-bottom: 4px solid transparent;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-report:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(6, 78, 59, 0.1);
            border-bottom: 4px solid var(--accent-color);
        }

        .card-icon-circle {
            width: 85px;
            height: 85px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2.2rem;
            background: var(--bg-light);
            color: var(--primary-color);
            transition: var(--transition);
        }

        .card-report:hover .card-icon-circle {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .card-report h5 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .card-report p {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        /* Botón de Reporte Unificado */
        .btn-report {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            width: 100%;
            transition: var(--transition);
        }

        .btn-report:hover {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 12px rgba(6, 78, 59, 0.2);
        }

        footer {
            background: white;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding: 30px 0;
            margin-top: 60px;
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a href="main.php" class="btn-back">
                <i class='bx bx-left-arrow-alt fs-4'></i>
                <span>Volver al Menú</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <span class="d-block small text-muted">Sesión de Reportes</span>
                    <span class="fw-bold" style="color: var(--primary-color);"><?php echo $_SESSION['username'] ?? 'Usuario'; ?></span>
                </div>
                <img src="https://ui-avatars.com/api/?name=Admin&background=064e3b&color=fff" class="rounded-circle border border-2 border-success" width="40">
            </div>
        </div>
    </header>

    <div class="main-content">
        <div class="mb-5">
            <span class="badge mb-2 px-3 py-2 rounded-pill" style="background: var(--accent-color);">Módulo de Exportación</span>
            <h1 class="page-title">Centro de Documentación y Reportes</h1>
            <p class="text-muted">Genera archivos oficiales y consultas detalladas del sistema.</p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-report">
                    <div>
                        <div class="card-icon-circle"><i class="fas fa-users"></i></div>
                        <h5>Reporte de Alumnos</h5>
                        <p>Documentación detallada de la matrícula escolar con filtros por grado y grupo.</p>
                    </div>
                    <button class="btn btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteAlumnos">
                        <i class='bx bx-file-find me-2'></i>Generar Reporte
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-report">
                    <div>
                        <div class="card-icon-circle"><i class="fas fa-chalkboard-teacher"></i></div>
                        <h5>Reporte de Maestros</h5>
                        <p>Información profesional, contacto y asignaciones de la plantilla docente.</p>
                    </div>
                    <button class="btn btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteMaestros">
                        <i class='bx bx-file-find me-2'></i>Generar Reporte
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-report">
                    <div>
                        <div class="card-icon-circle"><i class="fas fa-check-circle"></i></div>
                        <h5>Calificaciones</h5>
                        <p>Sábanas de notas y promedios por periodo académico y materias.</p>
                    </div>
                    <button class="btn btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteCalificaciones">
                        <i class='bx bx-file-find me-2'></i>Generar Reporte
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-report">
                    <div>
                        <div class="card-icon-circle"><i class="fas fa-briefcase"></i></div>
                        <h5>Académico Docente</h5>
                        <p>Perfil académico detallado, especialidades y formación profesional.</p>
                    </div>
                    <button class="btn btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteDatosAcademicosMaestros">
                        <i class='bx bx-file-find me-2'></i>Generar Reporte
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-report">
                    <div>
                        <div class="card-icon-circle"><i class="fas fa-calendar-alt"></i></div>
                        <h5>Reporte de Horarios</h5>
                        <p>Gestión visual de horarios de clase por grupo y maestro asignado.</p>
                    </div>
                    <button class="btn btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteHorarios">
                        <i class='bx bx-file-find me-2'></i>Generar Reporte
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-report">
                    <div>
                        <div class="card-icon-circle"><i class="fas fa-clipboard-list"></i></div>
                        <h5>Asistencias</h5>
                        <p>Registro de puntualidad y asistencia por periodos personalizados.</p>
                    </div>
                    <button class="btn btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteAsistencias">
                        <i class='bx bx-file-find me-2'></i>Generar Reporte
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-report">
                    <div>
                        <div class="card-icon-circle"><i class="fas fa-qrcode"></i></div>
                        <h5>Asistencia QR</h5>
                        <p>Acceso al sistema de escaneo y registro automático de entradas y salidas.</p>
                    </div>
                    <a href="qr_asistencia.php" class="btn btn-report">
                        <i class='bx bx-link-external me-2'></i>Abrir Sistema
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-report">
                    <div>
                        <div class="card-icon-circle"><i class="fas fa-book"></i></div>
                        <h5>Plan de Materias</h5>
                        <p>Listado de currícula por carrera, semestre y carga de créditos.</p>
                    </div>
                    <button class="btn btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteMaterias">
                        <i class='bx bx-file-find me-2'></i>Generar Reporte
                    </button>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-report">
                    <div>
                        <div class="card-icon-circle"><i class="fas fa-camera"></i></div>
                        <h5>Archivo Fotográfico</h5>
                        <p>Reporte visual de alumnos para identificación oficial y expedientes.</p>
                    </div>
                    <button class="btn btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteFotoAlumno">
                        <i class='bx bx-file-find me-2'></i>Generar Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <p class="mb-1 fw-bold text-dark">CECyTE SANTA CATARINA N.L.</p>
            <p class="mb-0 small">© <?php echo date("Y"); ?> Sistema de Gestión Escolar. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <?php include 'modales_reportes.php'; ?>
</body>
</html>