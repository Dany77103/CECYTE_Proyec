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
    <title>Control de Acceso | CECyTE SC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
    <style>
        :root { 
            --corp-green: #198754; 
            --corp-red: #dc3545; 
            --corp-dark: #212529;
            --corp-gray: #f8f9fa;
        }
        
        body { 
            background-color: #ebeef2;
            color: var(--corp-dark);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .navbar-custom { 
            background: #ffffff;
            border-bottom: 2px solid #dee2e6;
            padding: 0.8rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .navbar-brand { color: var(--corp-dark) !important; font-weight: 700; letter-spacing: -0.5px; }

        .card-custom { 
            border: none;
            border-radius: 12px; 
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }
        
        .qr-wrapper { 
            background: #f1f3f5; 
            border-radius: 8px; 
            overflow: hidden; 
            border: 2px solid #dee2e6;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .mode-entrada .qr-wrapper { border-color: var(--corp-green); }
        .mode-salida .qr-wrapper { border-color: var(--corp-red); }

        .stat-card { padding: 1.5rem; }
        .stat-number { font-size: 2.8rem; font-weight: 800; line-height: 1; margin-bottom: 5px; }
        .stat-label { font-size: 0.8rem; font-weight: 600; color: #6c757d; text-uppercase: uppercase; letter-spacing: 1px; }

        .table thead { background: #f1f3f5; border-bottom: 2px solid #dee2e6; }
        .table th { font-weight: 600; text-transform: uppercase; font-size: 0.75rem; color: #495057; }
        .row-anim { animation: fadeInRight 0.4s ease-out; }
        
        .mode-selector-corp {
            background: #e9ecef;
            padding: 4px;
            border-radius: 8px;
            display: inline-flex;
        }

        .btn-mode { 
            border: none; 
            padding: 8px 18px; 
            border-radius: 6px; 
            font-size: 0.85rem; 
            font-weight: 700; 
            transition: all 0.2s;
            color: #495057;
            background: transparent;
        }

        .btn-mode.active-in { background: var(--corp-green); color: white !important; }
        .btn-mode.active-out { background: var(--corp-red); color: white !important; }

        .btn-corp-primary { background: var(--corp-dark); color: white; border-radius: 6px; font-weight: 600; }
        
        /* Estilo Botón Excel */
        .btn-excel {
            background-color: #1d6f42;
            color: white;
            border: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-excel:hover {
            background-color: #155231;
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="mode-entrada">
    <nav class="navbar navbar-custom">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt text-secondary me-2"></i>CECyTE SC <span class="fw-light text-muted">| Acceso</span>
            </a>
            
            <div class="mode-selector-corp">
                <button class="btn-mode active-in" id="btnModoEntrada">ENTRADA</button>
                <button class="btn-mode" id="btnModoSalida">SALIDA</button>
            </div>

            <a href="main.php" class="btn btn-outline-dark btn-sm fw-bold px-3">PANEL PRINCIPAL</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div id="alertContainer"></div>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card card-custom h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h6 class="mb-0 fw-bold" id="tituloEscaner">LECTOR DE MATRÍCULAS</h6>
                        <small class="text-muted">Coloque el código frente a la cámara</small>
                    </div>
                    <div class="card-body text-center py-4">
                        <div class="qr-wrapper mb-4" id="reader-container">
                            <div id="qr-reader"></div>
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-corp-primary px-4" id="startBtn">INICIAR CÁMARA</button>
                            <button class="btn btn-outline-secondary px-4" id="stopBtn">DETENER</button>
                        </div>
                        <div class="mt-3">
                            <span id="cameraStatus" class="badge bg-light text-dark border">Estado: Inactivo</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="row h-100">
                    <div class="col-12 mb-3">
                        <div class="card card-custom stat-card text-center">
                            <p class="stat-label">Movimientos Totales (Hoy)</p>
                            <h2 id="totalHoy" class="stat-number text-dark">0</h2>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card card-custom stat-card text-center border-start border-4 border-primary">
                            <p class="stat-label">Alumnos en Plantel</p>
                            <h2 id="totalPendientes" class="stat-number text-primary">0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom mt-4 animate__animated animate__fadeInUp">
            <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="mb-0 fw-bold">BITÁCORA DE ASISTENCIA</h6>
                <div class="d-flex align-items-center gap-2">
                    <button id="btnExportarExcel" class="btn btn-excel btn-sm px-3 rounded-pill">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                    <div class="vr mx-2"></div>
                    <i class="fas fa-calendar-alt text-muted"></i>
                    <input type="date" id="fechaFiltro" class="form-control form-control-sm border-0 bg-light fw-bold w-auto" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaAsistencias">
                    <thead>
                        <tr>
                            <th class="ps-4">Matrícula</th>
                            <th>Alumno</th>
                            <th>Hora</th>
                            <th class="text-end pe-4">Tipo de Movimiento</th>
                        </tr>
                    </thead>
                    <tbody id="asistenciasBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let html5QrCode;
        let modoActual = 'entrada';

        // Lógica de descarga a Excel
        $('#btnExportarExcel').click(function() {
            const fecha = $('#fechaFiltro').val();
            const table = document.getElementById("tablaAsistencias");
            const wb = XLSX.utils.table_to_book(table, { sheet: "Asistencias" });
            XLSX.writeFile(wb, `Asistencias_CECyTE_${fecha}.xlsx`);
        });

        function playBeep() {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
            gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 0.1);
        }

        $('#btnModoEntrada').click(function() {
            modoActual = 'entrada';
            $('body').removeClass('mode-salida').addClass('mode-entrada');
            $(this).addClass('active-in');
            $('#btnModoSalida').removeClass('active-out');
            $('#tituloEscaner').text('REGISTRO DE ENTRADA');
        });

        $('#btnModoSalida').click(function() {
            modoActual = 'salida';
            $('body').removeClass('mode-entrada').addClass('mode-salida');
            $(this).addClass('active-out');
            $('#btnModoEntrada').removeClass('active-in');
            $('#tituloEscaner').text('REGISTRO DE SALIDA');
        });

        async function startScanner() {
            if (html5QrCode) { try { await html5QrCode.clear(); } catch(e) {} }
            html5QrCode = new Html5Qrcode("qr-reader");
            const config = { fps: 20, qrbox: { width: 250, height: 250 } };
            html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
                playBeep();
                html5QrCode.pause(true);
                procesarQR(decodedText);
            }).then(() => {
                $("#cameraStatus").removeClass("bg-light").addClass("bg-dark text-white").text("Cámara: Operativa");
            });
        }

        function procesarQR(codigoQR) {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'POST',
                data: { codigo_qr: codigoQR, action: 'registrar', tipo_registro: modoActual },
                success: function(response) {
                    try {
                        const data = (typeof response === 'object') ? response : JSON.parse(response);
                        showAlert(data.message, data.success ? 'success' : 'danger');
                        if(data.success) {
                            actualizarEstadisticas();
                            cargarHistorial();
                        }
                    } catch(e) { showAlert('Error en procesamiento.', 'danger'); }
                    setTimeout(() => { if(html5QrCode) html5QrCode.resume(); }, 1800);
                }
            });
        }

        function showAlert(m, t) {
            $('#alertContainer').hide().html(`
                <div class="alert alert-${t} animate__animated animate__fadeInDown shadow-sm text-center fw-bold">
                    ${m}
                </div>
            `).fadeIn();
            setTimeout(() => $("#alertContainer").fadeOut(), 3000);
        }

        function actualizarEstadisticas() {
            $.get('procesar_qr.php', { action: 'get_stats', t: new Date().getTime() }, function(res) {
                try {
                    const s = (typeof res === 'object') ? res : JSON.parse(res);
                    $('#totalHoy').addClass('animate__animated animate__pulse').text(s.total_hoy);
                    $('#totalPendientes').addClass('animate__animated animate__pulse').text(s.pendientes_salida);
                    setTimeout(() => $('.stat-number').removeClass('animate__animated animate__pulse'), 800);
                } catch(e){}
            });
        }

        function cargarHistorial() {
            const f = $('#fechaFiltro').val();
            $.get('procesar_qr.php', { action: 'get_asistencias', fecha: f, t: new Date().getTime() }, function(res) {
                try {
                    const asistencias = (typeof res === 'object') ? res : JSON.parse(res);
                    let html = '';
                    
                    asistencias.forEach((r, index) => {
                        const delay = index < 8 ? index * 0.05 : 0;
                        
                        if(r.hora_salida && r.hora_salida !== '00:00:00' && r.hora_salida !== null) {
                            html += `<tr class="row-anim" style="animation-delay: ${delay}s">
                                <td class="ps-4"><strong>${r.matricula}</strong></td>
                                <td>${r.nombre}</td>
                                <td>${r.hora_salida.substring(0,5)}</td>
                                <td class="text-end pe-4"><span class="badge bg-danger">SALIDA</span></td>
                            </tr>`;
                        }
                        if(r.hora_entrada) {
                            html += `<tr class="row-anim" style="animation-delay: ${delay}s">
                                <td class="ps-4"><strong>${r.matricula}</strong></td>
                                <td>${r.nombre}</td>
                                <td>${r.hora_entrada.substring(0,5)}</td>
                                <td class="text-end pe-4"><span class="badge bg-success">ENTRADA</span></td>
                            </tr>`;
                        }
                    });

                    $('#asistenciasBody').html(html || '<tr><td colspan="4" class="text-center py-4">No hay registros para esta fecha</td></tr>');
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
                    $("#cameraStatus").removeClass("bg-dark text-white").addClass("bg-light text-dark").text("Estado: Inactivo");
                }
            });
            $('#fechaFiltro').change(cargarHistorial);
        });
    </script>
</body>
</html>