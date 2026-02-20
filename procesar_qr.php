<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// 1. IMPORTAR LIBRERÍAS
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Iniciamos el búfer para atrapar cualquier error basura
ob_start(); 

error_reporting(E_ALL);
ini_set('display_errors', 0); // Evita que errores de PHP se impriman y rompan el JSON
header('Content-Type: application/json; charset=utf-8');

date_default_timezone_set('America/Mexico_City'); 

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
        $tipo_solicitado = $_POST['tipo_registro'] ?? ''; 
        $salon = $_POST['salon'] ?? 'No especificado'; 
        $hoy = date('Y-m-d');

        if (empty($codigo)) {
            throw new Exception("❌ Código QR vacío");
        }

        // --- 1. BUSCAR IDENTIDAD ---
        $stmt = $con->prepare("SELECT id, matricula, nombre, correo_tutor, 'ALUMNO' as tipo_u FROM qralumnos WHERE matricula = ? LIMIT 1");
        $stmt->execute([$codigo]);
        $persona = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$persona) {
            $stmt = $con->prepare("SELECT id, numero_empleado as matricula, nombre, NULL as correo_tutor, 'MAESTRO' as tipo_u FROM qrpersonal WHERE numero_empleado = ? LIMIT 1");
            $stmt->execute([$codigo]);
            $persona = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$persona) {
            throw new Exception("❌ Registro [$codigo] no encontrado");
        }

        $id_ref = $persona['id'];
        $nom_u = $persona['nombre'];
        $cod_u = $persona['matricula'];
        $rol_u = $persona['tipo_u'];
        $correo_tutor = $persona['correo_tutor'];

        // --- 2. LÓGICA DE REGISTRO ---
        if ($tipo_solicitado === 'entrada') {
            $stmt = $con->prepare("SELECT id FROM registros_asistencias WHERE matricula = ? AND fecha = ? AND hora_salida IS NULL LIMIT 1");
            $stmt->execute([$cod_u, $hoy]);
            
            if ($stmt->fetch()) {
                throw new Exception("⚠️ $nom_u ya está dentro.");
            } else {
                $ins = $con->prepare("INSERT INTO registros_asistencias (alumno_id, matricula, salon, nombre, fecha, hora_entrada) VALUES (?, ?, ?, ?, ?, CURRENT_TIME())");
                $ins->execute([$id_ref, $cod_u, $salon, $nom_u, $hoy]);
                
                $email_status = "No aplica/Sin correo";

                // --- 3. ENVÍO DE CORREO SILENCIOSO ---
                if ($rol_u === 'ALUMNO' && !empty($correo_tutor)) {
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'TU_CORREO@gmail.com'; // <--- CAMBIAR
                        $mail->Password   = 'TU_CLAVE_APP';      // <--- CAMBIAR
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;
                        $mail->Timeout    = 5; // Tiempo máximo de espera 5 seg.

                        $mail->setFrom('TU_CORREO@gmail.com', 'CECyTE SC');
                        $mail->addAddress($correo_tutor);
                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = "Aviso: Entrada de $nom_u";
                        $mail->Body = "El alumno registró su entrada en <b>$salon</b> a las " . date('H:i:s');

                        $mail->send();
                        $email_status = "Enviado";
                    } catch (Exception $e_mail) {
                        $email_status = "Error Mail: " . $mail->ErrorInfo;
                    }
                }

                // LIMPIEZA FINAL Y RESPUESTA
                if (ob_get_length()) ob_end_clean(); 
                echo json_encode([
                    "success" => true, 
                    "message" => "🚀 ENTRADA: $nom_u",
                    "email_log" => $email_status
                ]);
                exit;
            }
        } 
        // Lógica de salida abreviada para evitar errores
        else if ($tipo_solicitado === 'salida') {
            $stmt = $con->prepare("SELECT id FROM registros_asistencias WHERE matricula = ? AND fecha = ? AND hora_salida IS NULL ORDER BY id DESC LIMIT 1");
            $stmt->execute([$cod_u, $hoy]);
            $asist = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($asist) {
                $upd = $con->prepare("UPDATE registros_asistencias SET hora_salida = CURRENT_TIME() WHERE id = ?");
                $upd->execute([$asist['id']]);
                if (ob_get_length()) ob_end_clean();
                echo json_encode(["success" => true, "message" => "✅ SALIDA: $nom_u"]);
            } else {
                throw new Exception("❌ $nom_u no tiene entrada activa.");
            }
            exit;
        }
    }
    
    // Acción de estadísticas
    if ($action == 'get_stats') {
        $fecha = date('Y-m-d');
        $e = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha'")->fetchColumn();
        $s = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha' AND hora_salida IS NOT NULL")->fetchColumn();
        $p = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha' AND hora_salida IS NULL")->fetchColumn();
        
        if (ob_get_length()) ob_end_clean();
        echo json_encode(["total_hoy" => (int)$e, "pendientes_salida" => (int)$p]);
        exit;
    }

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>