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

// Función para obtener datos de la base de datos usando PDO
function obtenerDatos($query) {
    global $con; // Usamos la variable $con (conexión PDO)
    try {
        $stmt = $con->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve los datos como un array asociativo
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage()); // Manejo de errores
    }
}

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}



// Consultas SQL (corregidas)
$queryAlumnosActivos = "SELECT COUNT(*) AS alumnosActivos FROM historialacademicoalumnos haa
                        INNER JOIN estatus e ON e.id_estatus = haa.id_estatus
                        WHERE e.tipoEstatus = 'activo'";

$queryAlumnosBajaTemporal = "SELECT COUNT(*) AS alumnosBajaTemporal FROM historialacademicoalumnos haa
                             INNER JOIN estatus e ON e.id_estatus = haa.id_estatus
                             WHERE e.tipoEstatus = 'baja temporal'";

$queryMaestrosActivos = "SELECT COUNT(*) AS maestrosActivos FROM datoslaboralesmaestros dlm
                         INNER JOIN estatus e ON e.id_estatus = dlm.id_estatus
                         WHERE e.tipoEstatus = 'activo'";

// Obtener datos
$alumnosActivos = obtenerDatos($queryAlumnosActivos)[0]['alumnosActivos'];
$alumnosBajaTemporal = obtenerDatos($queryAlumnosBajaTemporal)[0]['alumnosBajaTemporal'];
$maestrosActivos = obtenerDatos($queryMaestrosActivos)[0]['maestrosActivos'];

// Convertir datos a JSON para JavaScript
$dataJSON = json_encode([
    'alumnosActivos' => $alumnosActivos,
    'alumnosBajaTemporal' => $alumnosBajaTemporal,
    'maestrosActivos' => $maestrosActivos
]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadisticas</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Contenido Principal -->
    <main class="container mt-5 flex-grow-1">
        <h1 class="text-center mb-4">Estadisticas</h1>

        <!-- Botones para generar gráficas -->
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
            <button id="btnAlumnosActivos" class="btn btn-primary">Alumnos Activos</button>
            <button id="btnAlumnosBajaTemporal" class="btn btn-warning">Alumnos Baja Temporal</button>
            <button id="btnMaestrosActivos" class="btn btn-success">Maestros Activos</button>
			<button id="btnGraficaCombinada" class="btn btn-info">Gráfica Combinada</button> <!-- Nuevo botón -->
        </div>

        <!-- Contenedor de la gráfica -->
        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>
    </main>

   
  <!-- Footer -->
  <footer class="bg-success  text-white text-center py-3 mt-5 mt-auto">
  <div class="footer-container">
	<div class="footer-info">
     <p > CECyTE SANTA CATARINA N.L.</p>
     <p class="footer-year">&copy <?php echo date("Y"); ?> Sistema de Reportes. Todos los derechos reservados.</p>
	</div>
</footer>


    <script>
        // Datos desde PHP (convertidos a JSON)
        const data = <?php echo $dataJSON; ?>;

        // Configuración inicial del gráfico
        const ctx = document.getElementById('myChart').getContext('2d');
        let myChart;

        // Función para crear una gráfica de barras
        function createBarChart(labels, dataset, label, backgroundColor) {
            if (myChart) myChart.destroy(); // Destruir la gráfica anterior si existe

            myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: dataset,
                        backgroundColor: backgroundColor,
                        borderColor: backgroundColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
		
		
		
		 // Función para crear una gráfica de líneas
        function createLineChart(labels, datasets) {
            if (myChart) myChart.destroy(); // Destruir la gráfica anterior si existe

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
		

        // Eventos para los botones
        document.getElementById('btnAlumnosActivos').addEventListener('click', () => {
            const labels = ['Alumnos Activos'];
            createBarChart(labels, [data.alumnosActivos], 'Alumnos Activos', 'rgba(54, 162, 235, 0.5)');
        });

        document.getElementById('btnAlumnosBajaTemporal').addEventListener('click', () => {
            const labels = ['Alumnos Baja Temporal'];
            createBarChart(labels, [data.alumnosBajaTemporal], 'Alumnos Baja Temporal', 'rgba(255, 206, 86, 0.5)');
        });

        document.getElementById('btnMaestrosActivos').addEventListener('click', () => {
            const labels = ['Maestros Activos'];
            createBarChart(labels, [data.maestrosActivos], 'Maestros Activos', 'rgba(75, 192, 192, 0.5)');
        });
		
		
		// Evento para el nuevo botón de gráfica combinada
        document.getElementById('btnGraficaCombinada').addEventListener('click', () => {
            const labels = ['Alumnos Activos', 'Alumnos Baja Temporal', 'Maestros Activos'];
            const datasets = [
                {
                    label: 'Alumnos Activos',
                    data: [data.alumnosActivos, 0, 0],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderWidth: 2
                },
                {
                    label: 'Alumnos Baja Temporal',
                    data: [0, data.alumnosBajaTemporal, 0],
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.5)',
                    borderWidth: 2
                },
                {
                    label: 'Maestros Activos',
                    data: [0, 0, data.maestrosActivos],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderWidth: 2
                }
            ];
            createLineChart(labels, datasets);
        });
		
		
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>