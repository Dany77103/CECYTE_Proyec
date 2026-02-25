<?php
// registrar_salida.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

ob_start(); 
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "cecyte_sc";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) throw new Exception("Error de conexión");

    // Recibimos la matrícula (asegúrate que tu JS envíe este nombre)
    $matricula = $_POST['matricula'] ?? $_POST['codigo_qr'] ?? '';
    $hoy = date('Y-m-d');

    if (empty($matricula)) throw new Exception("No se recibió la matrícula para procesar la salida.");

    // 1. Obtener datos del alumno y su tutor
    $stmt = $conn->prepare("SELECT nombre, correo_tutor FROM qralumnos WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $res = $stmt->get_result();
    $alumno = $res->fetch_assoc();

    if (!$alumno) throw new Exception("El alumno con matrícula $matricula no existe.");

    $nombre = $alumno['nombre'];
    $correo_tutor = $alumno['correo_tutor'];

    // 2. ACTUALIZAR SALIDA: Buscamos el último registro de HOY que no tenga salida
    $upd = $conn->prepare("UPDATE registros_asistencias 
                           SET hora_salida = CURRENT_TIME() 
                           WHERE matricula = ? 
                           AND fecha = ? 
                           AND (hora_salida IS NULL OR hora_salida = '00:00:00' OR hora_salida = '') 
                           ORDER BY id DESC LIMIT 1");
    $upd->bind_param("ss", $matricula, $hoy);
    $upd->execute();

    // Verificamos si realmente se encontró y actualizó una fila
    if ($conn->affected_rows == 0) {
        throw new Exception("No se encontró una entrada abierta para hoy. Debe registrar entrada antes de marcar la salida.");
    }

    // 3. ENVÍO DE CORREO DE SALIDA
    if (!empty($correo_tutor)) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'admprueva@gmail.com';
        $mail->Password   = 'ofkthykygjvkwcjh'; // Tu contraseña de 16 letras
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('admprueva@gmail.com', 'CECyTE SC - Salidas');
        $mail->addAddress($correo_tutor);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = "AVISO DE SALIDA: $nombre";
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px;'>
                <h2 style='color: #b91c1c;'>Notificación de Salida</h2>
                <p>Estimado tutor, le informamos que el alumno <b>$nombre</b> ha registrado su salida del plantel.</p>
                <p><b>Fecha:</b> $hoy<br>
                <b>Hora de Salida:</b> " . date('H:i:s') . "</p>
                <hr>
                <p style='font-size: 12px; color: #777;'>Sistema de Control Escolar CECyTE.</p>
            </div>";

        $mail->send();
    }

    ob_clean();
    echo json_encode(['status' => 'success', 'message' => "Salida exitosa. Correo enviado a: $correo_tutor"]);

} catch (Exception $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}