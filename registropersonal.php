<?php
session_start();
// Protección de ruta opcional
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
    <title>Registro de Personal | CECyTE SC</title>
    
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

        /* Estilo de Tarjetas */
        .form-card, .table-card {
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

        .form-body, .table-body { padding: 40px; }

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

        .form-control:focus {
            border-color: var(--accent-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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
        }

        .btn-submit:hover {
            background: #043a2c;
            transform: translateY(-2px);
        }

        .badge-tipo {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .bg-docente { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .bg-admin { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
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
        <div class="col-lg-10">
            
            <div class="form-card animate__animated animate__fadeIn">
                <div class="form-header">
                    <i class='bx bx-id-card fs-1 mb-2'></i>
                    <h3 class="fw-bold mb-0">Registro de Personal</h3>
                    <p class="opacity-75 mb-0 small">Alta de docentes y personal institucional</p>
                </div>
                
                <div class="form-body">
                    <form id="formRegistroPersonal">
                        <div class="section-title">DATOS GENERALES</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Nombre(s)</label>
                                <input type="text" class="form-control" placeholder="Nombre del empleado">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellidos</label>
                                <input type="text" class="form-control" placeholder="Apellidos completos">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">RFC</label>
                                <input type="text" class="form-control" placeholder="RFC con homoclave">
                            </div>
                        </div>

                        <div class="section-title">DETALLES DEL PUESTO</div>
                        <div class="row g-3 mb-5">
                            <div class="col-md-4">
                                <label class="form-label">Tipo de Personal</label>
                                <select class="form-select">
                                    <option selected disabled>Seleccionar tipo...</option>
                                    <option value="Docente">Docente / Maestro</option>
                                    <option value="Administrativo">Personal Administrativo</option>
                                    <option value="Directivo">Directivo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Área / Departamento</label>
                                <input type="text" class="form-control" placeholder="Ej: Ciencias Exactas">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha de Ingreso</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <button type="button" class="btn-submit" onclick="alert('Módulo de personal en construcción')">
                                    <i class='bx bx-user-check'></i> Registrar Colaborador
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-card animate__animated animate__fadeInUp">
                <div class="p-4 border-bottom bg-light">
                    <h5 class="fw-bold mb-0">Personal Registrado</h5>
                </div>
                <div class="table-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>RFC</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Área</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="small fw-bold">HERM850101XX1</td>
                                    <td>Ing. Mario Hernández</td>
                                    <td><span class="badge-tipo bg-docente">Docente</span></td>
                                    <td>Programación</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success"><i class='bx bx-edit'></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="small fw-bold">GAML900512YY2</td>
                                    <td>Lic. Lucía García</td>
                                    <td><span class="badge-tipo bg-admin">Administrativo</span></td>
                                    <td>Control Escolar</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success"><i class='bx bx-edit'></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>