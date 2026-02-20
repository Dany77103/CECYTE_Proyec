<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recibir datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $numero_empleado = $_POST['numero_empleado'] ?? '';
    $departamento = $_POST['departamento'] ?? '';
    $turno = $_POST['turno'] ?? '';

    if (empty($nombre) || empty($numero_empleado)) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios']);
        exit;
    }

    // Verificar si el número de empleado ya existe
    $check = $conn->prepare("SELECT id FROM qrpersonal WHERE numero_empleado = ?");
    $check->execute([$numero_empleado]);
    
    if ($check->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'El número de empleado ya está registrado']);
    } else {
        // Insertar en la tabla qrpersonal
        $sql = "INSERT INTO qrpersonal (nombre, numero_empleado, departamento, turno) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nombre, $numero_empleado, $departamento, $turno]);

        echo json_encode(['status' => 'success', 'message' => 'Maestro registrado correctamente']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>