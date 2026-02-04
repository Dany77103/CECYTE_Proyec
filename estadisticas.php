<?php
// Configuración de conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

function obtenerDatos($query) {
    global $con;
    try {
        $stmt = $con->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// --- CONSULTAS ---
$resActivos = obtenerDatos("SELECT COUNT(*) AS total FROM historialacademicoalumnos haa INNER JOIN estatus e ON e.id_estatus = haa.id_estatus WHERE e.tipoestatus = 'activo'");
$alumnosActivos = $resActivos[0]['total'] ?? 0;

$resTotalAlumnos = obtenerDatos("SELECT COUNT(*) AS total FROM alumnos");
$totalAlumnos = $resTotalAlumnos[0]['total'] ?? 0;

$resMaestros = obtenerDatos("SELECT COUNT(*) AS total FROM maestros");
$totalMaestros = $resMaestros[0]['total'] ?? 0;

$resPromedio = obtenerDatos("SELECT AVG(calificacion) as promedio FROM calificaciones");
$promedioCalificaciones = $resPromedio[0]['promedio'] ?? 0;

$resGenero = obtenerDatos("SELECT SUM(CASE WHEN genero = 'masculino' THEN 1 ELSE 0 END) as masc, SUM(CASE WHEN genero = 'femenino' THEN 1 ELSE 0 END) as fem FROM alumnos");
$generoData = $resGenero[0] ?? ['masc' => 0, 'fem' => 0];

$dataJSON = json_encode([
    'activos' => (int)$alumnosActivos,
    'totalAlumnos' => (int)$totalAlumnos,
    'totalMaestros' => (int)$totalMaestros,
    'promedio' => round((float)$promedioCalificaciones, 1),
    'genero' => [(int)($generoData['masc'] ?? 0), (int)($generoData['fem'] ?? 0)]
]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECYTE | Dashboard Estadístico</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #064e3b;
            --accent-color: #10b981;
            --bg-body: #f8fafc;
            --card-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
        }

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
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(16, 185, 129, 0.1); color: var(--primary-color);
            padding: 10px 18px; border-radius: 12px;
            text-decoration: none; font-weight: 600;
            transition: 0.3s ease;
        }

        .btn-back:hover {
            background: var(--primary-color); color: white; transform: translateX(-5px);
        }

        .content-wrapper {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .stat-card {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border-bottom: 4px solid transparent;
        }

        .stat-card:hover { 
            transform: translateY(-8px); 
            border-bottom: 4px solid var(--accent-color);
        }

        .stat-icon-wrapper {
            width: 55px; height: 55px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; margin-bottom: 15px;
        }

        /* Colores de Iconos Unificados */
        .icon-blue { background: #eff6ff; color: #2563eb; }
        .icon-green { background: #f0fdf4; color: #16a34a; }
        .icon-orange { background: #fffbeb; color: #d97706; }
        .icon-primary { background: #f0fdfa; color: var(--primary-color); }

        .stat-value { font-size: 2.2rem; font-weight: 800; color: #0f172a; display: block; }
        .stat-label { color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: 600; }

        .chart-box {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            margin-top: 20px;
            height: 100%;
        }

        @media print {
            .btn-back, .main-header, .print-hidden { display: none !important; }
            .content-wrapper { padding: 0; }
        }
    </style>
</head>
<body>

<header class="main-header d-flex justify-content-between align-items-center">
    <a href="main.php" class="btn-back">
        <i class='bx bx-left-arrow-alt fs-4'></i> 
        <span>Volver al Panel</span>
    </a>
    
    <div class="d-none d-md-block text-center">
        <h5 class="fw-bold mb-0">Centro de Inteligencia CECyTE</h5>
    </div>

    <button class="btn btn-dark px-4 py-2 print-hidden" style="border-radius: 12px; font-weight: 600;" onclick="window.print()">
        <i class='bx bx-printer me-2'></i> Exportar PDF
    </button>
</header>

<div class="content-wrapper">
    <div class="mb-5">
        <h2 class="fw-bold mb-1">Análisis de Datos Escolares</h2>
        <p class="text-muted">Estadísticas actualizadas basadas en la base de datos central.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper icon-blue"><i class='bx bxs-group'></i></div>
                <span class="stat-value"><?php echo $totalAlumnos; ?></span>
                <span class="stat-label">Matrícula Total</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper icon-green"><i class='bx bxs-user-check'></i></div>
                <span class="stat-value"><?php echo $alumnosActivos; ?></span>
                <span class="stat-label">Alumnos Activos</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper icon-orange"><i class='bx bxs-graduation'></i></div>
                <span class="stat-value"><?php echo $promedioCalificaciones ? round($promedioCalificaciones, 1) : '0'; ?></span>
                <span class="stat-label">Promedio General</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon-wrapper icon-primary"><i class='bx bxs-briefcase'></i></div>
                <span class="stat-value"><?php echo $totalMaestros; ?></span>
                <span class="stat-label">Personal Docente</span>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-lg-8">
            <div class="chart-box">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0">Tendencia de Población</h5>
                    <select class="form-select form-select-sm w-auto print-hidden" id="chartType" style="border-radius: 8px; background: #f1f5f9; border:none;">
                        <option value="bar">Gráfico de Barras</option>
                        <option value="line">Gráfico de Líneas</option>
                    </select>
                </div>
                <canvas id="mainChart" style="max-height: 380px;"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-box">
                <h5 class="fw-bold mb-4">Balance de Género</h5>
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    const data = <?php echo $dataJSON; ?>;
    const accentColor = '#10b981';
    const primaryColor = '#064e3b';

    // Configuración Gráfica Principal
    const ctxMain = document.getElementById('mainChart').getContext('2d');
    let mainChart = new Chart(ctxMain, {
        type: 'bar',
        data: {
            labels: ['Total Alumnos', 'Alumnos Activos', 'Personal Docente'],
            datasets: [{
                label: 'Registros',
                data: [data.totalAlumnos, data.activos, data.totalMaestros],
                backgroundColor: [primaryColor, accentColor, '#334155'],
                borderRadius: 12,
                barThickness: 50
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { 
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Configuración Gráfica de Género
    const ctxGender = document.getElementById('genderChart').getContext('2d');
    new Chart(ctxGender, {
        type: 'doughnut',
        data: {
            labels: ['Masculino', 'Femenino'],
            datasets: [{
                data: data.genero,
                backgroundColor: [primaryColor, '#34d399'],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            cutout: '75%',
            plugins: { 
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, font: { family: 'Inter', size: 12 } } } 
            }
        }
    });

    // Cambio dinámico de tipo de gráfico
    document.getElementById('chartType').addEventListener('change', function(e) {
        const type = e.target.value;
        const oldData = mainChart.data;
        mainChart.destroy();
        mainChart = new Chart(ctxMain, {
            type: type,
            data: {
                labels: oldData.labels,
                datasets: [{
                    label: 'Registros',
                    data: [data.totalAlumnos, data.activos, data.totalMaestros],
                    borderColor: primaryColor,
                    backgroundColor: type === 'line' ? 'rgba(16, 185, 129, 0.1)' : [primaryColor, accentColor, '#334155'],
                    fill: true,
                    tension: 0.4,
                    borderRadius: type === 'bar' ? 12 : 0
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    });
</script>

</body>
</html>