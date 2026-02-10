<?php
// 1. SEGURIDAD: Iniciar sesión y verificar acceso
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// 2. CONEXIÓN A LA BASE DE DATOS
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// 3. VERIFICAR SI SE RECIBIERON DATOS POR POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturar el ID (indispensable)
    $id_alumno = $_POST['id_alumno'];
    
    // Capturar el resto de los campos
    $matricula = $_POST['matriculaAlumno'];
    $rfc = $_POST['rfc'];
    $fechaNac = $_POST['fechaNacimiento'];
    $nombre = $_POST['nombre'];
    $apPaterno = $_POST['apellidoPaterno'];
    $apMaterno = $_POST['apellidoMaterno'];
    $mailInst = $_POST['mailInstitucional'];
    $mailPers = $_POST['mailPersonal'];
    $celular = $_POST['numCelular'];
    $telEmergencia = $_POST['telefonoEmergencia'];
    $direccion = $_POST['direccion'];
    $id_discapacidad = $_POST['id_discapacidad'];

    try {
        // 4. PREPARAR LA SENTENCIA SQL DE ACTUALIZACIÓN
        // Usamos marcadores de posición (?) por seguridad
        $sql = "UPDATE alumnos SET 
                matriculaAlumno = ?, 
                rfc = ?, 
                fechaNacimiento = ?, 
                nombre = ?, 
                apellidoPaterno = ?, 
                apellidoMaterno = ?, 
                mailInstitucional = ?, 
                mailPersonal = ?, 
                numCelular = ?, 
                telefonoEmergencia = ?, 
                direccion = ?, 
                id_discapacidad = ?,
                fechaModificacion = CURRENT_TIMESTAMP
                WHERE id_alumno = ?";

        $stmt = $con->prepare($sql);
        
        // 5. EJECUTAR CON LOS VALORES
        $resultado = $stmt->execute([
            $matricula, 
            $rfc, 
            $fechaNac, 
            $nombre, 
            $apPaterno, 
            $apMaterno, 
            $mailInst, 
            $mailPers, 
            $celular, 
            $telEmergencia, 
            $direccion, 
            $id_discapacidad,
            $id_alumno
        ]);

        if ($resultado) {
            // Éxito: Redirigir con un mensaje (puedes usar una variable GET)
            header("Location: reportes.php?status=success");
            exit;
        }

    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
} else {
    // Si alguien intenta entrar directamente al archivo sin enviar el formulario
    header("Location: reportes.php");
    exit;
}
?>