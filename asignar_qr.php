<?php
session_start();

// Seguridad básica
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Código QR - CECYTE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .qr-container {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .section-title {
            color: #28a745; /* Verde CECYTE */
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qr-preview {
            width: 240px;
            height: 240px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            border-radius: 12px;
            background: #fafafa;
            border: 2px dashed #ddd;
            padding: 10px;
        }

        /* Estilo para que el QR generado sea responsivo dentro del div */
        #qrPreview img {
            max-width: 100%;
            height: auto;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-download {
            display: none; /* Se activa al generar el QR */
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="qr-container">
                <h3 class="section-title mb-4">
                    <i class='bx bx-qr-scan'></i> Generador de Credencial QR
                </h3>

                <form id="qrForm">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Matrícula</label>
                            <input type="text" class="form-control" id="matricula" placeholder="Ej. 20231234" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Grupo</label>
                            <input type="text" class="form-control" id="grupo" placeholder="Ej. 4-A" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Alumno</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Nombre completo" required>
                    </div>

                    <hr>

                    <div class="text-center">
                        <label class="form-label d-block fw-bold text-muted">CÓDIGO GENERADO</label>
                        <div class="qr-preview" id="qrPreview">
                            <div class="text-muted small">Complete los datos y pulse "Generar"</div>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-download" id="btnDescargar">
                            <i class='bx bx-download'></i> Descargar Imagen QR
                        </button>
                    </div>

                    <div class="d-flex justify-content-between mt-5">
                        <button type="reset" class="btn btn-outline-secondary" id="btnReset">
                            <i class='bx bx-undo'></i> Reiniciar
                        </button>

                        <button type="button" class="btn btn-success px-4" id="btnGenerar">
                            <i class='bx bx-qr'></i> Generar Código QR
                        </button>
                    </div>
                </form>
            </div>

            <div class="text-center mt-4 text-muted">
                <small>Sistema de Control Escolar - CECYTE Santa Catarina</small>
            </div>

        </div>
    </div>
</div>

<script>
    let qrGenerator = null;

    document.getElementById('btnGenerar').addEventListener('click', function() {
        const matricula = document.getElementById('matricula').value.trim();
        const grupo = document.getElementById('grupo').value.trim();
        const nombre = document.getElementById('nombre').value.trim();

        if(!matricula || !grupo || !nombre){
            alert('Por favor, completa todos los campos.');
            return;
        }

        // Formato estructurado para el QR
        const qrData = `ALUMNO: ${nombre}\nMATRICULA: ${matricula}\nGRUPO: ${grupo}\nINSTITUCION: CECYTE`;

        const qrContainer = document.getElementById('qrPreview');
        qrContainer.innerHTML = ""; // Limpiar mensaje previo
        qrContainer.style.borderStyle = "solid"; // Cambiar borde de dashed a solid

        // Generar QR
        qrGenerator = new QRCode(qrContainer, {
            text: qrData,
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Mostrar botón de descarga después de un breve delay para asegurar renderizado
        setTimeout(() => {
            document.getElementById('btnDescargar').style.display = 'inline-block';
        }, 300);
    });

    // Función para descargar el QR como imagen PNG
    document.getElementById('btnDescargar').addEventListener('click', function() {
        const qrImg = document.querySelector('#qrPreview img');
        if (qrImg) {
            const link = document.createElement('a');
            link.href = qrImg.src;
            link.download = `QR_${document.getElementById('matricula').value}.png`;
            link.click();
        }
    });

    // Resetear todo
    document.getElementById('btnReset').addEventListener('click', function() {
        const qrContainer = document.getElementById('qrPreview');
        qrContainer.innerHTML = '<div class="text-muted small">Complete los datos y pulse "Generar"</div>';
        qrContainer.style.borderStyle = "dashed";
        document.getElementById('btnDescargar').style.display = 'none';
    });
</script>

</body>
</html>