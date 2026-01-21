<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}
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
            <button id="btnAlumnosInactivos" class="btn btn-warning">Alumnos Inactivos</button>
            <button id="btnMaestrosActivos" class="btn btn-success">Maestros Activos</button>
            <button id="btnDiscapacidad" class="btn btn-info">Alumnos por Discapacidad</button>
        </div>

        <!-- Contenedor de la gráfica -->
        <div class="chart-container">
            <canvas id="myChart"></canvas>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>
        // Datos de ejemplo (puedes reemplazarlos con datos reales desde tu base de datos)
        const data = {
            alumnosActivos: [30, 40, 50, 60, 70], // Ejemplo: alumnos activos por mes
            alumnosInactivos: [10, 15, 20, 25, 30], // Ejemplo: alumnos inactivos por mes
            maestrosActivos: [5, 10, 15, 20, 25], // Ejemplo: maestros activos por mes
            discapacidad: {
                labels: ['Visual', 'Auditiva', 'Motriz', 'Intelectual'], // Tipos de discapacidad
                data: [10, 5, 8, 3] // Cantidad de alumnos por discapacidad
            }
        };

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
        function createLineChart(labels, dataset, label, borderColor) {
            if (myChart) myChart.destroy(); // Destruir la gráfica anterior si existe

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: dataset,
                        borderColor: borderColor,
                        borderWidth: 2,
                        fill: false
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

        // Eventos para los botones
        document.getElementById('btnAlumnosActivos').addEventListener('click', () => {
            const labels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo']; // Ejemplo de meses
            createBarChart(labels, data.alumnosActivos, 'Alumnos Activos', 'rgba(54, 162, 235, 0.5)');
        });

        document.getElementById('btnAlumnosInactivos').addEventListener('click', () => {
            const labels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo']; // Ejemplo de meses
            createBarChart(labels, data.alumnosInactivos, 'Alumnos Inactivos', 'rgba(255, 206, 86, 0.5)');
        });

        document.getElementById('btnMaestrosActivos').addEventListener('click', () => {
            const labels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo']; // Ejemplo de meses
            createBarChart(labels, data.maestrosActivos, 'Maestros Activos', 'rgba(75, 192, 192, 0.5)');
        });

        document.getElementById('btnDiscapacidad').addEventListener('click', () => {
            createLineChart(data.discapacidad.labels, data.discapacidad.data, 'Alumnos por Discapacidad', 'rgba(153, 102, 255, 0.5)');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>