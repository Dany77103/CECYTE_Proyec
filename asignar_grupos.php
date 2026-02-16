<?php
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

$mensaje = "";

// --- LÓGICA DE GUARDADO Y ACTUALIZACIÓN DE CONTEO ---
if (isset($_POST['btn_asignar'])) {
    $id_grupo_sel = $_POST['id_grupo'];
    $alumnos_seleccionados = $_POST['alumnos_ids'] ?? [];

    if (!empty($id_grupo_sel)) {
        try {
            $con->beginTransaction(); // Iniciamos transacción para seguridad

            // 1. Asignamos los alumnos seleccionados al grupo
            if (!empty($alumnos_seleccionados)) {
                $sql_update = "UPDATE alumnos SET id_grupo = :id_grupo WHERE matriculaAlumno = :matricula";
                $stmt_upd = $con->prepare($sql_update);

                foreach ($alumnos_seleccionados as $matricula) {
                    $stmt_upd->execute([':id_grupo' => $id_grupo_sel, ':matricula' => $matricula]);
                }
            }

            // 2. LÓGICA DE ACTUALIZACIÓN DE CANTIDAD (Sincronización)
            // Contamos cuántos alumnos tiene el grupo realmente en la tabla alumnos
            $sql_count = "SELECT COUNT(*) FROM alumnos WHERE id_grupo = :id_grupo";
            $stmt_count = $con->prepare($sql_count);
            $stmt_count->execute([':id_grupo' => $id_grupo_sel]);
            $total_alumnos = $stmt_count->fetchColumn();

            // Actualizamos la columna cantidad_alumnos en la tabla grupos
            $sql_upd_grupo = "UPDATE grupos SET cantidad_alumnos = :total WHERE id_grupo = :id_grupo";
            $stmt_upd_grupo = $con->prepare($sql_upd_grupo);
            $stmt_upd_grupo->execute([
                ':total' => $total_alumnos,
                ':id_grupo' => $id_grupo_sel
            ]);

            $con->commit(); // Confirmamos los cambios
            $mensaje = "<div class='alert alert-success shadow-sm'><b>¡Éxito!</b> Alumnos asignados y contador de grupo actualizado a: $total_alumnos.</div>";
        } catch (PDOException $e) {
            $con->rollBack(); // Si algo falla, deshacemos los cambios
            $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

// --- CONSULTAS PARA LA VISTA ---
$grupos = $con->query("SELECT id_grupo, grupo, cantidad_alumnos FROM grupos ORDER BY grupo ASC")->fetchAll(PDO::FETCH_ASSOC);

$sql_alumnos = "SELECT a.matriculaAlumno, a.nombre, a.apellidoPaterno, a.apellidoMaterno, a.id_grupo, g.grupo as nombre_grupo_actual 
                FROM alumnos a 
                LEFT JOIN grupos g ON a.id_grupo = g.id_grupo 
                ORDER BY a.apellidoPaterno ASC";
$alumnos = $con->query($sql_alumnos)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignación y Conteo - CECYTE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        :root { --primary: #064e3b; --accent: #10b981; }
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .table-container { max-height: 500px; overflow-y: auto; }
        .ya-en-grupo { background-color: #f0fdf4 !important; }
        .badge-count { background: var(--primary); color: white; border-radius: 20px; padding: 2px 10px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="mb-4 d-flex justify-content-between">
                <a href="registro.php" class="btn btn-light border"><i class='bx bx-left-arrow-alt'></i> Panel Registro</a>
                <h3 class="fw-bold">Gestión de Grupos y Alumnos</h3>
            </div>

            <?php echo $mensaje; ?>

            <form method="POST">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted fw-bold small mb-3">Paso 1: Seleccionar Grupo</h6>
                                <select name="id_grupo" class="form-select form-select-lg mb-4" required>
                                    <option value="">-- Seleccionar Grupo --</option>
                                    <?php foreach($grupos as $g): ?>
                                        <option value="<?php echo $g['id_grupo']; ?>">
                                            <?php echo htmlspecialchars($g['grupo']); ?> 
                                            (Actual: <?php echo $g['cantidad_alumnos']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="submit" name="btn_asignar" class="btn btn-success w-100 py-3 fw-bold mb-2 shadow">
                                    <i class='bx bx-sync'></i> GUARDAR Y ACTUALIZAR CANTIDAD
                                </button>
                                
                                <button type="button" onclick="exportarExcel()" class="btn btn-outline-dark w-100">
                                    <i class='bx bxs-file-export'></i> Descargar Lista Excel
                                </button>
                                <p class="text-muted small mt-3"><i class='bx bx-info-circle'></i> Al guardar, el sistema recalculará automáticamente la cantidad de alumnos en el grupo seleccionado.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3">
                                <input type="text" id="busc" class="form-control" placeholder="Buscar alumno...">
                            </div>
                            <div class="card-body p-0">
                                <div class="table-container">
                                    <table class="table table-hover align-middle mb-0" id="tabla">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="40"><input type="checkbox" id="all" class="form-check-input"></th>
                                                <th>Matrícula y Nombre</th>
                                                <th>Grupo Actual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($alumnos as $a): ?>
                                            <tr class="fila <?php echo ($a['id_grupo']) ? 'ya-en-grupo' : ''; ?>">
                                                <td>
                                                    <input type="checkbox" name="alumnos_ids[]" value="<?php echo $a['matriculaAlumno']; ?>" class="form-check-input ck">
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?php echo $a['apellidoPaterno']." ".$a['nombre']; ?></div>
                                                    <div class="text-muted small"><?php echo $a['matriculaAlumno']; ?></div>
                                                </td>
                                                <td>
                                                    <?php if($a['nombre_grupo_actual']): ?>
                                                        <span class="badge bg-success"><?php echo $a['nombre_grupo_actual']; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-dark border">Sin asignar</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Buscador
    document.getElementById('busc').onkeyup = function() {
        let val = this.value.toLowerCase();
        document.querySelectorAll('.fila').forEach(f => {
            f.style.display = f.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    };

    // Checkbox All
    document.getElementById('all').onclick = function() {
        document.querySelectorAll('.ck').forEach(c => c.checked = this.checked);
    };

    // Exportación rápida
    function exportarExcel() {
        const wb = XLSX.utils.table_to_book(document.getElementById('tabla'));
        XLSX.writeFile(wb, "Lista_Alumnos_Cecyte.xlsx");
    }
</script>

</body>
</html>