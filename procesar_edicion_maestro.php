<?php
session_start();

// 1. SEGURIDAD: Verificar sesión activa
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    exit('Acceso denegado');
}

// 2. CONFIGURACIÓN DE CONEXIÓN
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Modo de error para desarrollo
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. PROCESAMIENTO DEL FORMULARIO
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_maestro'])) {
        
        // Recolección y limpieza básica de datos
        $id_maestro = $_POST['id_maestro'];
        $numEmpleado = trim($_POST['numEmpleado']);
        $apellidoPaterno = trim($_POST['apellidoPaterno']);
        $apellidoMaterno = trim($_POST['apellidoMaterno']);
        $nombre = trim($_POST['nombre']);
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $id_genero = $_POST['id_genero'];
        $rfc = trim($_POST['rfc']);
        $curp = trim($_POST['curp']);
        $id_nacionalidad = $_POST['id_nacionalidad'];
        $id_estadoNacimiento = $_POST['id_estadoNacimiento'];
        $direccion = trim($_POST['direccion']);
        $numCelular = trim($_POST['numCelular']);
        $mailInstitucional = trim($_POST['mailInstitucional']);
        $mailPersonal = trim($_POST['mailPersonal']);

        // 4. CONSULTA SQL
        $sql = "UPDATE maestros SET 
                numEmpleado = ?, 
                apellidoPaterno = ?, 
                apellidoMaterno = ?, 
                nombre = ?, 
                fechaNacimiento = ?, 
                id_genero = ?, 
                rfc = ?, 
                curp = ?, 
                id_nacionalidad = ?, 
                id_estadoNacimiento = ?, 
                direccion = ?, 
                numCelular = ?, 
                mailInstitucional = ?, 
                mailPersonal = ?,
                fechaModificacion = CURRENT_TIMESTAMP
                WHERE id_maestro = ?";

        $stmt = $con->prepare($sql);
        
        // 5. EJECUCIÓN
        $resultado = $stmt->execute([
            $numEmpleado,
            $apellidoPaterno,
            $apellidoMaterno,
            $nombre,
            $fechaNacimiento,
            $id_genero,
            $rfc,
            $curp,
            $id_nacionalidad,
            $id_estadoNacimiento,
            $direccion,
            $numCelular,
            $mailInstitucional,
            $mailPersonal,
            $id_maestro
        ]);

        // 6. REDIRECCIÓN TRAS ÉXITO
        if ($resultado) {
            // Asegúrate de que el archivo 'lista_maestros.php' exista con ese nombre exacto
            header("Location: lista_maestros.php?msj=actualizado");
            exit;
        } else {
            echo "No se pudo realizar la actualización.";
        }
    }
} catch (PDOException $e) {
    // Si hay un error de base de datos (como el error 1451 que mencionaste al inicio)
    echo "<h3>Error de Base de Datos:</h3>";
    echo "Detalle: " . $e->getMessage();
    echo "<br><br><a href='javascript:history.back()'>Regresar y corregir</a>";
}
?>