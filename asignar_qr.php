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

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        body {
            background-color: #f4f6f9;
        }

        .qr-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .qr-preview {
            width: 200px;
            height: 200px;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
            border-radius: 10px;
            background: #fafafa;
            color: #999;
            font-size: 0.9rem;
            text-align: center;
        }

        .section-title {
            color: #2c3e50;
            font-weight: 600;
        }
    </style>
</head>

<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="qr-container">
                <h3 class="section-title mb-4">
                    <i class='bx bx-qr'></i> Asignación de Código QR
                </h3>

                <!-- FORMULARIO -->
                <form>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Matrícula del Alumno</label>
                            <input type="text" class="form-control" placeholder="Ej. 20231234">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Grupo</label>
                            <input type="text" class="form-control" placeholder="Ej. 4-A">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre Completo del Alumno</label>
                        <input type="text" class="form-control" placeholder="Nombre del alumno">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Carrera</label>
                            <input type="text" class="form-control" placeholder="Ej. Programación">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Semestre</label>
                            <select class="form-select">
                                <option selected disabled>Seleccionar semestre</option>
                                <option>1°</option>
                                <option>2°</option>
                                <option>3°</option>
                                <option>4°</option>
                                <option>5°</option>
                                <option>6°</option>
                            </select>
                        </div>
                    </div>

                    <!-- PREVISUALIZACIÓN QR -->
                    <div class="mb-4 text-center">
                        <label class="form-label d-block mb-2">Vista previa del Código QR</label>
                        <div class="qr-preview">
                            Aquí se mostrará<br>el código QR
                        </div>
                    </div>

                    <!-- BOTONES -->
                    <div class="d-flex justify-content-between">
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class='bx bx-trash'></i> Limpiar
                        </button>

                        <button type="button" class="btn btn-success">
                            <i class='bx bx-qr'></i> Generar Código QR
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
