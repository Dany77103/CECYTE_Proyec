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
    <title>Control Físico | CECyTE SC</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <style>
        :root { --primary-color: #064e3b; --accent-color: #10b981; --corp-red: #be123c; --bg-light: #f1f5f9; }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }
        .navbar-custom { background: #ffffff; border-bottom: 3px solid var(--accent-color); padding: 1rem 0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card-custom { border: none; border-radius: 20px; background: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .scanner-visual { background: #f8fafc; border: 3px dashed #cbd5e1; border-radius: 20px; padding: 40px; transition: 0.3s; }
        .mode-entrada .scanner-visual { border-color: var(--accent-color); background: rgba(16, 185, 129, 0.05); }
        .mode-salida .scanner-visual { border-color: var(--corp-red); background: rgba(190, 18, 60, 0.05); }
        #physicalScannerInput { position: absolute; opacity: 0; top: 0; left: 0; }
        .btn-mode { border: none; padding: 10px 25px; border-radius: 8px; font-weight: 700; color: #64748b; background: transparent; }
        .btn-mode.active-in { background: var(--accent-color); color: white !important; }
        .btn-mode.active-out { background: var(--corp-red); color: white !important; }
        .stat-card { padding: 2rem; border-left: 5px solid var(--primary-color); }
        .stat-number { font-size: 3.5rem; font-weight: 800; color: var(--primary-color); }
        .badge-entrada { background: rgba(16, 185, 129, 0.1); color: #065f46; font-weight: 700; }
        .badge-salida { background: rgba(190, 18, 60, 0.1); color: #9f1239; font-weight: 700; }
    </style>
</head>
<body class="mode-entrada">
    <input type="text" id="physicalScannerInput" autofocus autocomplete="off">

    <nav class="navbar navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class='bx bxs-barcode-reader fs-3 me-2'></i>
                <span>CECyTE SC <span class="fw-light text-muted">| Escáner Físico</span></span>
            </a>
            <div class="mode-selector-corp">
                <button class="btn-mode active-in" id="btnModoEntrada">ENTRADA</button>
                <button class="btn-mode" id="btnModoSalida">SALIDA</button>
            </div>
            <a href="main.php" class="btn btn-outline-secondary btn-sm">VOLVER</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div id="alertContainer"></div>
        
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card card-custom h-100">
                    <div class="card-body p-5 text-center">
                        <div class="scanner-visual mb-4">
                            <i class='bx bx-barcode-reader display-1 mb-3' style="color: #64748b;"></i>
                            <h3 class="fw-bold" id="tituloEscaner">ESPERANDO ESCANEO...</h3>
                            <p class="text-muted">Use el escáner manual sobre el código QR/Barras</p>
                            <div id="statusPulse" class="spinner-grow text-success" role="status"></div>
                        </div>

                        <div class="room-selector mb-4">
                            <div class="btn-group w-100" id="roomButtonGroup">
                                <button class="btn btn-outline-dark btn-sm active btn-room" data-room="Aula A">Aula A</button>
                                <button class="btn btn-outline-dark btn-sm btn-room" data-room="Aula B">Aula B</button>
                                <button class="btn btn-outline-dark btn-sm btn-room" data-room="Laboratorio">Laboratorio</button>
                            </div>
                            <input type="hidden" id="salonSeleccionado" value="Aula A">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-custom stat-card mb-4">
                    <p class="text-muted small fw-bold mb-0">TOTAL HOY</p>
                    <h2 id="totalHoy" class="stat-number">0</h2>
                </div>
                <div class="card card-custom stat-card" style="border-left-color: var(--accent-color);">
                    <p class="text-muted small fw-bold mb-0">EN PLANTEL</p>
                    <h2 id="totalPendientes" class="stat-number" style="color: var(--accent-color);">0</h2>
                </div>
            </div>
        </div>

        <div class="card card-custom mt-5">
            <div class="card-body">
                <h5 class="fw-bold mb-4"><i class='bx bx-history me-2'></i>Últimos Registros</h5>
                <div class="table-responsive">
                    <table class="table" id="tablaAsistencias">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th>Hora</th>
                                <th class="text-end">Tipo</th>
                            </tr>
                        </thead>
                        <tbody id="asistenciasBody">
                            <tr><td colspan="5" class="text-center py-3">Cargando registros...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let modoActual = 'entrada';
        const inputFisico = $('#physicalScannerInput');

        $(document).on('click', function() { inputFisico.focus(); });

        inputFisico.on('keypress', function(e) {
            if (e.which == 13) {
                const codigo = $(this).val().trim();
                if (codigo !== "") { procesarRegistro(codigo); }
                $(this).val("");
            }
        });

        function procesarRegistro(codigo) {
            const salon = $('#salonSeleccionado').val();
            $('.scanner-visual').fadeOut(100).fadeIn(100);
            
            $.ajax({
                url: 'procesar_qr.php',
                type: 'POST',
                data: { 
                    codigo_qr: codigo, 
                    action: 'registrar', 
                    tipo_registro: modoActual,
                    salon: salon 
                },
                success: function(response) {
                    try {
                        const data = (typeof response === 'object') ? response : JSON.parse(response);
                        showAlert(data.message, data.success ? 'success' : 'danger');
                        actualizarEstadisticas();
                        cargarHistorial();
                    } catch(e) { 
                        showAlert('Error en el formato de respuesta', 'danger'); 
                    }
                }
            });
        }

        $('#btnModoEntrada').click(function() {
            modoActual = 'entrada';
            $('body').attr('class', 'mode-entrada');
            $(this).addClass('active-in');
            $('#btnModoSalida').removeClass('active-out');
            $('#statusPulse').attr('class', 'spinner-grow text-success');
        });

        $('#btnModoSalida').click(function() {
            modoActual = 'salida';
            $('body').attr('class', 'mode-salida');
            $(this).addClass('active-out');
            $('#btnModoEntrada').removeClass('active-in');
            $('#statusPulse').attr('class', 'spinner-grow text-danger');
        });

        $('.btn-room').click(function() {
            $('.btn-room').removeClass('active');
            $(this).addClass('active');
            $('#salonSeleccionado').val($(this).data('room'));
        });

        function showAlert(m, t) {
            $('#alertContainer').html(`<div class="alert alert-${t} fw-bold text-center shadow">${m}</div>`).fadeIn();
            setTimeout(() => $("#alertContainer").fadeOut(), 2500);
        }

        function actualizarEstadisticas() {
            $.get('procesar_qr.php', { action: 'get_stats', t: Date.now() }, function(res) {
                try {
                    const s = (typeof res === 'object') ? res : JSON.parse(res);
                    $('#totalHoy').text(s.total_hoy || 0);
                    $('#totalPendientes').text(s.pendientes_salida || 0);
                } catch(e) {}
            });
        }

        function cargarHistorial() {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'GET',
                data: { action: 'get_asistencias', t: Date.now() },
                success: function(res) {
                    try {
                        const asistencias = (typeof res === 'object') ? res : JSON.parse(res);
                        console.log("Datos recibidos para la tabla:", asistencias); // DEPURACIÓN: Ver en consola F12
                        
                        let html = '';
                        if(asistencias && asistencias.length > 0) {
                            asistencias.forEach(r => {
                                // Mapeo flexible por si los nombres de columnas varían
                                const matricula = r.matricula || r.codigo_qr || '---';
                                const nombre = r.nombre || r.alumno_nombre || 'Desconocido';
                                const salon = r.salon || r.ubicacion || '---';
                                const esSalida = (r.hora_salida && r.hora_salida !== '00:00:00' && r.hora_salida !== null);
                                const hora = esSalida ? r.hora_salida : (r.hora_entrada || '--:--');
                                const tipo = esSalida ? 'SALIDA' : 'ENTRADA';
                                const badge = esSalida ? 'badge-salida' : 'badge-entrada';

                                html += `<tr>
                                    <td><strong>${matricula}</strong></td>
                                    <td>${nombre}</td>
                                    <td>${salon}</td>
                                    <td>${hora.substring(0,5)}</td>
                                    <td class="text-end"><span class="badge ${badge} px-3 py-2 rounded-pill">${tipo}</span></td>
                                </tr>`;
                            });
                            $('#asistenciasBody').html(html);
                        } else {
                            $('#asistenciasBody').html('<tr><td colspan="5" class="text-center py-3">No hay registros hoy</td></tr>');
                        }
                    } catch(e) {
                        console.error("Error parseando JSON historial:", e);
                    }
                }
            });
        }

        $(document).ready(function() {
            inputFisico.focus();
            actualizarEstadisticas();
            cargarHistorial();
            setInterval(() => { if(!$('#alertContainer').is(':visible')) inputFisico.focus(); }, 3000);
        });
    </script>
</body>
</html>