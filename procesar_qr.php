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
    die(json_encode(['success' => false, 'message' => 'Error de conexión'])); 
}

// Función para generar código QR único
function generarCodigoQR($alumno_id) {
    return 'CECYTE-' . $alumno_id . '-' . uniqid() . '-' . bin2hex(random_bytes(4));
}

// === FUNCIONES DE PROCESAMIENTO QR (NUEVAS / CORREGIDAS) ===

function registrarAsistencia() {
    global $con;
    $codigo = $_POST['codigo_qr'] ?? '';
    $hoy = date('Y-m-d');
    $ahora = date('H:i:s');

    // 1. Buscamos a quién pertenece ese código QR en la tabla alumnos_qr
    $stmt = $con->prepare("SELECT a.id, a.nombre, a.matricula 
                           FROM alumnos a 
                           INNER JOIN alumnos_qr q ON a.id = q.alumno_id 
                           WHERE q.codigo_qr = ?");
    $stmt->execute([$codigo]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        echo json_encode(['success' => false, 'message' => 'Código QR no reconocido']);
        exit;
    }

    // 2. Verificar si ya tiene entrada hoy sin salida
    $stmt = $con->prepare("SELECT id FROM asistencias WHERE alumno_id = ? AND fecha = ? AND hora_salida IS NULL");
    $stmt->execute([$alumno['id'], $hoy]);
    $asistencia = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($asistencia) {
        // Registrar Salida
        $upd = $con->prepare("UPDATE asistencias SET hora_salida = ? WHERE id = ?");
        $upd->execute([$ahora, $asistencia['id']]);
        echo json_encode(['success' => true, 'message' => "Salida: " . $alumno['nombre']]);
    } else {
        // Registrar Entrada
        $ins = $con->prepare("INSERT INTO asistencias (alumno_id, matricula, nombre, fecha, hora_entrada) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$alumno['id'], $alumno['matricula'], $alumno['nombre'], $hoy, $ahora]);
        echo json_encode(['success' => true, 'message' => "Entrada: " . $alumno['nombre']]);
    }
}

function obtenerAsistencias() {
    global $con;
    // Recibimos la fecha del filtro, si no, usamos la de hoy
    $fecha = $_GET['fecha'] ?? date('Y-m-d');
    
    try {
        // Esta consulta trae los datos de la bitácora
        $stmt = $con->prepare("SELECT 
                                a.matricula, 
                                al.nombre, 
                                a.hora_entrada, 
                                a.hora_salida 
                               FROM asistencias a
                               INNER JOIN alumnos al ON a.alumno_id = al.id
                               WHERE a.fecha = ? 
                               ORDER BY a.hora_entrada DESC");
        $stmt->execute([$fecha]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($resultados);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit; // Importante para que no se ejecute nada más abajo
}

function obtenerEstadisticas() {
    global $con;
    $hoy = date('Y-m-d');
    
    $stmt1 = $con->prepare("SELECT COUNT(*) FROM asistencias WHERE fecha = ?");
    $stmt1->execute([$hoy]);
    $total = $stmt1->fetchColumn();

    $stmt2 = $con->prepare("SELECT COUNT(*) FROM asistencias WHERE fecha = ? AND hora_salida IS NULL");
    $stmt2->execute([$hoy]);
    $pendientes = $stmt2->fetchColumn();

    echo json_encode([
        'total_hoy' => $total,
        'pendientes_salida' => $pendientes
    ]);
}

// === TU FUNCIÓN DE GENERACIÓN MASIVA (MANTENIDA) ===
function generarTodosQR() {
    global $con;
    $sql = "SELECT a.id, a.nombre FROM alumnos a 
            LEFT JOIN alumnos_qr q ON a.id = q.alumno_id 
            WHERE q.id IS NULL";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $generados = 0; $errores = 0;
    echo "<h2>Generando QR para alumnos...</h2>";

    foreach ($alumnos as $alumno) {
        try {
            $codigo_qr = generarCodigoQR($alumno['id']);
            $sql = "INSERT INTO alumnos_qr (alumno_id, codigo_qr) VALUES (?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->execute([$alumno['id'], $codigo_qr]);
            $generados++;
            echo "✅ QR generado para: {$alumno['nombre']}<br>";
        } catch (Exception $e) {
            $errores++;
            echo "❌ Error en: {$alumno['nombre']}<br>";
        }
    }
    echo "<hr>Total: $generados | Errores: $errores";
}

// ... (Aquí irían tus otras funciones: generarQRIndividual, verQRExistente, obtenerGrupos, exportarExcel) ...
// Nota: Asegúrate de que esas funciones existan abajo para que el switch no falle.

// Manejo de acciones
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'registrar':
        registrarAsistencia();
        break;
    case 'generar_qr':
        // generarQRIndividual(); // Asegúrate de tener esta función definida
        break;
    case 'generar_todos_qr':
        generarTodosQR();
        break;
    case 'get_asistencias':
        obtenerAsistencias();
        break;
    case 'get_stats':
        obtenerEstadisticas();
        break;
    default:
        // Si la acción no coincide con las de arriba, puedes poner un mensaje o dejar que procese las otras
        // echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
}
?>