
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
    <title>Sistema de Asistencia QR - CECyTE</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- QR Scanner CSS -->
    <link rel="stylesheet" href="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .container-main {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            overflow: hidden;
        }
        
        .card-custom:hover {
            transform: translateY(-5px);
        }
        
        .card-header-custom {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            border-bottom: none;
        }
        
        .qr-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
            margin: 20px 0;
        }
        
        #qr-reader {
            width: 100%;
            margin: 20px 0;
        }
        
        #qr-reader-results {
            font-size: 1.1rem;
            margin-top: 20px;
        }
        
        .status-indicator {
            display: inline-block;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-entrada {
            background-color: var(--success-color);
        }
        
        .status-salida {
            background-color: var(--warning-color);
        }
        
        .status-pendiente {
            background-color: var(--danger-color);
        }
        
        .btn-custom {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-scan {
            background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
            color: white;
        }
        
        .btn-scan:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-generate {
            background: linear-gradient(90deg, #27ae60, #2ecc71);
            color: white;
        }
        
        .btn-generate:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        }
        
        .table-custom {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .table-custom thead {
            background: var(--primary-color);
            color: white;
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .qr-code-display {
            display: inline-block;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stats-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        @media (max-width: 768px) {
            .container-main {
                padding: 10px;
            }
            
            .card-custom {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-qrcode"></i> Sistema de Asistencia QR
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#escaneo"><i class="fas fa-camera"></i> Escanear QR</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#generador"><i class="fas fa-qrcode"></i> Generar QR</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#registros"><i class="fas fa-history"></i> Historial</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes.php"><i class="fas fa-arrow-left"></i> Volver a Reportes</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-main">
        <!-- Alertas -->
        <div id="alertContainer"></div>

        <!-- Sección de Escaneo -->
        <div class="card card-custom mb-5" id="escaneo">
            <div class="card-header card-header-custom">
                <h3 class="mb-0"><i class="fas fa-camera me-2"></i> Escanear Código QR</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="qr-container">
                            <div id="qr-reader"></div>
                            <div id="qr-reader-results"></div>
                        </div>
                        <div class="text-center mt-4">
                            <button class="btn btn-custom btn-scan" id="startScanner">
                                <i class="fas fa-play me-2"></i> Iniciar Escáner
                            </button>
                            <button class="btn btn-outline-secondary ms-2" id="stopScanner">
                                <i class="fas fa-stop me-2"></i> Detener Escáner
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card mb-4">
                            <div class="stats-number" id="totalHoy">0</div>
                            <div class="stats-label">Asistencias Hoy</div>
                        </div>
                        <div class="stats-card mb-4">
                            <div class="stats-number" id="totalPendientes">0</div>
                            <div class="stats-label">Pendientes de Salida</div>
                        </div>
                        <div class="alert alert-info alert-custom">
                            <h5><i class="fas fa-info-circle me-2"></i> Instrucciones:</h5>
                            <ol class="mb-0">
                                <li>Permite el acceso a la cámara</li>
                                <li>Coloca el código QR frente a la cámara</li>
                                <li>El sistema registrará automáticamente entrada/salida</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Generación de QR -->
        <div class="card card-custom mb-5" id="generador">
            <div class="card-header card-header-custom">
                <h3 class="mb-0"><i class="fas fa-qrcode me-2"></i> Generar Códigos QR</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchAlumno" placeholder="Buscar alumno por nombre o matrícula">
                            <button class="btn btn-outline-primary" type="button" id="btnBuscar">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-custom btn-generate" id="generateAllQR">
                            <i class="fas fa-sync-alt me-2"></i> Generar Todos los QR
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-custom" id="alumnosTable">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre</th>
                                <th>Grupo</th>
                                <th>Estado QR</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="alumnosBody">
                            <!-- Los datos se cargarán por AJAX -->
                        </tbody>
                    </table>
                </div>

                <!-- QR Preview Modal -->
                <div class="modal fade" id="qrModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Código QR del Alumno</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div id="qrPreview" class="mb-3"></div>
                                <div id="alumnoInfo"></div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" onclick="descargarQR()">
                                    <i class="fas fa-download me-2"></i> Descargar QR
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Historial -->
        <div class="card card-custom" id="registros">
            <div class="card-header card-header-custom">
                <h3 class="mb-0"><i class="fas fa-history me-2"></i> Historial de Asistencias</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <input type="date" class="form-control" id="fechaFiltro" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="grupoFiltro">
                            <option value="">Todos los grupos</option>
                            <!-- Opciones de grupos se cargarán por AJAX -->
                        </select>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-outline-primary" onclick="exportarExcel()">
                            <i class="fas fa-file-excel me-2"></i> Exportar Excel
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-custom" id="asistenciasTable">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre</th>
                                <th>Fecha</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="asistenciasBody">
                            <!-- Los datos se cargarán por AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- QR Scanner -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <!-- Excel Export -->
    <script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>
    
    <script>
        // Variables globales
        let html5QrCode;
        let qrGenerado = null;
        let alumnoActual = null;

        // Inicializar el escáner QR
        function initScanner() {
            html5QrCode = new Html5Qrcode("qr-reader");
            
            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                // Detener el escáner temporalmente
                stopScanner();
                
                // Procesar el código QR
                procesarQR(decodedText);
                
                // Reiniciar después de 2 segundos
                setTimeout(() => {
                    startScanner();
                }, 2000);
            };
            
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            };
            
            return { qrCodeSuccessCallback, config };
        }

        // Iniciar escáner
        function startScanner() {
            const { qrCodeSuccessCallback, config } = initScanner();
            
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const cameraId = devices[0].id;
                    html5QrCode.start(
                        cameraId,
                        config,
                        qrCodeSuccessCallback,
                        error => {
                            console.error(error);
                        }
                    );
                } else {
                    showAlert('No se encontraron cámaras disponibles', 'danger');
                }
            }).catch(err => {
                showAlert('Error al acceder a la cámara: ' + err, 'danger');
            });
        }

        // Detener escáner
        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(ignore => {
                    console.log("Escáner detenido");
                }).catch(err => {
                    console.error("Error al detener escáner:", err);
                });
            }
        }

        // Procesar código QR escaneado
        function procesarQR(codigoQR) {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'POST',
                data: {
                    codigo_qr: codigoQR,
                    action: 'registrar'
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showAlert(data.message, 'success');
                        actualizarEstadisticas();
                        cargarHistorial();
                    } else {
                        showAlert(data.message, 'danger');
                    }
                },
                error: function() {
                    showAlert('Error al conectar con el servidor', 'danger');
                }
            });
        }

        // Mostrar alerta
        function showAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show alert-custom" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('#alertContainer').html(alertHtml);
        }

        // Cargar lista de alumnos
        function cargarAlumnos(busqueda = '') {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'GET',
                data: { 
                    action: 'get_alumnos',
                    search: busqueda 
                },
                success: function(response) {
                    const alumnos = JSON.parse(response);
                    let html = '';
                    
                    alumnos.forEach(alumno => {
                        html += `
                            <tr>
                                <td>${alumno.matricula}</td>
                                <td>${alumno.nombre}</td>
                                <td>${alumno.grupo}</td>
                                <td>
                                    <span class="badge ${alumno.qr_generado ? 'bg-success' : 'bg-warning'}">
                                        ${alumno.qr_generado ? 'Generado' : 'Pendiente'}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="generarQRIndividual(${alumno.id})">
                                        <i class="fas fa-qrcode"></i> Generar QR
                                    </button>
                                    ${alumno.qr_generado ? `
                                        <button class="btn btn-sm btn-info" onclick="verQR(${alumno.id})">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                    ` : ''}
                                </td>
                            </tr>
                        `;
                    });
                    
                    $('#alumnosBody').html(html);
                }
            });
        }

        // Generar QR para un alumno individual
        function generarQRIndividual(alumnoId) {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'POST',
                data: {
                    action: 'generar_qr',
                    alumno_id: alumnoId
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showAlert(data.message, 'success');
                        alumnoActual = data.alumno;
                        mostrarQRModal(data.qr_code);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                }
            });
        }

        // Generar todos los QR
        function generarTodosQR() {
            if (confirm('¿Estás seguro de generar códigos QR para todos los alumnos?')) {
                $.ajax({
                    url: 'procesar_qr.php',
                    type: 'POST',
                    data: {
                        action: 'generar_todos_qr'
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        showAlert(data.message, data.success ? 'success' : 'warning');
                        cargarAlumnos();
                    }
                });
            }
        }

        // Mostrar QR en modal
        function mostrarQRModal(qrData) {
            const modal = new bootstrap.Modal(document.getElementById('qrModal'));
            const qrPreview = document.getElementById('qrPreview');
            const alumnoInfo = document.getElementById('alumnoInfo');
            
            // Generar QR visual
            QRCode.toCanvas(qrPreview, qrData, {
                width: 200,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            }, function(error) {
                if (error) console.error(error);
            });
            
            // Mostrar información del alumno
            alumnoInfo.innerHTML = `
                <h5>${alumnoActual.nombre}</h5>
                <p class="mb-1">Matrícula: ${alumnoActual.matricula}</p>
                <p>Grupo: ${alumnoActual.grupo}</p>
                <small class="text-muted">Escanea este código para registrar asistencia</small>
            `;
            
            // Guardar QR para descarga
            qrGenerado = {
                data: qrData,
                nombre: alumnoActual.nombre.replace(/\s+/g, '_'),
                matricula: alumnoActual.matricula
            };
            
            modal.show();
        }

        // Ver QR existente
        function verQR(alumnoId) {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'POST',
                data: {
                    action: 'ver_qr',
                    alumno_id: alumnoId
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        alumnoActual = data.alumno;
                        mostrarQRModal(data.qr_code);
                    }
                }
            });
        }

        // Descargar QR
        function descargarQR() {
            if (!qrGenerado) return;
            
            const canvas = document.querySelector('#qrPreview canvas');
            const link = document.createElement('a');
            link.download = `QR_${qrGenerado.nombre}_${qrGenerado.matricula}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        }

        // Cargar historial de asistencias
        function cargarHistorial() {
            const fecha = $('#fechaFiltro').val();
            const grupo = $('#grupoFiltro').val();
            
            $.ajax({
                url: 'procesar_qr.php',
                type: 'GET',
                data: {
                    action: 'get_asistencias',
                    fecha: fecha,
                    grupo: grupo
                },
                success: function(response) {
                    const asistencias = JSON.parse(response);
                    let html = '';
                    
                    asistencias.forEach(asistencia => {
                        const estado = asistencia.hora_salida ? 
                            '<span class="badge bg-success">Completo</span>' : 
                            '<span class="badge bg-warning">En clase</span>';
                        
                        html += `
                            <tr>
                                <td>${asistencia.matricula}</td>
                                <td>${asistencia.nombre}</td>
                                <td>${asistencia.fecha}</td>
                                <td>${asistencia.hora_entrada || '-'}</td>
                                <td>${asistencia.hora_salida || '-'}</td>
                                <td>${estado}</td>
                            </tr>
                        `;
                    });
                    
                    $('#asistenciasBody').html(html);
                }
            });
        }

        // Actualizar estadísticas
        function actualizarEstadisticas() {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'GET',
                data: { action: 'get_stats' },
                success: function(response) {
                    const stats = JSON.parse(response);
                    $('#totalHoy').text(stats.total_hoy);
                    $('#totalPendientes').text(stats.pendientes_salida);
                }
            });
        }

        // Exportar a Excel
        function exportarExcel() {
            const fecha = $('#fechaFiltro').val();
            const grupo = $('#grupoFiltro').val();
            
            $.ajax({
                url: 'procesar_qr.php',
                type: 'GET',
                data: {
                    action: 'export_excel',
                    fecha: fecha,
                    grupo: grupo
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    
                    // Crear libro de Excel
                    const ws = XLSX.utils.json_to_sheet(data);
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Asistencias");
                    
                    // Generar nombre del archivo
                    const fechaStr = fecha || 'todas';
                    const grupoStr = grupo || 'todos';
                    const filename = `Asistencias_${fechaStr}_${grupoStr}.xlsx`;
                    
                    // Descargar
                    XLSX.writeFile(wb, filename);
                }
            });
        }

        // Cargar opciones de grupos
        function cargarGrupos() {
            $.ajax({
                url: 'procesar_qr.php',
                type: 'GET',
                data: { action: 'get_grupos' },
                success: function(response) {
                    const grupos = JSON.parse(response);
                    let html = '<option value="">Todos los grupos</option>';
                    
                    grupos.forEach(grupo => {
                        html += `<option value="${grupo}">${grupo}</option>`;
                    });
                    
                    $('#grupoFiltro').html(html);
                }
            });
        }

        // Event Listeners
        $(document).ready(function() {
            // Cargar datos iniciales
            cargarAlumnos();
            cargarHistorial();
            cargarGrupos();
            actualizarEstadisticas();
            
            // Eventos
            $('#startScanner').click(startScanner);
            $('#stopScanner').click(stopScanner);
            $('#generateAllQR').click(generarTodosQR);
            $('#btnBuscar').click(() => cargarAlumnos($('#searchAlumno').val()));
            $('#searchAlumno').keypress(function(e) {
                if (e.which === 13) cargarAlumnos($(this).val());
            });
            $('#fechaFiltro, #grupoFiltro').change(cargarHistorial);
            
            // Inicializar escáner al cargar
            initScanner();
        });
    </script>
</body>
</html>