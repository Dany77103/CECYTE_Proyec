<?php
ob_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <style>
        :root { --cecyte-green: #28a745; --cecyte-dark: #1e293b; }
        body { background: linear-gradient(135deg, #d4edda 0%, #ffffff 100%); font-family: 'Inter', sans-serif; min-height: 100vh; }
        .navbar-custom { background: var(--cecyte-dark); border-bottom: 3px solid var(--cecyte-green); padding: 1rem 0; }
        .card-custom { border: none; border-radius: 16px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem; overflow: hidden; }
        
        .qr-wrapper { 
            background: #000; 
            border-radius: 12px; 
            overflow: hidden; 
            border: 4px solid #e2e8f0; 
            max-width: 600px; 
            margin: 0 auto; 
            position: relative; 
            transition: all 0.3s ease; 
        }
        #qr-reader { width: 100% !important; border: none !important; }
        #qr-reader video { width: 100% !important; object-fit: cover !important; }
        .status-active { border-color: var(--cecyte-green) !important; box-shadow: 0 0 20px rgba(40, 167, 69, 0.4); }
        
        /* Estilo para los botones */
        .btn-action { border-radius: 50px; padding: 10px 25px; font-weight: 600; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-custom">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-qrcode text-success me-2"></i> CECyTE SC</a>
            <a href="main.php" class="btn btn-outline-success btn-sm rounded-pill px-4">Volver</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div id="alertContainer"></div>
        <div class="row">
            <div class="col-lg-7">
                <div class="card card-custom text-center">
                    <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Escáner de Alta Sensibilidad</h5>
                        <span id="cameraStatus" class="badge bg-secondary">Inactiva</span>
                    </div>
                    <div class="card-body">
                        <div class="qr-wrapper mb-3" id="reader-container">
                            <div id="qr-reader"></div>
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-success btn-action" id="startBtn">
                                <i class="fas fa-video me-2"></i>Iniciar Escaneo
                            </button>
                            <button class="btn btn-danger btn-action" id="stopBtn">
                                <i class="fas fa-video-slash me-2"></i>Detener
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-custom p-3 text-center">
                    <h5 class="fw-bold mb-3">Estadísticas de Hoy</h5>
                    <div class="row">
                        <div class="col-6 border-end">
                            <h2 id="totalHoy" class="text-success fw-bold">0</h2>
                            <p class="text-muted small">TOTAL ASISTENCIAS</p>
                        </div>
                        <div class="col-6">
                            <h2 id="totalPendientes" class="text-primary fw-bold">0</h2>
                            <p class="text-muted small">ALUMNOS EN PLANTEL</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom mt-2">
            <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-history text-primary me-2"></i>Últimos Movimientos</h5>
                <input type="date" id="fechaFiltro" class="form-control w-auto" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Matrícula</th><th>Alumno</th><th>Entrada</th><th>Salida</th><th>Estado</th></tr>
                    </thead>
                    <tbody id="asistenciasBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let html5QrCode;

        async function startScanner() {
            // Limpiar instancia previa si existe
            if (html5QrCode) {
                try { await html5QrCode.clear(); } catch(e) { console.log(e); }
            }

            html5QrCode = new Html5Qrcode("qr-reader");
            
            const config = { 
                fps: 25, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                // Fuerza a usar el buscador de códigos nativo del celular si existe
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                },
                // Ayuda visual: oscurece lo que está fuera del cuadro de escaneo
                videoConstraints: {
                    facingMode: "environment",
                    width: { min: 640, ideal: 1280, max: 1920 },
                    height: { min: 480, ideal: 720, max: 1080 }
                }
            };

            html5QrCode.start(
                { facingMode: "environment" }, 
                config, 
                (decodedText) => {
                    $("#reader-container").addClass("status-active");
                    if (navigator.vibrate) navigator.vibrate([100, 50, 100]); // Vibración doble
                    
                    html5QrCode.pause(true); // Pausa para procesar
                    procesarQR(decodedText);
                    
                    // Feedback visual de éxito
                    setTimeout(() => $("#reader-container").removeClass("status-active"), 800);
                },
                (errorMessage) => {
                    // No saturar consola
                }
            ).then(() => {
                $("#cameraStatus").removeClass("bg-secondary").addClass("bg-success").text("Escaneando...");
            }).catch(err => {
                alert("Error al iniciar cámara. Asegúrate de dar permisos y cerrar otras apps de cámara.");
            });
        }

        function procesarQR(codigoQR) {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'POST',
                data: { codigo_qr: codigoQR, action: 'registrar' },
                success: function(response) {
                    try {
                        const data = (typeof response === 'object') ? response : JSON.parse(response);
                        showAlert(data.message, data.success ? 'success' : 'danger');
                        actualizarEstadisticas();
                        cargarHistorial();
                    } catch(e) { 
                        showAlert('Error en la respuesta del servidor.', 'danger'); 
                    }
                    // Reanudar después de 2 segundos para dar tiempo a retirar el QR
                    setTimeout(() => { if(html5QrCode) html5QrCode.resume(); }, 2000);
                },
                error: function() {
                    showAlert('Error de conexión con procesar_qr.php', 'danger');
                    setTimeout(() => { if(html5QrCode) html5QrCode.resume(); }, 2000);
                }
            });
        }

        function showAlert(m, t) {
            const icon = t === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            $('#alertContainer').hide().html(`
                <div class="alert alert-${t} d-flex align-items-center shadow border-0">
                    <i class="fas ${icon} me-2"></i>
                    <div>${m}</div>
                </div>
            `).fadeIn();
            setTimeout(() => $("#alertContainer").fadeOut(), 4000);
        }

        function actualizarEstadisticas() {
            $.get('procesar_qr.php', { action: 'get_stats' }, function(res) {
                try {
                    const s = JSON.parse(res);
                    $('#totalHoy').text(s.total_hoy);
                    $('#totalPendientes').text(s.pendientes_salida);
                } catch(e){}
            });
        }

        function cargarHistorial() {
            const f = $('#fechaFiltro').val();
            $.get('procesar_qr.php', { action: 'get_asistencias', fecha: f }, function(res) {
                try {
                    const asistencias = JSON.parse(res);
                    let html = '';
                    asistencias.forEach(r => {
                        const b = r.hora_salida ? 
                            '<span class="badge bg-success"><i class="fas fa-sign-out-alt"></i> Salida</span>' : 
                            '<span class="badge bg-primary"><i class="fas fa-sign-in-alt"></i> Presente</span>';
                        html += `<tr>
                            <td><strong>${r.matricula}</strong></td>
                            <td>${r.nombre}</td>
                            <td>${r.hora_entrada}</td>
                            <td>${r.hora_salida || '--:--'}</td>
                            <td>${b}</td>
                        </tr>`;
                    });
                    $('#asistenciasBody').html(html || '<tr><td colspan="5" class="text-center text-muted">No hay registros para este día</td></tr>');
                } catch(e){}
            });
        }

        $(document).ready(function() {
            actualizarEstadisticas();
            cargarHistorial();
            $('#startBtn').click(startScanner);
            $('#stopBtn').click(async () => {
                if(html5QrCode) {
                    await html5QrCode.stop();
                    $("#cameraStatus").removeClass("bg-success").addClass("bg-secondary").text("Inactiva");
                }
            });
            $('#fechaFiltro').change(cargarHistorial);
        });
    </script>
</body>
</html>