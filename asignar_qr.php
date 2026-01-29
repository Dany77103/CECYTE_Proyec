<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar QR Grande | CECyTE SC</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root { --cecyte-green: #28a745; --cecyte-dark: #1e293b; }
        body { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); font-family: 'Inter', sans-serif; min-height: 100vh; }
        .navbar-custom { background: var(--cecyte-dark); border-bottom: 3px solid var(--cecyte-green); }
        .card-qr { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border: none; }
        .preview-box { width: 340px; height: 340px; border: 3px solid #f1f5f9; margin: 25px auto; display: flex; align-items: center; justify-content: center; background: #ffffff; border-radius: 15px; box-shadow: inset 0 2px 10px rgba(0,0,0,0.05); }
        #qrCanvas img { max-width: 100%; height: auto; padding: 15px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom mb-5">
    <div class="container">
        <span class="navbar-brand fw-bold"><i class='bx bx-qr-scan text-success me-2'></i>REGISTRO NUEVO - CECyTE SC</span>
        <a href="main.php" class="btn btn-outline-light btn-sm rounded-pill px-3">Volver al Inicio</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-qr">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Nombre del Alumno</label>
                        <input type="text" id="nom" class="form-control" placeholder="Ej: Juan Pérez López">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Matrícula</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                            <input type="text" id="mat" class="form-control" placeholder="Ej: 21008">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Grado</label>
                        <select id="grado" class="form-select">
                            <option value="1">1°</option><option value="2">2°</option>
                            <option value="3">3°</option><option value="4">4°</option>
                            <option value="5">5°</option><option value="6">6°</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Grupo</label>
                        <input type="text" id="gru" class="form-control" placeholder="Ej: A">
                    </div>
                </div>

                <div class="preview-box" id="qrCanvas">
                    <div class="text-center text-muted p-4">
                        <i class='bx bx-qr' style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="small mb-0">Genera el QR para visualizarlo</p>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" id="btnAccion" class="btn btn-success btn-lg">
                        <i class='bx bx-save'></i> Registrar Alumno
                    </button>
                    <button type="button" id="btnSave" class="btn btn-primary btn-lg" style="display:none;">
                        <i class='bx bx-download'></i> Descargar PNG
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnAccion').addEventListener('click', function() {
    const nom = document.getElementById('nom').value.trim();
    const mat = document.getElementById('mat').value.trim();
    const gra = document.getElementById('grado').value;
    const gru = document.getElementById('gru').value.trim();

    if(!nom || !mat || !gru) return alert("Completa todos los campos.");

    const fd = new FormData();
    fd.append('nombre', nom);
    fd.append('matricula', mat);
    fd.append('grupo', gra + gru); // Combina 6 + A = 6A como en tu DB

    fetch('registrar_qr.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') {
            const canvas = document.getElementById('qrCanvas');
            canvas.innerHTML = "";
            new QRCode(canvas, {
                text: mat,
                width: 300,
                height: 300,
                correctLevel : QRCode.CorrectLevel.H
            });
            document.getElementById('btnSave').style.display = 'block';
            alert("Alumno guardado exitosamente.");
        } else {
            alert("Error: " + data.message);
        }
    });
});

document.getElementById('btnSave').addEventListener('click', function() {
    const img = document.querySelector('#qrCanvas img');
    if(img) {
        const a = document.createElement('a');
        a.href = img.src;
        a.download = `QR_${document.getElementById('mat').value}.png`;
        a.click();
    }
});
</script>
</body>
</html>