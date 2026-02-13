<?php
// 1. SEGURIDAD Y CONEXIÓN
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. CONSULTA DE PERSONAL
    $sql = "SELECT id_personal, num_empleado, nombre, apellido_paterno, apellido_materno, rfc, id_rol, estatus, mail_institucional 
            FROM personal_institucional 
            ORDER BY id_rol ASC, apellido_paterno ASC";
    $stmt = $con->query($sql);
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$roles_nombres = [
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
    <title>Reporte de Personal - CECyTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --primary-color: #064e3b;
            --accent-color: #10b981;
            --bg-light: #f0fdf4;
            --danger-color: #dc3545;
        }
        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }
        .main-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .table thead { background-color: var(--primary-color); color: white; }
        .status-badge { font-size: 0.8rem; padding: 5px 12px; border-radius: 50px; }
        
        /* Estilos de botones */
        .btn-edit { color: var(--primary-color); border: 1px solid var(--primary-color); transition: 0.3s; }
        .btn-edit:hover { background: var(--primary-color); color: white; }
        
        .btn-delete { color: var(--danger-color); border: 1px solid var(--danger-color); transition: 0.3s; }
        .btn-delete:hover { background: var(--danger-color); color: white; }
        
        .role-text { font-weight: 600; color: #475569; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold" style="color: var(--primary-color);">Plantilla de Personal Institucional</h2>
            <p class="text-muted mb-0">Gestión de administrativos, apoyo y servicios generales.</p>
        </div>
        <a href="reportes.php" class="btn btn-outline-secondary rounded-pill">
            <i class='bx bx-arrow-back'></i> Regresar a Reportes
        </a>
    </div>

    <div class="card main-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No. Emp</th>
                            <th>Nombre Completo</th>
                            <th>RFC</th>
                            <th>Rol / Función</th>
                            <th>Estatus</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($personal) > 0): ?>
                            <?php foreach ($personal as $p): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?php echo htmlspecialchars($p['num_empleado']); ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><?php echo htmlspecialchars($p['nombre'] . " " . $p['apellido_paterno'] . " " . $p['apellido_materno']); ?></span>
                                        <small class="text-muted"><?php echo htmlspecialchars($p['mail_institucional']); ?></small>
                                    </div>
                                </td>
                                <td><code class="text-dark"><?php echo htmlspecialchars($p['rfc']); ?></code></td>
                                <td>
                                    <span class="role-text">
                                        <i class='bx bx-briefcase-alt-2 me-1'></i>
                                        <?php echo $roles_nombres[$p['id_rol']] ?? 'No asignado'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                        $status_class = ($p['estatus'] == 'Activo') ? 'bg-success' : 'bg-warning text-dark';
                                    ?>
                                    <span class="badge status-badge <?php echo $status_class; ?>">
                                        <?php echo $p['estatus']; ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="editar_personal.php?id=<?php echo $p['id_personal']; ?>" 
                                           class="btn btn-sm btn-edit rounded-pill px-3">
                                            <i class='bx bx-edit-alt'></i> Editar
                                        </a>
                                        
                                        <a href="eliminar_personal.php?id=<?php echo $p['id_personal']; ?>" 
                                           class="btn btn-sm btn-delete rounded-pill px-3"
                                           onclick="return confirmarEliminacion('<?php echo $p['nombre'] . ' ' . $p['apellido_paterno']; ?>')">
                                            <i class='bx bx-trash'></i> Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class='bx bx-info-circle fs-2 d-block mb-2'></i>
                                    No se encontró personal registrado en la base de datos.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Función para confirmar antes de borrar
function confirmarEliminacion(nombre) {
    return confirm("¿Estás seguro de que deseas eliminar a " + nombre + "? Esta acción no se puede deshacer.");
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>