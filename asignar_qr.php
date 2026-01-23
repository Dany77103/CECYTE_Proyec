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
    <title>Asignar QR - CECYTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .card-qr { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-top: 40px; }
        .preview-box { width: 220px; height: 220px; border: 2px dashed #ccc; margin: 20px auto; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 10px; }
        #qrCanvas img { max-width: 100%; height: auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-qr">
                <h4 class="text-success mb-4"><i class='bx bx-qr-scan'></i> Nuevo Registro (QR Alumnos)</h4>
                <div class="mb-3">
                    <label class="form-label fw-bold">Matrícula</label>
                    <input type="text" id="mat" class="form-control" placeholder="Matrícula del alumno">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre</label>
                    <input type="text" id="nom" class="form-control" placeholder="Nombre completo">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Grupo</label>
                    <input type="text" id="gru" class="form-control" placeholder="Ej: 4-A">
                </div>
                <div class="preview-box" id="qrCanvas">Esperando...</div>
                <div class="d-grid gap-2">
                    <button type="button" id="btnAccion" class="btn btn-success">Guardar y Generar QR</button>
                    <button type="button" id="btnSave" class="btn btn-outline-primary" style="display:none;">Descargar PNG</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnAccion').addEventListener('click', function() {
    const fd = new FormData();
    fd.append('matricula', document.getElementById('mat').value.trim());
    fd.append('nombre', document.getElementById('nom').value.trim());
    fd.append('grupo', document.getElementById('gru').value.trim());

    if(!document.getElementById('mat').value || !document.getElementById('nom').value) {
        return alert("Llena los campos obligatorios.");
    }

    fetch('registrar_qr.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') {
            const canvas = document.getElementById('qrCanvas');
            canvas.innerHTML = "";
            
            // El QR contiene el ID que usaremos para la tabla de asistencias
            new QRCode(canvas, {
                text: data.alumno_id.toString(),
                width: 180, height: 180
            });

            document.getElementById('btnSave').style.display = 'block';
            alert("Registrado con éxito en qralumnos. ID: " + data.alumno_id);
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert("Fallo de conexión.");
    });
});

document.getElementById('btnSave').addEventListener('click', function() {
    const img = document.querySelector('#qrCanvas img');
    const a = document.createElement('a');
    a.href = img.src;
    a.download = `QR_Alumno_${document.getElementById('mat').value}.png`;
    a.click();
});
</script>
</body>
</html>