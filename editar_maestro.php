<?php
// Conexión a la base de datos
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

session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: index.php'); exit; }

$maestro = null;
$mensaje = "";

// 1. Lógica de Búsqueda
if (isset($_POST['buscar_maestro'])) {
    $busqueda = $_POST['busqueda'];
    $stmt = $con->prepare("SELECT * FROM maestros WHERE num_empleado = ? OR nombre_completo LIKE ?");
    $stmt->execute([$busqueda, "%$busqueda%"]);
    $maestro = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$maestro) {
        $mensaje = "<div class='alert alert-warning'>No se encontró ningún maestro con esos datos.</div>";
    }
}

// 2. Lógica de Actualización
if (isset($_POST['actualizar_maestro'])) {
    try {
        $sql = "UPDATE maestros SET 
                nombre_completo = :nom, 
                correo = :correo, 
                telefono = :tel, 
                especialidad = :esp, 
                turno = :turno 
                WHERE id_maestro = :id";
        
        $stmt = $con->prepare($sql);
        $stmt->execute([
            ':nom'    => $_POST['nombre'],
            ':correo' => $_POST['correo'],
            ':tel'    => $_POST['telefono'],
            ':esp'    => $_POST['especialidad'],
            ':turno'  => $_POST['turno'],
            ':id'     => $_POST['id_maestro']
        ]);
        $mensaje = "<div class='alert alert-success'>Datos actualizados correctamente.</div>";
        $maestro = null; // Limpiar tras actualizar
    } catch (PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Maestro - CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root { --primary: #064e3b; --accent: #10b981; --bg: #f0fdf4; }
        body { background-color: var(--bg); font-family: 'Inter', sans-serif; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .btn-save { background-color: var(--primary); color: white; border-radius: 10px; padding: 10px 25px; }
        .btn-save:hover { background-color: #043a2c; color: white; }
        .header-edit { background: white; border-bottom: 3px solid var(--accent); padding: 20px; }
    </style>
</head>
<body>

<div class="header-edit mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="reportes.php" class="btn btn-outline-secondary btn-sm"><i class='bx bx-left-arrow-alt'></i> Volver</a>
        <h4 class="mb-0 fw-bold" style="color: var(--primary);">Edición de Personal Docente</h4>
        <img src="img/logo.png" height="40" alt="">
    </div>
</div>

<div class="container">
    <?php echo $mensaje; ?>

    <div class="card card-custom p-4 mb-4">
        <form method="POST" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label fw-bold">Buscar Maestro</label>
                <input type="text" name="busqueda" class="form-control" placeholder="Ingrese número de empleado o nombre completo..." required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="buscar_maestro" class="btn btn-save w-100">
                    <i class='bx bx-search-alt'></i> Buscar Maestro
                </button>
            </div>
        </form>
    </div>

    <?php if ($maestro): ?>
    <div class="card card-custom p-4">
        <h5 class="mb-4 border-bottom pb-2">Editando a: <strong><?php echo $maestro['nombre_completo']; ?></strong></h5>
        
        <form method="POST" class="row g-3">
            <input type="hidden" name="id_maestro" value="<?php echo $maestro['id_maestro']; ?>">
            
            <div class="col-md-6">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo $maestro['nombre_completo']; ?>" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Correo Institucional</label>
                <input type="email" name="correo" class="form-control" value="<?php echo $maestro['correo']; ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="<?php echo $maestro['telefono']; ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Especialidad / Materia</label>
                <input type="text" name="especialidad" class="form-control" value="<?php echo $maestro['especialidad']; ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Turno</label>
                <select name="turno" class="form-select">
                    <option value="Matutino" <?php echo ($maestro['turno'] == 'Matutino') ? 'selected' : ''; ?>>Matutino</option>
                    <option value="Vespertino" <?php echo ($maestro['turno'] == 'Vespertino') ? 'selected' : ''; ?>>Vespertino</option>
                </select>
            </div>

            <div class="col-12 mt-4 text-end">
                <a href="editar_maestro.php" class="btn btn-light me-2">Cancelar</a>
                <button type="submit" name="actualizar_maestro" class="btn btn-save">
                    <i class='bx bx-save'></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>