<?php
ob_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
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
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
    <style>
        :root { 
            --primary-color: #064e3b; 
            --accent-color: #10b981; 
            --corp-red: #be123c; 
            --bg-light: #f1f5f9;
        }
        
        body { 
            background-color: var(--bg-light);
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        .navbar-custom { 
            background: #ffffff;
            border-bottom: 3px solid var(--accent-color);
            padding: 1rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .navbar-brand { font-weight: 700; color: var(--primary-color) !important; }

        .btn-back-main {
            background: rgba(6, 78, 59, 0.1);
            color: var(--primary-color);
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn-back-main:hover {
            background: var(--primary-color);
            color: white;
        }

        .card-custom { 
            border: none;
            border-radius: 20px; 
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .qr-wrapper { 
            background: #000; 
            border-radius: 15px; 
            overflow: hidden; 
            border: 5px solid #e2e8f0;
            max-width: 450px;
            margin: 0 auto;
            transition: 0.3s;
        }
        
        .mode-entrada .qr-wrapper { border-color: var(--accent-color); box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); }
        .mode-salida .qr-wrapper { border-color: var(--corp-red); box-shadow: 0 0 20px rgba(190, 18, 60, 0.2); }

        .stat-card { padding: 2rem; border-left: 5px solid var(--primary-color); }
        .stat-number { font-size: 3.5rem; font-weight: 800; color: var(--primary-color); }
        .stat-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }

        .mode-selector-corp {
            background: #f1f5f9;
            padding: 5px;
            border-radius: 12px;
            display: inline-flex;
            gap: 5px;
        }

        .btn-mode { 
            border: none; 
            padding: 10px 25px; 
            border-radius: 8px; 
            font-size: 0.8rem; 
            font-weight: 700; 
            transition: all 0.3s;
            color: #64748b;
            background: transparent;
        }

        .btn-mode.active-in { background: var(--accent-color); color: white !important; }
        .btn-mode.active-out { background: var(--corp-red); color: white !important; }

        .room-selector {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .room-btn-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 8px;
        }

        .btn-room {
            border: 2px solid #e2e8f0;
            background: white;
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 8px 5px;
            border-radius: 10px;
            transition: 0.2s;
        }

        .btn-room.active {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }

        .table thead th { 
            background: #f8fafc; 
            color: #64748b; 
            font-size: 0.7rem; 
            letter-spacing: 1px;
            border: none;
            padding: 15px;
        }
        .table tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        
        .badge-entrada { background: rgba(16, 185, 129, 0.1); color: #065f46; font-weight: 700; }
        .badge-salida { background: rgba(190, 18, 60, 0.1); color: #9f1239; font-weight: 700; }

        .btn-excel {
            background-color: #166534;
            color: white; border: none; font-weight: 600;
            padding: 8px 16px; border-radius: 10px;
        }
        
        .row-anim { animation: fadeInRight 0.5s ease backwards; }
    </style>
</head>
<body class="mode-entrada">
    <nav class="navbar navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class='bx bxs-shield-quarter fs-3 me-2'></i>
                <span>CECyTE SC <span class="fw-light text-muted">| Gestión de Acceso</span></span>
            </a>
            
            <div class="mode-selector-corp d-none d-md-flex">
                <button class="btn-mode active-in" id="btnModoEntrada">
                    <i class='bx bx-log-in-circle me-1'></i> ENTRADA
                </button>
                <button class="btn-mode" id="btnModoSalida">
                    <i class='bx bx-log-out-circle me-1'></i> SALIDA
                </button>
            </div>

            <a href="main.php" class="btn-back-main">
                <i class='bx bx-home-alt-2 me-1'></i> PANEL
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div id="alertContainer"></div>
        
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card card-custom h-100">
                    <div class="card-body p-5 text-center">
                        <div class="room-selector animate__animated animate__fadeIn">
                            <label class="stat-label d-block mb-3 text-start"><i class='bx bx-map-pin'></i> Ubicación del Scanner</label>
                            <div class="room-btn-group" id="roomButtonGroup">
                                <button class="btn-room" data-room="Aula A">Aula A</button>
                                <button class="btn-room" data-room="Aula B">Aula B</button>
                                <button class="btn-room" data-room="Aula C">Aula C</button>
                                <button class="btn-room" data-room="Aula D">Aula D</button>
                                <button class="btn-room" data-room="Lab 1">Lab 1</button>
                                <button class="btn-room" data-room="Lab 2">Lab 2</button>
                                <button class="btn-room" data-room="Biblioteca">Biblio</button>
                            </div>
                            <input type="hidden" id="salonSeleccionado" value="No especificado">
                        </div>

                        <div class="mb-4">
                            <h4 class="fw-bold mb-1" id="tituloEscaner">REGISTRO DE ENTRADA</h4>
                            <p class="text-muted small">Alinee el código QR del alumno dentro del recuadro</p>
                        </div>
                        
                        <div class="qr-wrapper mb-4" id="reader-container">
                            <div id="qr-reader"></div>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-dark btn-lg px-4 fw-bold" id="startBtn" style="border-radius: 12px;">
                                <i class='bx bx-camera me-2'></i>Activar Cámara
                            </button>
                            <button class="btn btn-outline-danger btn-lg px-4 fw-bold" id="stopBtn" style="border-radius: 12px;">
                                <i class='bx bx-power-off me-2'></i>Apagar
                            </button>
                        </div>
                        
                        <div class="mt-4">
                            <span id="cameraStatus" class="badge rounded-pill bg-light text-dark px-3 py-2 border">
                                <i class='bx bxs-circle me-1'></i> Estado: Inactivo
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="row g-4 h-100">
                    <div class="col-12">
                        <div class="card card-custom stat-card">
                            <p class="stat-label mb-1">Movimientos del Día</p>
                            <h2 id="totalHoy" class="stat-number mb-0">0</h2>
                            <div class="mt-2 small text-muted">
                                <i class='bx bx-trending-up me-1'></i> Registros procesados hoy
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card card-custom stat-card" style="border-left-color: var(--accent-color);">
                            <p class="stat-label mb-1">Alumnos en el Plantel</p>
                            <h2 id="totalPendientes" class="stat-number mb-0" style="color: var(--accent-color);">0</h2>
                            <div class="mt-2 small text-muted">
                                <i class='bx bx-user-check me-1'></i> Estancia actual detectada
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom mt-5 animate__animated animate__fadeInUp">
            <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center border-0">
                <div>
                    <h5 class="mb-0 fw-bold"><i class='bx bx-list-ul me-2 text-success'></i>Bitácora en Tiempo Real</h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <input type="date" id="fechaFiltro" class="form-control form-control-sm border-0 bg-light fw-bold px-3" value="<?php echo date('Y-m-d'); ?>" style="border-radius: 8px;">
                    <button id="btnExportarExcel" class="btn btn-excel btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Exportar
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaAsistencias">
                    <thead>
                        <tr>
                            <th class="ps-4">Matrícula</th>
                            <th>Ubicación</th> <th>Nombre del Alumno</th>
                            <th>Hora de Registro</th>
                            <th class="text-end pe-4">Estatus</th>
                        </tr>
                    </thead>
                    <tbody id="asistenciasBody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let html5QrCode;
        let modoActual = 'entrada';

        $('.btn-room').click(function() {
            $('.btn-room').removeClass('active');
            $(this).addClass('active');
            const room = $(this).data('room');
            $('#salonSeleccionado').val(room);
            localStorage.setItem('sc_current_room', room);
        });

        function restaurarSalon() {
            const savedRoom = localStorage.getItem('sc_current_room');
            if(savedRoom) {
                $(`.btn-room[data-room="${savedRoom}"]`).trigger('click');
            } else {
                $('.btn-room').first().trigger('click');
            }
        }

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
                $("#cameraStatus").removeClass("bg-light").addClass("bg-success text-white").html("<i class='bx bxs-check-circle me-1'></i> Cámara: Operativa");
            });
        }

        function procesarQR(codigoQR) {
            const salon = $('#salonSeleccionado').val();
            $.ajax({
                url: 'procesar_qr.php',
                type: 'POST',
                data: { 
                    codigo_qr: codigoQR, 
                    action: 'registrar', 
                    tipo_registro: modoActual,
                    salon: salon 
                },
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
                <div class="alert alert-${t} animate__animated animate__fadeInDown shadow-lg text-center fw-bold py-3" style="border-radius:15px; border:none;">
                    ${m}
                </div>
            `).fadeIn();
            setTimeout(() => $("#alertContainer").fadeOut(), 3000);
        }

        function actualizarEstadisticas() {
            $.get('procesar_qr.php', { action: 'get_stats', t: new Date().getTime() }, function(res) {
                try {
                    const s = (typeof res === 'object') ? res : JSON.parse(res);
                    $('#totalHoy').text(s.total_hoy);
                    $('#totalPendientes').text(s.pendientes_salida);
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
                        const delay = index < 10 ? index * 0.05 : 0;
                        const salonTxt = r.salon ? r.salon : 'S/N';
                        
                        // Fila para SALIDA (si existe)
                        if(r.hora_salida && r.hora_salida !== '00:00:00' && r.hora_salida !== null) {
                            html += `<tr class="row-anim" style="animation-delay: ${delay}s">
                                <td class="ps-4 fw-bold text-muted">${r.matricula}</td>
                                <td><span class="badge bg-light text-dark border"><i class='bx bx-map-pin me-1 text-danger'></i>${salonTxt}</span></td>
                                <td class="fw-semibold">${r.nombre}</td>
                                <td>${r.hora_salida.substring(0,5)} <small class="text-muted">hrs</small></td>
                                <td class="text-end pe-4"><span class="badge badge-salida px-3 py-2 rounded-pill">SALIDA</span></td>
                            </tr>`;
                        }
                        // Fila para ENTRADA
                        if(r.hora_entrada) {
                            html += `<tr class="row-anim" style="animation-delay: ${delay}s">
                                <td class="ps-4 fw-bold text-muted">${r.matricula}</td>
                                <td><span class="badge bg-light text-dark border"><i class='bx bx-map-pin me-1 text-success'></i>${salonTxt}</span></td>
                                <td class="fw-semibold">${r.nombre}</td>
                                <td>${r.hora_entrada.substring(0,5)} <small class="text-muted">hrs</small></td>
                                <td class="text-end pe-4"><span class="badge badge-entrada px-3 py-2 rounded-pill">ENTRADA</span></td>
                            </tr>`;
                        }
                    });
                    $('#asistenciasBody').html(html || '<tr><td colspan="5" class="text-center py-5 text-muted">No se encontraron registros hoy</td></tr>');
                } catch(e){}
            });
        }

        $(document).ready(function() {
            restaurarSalon();
            actualizarEstadisticas();
            cargarHistorial();
            $('#startBtn').click(startScanner);
            $('#stopBtn').click(async () => {
                if(html5QrCode) {
                    await html5QrCode.stop();
                    $("#cameraStatus").removeClass("bg-success text-white").addClass("bg-light text-dark").html("<i class='bx bxs-circle me-1'></i> Estado: Inactivo");
                }
            });
            $('#fechaFiltro').change(cargarHistorial);
        });
    </script>
</body>
</html>