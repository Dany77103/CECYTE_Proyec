<?php
// Evitar que PHP mande errores como texto
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Datos de conexión - VERIFICA TU PASSWORD DE SQLYOG
$host = "localhost";
$user = "root";     
$pass = ""; // SI TIENES PASSWORD EN SQLYOG, PONLA AQUÍ
$db   = "cecyte_sc";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a BD']);
    exit;
}

// Recibir datos
$matricula = $_POST['matricula'] ?? '';
$nombre    = $_POST['nombre'] ?? '';
$grupo     = $_POST['grupo'] ?? '';

if (empty($matricula) || empty($nombre)) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

// INSERTAR - Asegúrate que la tabla se llama alumnos_qr
$sql = "INSERT INTO alumnos_qr (matricula, nombre, grupo) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sss", $matricula, $nombre, $grupo);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: Matrícula duplicada o tabla no encontrada']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error en la consulta SQL']);
}

$conn->close();
exit;