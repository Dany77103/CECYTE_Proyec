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
    <title>Captura de Calificaciones | CECyTE SC</title>
    
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

        .filter-card, .grading-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .card-header-custom {
            background: var(--primary-color);
            color: white;
            padding: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-label-small {
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
        }

        /* Estilos de la tabla de captura */
        .grade-input {
            width: 70px;
            text-align: center;
            font-weight: 700;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            padding: 5px;
        }

        .grade-input:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .btn-save-all {
            background: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-save-all:hover {
            background: #043a2c;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class='bx bxs-spreadsheet fs-3 me-2 text-success'></i>
            <span>CECyTE SC <span class="fw-light text-muted">| Académico</span></span>
        </a>
        <a href="main.php" class="btn-back">
            <i class='bx bx-home-alt me-1'></i> Inicio
        </a>
    </div>
</nav>

<div class="container pb-5">
    <div class="filter-card animate__animated animate__fadeIn">
        <div class="p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label-small">Ciclo Escolar</label>
                    <select class="form-select">
                        <option>2025-2026</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-small">Grupo / Semestre</label>
                    <select class="form-select">
                        <option selected disabled>Seleccionar grupo...</option>
                        <option>4° A - Programación</option>
                        <option>2° B - Logística</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-small">Materia</label>
                    <select class="form-select">
                        <option selected disabled>Seleccionar materia...</option>
                        <option>Desarrollo de Aplicaciones Web</option>
                        <option>Base de Datos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-small">Parcial</label>
                    <select class="form-select">
                        <option>1er Parcial</option>
                        <option>2do Parcial</option>
                        <option>3er Parcial</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success w-100" title="Cargar lista">
                        <i class='bx bx-search-alt-2'></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grading-card animate__animated animate__fadeInUp">
        <div class="card-header-custom">
            <i class='bx bx-edit'></i> Captura de Notas - 4° A Programación
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">No.</th>
                            <th>Matrícula</th>
                            <th>Nombre del Alumno</th>
                            <th class="text-center">Asistencia %</th>
                            <th class="text-center">Calificación</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4 text-muted small">1</td>
                            <td class="fw-bold">2024001</td>
                            <td>Carlos Eduardo Martínez</td>
                            <td class="text-center">
                                <input type="number" class="grade-input" value="100" min="0" max="100">
                            </td>
                            <td class="text-center">
                                <input type="number" class="grade-input border-success" step="0.1" placeholder="0.0">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" placeholder="Opcional...">
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted small">2</td>
                            <td class="fw-bold">2024002</td>
                            <td>Ana Sofía Jiménez</td>
                            <td class="text-center">
                                <input type="number" class="grade-input" value="95" min="0" max="100">
                            </td>
                            <td class="text-center">
                                <input type="number" class="grade-input border-success" step="0.1" placeholder="0.0">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" placeholder="Opcional...">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-4 border-top d-flex justify-content-between align-items-center bg-light">
            <div class="text-muted small">
                <i class='bx bx-info-circle me-1'></i> Los cambios no se guardarán hasta hacer clic en el botón.
            </div>
            <button class="btn-save-all" onclick="alert('Captura enviada al sistema (Simulación)')">
                <i class='bx bx-cloud-upload me-2'></i> Guardar Calificaciones
            </button>
        </div>
    </div>
</div>

</body>
</html>