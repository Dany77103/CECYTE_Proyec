<?php
// registrar_qr.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Importar archivos de la librería PHPMailer (ajusta las rutas si es necesario)
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

    // Capturamos los datos del POST
    $matricula    = $_POST['matricula'] ?? '';
    $nombre       = $_POST['nombre'] ?? '';
    $grupo        = $_POST['grupo'] ?? '';
    $correo_tutor = $_POST['correo_tutor'] ?? '';

    if (empty($correo_tutor)) {
        throw new Exception("El correo del tutor es obligatorio.");
    }

    // Estructura original de inserción
    $stmt = $conn->prepare("INSERT INTO qralumnos (matricula, nombre, grupo, correo_tutor) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Error en la tabla qralumnos: " . $conn->error);
    }

    $stmt->bind_param("ssss", $matricula, $nombre, $grupo, $correo_tutor);
    
    if ($stmt->execute()) {
        $nuevo_id = $conn->insert_id; 

        // --- INICIO DE BLOQUE DE ENVÍO DE CORREO ---
        $mail = new PHPMailer(true);
        $correoEnviado = false;

        try {
            // Configuración del servidor SMTP (Usa tus datos reales aquí)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tu_correo@gmail.com';      // Tu correo institucional/Gmail
            $mail->Password   = 'tu_contraseña_aplicacion'; // Tu contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Configuración del correo
            $mail->setFrom('tu_correo@gmail.com', 'Sistema de Registro CECyTE');
            $mail->addAddress($correo_tutor, $nombre); 

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Registro Exitoso de Alumno';
            $mail->Body    = "Hola, se ha registrado correctamente al alumno <b>$nombre</b> con matrícula <b>$matricula</b> en nuestro sistema de asistencia.";

            $mail->send();
            $correoEnviado = true;
        } catch (Exception $e_mail) {
            // No lanzamos excepción aquí para que el registro del alumno no se cancele si el mail falla
            $error_mail = $mail->ErrorInfo;
        }
        // --- FIN DE BLOQUE DE CORREO ---

        ob_clean();
        echo json_encode([
            'status' => 'success', 
            'alumno_id' => $nuevo_id,
            'email_sent' => $correoEnviado,
            'debug_mail' => $error_mail ?? 'OK'
        ]);

    } else {
        throw new Exception("Error: La matrícula ya existe en el sistema.");
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;