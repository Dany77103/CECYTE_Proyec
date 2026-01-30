<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

// Configurar zona horaria para México
date_default_timezone_set('America/Mexico_City'); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Detectar acción vía POST o GET
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    if ($action == 'registrar') {
        $codigo = trim($_POST['codigo_qr'] ?? '');
        $tipo_solicitado = $_POST['tipo_registro'] ?? ''; 
        $hoy = date('Y-m-d');

        // 1. Validar existencia del alumno
        $stmt = $con->prepare("SELECT id, matricula, nombre FROM qralumnos WHERE matricula = ? LIMIT 1");
        $stmt->execute([$codigo]);
        $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$alumno) {
            echo json_encode(["success" => false, "message" => "❌ Matrícula [$codigo] no encontrada"]);
            exit;
        }

        $id_al = $alumno['id'];
        $nom_al = $alumno['nombre'];
        $mat_al = $alumno['matricula'];

        if ($tipo_solicitado === 'entrada') {
            // Verificar si tiene entrada abierta
            $stmt = $con->prepare("SELECT id FROM registros_asistencias WHERE matricula = ? AND fecha = ? AND hora_salida IS NULL LIMIT 1");
            $stmt->execute([$mat_al, $hoy]);
            
            if ($stmt->fetch()) {
                echo json_encode(["success" => false, "message" => "⚠️ $nom_al ya está dentro."]);
            } else {
                $ins = $con->prepare("INSERT INTO registros_asistencias (alumno_id, matricula, nombre, fecha, hora_entrada) VALUES (?, ?, ?, ?, CURRENT_TIME())");
                $ins->execute([$id_al, $mat_al, $nom_al, $hoy]);
                echo json_encode(["success" => true, "message" => "🚀 ENTRADA: $nom_al"]);
            }
        } 
        else if ($tipo_solicitado === 'salida') {
            // Buscar última entrada sin salida
            $stmt = $con->prepare("SELECT id FROM registros_asistencias WHERE matricula = ? AND fecha = ? AND hora_salida IS NULL ORDER BY id DESC LIMIT 1");
            $stmt->execute([$mat_al, $hoy]);
            $asist = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($asist) {
                $upd = $con->prepare("UPDATE registros_asistencias SET hora_salida = CURRENT_TIME() WHERE id = ?");
                $upd->execute([$asist['id']]);
                echo json_encode(["success" => true, "message" => "✅ SALIDA: $nom_al"]);
            } else {
                echo json_encode(["success" => false, "message" => "❌ $nom_al no tiene entrada activa."]);
            }
        }
    } 
    
    elseif ($action == 'get_stats') {
        $fecha = date('Y-m-d');
        
        // --- RESUMEN DEL DÍA ---
        // Contamos cuántas entradas se han creado hoy
        $entradas = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha'")->fetchColumn();
        
        // Contamos cuántas salidas se han registrado hoy
        $salidas = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha' AND hora_salida IS NOT NULL")->fetchColumn();
        
        // TOTAL REGISTROS = Suma de todas las entradas y salidas
        $total_movimientos = $entradas + $salidas;

        // EN PLANTEL = Alumnos que entraron pero no han salido
        $en_plantel = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha' AND hora_salida IS NULL")->fetchColumn();
        
        echo json_encode([
            "total_hoy" => (int)$total_movimientos, 
            "pendientes_salida" => (int)$en_plantel
        ]);
    }

    elseif ($action == 'get_asistencias') {
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        // Orden descendente por ID para ver lo más nuevo al principio
        $stmt = $con->prepare("SELECT matricula, nombre, hora_entrada, hora_salida FROM registros_asistencias WHERE fecha = ? ORDER BY id DESC");
        $stmt->execute([$fecha]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error de servidor: " . $e->getMessage()]);
}