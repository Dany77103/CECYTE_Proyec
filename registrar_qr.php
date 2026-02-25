<?php
// asignar_qr.php / registrar_qr.php
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
    if ($conn->connect_error) throw new Exception("Error de conexión a la base de datos");

    $matricula    = $_POST['matricula'] ?? '';
    $nombre       = $_POST['nombre'] ?? '';
    $grupo        = $_POST['grupo'] ?? '';
    $correo_tutor = $_POST['correo_tutor'] ?? '';

    if (empty($matricula) || empty($nombre)) {
        throw new Exception("La matrícula y el nombre son obligatorios.");
    }

    // --- 1. VERIFICAR SI YA EXISTE (CORREGIDO) ---
    $check = $conn->prepare("SELECT matricula FROM qralumnos WHERE matricula = ?");
    $check->bind_param("s", $matricula);
    $check->execute();
    $check->store_result(); // Esto permite usar num_rows correctamente

    if ($check->num_rows > 0) {
        $check->close();
        // En lugar de un Fatal Error, mandamos un mensaje limpio
        throw new Exception("La matrícula $matricula ya está registrada en el sistema.");
    }
    $check->close();

    // --- 2. INSERTAR SI NO ES DUPLICADO ---
    $stmt = $conn->prepare("INSERT INTO qralumnos (matricula, nombre, grupo, correo_tutor) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $matricula, $nombre, $grupo, $correo_tutor);
    
    if ($stmt->execute()) {
        // --- 3. ENVÍO DE CORREO DE BIENVENIDA ---
        if (!empty($correo_tutor)) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'admprueva@gmail.com';
                $mail->Password   = 'ofkthykygjvkwcjh'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('admprueva@gmail.com', 'Sistema CECyTE SC');
                $mail->addAddress($correo_tutor);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Registro de Tutoría Exitoso';
                $mail->Body    = "El alumno <b>$nombre</b> ha sido registrado correctamente.";
                $mail->send();
            } catch (Exception $e_mail) {
                // Si el correo falla, no detenemos el éxito del registro
            }
        }

        ob_clean();
        echo json_encode(['status' => 'success', 'message' => 'Alumno registrado con éxito']);
    } else {
        throw new Exception("Error al insertar el registro en la base de datos.");
    }

} catch (Exception $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;