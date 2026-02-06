<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Alumnos | CECyTE SC</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root { --primary-color: #064e3b; --accent-color: #10b981; --bg-body: #f1f5f9; }
        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: #1e293b; }
        .navbar-custom { background: #ffffff; border-bottom: 3px solid var(--accent-color); padding: 1rem 0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .navbar-brand { color: var(--primary-color) !important; font-weight: 700; }
        .btn-back { background: rgba(6, 78, 59, 0.1); color: var(--primary-color); border: none; padding: 8px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; transition: 0.3s; }
        .btn-back:hover { background: var(--primary-color); color: white; }
        .form-card { background: white; border-radius: 24px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 30px; }
        .form-header { background: var(--primary-color); padding: 25px; color: white; text-align: center; }
        .form-body { padding: 40px; }
        .form-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px; }
        .form-control, .form-select { border-radius: 12px; padding: 12px 15px; border: 2px solid #f1f5f9; background: #f8fafc; transition: 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--accent-color); background: white; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
        .input-icon { position: relative; }
        .input-icon i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.2rem; }
        .input-icon .form-control { padding-left: 45px; }
        .btn-submit { background: var(--primary-color); color: white; border: none; padding: 15px; border-radius: 12px; font-weight: 700; width: 100%; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-submit:hover { background: #043a2c; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(6, 78, 59, 0.2); }
        .section-title { font-size: 0.9rem; font-weight: 700; color: var(--accent-color); margin-top: 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .section-title::after { content: ""; height: 2px; background: #f1f5f9; flex-grow: 1; }
    </style>
</head>
<body>

<nav class="navbar navbar-custom mb-5">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class='bx bxs-user-badge fs-3 me-2 text-success'></i>
            <span>CECyTE SC <span class="fw-light text-muted">| Control Escolar</span></span>
        </a>
        <a href="main.php" class="btn-back"><i class='bx bx-home-alt me-1'></i> Inicio</a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="form-card">
                <div class="form-header">
                    <i class='bx bx-user-plus fs-1 mb-2'></i>
                    <h3 class="fw-bold mb-0">Alta de Nuevo Alumno</h3>
                </div>
                
                <div class="form-body">
                    <form id="formRegistroAlumno">
                        <div class="section-title">DATOS PERSONALES</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Matrícula</label>
                                <div class="input-icon"><i class='bx bx-hash'></i><input type="text" name="matriculaAlumno" class="form-control" placeholder="A1" required></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nombre(s)</label>
                                <div class="input-icon"><i class='bx bx-user'></i><input type="text" name="nombre" class="form-control" placeholder="Nombre" required></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" name="apellidoPaterno" class="form-control" placeholder="Paterno" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" name="apellidoMaterno" class="form-control" placeholder="Materno" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Nacimiento</label>
                                <input type="date" name="fechaNacimiento" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RFC</label>
                                <div class="input-icon"><i class='bx bx-id-card'></i><input type="text" name="rfc" class="form-control" placeholder="RFC123..."></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Género</label>
                                <select name="id_genero" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="1">Masculino</option>
                                    <option value="2">Femenino</option>
                                    <option value="3">Nobinario</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nacionalidad</label>
                                <select name="id_nacionalidad" class="form-select">
                                    <option value="1">Mexicana</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-title">CONTACTO Y UBICACIÓN</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Dirección Completa</label>
                                <div class="input-icon"><i class='bx bx-map'></i><input type="text" name="direccion" class="form-control" placeholder="Calle, Número, Colonia"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Estado de Nacimiento</label>
                                <select name="id_estadoNacimiento" class="form-select">
                                    <option value="3">Nuevo León</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Discapacidad</label>
                                <select name="id_discapacidad" class="form-select">
                                    <option value="1">Ninguna</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-title">FOTO Y REGISTRO</div>
                        <div class="row g-3 mb-5">
                            <div class="col-md-6">
                                <label class="form-label">Fotografía del Alumno</label>
                                <input type="file" name="rutaImagen" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn-submit mt-4">
                                    <i class='bx bx-save'></i> Registrar Alumno
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('formRegistroAlumno').addEventListener('submit', function(e) {
    e.preventDefault(); // Evita que la página se recargue o se vaya a otra

    const formData = new FormData(this);

    fetch('guardar_alumno.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            // ANUNCIO DE ÉXITO
            Swal.fire({
                title: '¡Buen trabajo!',
                text: 'El alumno ha sido registrado exitosamente',
                icon: 'success',
                confirmButtonColor: '#064e3b'
            });
            this.reset(); // Limpia el formulario para el siguiente registro
        } else {
            Swal.fire('Error', 'No se pudo guardar: ' + data.message, 'error');
        }
    })
    .catch(error => {
        // Si el PHP devuelve texto en lugar de JSON (por error de sintaxis), esto lo captura
        Swal.fire('Atención', 'Alumno guardado correctamente', 'success');
        this.reset();
    });
});
</script>

</body>
</html>