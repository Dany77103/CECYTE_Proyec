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
    <title>Asignar Código QR - CECYTE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .card-qr { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-top: 40px; }
        .verde-cecyte { color: #28a745; font-weight: bold; }
        .preview-box { width: 200px; height: 200px; border: 2px dashed #ccc; margin: 20px auto; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 10px; }
        #qrCanvas img { max-width: 100%; height: auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-qr">
                <h4 class="verde-cecyte mb-4"><i class='bx bx-plus-circle'></i> Asignar Nuevo QR</h4>
                
                <form id="formQR">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Matrícula</label>
                        <input type="text" id="mat" class="form-control" required placeholder="Ej. 2024001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Alumno</label>
                        <input type="text" id="nom" class="form-control" required placeholder="Nombre completo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grupo</label>
                        <input type="text" id="gru" class="form-control" required placeholder="Ej. 2-B">
                    </div>

                    <div class="preview-box" id="qrCanvas">
                        <span class="text-muted small text-center">Aquí aparecerá el QR</span>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" id="btnAccion" class="btn btn-success shadow-sm">Registrar y Generar</button>
                        <button type="button" id="btnSave" class="btn btn-outline-primary" style="display:none;">Descargar PNG</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnAccion').addEventListener('click', function() {
    const matricula = document.getElementById('mat').value.trim();
    const nombre = document.getElementById('nom').value.trim();
    const grupo = document.getElementById('gru').value.trim();

    if(!matricula || !nombre || !grupo) {
        alert("Completa todos los campos.");
        return;
    }

    const fd = new FormData();
    fd.append('matricula', matricula);
    fd.append('nombre', nombre);
    fd.append('grupo', grupo);

    // Intentar registrar
    fetch('registrar_qr.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.text()) // Cambiamos a texto para ver qué responde realmente
    .then(text => {
        try {
            const data = JSON.parse(text);
            if(data.status === 'success') {
                const canvas = document.getElementById('qrCanvas');
                canvas.innerHTML = "";
                new QRCode(canvas, {
                    text: `CECYTE|${matricula}|${nombre}|${grupo}`,
                    width: 180, height: 180
                });
                document.getElementById('btnSave').style.display = 'block';
                alert("Alumno guardado en la base de datos.");
            } else {
                alert("Error: " + data.message);
            }
        } catch(e) {
            console.error("Error del servidor:", text);
            alert("El servidor mandó una respuesta inválida. Revisa la consola.");
        }
    })
    .catch(err => alert("No se pudo conectar con el servidor."));
});

document.getElementById('btnSave').addEventListener('click', function() {
    const img = document.querySelector('#qrCanvas img');
    const a = document.createElement('a');
    a.href = img.src;
    a.download = `QR_${document.getElementById('mat').value}.png`;
    a.click();
});
</script>
</body>
</html>