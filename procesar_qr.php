<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    if ($action == 'registrar') {
        $codigo = trim($_POST['codigo_qr'] ?? '');

        // 1. Buscamos en qralumnos
        $stmt = $con->prepare("SELECT id, matricula, nombre FROM qralumnos WHERE matricula = ? LIMIT 1");
        $stmt->execute([$codigo]);
        $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$alumno) {
            echo json_encode(["success" => false, "message" => "Matrícula [$codigo] no encontrada"]);
            exit;
        }

        $id_al = $alumno['id'];
        $nom_al = $alumno['nombre'];
        $mat_al = $alumno['matricula'];
        $hoy = date('Y-m-d');

        // 2. Buscamos en la NUEVA TABLA registros_asistencias
        $stmt = $con->prepare("SELECT id FROM registros_asistencias WHERE matricula = ? AND fecha = ? AND hora_salida IS NULL LIMIT 1");
        $stmt->execute([$mat_al, $hoy]);
        $asist = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($asist) {
            // REGISTRAR SALIDA
            $upd = $con->prepare("UPDATE registros_asistencias SET hora_salida = CURRENT_TIME() WHERE id = ?");
            $upd->execute([$asist['id']]);
            echo json_encode(["success" => true, "message" => "✅ SALIDA: $nom_al"]);
        } else {
            // REGISTRAR ENTRADA
            $ins = $con->prepare("INSERT INTO registros_asistencias (alumno_id, matricula, nombre, fecha, hora_entrada) VALUES (?, ?, ?, ?, CURRENT_TIME())");
            $ins->execute([$id_al, $mat_al, $nom_al, $hoy]);
            echo json_encode(["success" => true, "message" => "🚀 ENTRADA: $nom_al"]);
        }
    } 
    
    elseif ($action == 'get_stats') {
        $fecha = date('Y-m-d');
        $total = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha'")->fetchColumn();
        $pend = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha' AND hora_salida IS NULL")->fetchColumn();
        echo json_encode(["total_hoy" => (int)$total, "pendientes_salida" => (int)$pend]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}