<?php
// registrar_qr.php
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

    $matricula = $_POST['matricula'] ?? '';
    $nombre    = $_POST['nombre'] ?? '';
    $grupo     = $_POST['grupo'] ?? '';

    // Ajustado a minúsculas: qralumnos
    $stmt = $conn->prepare("INSERT INTO qralumnos (matricula, nombre, grupo) VALUES (?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Error en la tabla qralumnos: " . $conn->error);
    }

    $stmt->bind_param("sss", $matricula, $nombre, $grupo);
    
    if ($stmt->execute()) {
        $nuevo_id = $conn->insert_id; 
        ob_clean();
        echo json_encode(['status' => 'success', 'alumno_id' => $nuevo_id]);
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