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

// --- 1. LÓGICA PARA CALIFICACIONES ---
$sql_calif = "SELECT c.id_calificacion, c.matriculaAlumno, a.nombre, a.apellidoPaterno, c.numEmpleado, c.calificacion
              FROM calificaciones c
              INNER JOIN alumnos a ON c.matriculaAlumno = a.matriculaAlumno
              ORDER BY c.id_calificacion DESC";
$stmt_calif = $con->query($sql_calif);
$calificaciones = $stmt_calif->fetchAll(PDO::FETCH_ASSOC);

// --- 2. LÓGICA PARA ALUMNOS Y FOTOS ---
$sql_fotos = "SELECT id_alumno, matriculaAlumno, nombre, apellidoPaterno, apellidoMaterno, rutaImagen 
              FROM alumnos 
              ORDER BY apellidoPaterno ASC";
$stmt_fotos = $con->query($sql_fotos);
$fotos_alumnos = $stmt_fotos->fetchAll(PDO::FETCH_ASSOC);

// --- 3. LÓGICA PARA MAESTROS ---
try {
    $sql_maestros = "SELECT num_empleado, nombre, apellido_paterno, apellido_materno, rfc, estatus, cargo 
                     FROM personal_institucional 
                     WHERE cargo = 'Maestro' 
                     ORDER BY apellido_paterno ASC";
    $stmt_maestros = $con->query($sql_maestros);
    $maestros_unificados = $stmt_maestros->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $maestros_unificados = [];
}

// --- 4. LÓGICA PARA GRUPOS (Sincronizada con tus imágenes: id_grupo, grupo, cantidad_alumnos) ---
try {
    $sql_grupos = "SELECT id_grupo, grupo, cantidad_alumnos 
                   FROM grupos 
                   ORDER BY grupo ASC";
    $stmt_grupos = $con->query($sql_grupos);
    $grupos = $stmt_grupos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $grupos = [];
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
            --accent-color: #10b981;     
            --bg-light: #f0fdf4;         
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg-light); color: #1e293b; }

        .main-header {
            background: #fff; padding: 15px 40px; box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 100; border-bottom: 3px solid var(--accent-color);
        }

        .btn-back {
            display: inline-flex; align-items: center; gap: 8px; color: var(--primary-color);
            background: rgba(16, 185, 129, 0.1); padding: 10px 20px; border-radius: 12px;
            text-decoration: none; font-weight: 600; transition: var(--transition);
        }

        .btn-back:hover { background: var(--primary-color); color: white; transform: translateX(-5px); }

        .main-content { padding: 40px 20px; max-width: 1300px; margin: 0 auto; }

        .card-report {
            background: white; border: none; border-radius: 24px; padding: 35px 25px;
            text-align: center; height: 100%; transition: var(--transition);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); display: flex; flex-direction: column; justify-content: space-between;
        }

        .card-report:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(6, 78, 59, 0.1); }

        .card-icon-circle {
            width: 85px; height: 85px; border-radius: 20px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 25px; font-size: 2.2rem;
            background: var(--bg-light); color: var(--primary-color);
        }

        .btn-report {
            background: transparent; border: 2px solid var(--primary-color); color: var(--primary-color);
            border-radius: 12px; padding: 12px 20px; font-weight: 600; width: 100%;
            transition: var(--transition); text-decoration: none; display: flex;
            align-items: center; justify-content: center; cursor: pointer;
        }

        .btn-report:hover { background: var(--primary-color); color: white; }

        .table-custom thead { background-color: var(--primary-color); color: white; }
        .img-alumno-tabla { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-color); }
        footer { background: white; color: #64748b; border-top: 1px solid #e2e8f0; padding: 30px 0; margin-top: 60px; }
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
                <span class="fw-bold" style="color: var(--primary-color);"><?php echo $_SESSION['username'] ?? 'Admin'; ?></span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=064e3b&color=fff" class="rounded-circle" width="40">
            </div>
        </div>
    </header>

    <div class="main-content">
        <div class="mb-5">
            <h1 class="fw-bold" style="color: var(--primary-color);">Centro de Reportes</h1>
            <p class="text-muted">Gestión integral de datos académicos y administrativos.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-report">
                    <div class="card-icon-circle"><i class="fas fa-user-graduate"></i></div>
                    <h5>Reporte Alumnos</h5>
                    <p>Listado general y matrículas de estudiantes.</p>
                    <button class="btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteAlumnos">Generar Reporte</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-report">
                    <div class="card-icon-circle"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h5>Reporte Maestros</h5>
                    <p>Plantilla docente activa e información institucional.</p>
                    <button class="btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteMaestros">Generar Reporte</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-report">
                    <div class="card-icon-circle"><i class="fas fa-users"></i></div>
                    <h5>Consulta de Grupos</h5>
                    <p>Visualización de grupos y carga de alumnos.</p>
                    <button class="btn-report" data-bs-toggle="modal" data-bs-target="#modalConsultaGrupos">Consultar Grupos</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-report">
                    <div class="card-icon-circle"><i class="fas fa-user-tie"></i></div>
                    <h5>Reporte Personal</h5>
                    <p>Documentación de personal administrativo.</p>
                    <a href="reporte_personal.php" class="btn-report">Ver Reporte</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-report">
                    <div class="card-icon-circle"><i class="fas fa-check-circle"></i></div>
                    <h5>Calificaciones</h5>
                    <p>Sábanas de notas y seguimiento académico.</p>
                    <button class="btn-report" data-bs-toggle="modal" data-bs-target="#modalListaCalificaciones">Ver Calificaciones</button>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-report">
                    <div class="card-icon-circle"><i class="fas fa-camera"></i></div>
                    <h5>Archivo Fotográfico</h5>
                    <p>Identificación visual para expedientes oficiales.</p>
                    <button class="btn-report" data-bs-toggle="modal" data-bs-target="#modalReporteFotoAlumno">Ver Fotos</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConsultaGrupos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header text-white" style="background: var(--primary-color);">
                    <h5 class="modal-title fw-bold"><i class='bx bx-grid-alt me-2'></i>Control de Grupos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-custom">
                            <thead>
                                <tr>
                                    <th>ID Grupo</th>
                                    <th>Nombre del Grupo</th>
                                    <th class="text-center">Cant. Alumnos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($grupos) > 0): ?>
                                    <?php foreach ($grupos as $g): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($g['id_grupo']); ?></span></td>
                                        <td class="fw-bold text-success"><?php echo htmlspecialchars($g['grupo']); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-success px-4 py-2">
                                                <i class="fas fa-users me-2"></i><?php echo $g['cantidad_alumnos']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">No hay datos registrados.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalReporteAlumnos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white"><h5 class="modal-title">Lista de Alumnos</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <thead><tr><th>Matrícula</th><th>Nombre</th></tr></thead>
                        <tbody><?php foreach ($fotos_alumnos as $a): ?><tr><td><?php echo $a['matriculaAlumno']; ?></td><td><?php echo $a['apellidoPaterno']." ".$a['nombre']; ?></td></tr><?php endforeach; ?></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalReporteMaestros" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white"><h5 class="modal-title">Plantilla de Maestros</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <table class="table"><thead><tr><th>RFC</th><th>Nombre</th><th>Estatus</th></tr></thead>
                    <tbody><?php foreach ($maestros_unificados as $m): ?><tr><td><?php echo $m['rfc']; ?></td><td><?php echo $m['nombre']; ?></td><td><?php echo $m['estatus']; ?></td></tr><?php endforeach; ?></tbody></table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalListaCalificaciones" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white"><h5 class="modal-title">Calificaciones Recientes</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <table class="table"><thead><tr><th>Alumno</th><th>Calificación</th></tr></thead>
                    <tbody><?php foreach ($calificaciones as $c): ?><tr><td><?php echo $c['nombre']; ?></td><td><?php echo $c['calificacion']; ?></td></tr><?php endforeach; ?></tbody></table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalReporteFotoAlumno" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white"><h5 class="modal-title">Archivo Fotográfico</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row"><?php foreach ($fotos_alumnos as $f): ?>
                        <div class="col-3 text-center mb-3">
                            <img src="<?php echo !empty($f['rutaImagen']) ? $f['rutaImagen'] : 'https://ui-avatars.com/api/?name='.$f['nombre']; ?>" class="img-thumbnail" width="80"><br><small><?php echo $f['nombre']; ?></small>
                        </div>
                    <?php endforeach; ?></div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center">
        <p class="mb-0 small">© <?php echo date("Y"); ?> CECyTE SANTA CATARINA N.L. - Sistema de Gestión Escolar</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>