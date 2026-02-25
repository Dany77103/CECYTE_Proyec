<?php
// procesar_asistencia.php - VERSIÓN DE DIAGNÓSTICO
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

    // 1. CAPTURA Y LIMPIEZA DE DATOS
    $matricula = isset($_POST['codigo_qr']) ? trim($_POST['codigo_qr']) : (isset($_POST['matricula']) ? trim($_POST['matricula']) : '');
    // Convertimos a minúsculas y quitamos espacios para evitar errores de escritura
    $accion = isset($_POST['tipo_registro']) ? strtolower(trim($_POST['tipo_registro'])) : ''; 
    $hoy = date('Y-m-d');

    if (empty($matricula)) throw new Exception("Error: No se recibió ninguna matrícula.");

    // 2. BUSCAR ALUMNO
    $stmt = $conn->prepare("SELECT nombre, correo_tutor FROM qralumnos WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $res = $stmt->get_result();
    $alumno = $res->fetch_assoc();

    if (!$alumno) throw new Exception("Alumno con matrícula $matricula no encontrado.");

    $nombre = $alumno['nombre'];
    $correo_tutor = $alumno['correo_tutor'];

    // 3. LOGICA DE REGISTRO
    if ($accion === 'entrada') {
        $ins = $conn->prepare("INSERT INTO registros_asistencias (matricula, nombre, fecha, hora_entrada) VALUES (?, ?, ?, CURRENT_TIME())");
        $ins->bind_param("sss", $matricula, $nombre, $hoy);
        $ins->execute();
        $asunto = "Aviso de Entrada: $nombre";
        $mensaje_body = "ha registrado su ENTRADA.";
    } 
    elseif ($accion === 'salida') {
        // Buscamos el último registro de hoy que tenga la salida vacía
        $upd = $conn->prepare("UPDATE registros_asistencias SET hora_salida = CURRENT_TIME() WHERE matricula = ? AND fecha = ? AND (hora_salida IS NULL OR hora_salida = '00:00:00' OR hora_salida = '') ORDER BY id DESC LIMIT 1");
        $upd->bind_param("ss", $matricula, $hoy);
        $upd->execute();

        if ($conn->affected_rows == 0) {
            // Si esto falla, es porque no hay una fila de "Entrada" previa para hoy.
            throw new Exception("DEBUG: No se encontró una fila de ENTRADA previa para hoy. Verifique que primero registró entrada.");
        }
        $asunto = "Aviso de Salida: $nombre";
        $mensaje_body = "ha registrado su SALIDA.";
    } 
    else {
        throw new Exception("DEBUG: La acción recibida fue '$accion', pero el sistema solo acepta 'entrada' o 'salida'. Revise su código de escáner.");
    }

    // 4. ENVÍO DE CORREO
    $correoEnviado = false;
    if (!empty($correo_tutor)) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'admprueva@gmail.com';
        $mail->Password   = 'ofkthykygjvkwcjh'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('admprueva@gmail.com', 'Sistema Asistencia');
        $mail->addAddress($correo_tutor);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $asunto;
        $mail->Body    = "El alumno <b>$nombre</b> $mensaje_body <br> Hora: " . date('H:i:s');
        $mail->send();
        $correoEnviado = true;
    }

    ob_clean();
    echo json_encode(['status' => 'success', 'message' => "Registro de $accion ok", 'mail' => $correoEnviado]);

} catch (Exception $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}