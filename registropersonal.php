<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos Humanos | CECyTE SC</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root { 
            --primary-color: #064e3b; 
            --accent-color: #10b981; 
            --bg-body: #f1f5f9;
        }

        body { 
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif; 
            color: #1e293b;
        }

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

        .form-card {
            background: white;
            border-radius: 24px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .form-header {
            background: var(--primary-color);
            padding: 30px;
            color: white;
            text-align: center;
        }

        .form-body { padding: 40px; }

        .form-label { 
            font-size: 0.75rem; 
            font-weight: 700; 
            color: #64748b; 
            text-transform: uppercase; 
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            transition: 0.3s;
        }

        .section-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--accent-color);
            margin-bottom: 20px;
            margin-top: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 1px;
        }

        .section-title::after {
            content: "";
            height: 2px;
            background: #f1f5f9;
            flex-grow: 1;
        }

        .btn-submit {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        #seccionMaestro {
            display: none; 
            background: #f0fdf4;
            padding: 25px;
            border-radius: 15px;
            border: 1px dashed var(--accent-color);
        }

        .btn-add-row {
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 5px 15px;
            font-size: 0.8rem;
            transition: 0.3s;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-custom mb-5">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class='bx bxs-business fs-3 me-2 text-success'></i>
            <span>CECyTE SC <span class="fw-light text-muted">| Recursos Humanos</span></span>
        </a>
        <a href="main.php" class="btn-back">
            <i class='bx bx-home-alt me-1'></i> Inicio
        </a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="form-card animate__animated animate__fadeIn">
                <div class="form-header">
                    <i class='bx bx-user-plus fs-1 mb-2'></i>
                    <h3 class="fw-bold mb-0">Registro de Colaborador</h3>
                </div>
                
                <div class="form-body">
                    <form id="formRegistroPersonal">
                        
                        <div class="section-title">DATOS DE IDENTIDAD</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Num. Empleado</label>
                                <input type="text" name="numEmpleado" class="form-control" placeholder="0000" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" name="apellidoPaterno" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" name="apellidoMaterno" class="form-control">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">RFC</label>
                                <input type="text" name="rfc" class="form-control" required style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CURP</label>
                                <input type="text" name="curp" class="form-control" required style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="fechaNacimiento" class="form-control" required>
                            </div>
                        </div>

                        <div class="section-title">CONTACTO Y DIRECCIÓN</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Dirección Particular</label>
                                <input type="text" name="direccion" class="form-control" placeholder="Calle, Número, Colonia">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Teléfono Celular</label>
                                <input type="tel" name="numCelular" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tel. Emergencia</label>
                                <input type="tel" name="telefonoEmergencia" class="form-control">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Email Institucional</label>
                                <input type="email" name="mailInstitucional" class="form-control" placeholder="ejemplo@cecyte.edu.mx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Personal</label>
                                <input type="email" name="mailPersonal" class="form-control">
                            </div>
                        </div>

                        <div class="section-title">DATOS LABORALES</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Rol / Función en el Plantel</label>
                                <select name="id_rol" id="id_rol" class="form-select" required>
                                    <option value="" selected disabled>Seleccione un rol...</option>
                                    <option value="1">Maestro(a)</option>
                                    <option value="2">Administrativo(a)</option>
                                    <option value="3">Conserje / Intendencia</option>
                                    <option value="4">Directivo(a)</option>
                                    <option value="5">Prefecto(a)</option>
                                    <option value="6">Seguridad</option>
                                    <option value="7">Otro personal de apoyo</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estatus Laboral</label>
                                <select name="estatus" class="form-select">
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo / Licencia</option>
                                </select>
                            </div>
                        </div>

                        <div id="seccionMaestro" class="animate__animated animate__fadeIn mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="section-title text-success mb-0" style="flex-grow: 1;">CARGA ACADÉMICA</div>
                                <button type="button" class="btn-add-row" id="btnAgregarFila">
                                    <i class='bx bx-plus-circle'></i> Agregar Materia
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle bg-white" id="tablaMaterias">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Asignatura / Materia</th>
                                            <th>Grupo</th>
                                            <th>Turno</th>
                                            <th width="50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="materia[]" class="form-control form-control-sm" placeholder="Ej: Matemáticas I"></td>
                                            <td>
                                                <select name="grupo[]" class="form-select form-select-sm">
                                                    <option value="">Grupo...</option>
                                                    <option value="1A">1A</option><option value="1B">1B</option>
                                                    <option value="2A">2A</option><option value="2B">2B</option>
                                                    <option value="3A">3A</option><option value="4A">4A</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="turno[]" class="form-select form-select-sm">
                                                    <option value="Matutino">Matutino</option>
                                                    <option value="Vespertino">Vespertino</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm border-0 btn-eliminar-fila"><i class='bx bx-trash'></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <button type="submit" class="btn-submit">
                                    <i class='bx bx-save'></i> Guardar Registro Completo
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
const selectRol = document.getElementById('id_rol');
const seccionMaestro = document.getElementById('seccionMaestro');
const btnAgregar = document.getElementById('btnAgregarFila');
const tablaMaterias = document.getElementById('tablaMaterias').getElementsByTagName('tbody')[0];

selectRol.addEventListener('change', function() {
    seccionMaestro.style.display = (this.value === "1") ? 'block' : 'none';
});

btnAgregar.addEventListener('click', function() {
    const nuevaFila = tablaMaterias.insertRow();
    nuevaFila.innerHTML = `
        <td><input type="text" name="materia[]" class="form-control form-control-sm" placeholder="Ej: Matemáticas I"></td>
        <td>
            <select name="grupo[]" class="form-select form-select-sm">
                <option value="">Grupo...</option>
                <option value="1A">1A</option><option value="1B">1B</option>
                <option value="2A">2A</option><option value="2B">2B</option>
                <option value="3A">3A</option><option value="4A">4A</option>
            </select>
        </td>
        <td>
            <select name="turno[]" class="form-select form-select-sm">
                <option value="Matutino">Matutino</option>
                <option value="Vespertino">Vespertino</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm border-0 btn-eliminar-fila"><i class='bx bx-trash'></i></button>
        </td>
    `;
});

tablaMaterias.addEventListener('click', function(e) {
    if (e.target.closest('.btn-eliminar-fila')) {
        const filas = tablaMaterias.getElementsByTagName('tr');
        if (filas.length > 1) {
            e.target.closest('tr').remove();
        }
    }
});

document.getElementById('formRegistroPersonal').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('guardar_maestro.php', { 
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if(data.trim() === "success"){
            Swal.fire('¡Éxito!', 'Colaborador guardado correctamente.', 'success');
            this.reset();
            seccionMaestro.style.display = 'none';
        } else {
            Swal.fire('Error', data, 'error');
        }
    });
});
</script>

</body>
</html>