<?php
// Conexión a la base de datos
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <style>
        :root {
            --cecyte-green: #28a745;
            --cecyte-dark: #1e293b;
            --cecyte-light: #f8fafc;
        }
        
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #334155;
        }

        .navbar-custom {
            background: var(--cecyte-dark);
            padding: 1rem 0;
            border-bottom: 3px solid var(--cecyte-green);
        }

        .container-main { max-width: 1300px; margin: 2rem auto; padding: 0 15px; }

        .card-custom {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header-custom {
            background: white;
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .qr-wrapper {
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            border: 4px solid white;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 0 auto;
        }

        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .stat-item {
            background: var(--cecyte-light);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .btn-cecyte {
            background: var(--cecyte-green);
            color: white;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            border: none;
            transition: all 0.2s;
        }

        .btn-cecyte:hover { background: #218838; transform: translateY(-1px); }

        .alert-custom { border-radius: 12px; border: none; font-weight: 500; }

        #qr-reader__scan_region { background: white !important; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <div class="bg-success p-2 rounded-3 me-2">
                    <i class="fas fa-qrcode text-white"></i>
                </div>
                <span>CECyTE <span class="text-success">SC</span></span>
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#escaneo"><i class="fas fa-camera me-1"></i> Escaneo</a></li>
                    <li class="nav-item"><a class="nav-link" href="#registros"><i class="fas fa-list-ul me-1"></i> Historial</a></li>
                    <li class="nav-item ms-lg-3"><a class="btn btn-outline-light btn-sm px-3" href="reportes.php">Volver</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <div id="alertContainer"></div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card card-custom" id="escaneo">
                    <div class="card-header-custom">
                        <h3><i class="fas fa-video me-2 text-success"></i>Control de Acceso</h3>
                        <span id="cameraStatus" class="badge bg-secondary">Cámara Inactiva</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="qr-wrapper mb-4">
                            <div id="qr-reader"></div>
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-cecyte" id="startScanner">
                                <i class="fas fa-play me-2"></i>Iniciar Cámara
                            </button>
                            <button class="btn btn-outline-danger" id="stopScanner">
                                <i class="fas fa-stop me-2"></i>Detener
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-custom">
                    <div class="card-header-custom">
                        <h3><i class="fas fa-chart-pie me-2 text-primary"></i>Resumen Hoy</h3>
                    </div>
                    <div class="card-body">
                        <div class="stats-grid mb-4">
                            <div class="stat-item">
                                <div class="stats-number h2 fw-bold text-success" id="totalHoy">0</div>
                                <div class="stats-label small text-muted">TOTAL ASISTENCIAS</div>
                            </div>
                            <div class="stat-item">
                                <div class="stats-number h2 fw-bold text-primary" id="totalPendientes">0</div>
                                <div class="stats-label small text-muted">EN PLANTEL</div>
                            </div>
                        </div>
                        <div class="alert alert-info border-0 bg-light p-3">
                            <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>Ayuda</h6>
                            <p class="small mb-0">Si la cámara no inicia, verifica que estás usando <b>localhost</b> o <b>HTTPS</b>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom" id="registros">
            <div class="card-header-custom">
                <h3><i class="fas fa-clock-rotate-left me-2 text-primary"></i>Bitácora Reciente</h3>
                <input type="date" class="form-control w-auto" id="fechaFiltro" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="asistenciasTable">
                        <thead class="table-light">
                            <tr>
                                <th>Matrícula</th>
                                <th>Alumno</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody id="asistenciasBody">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        let html5QrCode;

        // 1. LÓGICA DEL ESCÁNER
        async function startScanner() {
            const isLocalhost = ["localhost", "127.0.0.1"].includes(window.location.hostname);
            const isHttps = window.location.protocol === "https:";

            if (!isLocalhost && !isHttps) {
                showAlert('<strong>Error de Seguridad:</strong> Acceso a cámara denegado en HTTP. Usa Localhost o HTTPS.', 'danger');
                return;
            }

            try {
                if (html5QrCode) {
                    await html5QrCode.stop().catch(() => {});
                }

                html5QrCode = new Html5Qrcode("qr-reader");
                const config = { fps: 20, qrbox: { width: 250, height: 250 } };

                await html5QrCode.start(
                    { facingMode: "environment" }, 
                    config,
                    (decodedText) => {
                        $("#cameraStatus").removeClass("bg-success").addClass("bg-warning").text("Procesando...");
                        procesarQR(decodedText);
                    }
                );
                $("#cameraStatus").removeClass("bg-secondary bg-warning").addClass("bg-success").text("Cámara Activa");
            } catch (err) {
                console.error(err);
                showAlert('Error al iniciar cámara: ' + err, 'danger');
            }
        }

        async function stopScanner() {
            if (html5QrCode) {
                try {
                    await html5QrCode.stop();
                    $("#cameraStatus").removeClass("bg-success").addClass("bg-secondary").text("Cámara Inactiva");
                } catch (err) { console.log(err); }
            }
        }

        // 2. COMUNICACIÓN CON EL SERVIDOR (CORREGIDA)
        function procesarQR(codigoQR) {
            stopScanner(); // Detener para evitar lecturas duplicadas

            $.ajax({
                url: 'procesar_qr.php',
                type: 'POST',
                data: { codigo_qr: codigoQR, action: 'registrar' },
                success: function(response) {
                    try {
                        // Intentamos limpiar la respuesta por si hay basura antes del JSON
                        const jsonStart = response.indexOf('{');
                        const jsonString = response.substring(jsonStart);
                        const data = JSON.parse(jsonString);
                        
                        showAlert(data.message, data.success ? 'success' : 'danger');
                        actualizarEstadisticas();
                        cargarHistorial();
                    } catch(e) {
                        console.error("Error raw:", response);
                        showAlert('Error en respuesta del servidor. Revisa la consola.', 'warning');
                    }
                    setTimeout(startScanner, 2000); // Reiniciar después de 2 seg
                },
                error: function() {
                    showAlert('Error de conexión con el servidor', 'danger');
                    setTimeout(startScanner, 2000);
                }
            });
        }

        // 3. CARGA DE DATOS (CORREGIDA)
        function cargarHistorial() {
            const fecha = $('#fechaFiltro').val();
            
            $.ajax({
                url: 'procesar_qr.php',
                type: 'GET',
                data: { action: 'get_asistencias', fecha: fecha },
                success: function(response) {
                    try {
                        // Limpieza de JSON para evitar el error visual
                        const jsonStart = response.indexOf('[');
                        if (jsonStart === -1) throw "No JSON array found";
                        const jsonString = response.substring(jsonStart);
                        const asistencias = JSON.parse(jsonString);
                        
                        let html = '';
                        if (asistencias.length === 0) {
                            html = '<tr><td colspan="5" class="text-center text-muted p-4">Sin registros para esta fecha.</td></tr>';
                        } else {
                            asistencias.forEach(reg => {
                                const statusBadge = reg.hora_salida ? 
                                    '<span class="badge bg-success shadow-sm"><i class="fas fa-check me-1"></i> Completado</span>' : 
                                    '<span class="badge bg-primary animate-pulse shadow-sm"><i class="fas fa-sign-in-alt me-1"></i> En Plantel</span>';
                                
                                html += `
                                    <tr>
                                        <td class="fw-bold text-dark">${reg.matricula}</td>
                                        <td>${reg.nombre}</td>
                                        <td class="text-success fw-medium"><i class="far fa-clock me-1"></i> ${reg.hora_entrada || '-'}</td>
                                        <td class="text-danger fw-medium"><i class="far fa-clock me-1"></i> ${reg.hora_salida || '--:--'}</td>
                                        <td>${statusBadge}</td>
                                    </tr>`;
                            });
                        }
                        $('#asistenciasBody').html(html);
                    } catch (e) {
                        console.error("Error carga:", response);
                        $('#asistenciasBody').html('<tr><td colspan="5" class="text-center text-danger">Error de formato en el servidor.</td></tr>');
                    }
                }
            });
        }

        function actualizarEstadisticas() {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'GET',
                data: { action: 'get_stats' },
                success: function(response) {
                    try {
                        const jsonStart = response.indexOf('{');
                        const stats = JSON.parse(response.substring(jsonStart));
                        $('#totalHoy').text(stats.total_hoy || 0);
                        $('#totalPendientes').text(stats.pendientes_salida || 0);
                    } catch(e) { console.log("Stats error"); }
                }
            });
        }

        function showAlert(message, type) {
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show alert-custom shadow-sm mb-4">
                    <i class="fas ${icon} me-2"></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            $('#alertContainer').html(alertHtml);
            // Auto cerrar alertas de éxito después de 4 seg
            if(type === 'success') setTimeout(() => $('.alert').alert('close'), 4000);
        }

        $(document).ready(function() {
            actualizarEstadisticas();
            cargarHistorial();
            $('#startScanner').click(startScanner);
            $('#stopScanner').click(stopScanner);
            $('#fechaFiltro').change(cargarHistorial);
        });
    </script>
</body>
</html>