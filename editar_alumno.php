<?php
// 1. SEGURIDAD: Siempre al inicio
session_start();

// Verifica si existe la sesión. Ajusta 'loggedin' si en tu login usas otro nombre
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// 2. CONEXIÓN (Ajusta si usas un archivo externo como conexion.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 3. OBTENER DATOS DEL ALUMNO
$alumno = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM alumnos WHERE id_alumno = ?");
    $stmt->execute([$id]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$alumno) {
    die("Alumno no encontrado o ID no válido.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Alumno - CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body { background-color: #f0fdf4; font-family: 'Inter', sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .form-label { font-weight: 600; color: #064e3b; }
        .btn-save { background-color: #064e3b; color: white; border-radius: 10px; padding: 10px 25px; }
        .btn-save:hover { background-color: #065f46; color: white; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-success"><i class='bx bx-user-circle me-2'></i>Editar Expediente de Alumno</h4>
                    <a href="reportes.php" class="btn btn-outline-secondary btn-sm">Volver</a>
                </div>
                <div class="card-body p-4">
                    <form action="procesar_edicion.php" method="POST">
                        <input type="hidden" name="id_alumno" value="<?php echo $alumno['id_alumno']; ?>">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Matrícula</label>
                                <input type="text" name="matriculaAlumno" class="form-control" value="<?php echo $alumno['matriculaAlumno']; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">RFC</label>
                                <input type="text" name="rfc" class="form-control" value="<?php echo $alumno['rfc']; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha Nacimiento</label>
                                <input type="date" name="fechaNacimiento" class="form-control" value="<?php echo $alumno['fechaNacimiento']; ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo $alumno['nombre']; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" name="apellidoPaterno" class="form-control" value="<?php echo $alumno['apellidoPaterno']; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" name="apellidoMaterno" class="form-control" value="<?php echo $alumno['apellidoMaterno']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo Institucional</label>
                                <input type="email" name="mailInstitucional" class="form-control" value="<?php echo $alumno['mailInstitucional']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo Personal</label>
                                <input type="email" name="mailPersonal" class="form-control" value="<?php echo $alumno['mailPersonal']; ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Celular</label>
                                <input type="text" name="numCelular" class="form-control" value="<?php echo $alumno['numCelular']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono Emergencia</label>
                                <input type="text" name="telefonoEmergencia" class="form-control" value="<?php echo $alumno['telefonoEmergencia']; ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Dirección Completa</label>
                                <textarea name="direccion" class="form-control" rows="2"><?php echo $alumno['direccion']; ?></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">ID Discapacidad</label>
                                <input type="number" name="id_discapacidad" class="form-control" value="<?php echo $alumno['id_discapacidad']; ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Ruta de Imagen / Foto</label>
                                <input type="text" name="rutaImagen" class="form-control" value="<?php echo $alumno['rutaImagen']; ?>" readonly>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <hr>
                            <button type="submit" class="btn btn-save">
                                <i class='bx bx-save me-1'></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>