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

// ... (todas tus otras funciones) ...

// === NUEVA VERSIÓN DE generarTodosQR() ===
function generarTodosQR() {
    global $con;

    $sql = "SELECT a.id, a.nombre FROM alumnos a 
            LEFT JOIN alumnos_qr q ON a.id = q.alumno_id 
            WHERE q.id IS NULL";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $generados = 0;
    $errores = 0;

    echo "<h2>Generando QR para alumnos...</h2>";

    foreach ($alumnos as $alumno) {
        try {
            $codigo_qr = generarCodigoQR($alumno['id']);

            $sql = "INSERT INTO alumnos_qr (alumno_id, codigo_qr) VALUES (?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->execute([$alumno['id'], $codigo_qr]);

            $generados++;
            echo "✅ QR generado para: {$alumno['nombre']} (ID: {$alumno['id']})<br>";
        } catch (Exception $e) {
            $errores++;
            echo "❌ Error generando QR para: {$alumno['nombre']}<br>";
        }
    }

    echo "<hr>";
    echo "<b>Total generados:</b> $generados<br>";
    echo "<b>Errores:</b> $errores<br>";
}

// Manejo de acciones
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'registrar':
        registrarAsistencia();
        break;

    case 'generar_qr':
        generarQRIndividual();
        break;

    case 'generar_todos_qr':
        generarTodosQR(); // Aquí se usa la versión mejorada
        break;

    case 'ver_qr':
        verQRExistente();
        break;

    case 'get_alumnos':
        obtenerAlumnos();
        break;

    case 'get_asistencias':
        obtenerAsistencias();
        break;

    case 'get_stats':
        obtenerEstadisticas();
        break;

    case 'get_grupos':
        obtenerGrupos();
        break;

    case 'export_excel':
        exportarExcel();
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>
