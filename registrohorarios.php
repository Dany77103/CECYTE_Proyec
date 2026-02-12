<?php
// 1. CONEXIÓN USANDO TU ARCHIVO REAL
require 'conexion.php'; 
session_start();

// --- NUEVO: GESTIÓN DE GRADO Y GRUPO ---
$grado_sel = isset($_POST['grado']) ? $_POST['grado'] : '1';
$grupo_sel = isset($_POST['grupo']) ? $_POST['grupo'] : 'A';
// El ID del grupo ahora se busca o se define según la selección
// Para este ejemplo, usaremos una lógica de concatenación o búsqueda
$id_grupo_actual = $grado_sel . $grupo_sel; 

// 2. OBTENER MATERIAS REALES DE LA BASE DE DATOS
$materias = [];
try {
    $stmt_mat = $con->prepare("SELECT id_materia as id, nombre, aula FROM materias");
    $stmt_mat->execute();
    while($row = $stmt_mat->fetch(PDO::FETCH_ASSOC)) { 
        $colores = ['#dcfce7', '#dbeafe', '#fef9c3', '#ffedd5', '#f3e8ff'];
        $row['color'] = $colores[$row['id'] % 5]; 
        $materias[] = $row; 
    }
} catch (PDOException $e) {
    echo "Error al cargar materias: " . $e->getMessage();
}

// 3. CONFIGURACIÓN DE BLOQUES
$bloques_config = [
    0 => ["label" => "11:45 - 12:45", "inicio" => "11:45:00", "fin" => "12:45:00"],
    1 => ["label" => "12:45 - 13:45", "inicio" => "12:45:00", "fin" => "13:45:00"],
    2 => ["label" => "13:45 - 14:45", "inicio" => "13:45:00", "fin" => "14:45:00"],
    3 => ["label" => "RECESO",       "inicio" => "14:45:00", "fin" => "15:15:00"],
    4 => ["label" => "15:15 - 16:15", "inicio" => "15:15:00", "fin" => "16:15:00"],
    5 => ["label" => "16:15 - 17:15", "inicio" => "16:15:00", "fin" => "17:15:00"],
    6 => ["label" => "17:15 - 17:50", "inicio" => "17:15:00", "fin" => "17:50:00"]
];

$dias_nombres = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"];

// --- CARGAR HORARIO GUARDADO SEGÚN GRUPO SELECCIONADO ---
$horario_guardado = [];
try {
    // Nota: Asegúrate de que tu tabla horarios acepte el formato de id_grupo que estás usando
    $stmt_view = $con->prepare("SELECT id_materia, dia, hora_inicio FROM horarios WHERE id_grupo = ?");
    $stmt_view->execute([$id_grupo_actual]);
    while($h = $stmt_view->fetch(PDO::FETCH_ASSOC)) {
        $horario_guardado[$h['hora_inicio']][$h['dia']] = $h['id_materia'];
    }
} catch (PDOException $e) { /* Manejar error */ }


// 4. LÓGICA DE GUARDADO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_guardar'])) {
    try {
        $con->beginTransaction();
        // Limpiamos solo el horario del grupo específico seleccionado
        $stmt_del = $con->prepare("DELETE FROM horarios WHERE id_grupo = ?");
        $stmt_del->execute([$id_grupo_actual]);

        $stmt_ins = $con->prepare("INSERT INTO horarios (id_materia, id_grupo, dia, hora_inicio, hora_fin) VALUES (?, ?, ?, ?, ?)");

        if(isset($_POST['materia_id'])) {
            foreach ($_POST['materia_id'] as $b_idx => $dias_data) {
                if ($b_idx == 3) continue; // Saltar el receso
                foreach ($dias_data as $d_idx => $id_materia) {
                    if (!empty($id_materia)) {
                        $dia_texto = $dias_nombres[$d_idx];
                        $h_inicio = $bloques_config[$b_idx]['inicio'];
                        $h_fin = $bloques_config[$b_idx]['fin'];
                        $stmt_ins->execute([$id_materia, $id_grupo_actual, $dia_texto, $h_inicio, $h_fin]);
                    }
                }
            }
        }
        $con->commit();
        echo "<script>alert('¡Horario del grupo $id_grupo_actual guardado correctamente!'); window.location.href='registrohorarios.php';</script>";
    } catch (PDOException $e) {
        $con->rollBack();
        echo "Error al guardar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Captura de Horarios | CECyTE SC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root { --primary-color: #064e3b; --accent-color: #10b981; --bg-body: #f1f5f9; }
        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; }
        .glass-card { background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 30px; }
        .slot-selector { border: 2px solid #f1f5f9; background: #f8fafc; font-size: 0.75rem; width: 100%; padding: 8px; border-radius: 10px; cursor: pointer; }
        .table-view td { background: #fff; border-radius: 12px; height: 80px; text-align: center; border: 1px solid #e2e8f0; vertical-align: middle; transition: 0.3s; }
        .view-time { font-weight: 700; color: var(--primary-color); font-size: 0.75rem; background: #f8fafc !important; }
        .btn-capture { background: var(--primary-color); color: white; border: none; padding: 12px 35px; border-radius: 12px; font-weight: 700; transition: 0.3s; width: 100%; }
        .btn-capture:hover { background: var(--accent-color); transform: translateY(-2px); }
        
        /* Estilo para los nuevos selectores */
        .group-select {
            background: #f0fdf4;
            border: 2px solid var(--accent-color);
            border-radius: 12px;
            padding: 10px;
            font-weight: 600;
            color: var(--primary-color);
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success"><i class='bx bxs-calendar-plus'></i> Captura de Horarios</h3>
        <a href="main.php" class="btn btn-outline-dark rounded-pill shadow-sm"><i class='bx bx-arrow-back'></i> Menú Principal</a>
    </div>

    <form method="POST">
        <div class="glass-card mb-4 border-start border-4 border-success">
            <div class="row align-items-center">
                <div class="col-md-auto">
                    <label class="small fw-bold text-muted d-block mb-1">SELECCIONAR GRADO</label>
                    <select name="grado" class="form-select group-select" onchange="this.form.submit()">
                        <?php for($i=1; $i<=6; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $grado_sel == $i ? 'selected' : ''; ?>>
                                <?php echo $i; ?>° Semestre
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-auto">
                    <label class="small fw-bold text-muted d-block mb-1">SELECCIONAR GRUPO</label>
                    <select name="grupo" class="form-select group-select" onchange="this.form.submit()">
                        <?php foreach(['A', 'B', 'C', 'D', 'E'] as $g): ?>
                            <option value="<?php echo $g; ?>" <?php echo $grupo_sel == $g ? 'selected' : ''; ?>>
                                Grupo <?php echo $g; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md text-end pt-3">
                    <span class="badge bg-success p-2 px-3 rounded-pill">
                        Editando Horario: <b class="fs-6"><?php echo $id_grupo_actual; ?></b>
                    </span>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <thead class="text-center text-muted small">
                        <tr>
                            <th>BLOQUE</th>
                            <?php foreach($dias_nombres as $d): ?> <th><?php echo $d; ?></th> <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($bloques_config as $idx => $bloque): ?>
                            <?php if($idx == 3): ?>
                                <tr class="text-center"><td colspan="6" class="py-2 bg-light rounded-pill small text-muted">-- RECESO --</td></tr>
                            <?php else: ?>
                                <tr>
                                    <td class="small fw-bold text-success text-center"><?php echo $bloque['label']; ?></td>
                                    <?php for($d=0; $d<5; $d++): ?>
                                    <td>
                                        <select name="materia_id[<?php echo $idx; ?>][<?php echo $d; ?>]" 
                                                class="slot-selector" 
                                                data-hora="<?php echo $idx; ?>" 
                                                data-dia="<?php echo $d; ?>" 
                                                onchange="updatePreview(this)">
                                            <option value="">Vacío</option>
                                            <?php foreach($materias as $m): ?>
                                                <?php 
                                                  $h_ini = $bloque['inicio'];
                                                  $d_nom = $dias_nombres[$d];
                                                  $isSelected = (isset($horario_guardado[$h_ini][$d_nom]) && $horario_guardado[$h_ini][$d_nom] == $m['id']) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $m['id']; ?>" <?php echo $isSelected; ?>
                                                        data-nombre="<?php echo $m['nombre']; ?>" 
                                                        data-aula="<?php echo $m['aula']; ?>" 
                                                        data-color="<?php echo $m['color']; ?>">
                                                    <?php echo $m['nombre']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="row mt-3">
                <div class="col-md-4 offset-md-8">
                    <button type="submit" name="btn_guardar" class="btn-capture">
                        <i class='bx bx-save'></i> GUARDAR HORARIO <?php echo $id_grupo_actual; ?>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="glass-card">
        <h6 class="fw-bold text-muted mb-4 text-center">VISTA PREVIA DEL HORARIO (GRUPO <?php echo $id_grupo_actual; ?>)</h6>
        <div class="table-responsive">
            <table class="table-view w-100" style="border-collapse: separate; border-spacing: 10px;">
                <tbody>
                    <?php foreach($bloques_config as $idx => $bloque): ?>
                        <?php if($idx == 3): ?>
                            <tr>
                                <td class="view-time">14:45</td>
                                <td colspan="5" style="background:#f1f5f9; border:none; letter-spacing:10px; color:#cbd5e1; font-size:0.7rem;">RECESO</td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td class="view-time" style="width:10%"><?php echo substr($bloque['inicio'],0,5); ?></td>
                                <?php for($d=0; $d<5; $d++): ?>
                                    <td id="preview-<?php echo $idx; ?>-<?php echo $d; ?>" style="width:18%">--</td>
                                <?php endfor; ?>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updatePreview(select) {
        const hora = select.dataset.hora;
        const dia = select.dataset.dia;
        const target = document.getElementById(`preview-${hora}-${dia}`);
        if(!target) return;
        
        const opt = select.options[select.selectedIndex];
        
        if (select.value === "") {
            target.innerHTML = "--";
            target.style.backgroundColor = "#fff";
            target.style.border = "1px solid #e2e8f0";
        } else {
            target.style.backgroundColor = opt.dataset.color;
            target.style.border = `2px solid ${opt.dataset.color}`;
            target.innerHTML = `
                <div style="padding: 5px;">
                    <b style="font-size:0.75rem; color:#064e3b; display:block; line-height:1.1;">${opt.dataset.nombre}</b>
                    <small style="font-size:0.65rem; color:#475569; display:block; margin-top:4px;"><i class='bx bx-map-pin'></i> ${opt.dataset.aula}</small>
                </div>
            `;
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const selectors = document.querySelectorAll('.slot-selector');
        selectors.forEach(select => {
            if(select.value !== "") {
                updatePreview(select);
            }
        });
    });
</script>
</body>
</html>