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

    // --- ACCIÓN: REGISTRAR ---
    if ($action == 'registrar') {
        $codigo = trim($_POST['codigo_qr'] ?? '');
        $tipo_solicitado = $_POST['tipo_registro'] ?? ''; 
        $salon = $_POST['salon'] ?? 'No especificado'; 
        $hoy = date('Y-m-d');

        if (empty($codigo)) {
            throw new Exception("❌ Código QR vacío");
        }

        // Buscar identidad (Alumnos o Personal)
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

        // --- LÓGICA DE ENTRADA ---
        if ($tipo_solicitado === 'entrada') {
            $stmt = $con->prepare("SELECT id FROM registros_asistencias WHERE matricula = ? AND fecha = ? AND (hora_salida IS NULL OR hora_salida = '00:00:00') LIMIT 1");
            $stmt->execute([$cod_u, $hoy]);
            
            if ($stmt->fetch()) {
                throw new Exception("⚠️ $nom_u ya está dentro.");
            } else {
                $ins = $con->prepare("INSERT INTO registros_asistencias (alumno_id, matricula, salon, nombre, fecha, hora_entrada) VALUES (?, ?, ?, ?, ?, CURRENT_TIME())");
                $ins->execute([$id_ref, $cod_u, $salon, $nom_u, $hoy]);
                
                $email_status = "No aplica/Sin correo";

                if ($rol_u === 'ALUMNO' && !empty($correo_tutor)) {
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'admprueva@gmail.com'; 
                        $mail->Password   = 'ofkthykygjvkwcjh'; 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;
                        $mail->Timeout    = 5; 

                        $mail->setFrom('admprueva@gmail.com', 'CECyTE SC - Control Escolar');
                        $mail->addAddress($correo_tutor);
                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = "Aviso de Entrada: $nom_u";
                        $mail->Body    = "
                            <div style='font-family: sans-serif; border: 1px solid #ddd; padding: 20px;'>
                                <h2 style='color: #064e3b;'>Notificación de Asistencia</h2>
                                <p>Le informamos que el alumno <b>$nom_u</b> con matrícula <b>$cod_u</b> 
                                ha registrado su entrada al plantel.</p>
                                <p><b>Salón:</b> $salon<br>
                                <b>Hora:</b> " . date('H:i:s') . "</p>
                                <hr>
                                <p style='font-size: 12px; color: #777;'>Este es un mensaje automático del sistema CECyTE SC.</p>
                            </div>";

                        $mail->send();
                        $email_status = "Enviado";
                    } catch (Exception $e_mail) {
                        $email_status = "Error Mail: " . $mail->ErrorInfo;
                    }
                }

                if (ob_get_length()) ob_end_clean(); 
                echo json_encode(["success" => true, "message" => "🚀 ENTRADA: $nom_u", "email" => $email_status]);
                exit;
            }
        } 
        // --- LÓGICA DE SALIDA (ACTUALIZADA CON CORREO) ---
        else if ($tipo_solicitado === 'salida') {
            $stmt = $con->prepare("SELECT id FROM registros_asistencias WHERE matricula = ? AND fecha = ? AND (hora_salida IS NULL OR hora_salida = '00:00:00') ORDER BY id DESC LIMIT 1");
            $stmt->execute([$cod_u, $hoy]);
            $asist = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($asist) {
                // Actualizar hora de salida en la BD
                $upd = $con->prepare("UPDATE registros_asistencias SET hora_salida = CURRENT_TIME() WHERE id = ?");
                $upd->execute([$asist['id']]);
                
                $email_status = "No aplica/Sin correo";

                // Enviar correo de salida si es alumno
                if ($rol_u === 'ALUMNO' && !empty($correo_tutor)) {
                    try {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'admprueva@gmail.com'; 
                        $mail->Password   = 'ofkthykygjvkwcjh'; 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;
                        $mail->Timeout    = 5; 

                        $mail->setFrom('admprueva@gmail.com', 'CECyTE SC - Control Escolar');
                        $mail->addAddress($correo_tutor);
                        $mail->isHTML(true);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = "Aviso de Salida: $nom_u";
                        $mail->Body    = "
                            <div style='font-family: sans-serif; border: 1px solid #ddd; padding: 20px;'>
                                <h2 style='color: #be123c;'>Notificación de Asistencia</h2>
                                <p>Le informamos que el alumno <b>$nom_u</b> con matrícula <b>$cod_u</b> 
                                ha registrado su <b>SALIDA</b> del plantel.</p>
                                <p><b>Hora:</b> " . date('H:i:s') . "</p>
                                <hr>
                                <p style='font-size: 12px; color: #777;'>Este es un mensaje automático del sistema CECyTE SC.</p>
                            </div>";

                        $mail->send();
                        $email_status = "Enviado";
                    } catch (Exception $e_mail) {
                        $email_status = "Error Mail: " . $mail->ErrorInfo;
                    }
                }

                if (ob_get_length()) ob_end_clean();
                echo json_encode([
                    "success" => true, 
                    "message" => "✅ SALIDA: $nom_u",
                    "email" => $email_status
                ]);
            } else {
                throw new Exception("❌ $nom_u no tiene entrada activa.");
            }
            exit;
        }
    }
    
    // --- ACCIÓN: ESTADÍSTICAS ---
    if ($action == 'get_stats') {
        $fecha = date('Y-m-d');
        $e = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha'")->fetchColumn();
        $p = $con->query("SELECT COUNT(*) FROM registros_asistencias WHERE fecha = '$fecha' AND (hora_salida IS NULL OR hora_salida = '00:00:00')")->fetchColumn();
        
        if (ob_get_length()) ob_end_clean();
        echo json_encode(["total_hoy" => (int)$e, "pendientes_salida" => (int)$p]);
        exit;
    }

    // --- ACCIÓN: OBTENER ASISTENCIAS ---
    if ($action == 'get_asistencias') {
        $fecha = date('Y-m-d');
        $stmt = $con->prepare("SELECT matricula, nombre, salon, hora_entrada, hora_salida 
                               FROM registros_asistencias 
                               WHERE fecha = ? 
                               ORDER BY id DESC LIMIT 10");
        $stmt->execute([$fecha]);
        $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (ob_get_length()) ob_end_clean();
        echo json_encode($asistencias);
        exit;
    }

} catch (Exception $e) {
    if (ob_get_length()) ob_end_clean();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>