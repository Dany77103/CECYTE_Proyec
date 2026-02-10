<?php
// 1. SEGURIDAD: Control de acceso
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// 2. CONEXIÓN A LA BASE DE DATOS
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// 3. OBTENER DATOS DEL MAESTRO
$maestro = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM maestros WHERE id_maestro = ?");
    $stmt->execute([$id]);
    $maestro = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$maestro) {
    echo "<script>alert('Error: Maestro no encontrado.'); window.history.back();</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Maestro - CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body { background-color: #f0fdf4; font-family: 'Inter', sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .form-label { font-weight: 600; color: #064e3b; }
        .btn-save { background-color: #064e3b; color: white; border-radius: 10px; padding: 10px 25px; transition: 0.3s; }
        .btn-save:hover { background-color: #065f46; transform: translateY(-2px); }
        .form-control:focus { border-color: #064e3b; box-shadow: 0 0 0 0.25rem rgba(6, 78, 59, 0.1); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-success"><i class='bx bx-edit-alt me-2'></i>Editar Expediente de Maestro</h4>
                    <button type="button" onclick="window.history.back();" class="btn btn-outline-secondary btn-sm">
                        <i class='bx bx-arrow-back'></i> Volver
                    </button>
                </div>
                
                <div class="card-body p-4">
                    <form action="procesar_edicion_maestro.php" method="POST">
                        
                        <input type="hidden" name="id_maestro" value="<?php echo $maestro['id_maestro']; ?>">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Num. Empleado</label>
                                <input type="text" name="numEmpleado" class="form-control" value="<?php echo htmlspecialchars($maestro['numEmpleado']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RFC</label>
                                <input type="text" name="rfc" class="form-control" value="<?php echo htmlspecialchars($maestro['rfc']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">CURP</label>
                                <input type="text" name="curp" class="form-control" value="<?php echo htmlspecialchars($maestro['curp']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Nacimiento</label>
                                <input type="date" name="fechaNacimiento" class="form-control" value="<?php echo $maestro['fechaNacimiento']; ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($maestro['nombre']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" name="apellidoPaterno" class="form-control" value="<?php echo htmlspecialchars($maestro['apellidoPaterno']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" name="apellidoMaterno" class="form-control" value="<?php echo htmlspecialchars($maestro['apellidoMaterno']); ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">ID Género</label>
                                <input type="number" name="id_genero" class="form-control" value="<?php echo $maestro['id_genero']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID Nacionalidad</label>
                                <input type="number" name="id_nacionalidad" class="form-control" value="<?php echo $maestro['id_nacionalidad']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID Estado Nac.</label>
                                <input type="number" name="id_estadoNacimiento" class="form-control" value="<?php echo $maestro['id_estadoNacimiento']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Celular</label>
                                <input type="text" name="numCelular" class="form-control" value="<?php echo htmlspecialchars($maestro['numCelular']); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo Institucional</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                    <input type="email" name="mailInstitucional" class="form-control" value="<?php echo htmlspecialchars($maestro['mailInstitucional']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo Personal</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-envelope-open'></i></span>
                                    <input type="email" name="mailPersonal" class="form-control" value="<?php echo htmlspecialchars($maestro['mailPersonal']); ?>">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <textarea name="direccion" class="form-control" rows="2"><?php echo htmlspecialchars($maestro['direccion']); ?></textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light border">Descartar Cambios</button>
                            <button type="submit" class="btn btn-save">
                                <i class='bx bx-save'></i> Guardar y Actualizar Maestro
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