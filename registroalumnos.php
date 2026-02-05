<?php
session_start();
// Opcional: Descomenta si quieres proteger la ruta
/*
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}
*/
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

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

        /* Navbar Estilo Institucional */
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

        /* Tarjeta de Formulario y Tabla */
        .form-card, .table-card {
            background: white;
            border-radius: 24px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .form-header, .table-header {
            background: var(--primary-color);
            padding: 25px;
            color: white;
            text-align: center;
        }

        .form-body, .table-body { padding: 40px; }

        .form-label { 
            font-size: 0.75rem; 
            font-weight: 700; 
            color: #64748b; 
            text-transform: uppercase; 
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            border: 2px solid #f1f5f9;
            background: #f8fafc;
            transition: 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .input-icon { position: relative; }
        .input-icon i {
            position: absolute;
            left: 15px; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8; font-size: 1.2rem;
        }
        .input-icon .form-control { padding-left: 45px; }

        .btn-submit {
            background: var(--primary-color);
            color: white; border: none; padding: 15px;
            border-radius: 12px; font-weight: 700;
            width: 100%; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }

        .btn-submit:hover {
            background: #043a2c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(6, 78, 59, 0.2);
        }

        .section-title {
            font-size: 0.9rem; font-weight: 700;
            color: var(--accent-color); margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        .section-title::after {
            content: ""; height: 2px;
            background: #f1f5f9; flex-grow: 1;
        }

        /* Estilos de Tabla */
        .table thead {
            background: #f8fafc;
        }
        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #64748b;
            border: none;
            padding: 15px;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
        }
        .tr-hover:hover {
            background-color: #f0fdf4 !important;
        }
        .badge-especialidad {
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent-color);
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-custom mb-5">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class='bx bxs-user-badge fs-3 me-2 text-success'></i>
            <span>CECyTE SC <span class="fw-light text-muted">| Control Escolar</span></span>
        </a>
        <a href="main.php" class="btn-back">
            <i class='bx bx-home-alt me-1'></i> Inicio
        </a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-card animate__animated animate__fadeInDown">
                <div class="form-header">
                    <i class='bx bx-user-plus fs-1 mb-2'></i>
                    <h3 class="fw-bold mb-0">Alta de Nuevo Alumno</h3>
                    <p class="opacity-75 mb-0 small">Ingresa la información para el registro en el sistema</p>
                </div>
                
                <div class="form-body">
                    <form id="formRegistroAlumno">
                        <div class="section-title">DATOS PERSONALES</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Nombre(s)</label>
                                <div class="input-icon"><i class='bx bx-user'></i><input type="text" class="form-control" placeholder="Ej: Juan"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellidos</label>
                                <div class="input-icon"><i class='bx bx-group'></i><input type="text" class="form-control" placeholder="Ej: Pérez López"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CURP</label>
                                <div class="input-icon"><i class='bx bx-fingerprint'></i><input type="text" class="form-control" placeholder="18 caracteres"></div>
                            </div>
                        </div>

                        <div class="section-title">DATOS ACADÉMICOS</div>
                        <div class="row g-3 mb-5">
                            <div class="col-md-3">
                                <label class="form-label">Matrícula</label>
                                <div class="input-icon"><i class='bx bx-hash'></i><input type="text" class="form-control" placeholder="Ej: 21008000"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Carrera</label>
                                <select class="form-select">
                                    <option selected disabled>Seleccionar...</option>
                                    <option>Programación</option>
                                    <option>Mantenimiento Industrial</option>
                                    <option>Logística</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Semestre</label>
                                <select class="form-select">
                                    <option>1°</option><option>2°</option><option>3°</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn-submit mt-4" onclick="alert('Guardado deshabilitado')">
                                    <i class='bx bx-save'></i> Registrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-card animate__animated animate__fadeInUp">
                <div class="table-header">
                    <h5 class="fw-bold mb-0"><i class='bx bx-list-ul me-2'></i>Lista de Alumnos Recientes</h5>
                </div>
                <div class="table-body">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nombre Completo</th>
                                    <th>Carrera</th>
                                    <th>Semestre</th>
                                    <th>Estatus</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="tr-hover">
                                    <td class="fw-bold text-success">2024001</td>
                                    <td>Carlos Eduardo Martínez</td>
                                    <td><span class="badge-especialidad">Programación</span></td>
                                    <td>4° Semestre</td>
                                    <td><span class="text-success small fw-bold">Activo</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light text-primary"><i class='bx bx-edit-alt'></i></button>
                                        <button class="btn btn-sm btn-light text-danger"><i class='bx bx-trash'></i></button>
                                    </td>
                                </tr>
                                <tr class="tr-hover">
                                    <td class="fw-bold text-success">2024002</td>
                                    <td>Ana Sofía Jiménez</td>
                                    <td><span class="badge-especialidad">Logística</span></td>
                                    <td>2° Semestre</td>
                                    <td><span class="text-success small fw-bold">Activo</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light text-primary"><i class='bx bx-edit-alt'></i></button>
                                        <button class="btn btn-sm btn-light text-danger"><i class='bx bx-trash'></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted italic">Próximamente: Los datos se cargarán desde la base de datos.</small>
                    </div>
                </div>
            </div>
            </div>
    </div>
</div>

</body>
</html>