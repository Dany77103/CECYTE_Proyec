<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    exit('Acceso denegado');
}

// CONEXIÓN
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recoger datos del formulario
        $id_personal         = $_POST['id_personal'];
        $num_empleado        = $_POST['num_empleado'];
        $nombre              = $_POST['nombre'];
        $apellido_paterno    = $_POST['apellido_paterno'];
        $apellido_materno    = $_POST['apellido_materno'];
        $rfc                 = $_POST['rfc'];
        $curp                = $_POST['curp'];
        $fecha_nacimiento    = $_POST['fecha_nacimiento'];
        $id_rol              = $_POST['id_rol'];
        $num_celular         = $_POST['num_celular'];
        $estatus             = $_POST['estatus'];
        $telefono_emergencia = $_POST['telefono_emergencia'];
        $mail_institucional  = $_POST['mail_institucional'];
        $mail_personal       = $_POST['mail_personal'];
        $direccion           = $_POST['direccion'];

        // QUERY DE ACTUALIZACIÓN (Basado exactamente en tus capturas)
        $sql = "UPDATE personal_institucional SET 
                    num_empleado = ?, 
                    nombre = ?, 
                    apellido_paterno = ?, 
                    apellido_materno = ?, 
                    rfc = ?, 
                    curp = ?, 
                    fecha_nacimiento = ?, 
                    id_rol = ?, 
                    num_celular = ?, 
                    estatus = ?, 
                    telefono_emergencia = ?, 
                    mail_institucional = ?, 
                    mail_personal = ?, 
                    direccion = ?
                WHERE id_personal = ?";

        $stmt = $con->prepare($sql);
        $stmt->execute([
            $num_empleado, 
            $nombre, 
            $apellido_paterno, 
            $apellido_materno, 
            $rfc, 
            $curp, 
            $fecha_nacimiento, 
            $id_rol, 
            $num_celular, 
            $estatus, 
            $telefono_emergencia, 
            $mail_institucional, 
            $mail_personal, 
            $direccion,
            $id_personal
        ]);

        // Redireccionar con éxito
        echo "<script>
                alert('¡Expediente actualizado correctamente!');
                window.location.href = 'reporte_personal.php'; 
              </script>";
    }
} catch (PDOException $e) {
    echo "Error al actualizar: " . $e->getMessage();
}
?>