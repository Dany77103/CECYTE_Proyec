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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar QR | CECyTE SC</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        :root { 
            --primary-color: #064e3b; /* Verde Bosque Institucional */
            --accent-color: #10b981; /* Verde Esmeralda */
            --bg-body: #f8fafc;
        }

        body { 
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif; 
            min-height: 100vh;
            color: #1e293b;
        }

        /* Navbar Unificado */
        .navbar-custom { 
            background: #ffffff; 
            border-bottom: 3px solid var(--accent-color);
            padding: 1rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .navbar-brand { color: var(--primary-color) !important; font-weight: 700; }

        .btn-back {
            background: rgba(6, 78, 59, 0.1);
            color: var(--primary-color);
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-back:hover { background: var(--primary-color); color: white; }

        /* Tarjeta Principal */
        .card-qr { 
            background: white; 
            border-radius: 24px; 
            padding: 40px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
            border: none;
            margin-top: 2rem;
        }

        .form-label { font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px; }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            background: white;
            box-shadow: none;
        }

        /* Contenedor del QR */
        .preview-box { 
            width: 320px; 
            height: 320px; 
            border: 2px dashed #e2e8f0; 
            margin: 30px auto; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: #ffffff; 
            border-radius: 20px;
            transition: 0.3s;
        }

        .preview-box:hover { border-color: var(--accent-color); }

        #qrCanvas img { 
            max-width: 90%; 
            height: auto; 
            padding: 10px;
            animation: zoomIn 0.5s ease;
        }

        /* Botones Especiales */
        .btn-action {
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-register { background-color: var(--primary-color); border: none; color: white; }
        .btn-register:hover { background-color: #043a2c; transform: translateY(-2px); }

        .btn-download { background-color: #334155; border: none; color: white; }
        .btn-download:hover { background-color: #1e293b; }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class='bx bx-user-plus fs-3 me-2 text-success'></i>
            <span>CECyTE SC <span class="fw-light text-muted">| Registro Nuevo</span></span>
        </a>
        <a href="main.php" class="btn-back">
            <i class='bx bx-arrow-back me-1'></i> Volver
        </a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card-qr">
                <div class="text-center mb-4">
                    <h4 class="fw-bold m-0">Credencial Digital</h4>
                    <p class="text-muted small">Genera el código único de acceso para el estudiante</p>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Nombre del Alumno</label>
                        <input type="text" id="nom" class="form-control" placeholder="Nombre completo">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Matrícula Escolar</label>
                        <div class="input-group">
                            <span class="input-group-text border-0" style="border-radius: 12px 0 0 12px;"><i class='bx bx-id-card'></i></span>
                            <input type="text" id="mat" class="form-control" placeholder="Ej: 210080000" style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>
                    <div class="col-6 mb-4">
                        <label class="form-label">Grado</label>
                        <select id="grado" class="form-select">
                            <option value="1">1° Semestre</option><option value="2">2° Semestre</option>
                            <option value="3">3° Semestre</option><option value="4">4° Semestre</option>
                            <option value="5">5° Semestre</option><option value="6">6° Semestre</option>
                        </select>
                    </div>
                    <div class="col-6 mb-4">
                        <label class="form-label">Grupo</label>
                        <input type="text" id="gru" class="form-control" placeholder="Ej: A">
                    </div>
                </div>

                <div class="preview-box" id="qrCanvas">
                    <div class="text-center text-muted opacity-50 p-4">
                        <i class='bx bx-qr-scan' style="font-size: 4rem;"></i>
                        <p class="small mt-2 mb-0">Esperando datos...</p>
                    </div>
                </div>

                <div class="d-grid gap-3">
                    <button type="button" id="btnAccion" class="btn btn-action btn-register">
                        <i class='bx bx-cloud-upload fs-5'></i> Registrar y Generar QR
                    </button>
                    <button type="button" id="btnSave" class="btn btn-action btn-download" style="display:none;">
                        <i class='bx bx-download fs-5'></i> Guardar Imagen QR
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

    if(!nom || !mat || !gru) {
        alert("Por favor, completa todos los campos para continuar.");
        return;
    }

    const fd = new FormData();
    fd.append('nombre', nom);
    fd.append('matricula', mat);
    fd.append('grupo', gra + gru); 

    fetch('registrar_qr.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') {
            const canvas = document.getElementById('qrCanvas');
            canvas.innerHTML = "";
            new QRCode(canvas, {
                text: mat,
                width: 280,
                height: 280,
                colorDark : "#064e3b",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
            document.getElementById('btnSave').style.display = 'flex';
            canvas.style.borderColor = "#10b981";
            alert("Alumno registrado correctamente en el sistema.");
        } else {
            alert("Atención: " + data.message);
        }
    });
});

document.getElementById('btnSave').addEventListener('click', function() {
    const img = document.querySelector('#qrCanvas img');
    if(img) {
        const a = document.createElement('a');
        a.href = img.src;
        a.download = `QR_${document.getElementById('mat').value}_CECyTE.png`;
        a.click();
    }
});
</script>
</body>
</html>