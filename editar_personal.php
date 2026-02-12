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

// 3. OBTENER DATOS DEL COLABORADOR
$persona = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Consulta directa a la tabla personal_institucional
    $stmt = $con->prepare("SELECT * FROM personal_institucional WHERE id_personal = ?");
    $stmt->execute([$id]);
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$persona) {
    echo "<script>alert('Error: Personal no encontrado.'); window.history.back();</script>";
    exit;
}

// Listado de roles para el select
$roles = [
    2 => "Administrativo(a)",
    3 => "Conserje / Intendencia",
    4 => "Directivo(a)",
    5 => "Prefecto(a)",
    6 => "Seguridad",
    7 => "Psicólogo(a) / Apoyo"
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Personal - CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body { background-color: #f0f9ff; font-family: 'Inter', sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .form-label { font-weight: 600; color: #0c4a6e; }
        .btn-save { background-color: #0c4a6e; color: white; border-radius: 10px; padding: 10px 25px; transition: 0.3s; }
        .btn-save:hover { background-color: #075985; transform: translateY(-2px); }
        .form-control:focus, .form-select:focus { border-color: #0c4a6e; box-shadow: 0 0 0 0.25rem rgba(12, 74, 110, 0.1); }
        .text-custom { color: #0c4a6e; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-custom"><i class='bx bx-user-pin me-2'></i>Editar Expediente de Personal</h4>
                    <button type="button" onclick="window.history.back();" class="btn btn-outline-secondary btn-sm">
                        <i class='bx bx-arrow-back'></i> Volver
                    </button>
                </div>
                
                <div class="card-body p-4">
                    <form action="procesar_edicion_personal.php" method="POST">
                        
                        <input type="hidden" name="id_personal" value="<?php echo $persona['id_personal']; ?>">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Num. Empleado</label>
                                <input type="text" name="num_empleado" class="form-control" value="<?php echo htmlspecialchars($persona['num_empleado']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RFC</label>
                                <input type="text" name="rfc" class="form-control" value="<?php echo htmlspecialchars($persona['rfc']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">CURP</label>
                                <input type="text" name="curp" class="form-control" value="<?php echo htmlspecialchars($persona['curp']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Rol / Función</label>
                                <select name="id_rol" class="form-select" required>
                                    <option value="">Seleccione un rol...</option>
                                    <?php foreach($roles as $val => $texto): ?>
                                        <option value="<?php echo $val; ?>" <?php echo ($persona['id_rol'] == $val) ? 'selected' : ''; ?>>
                                            <?php echo $texto; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($persona['nombre']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido Paterno</label>
                                <input type="text" name="apellido_paterno" class="form-control" value="<?php echo htmlspecialchars($persona['apellido_paterno']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Apellido Materno</label>
                                <input type="text" name="apellido_materno" class="form-control" value="<?php echo htmlspecialchars($persona['apellido_materno']); ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo $persona['fecha_nacimiento']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Celular</label>
                                <input type="text" name="num_celular" class="form-control" value="<?php echo htmlspecialchars($persona['num_celular']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Estatus</label>
                                <select name="estatus" class="form-select">
                                    <option value="Activo" <?php echo $persona['estatus'] == 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="Inactivo" <?php echo $persona['estatus'] == 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                    <option value="Licencia" <?php echo $persona['estatus'] == 'Licencia' ? 'selected' : ''; ?>>Licencia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tel. Emergencia</label>
                                <input type="text" name="telefono_emergencia" class="form-control" value="<?php echo htmlspecialchars($persona['telefono_emergencia']); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo Institucional</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                    <input type="email" name="mail_institucional" class="form-control" value="<?php echo htmlspecialchars($persona['mail_institucional']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo Personal</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-envelope-open'></i></span>
                                    <input type="email" name="mail_personal" class="form-control" value="<?php echo htmlspecialchars($persona['mail_personal']); ?>">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Dirección Particular</label>
                                <textarea name="direccion" class="form-control" rows="2"><?php echo htmlspecialchars($persona['direccion']); ?></textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light border">Descartar Cambios</button>
                            <button type="submit" class="btn btn-save">
                                <i class='bx bx-save'></i> Actualizar Datos de Colaborador
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