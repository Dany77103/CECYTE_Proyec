<?php
// 1. Limpieza de buffer para evitar caracteres extraños antes de la carga
ob_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Error de conexión"]);
        exit;
    }
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia QR | CECyTE SC</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        :root {
            --cecyte-green: #28a745;
            --cecyte-dark: #1e293b;
            --cecyte-light: #f8fafc;
        }
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; color: #334155; }
        .navbar-custom { background: var(--cecyte-dark); border-bottom: 3px solid var(--cecyte-green); padding: 1rem 0; }
        .container-main { max-width: 1300px; margin: 2rem auto; padding: 0 15px; }
        .card-custom { border: none; border-radius: 16px; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem; overflow: hidden; }
        .card-header-custom { background: white; padding: 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; }
        .qr-wrapper { background: #f8fafc; border-radius: 12px; overflow: hidden; border: 2px dashed #cbd5e1; max-width: 500px; margin: 0 auto; min-height: 300px; }
        .btn-cecyte { background: var(--cecyte-green); color: white; border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600; border: none; }
        .btn-cecyte:hover { background: #218838; }
        .animate-pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
        /* Ajuste para que la cámara no se vea gigante */
        #qr-reader video { border-radius: 8px !important; object-fit: cover !important; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-qrcode text-success me-2"></i> CECyTE SC
            </a>

            <a href="main.php" class="btn btn-outline-success btn-sm d-flex align-items-center" style="border-radius: 20px; padding: 5px 20px;">
            <i class="bi bi-arrow-left-short me-1"></i> Volver
        </a>

        </div>
    </nav>

    <div class="container-main">
        <div id="alertContainer"></div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card card-custom">
                    <div class="card-header-custom">
                        <h3><i class="fas fa-video me-2 text-success"></i>Control de Acceso</h3>
                        <span id="cameraStatus" class="badge bg-secondary">Inactiva</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="qr-wrapper mb-4">
                            <div id="qr-reader" style="width: 100%;"></div>
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-cecyte" id="startBtn"><i class="fas fa-play me-2"></i>Iniciar Escaneo</button>
                            <button class="btn btn-outline-danger" id="stopBtn"><i class="fas fa-stop me-2"></i>Detener</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-custom">
                    <div class="card-header-custom">
                        <h3><i class="fas fa-chart-pie me-2 text-primary"></i>Resumen</h3>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h2 id="totalHoy" class="text-success fw-bold">0</h2>
                                <small class="text-muted">ASISTENCIAS</small>
                            </div>
                            <div class="col-6">
                                <h2 id="totalPendientes" class="text-primary fw-bold">0</h2>
                                <small class="text-muted">EN PLANTEL</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom" id="registros">
            <div class="card-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <h3><i class="fas fa-list me-2 text-primary"></i>Bitácora</h3>
                    <button class="btn btn-sm btn-outline-secondary" id="btnToggleBitacora">
                        <i class="fas fa-eye-slash me-1"></i> <span id="toggleText">Ocultar</span>
                    </button>
                </div>
                <input type="date" class="form-control w-auto" id="fechaFiltro" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="card-body" id="bitacoraContent">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Matrícula</th><th>Alumno</th><th>Entrada</th><th>Salida</th><th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody id="asistenciasBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let scanner;

        // Función para iniciar el escáner (Versión robusta)
        function startScanner() {
            if (!scanner) {
                scanner = new Html5QrcodeScanner("qr-reader", { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                });
            }

            scanner.render((decodedText) => {
                // Al detectar éxito:
                $("#cameraStatus").removeClass("bg-success").addClass("bg-warning").text("Procesando...");
                // Detenemos temporalmente para no duplicar lectura
                scanner.clear();
                procesarQR(decodedText);
            }, (error) => {
                // Fallos silenciosos de lectura
            });

            $("#cameraStatus").removeClass("bg-secondary").addClass("bg-success").text("Cámara Activa");
        }

        function stopScanner() {
            if (scanner) {
                scanner.clear().then(() => {
                    $("#cameraStatus").removeClass("bg-success").addClass("bg-secondary").text("Inactiva");
                });
            }
        }

        function procesarQR(codigoQR) {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'POST',
                data: { codigo_qr: codigoQR, action: 'registrar' },
                success: function(response) {
                    try {
                        const data = typeof response === "string" ? JSON.parse(response) : response;
                        showAlert(data.message, data.success ? 'success' : 'danger');
                        actualizarEstadisticas();
                        cargarHistorial();
                    } catch(e) { showAlert('Error procesando respuesta.', 'warning'); }
                    // Reiniciar cámara después de 2 segundos
                    setTimeout(startScanner, 2000);
                }
            });
        }

        function cargarHistorial() {
            const fecha = $('#fechaFiltro').val();
            $.ajax({
                url: 'procesar_qr.php',
                type: 'GET',
                data: { action: 'get_asistencias', fecha: fecha },
                success: function(response) {
                    try {
                        const asistencias = typeof response === "string" ? JSON.parse(response) : response;
                        let html = '';
                        asistencias.forEach(reg => {
                            const badge = reg.hora_salida ? 
                                '<span class="badge bg-success">Completado</span>' : 
                                '<span class="badge bg-primary animate-pulse">En Plantel</span>';
                            html += `<tr>
                                <td>${reg.matricula}</td><td>${reg.nombre}</td>
                                <td class="text-success">${reg.hora_entrada || '-'}</td>
                                <td class="text-danger">${reg.hora_salida || '--:--'}</td>
                                <td>${badge}</td>
                            </tr>`;
                        });
                        $('#asistenciasBody').html(html || '<tr><td colspan="5" class="text-center">No hay registros</td></tr>');
                    } catch(e) { console.error("Error al cargar historial"); }
                }
            });
        }

        function actualizarEstadisticas() {
            $.get('procesar_qr.php', { action: 'get_stats' }, function(response) {
                try {
                    const stats = typeof response === "string" ? JSON.parse(response) : response;
                    $('#totalHoy').text(stats.total_hoy || 0);
                    $('#totalPendientes').text(stats.pendientes_salida || 0);
                } catch(e) {}
            });
        }

        function showAlert(message, type) {
            const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show">
                ${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            $('#alertContainer').html(alertHtml);
        }

        $(document).ready(function() {
            actualizarEstadisticas();
            cargarHistorial();
            $('#startBtn').click(startScanner);
            $('#stopBtn').click(stopScanner);
            $('#fechaFiltro').change(cargarHistorial);
            
            $('#btnToggleBitacora').click(function() {
                $('#bitacoraContent').slideToggle();
                const text = $('#toggleText').text() === 'Ocultar' ? 'Mostrar' : 'Ocultar';
                $('#toggleText').text(text);
            });
        });
    </script>
</body>
</html>