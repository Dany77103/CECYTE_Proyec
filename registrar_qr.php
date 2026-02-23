<?php
// registrar_qr.php
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

    if ($conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos");
    }

    $matricula    = $_POST['matricula'] ?? '';
    $nombre       = $_POST['nombre'] ?? '';
    $grupo        = $_POST['grupo'] ?? '';
    $correo_tutor = $_POST['correo_tutor'] ?? '';

    if (empty($matricula) || empty($nombre) || empty($correo_tutor)) {
        throw new Exception("Todos los campos son obligatorios.");
    }

    // --- CORRECCIÓN AQUÍ: Verificación de duplicados corregida ---
    $checkStmt = $conn->prepare("SELECT matricula FROM qralumnos WHERE matricula = ?");
    $checkStmt->bind_param("s", $matricula);
    $checkStmt->execute();
    $checkStmt->store_result(); // Necesario para que num_rows funcione

    if ($checkStmt->num_rows > 0) { // <-- Se cambió num_result por num_rows
        $checkStmt->close();
        throw new Exception("La matrícula $matricula ya está registrada.");
    }
    $checkStmt->close();

    // Inserción limpia
    $stmt = $conn->prepare("INSERT INTO qralumnos (matricula, nombre, grupo, correo_tutor) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $matricula, $nombre, $grupo, $correo_tutor);
    
    if ($stmt->execute()) {
        $nuevo_id = $conn->insert_id; 

        // Bloque de correo
        $mail = new PHPMailer(true);
        $correoEnviado = false;
        $error_mail = "OK";

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'admprueva@gmail.com';
            $mail->Password   = 'ofkthykygjvkwcjh'; // Tu clave de 16 letras
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('admprueva@gmail.com', 'Sistema CECyTE');
            $mail->addAddress($correo_tutor, $nombre); 

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Registro Exitoso';
            $mail->Body    = "Hola, el alumno <b>$nombre</b> ha sido registrado correctamente.";

            $mail->send();
            $correoEnviado = true;
        } catch (Exception $e_mail) {
            $error_mail = $mail->ErrorInfo;
        }

        ob_clean();
        echo json_encode([
            'status' => 'success', 
            'alumno_id' => $nuevo_id,
            'email_sent' => $correoEnviado,
            'debug_mail' => $error_mail
        ]);

    } else {
        throw new Exception("Error al insertar.");
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    ob_clean();
    // Enviamos el mensaje de error en formato JSON para que el navegador lo muestre bonito
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;