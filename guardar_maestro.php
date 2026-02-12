<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos del formulario y mapearlos a los nombres de la tabla
    $num_empleado        = $_POST['numEmpleado'] ?? '';
    $nombre              = $_POST['nombre'] ?? '';
    $apellido_paterno    = $_POST['apellidoPaterno'] ?? '';
    $apellido_materno    = $_POST['apellidoMaterno'] ?? '';
    $rfc                 = $_POST['rfc'] ?? '';
    $curp                = $_POST['curp'] ?? '';
    $fecha_nacimiento    = $_POST['fechaNacimiento'] ?? '';
    $id_genero           = $_POST['id_genero'] ?? null;
    $id_nacionalidad     = $_POST['id_nacionalidad'] ?? 1;
    $id_estado_nacimiento = $_POST['id_estadoNacimiento'] ?? null;
    $direccion           = $_POST['direccion'] ?? '';
    $num_celular         = $_POST['numCelular'] ?? '';
    $telefono_emergencia = $_POST['telefonoEmergencia'] ?? '';
    $mail_institucional  = $_POST['mailInstitucional'] ?? '';
    $mail_personal       = $_POST['mailPersonal'] ?? '';
    $id_rol              = $_POST['id_rol'] ?? null;
    $estatus             = $_POST['estatus'] ?? 'Activo';

    try {
        // Usando PDO (más seguro y moderno)
        $sql = "INSERT INTO personal_institucional (
                    num_empleado, nombre, apellido_paterno, apellido_materno, 
                    rfc, curp, fecha_nacimiento, id_genero, id_nacionalidad, 
                    id_estado_nacimiento, direccion, num_celular, 
                    telefono_emergencia, mail_institucional, mail_personal, id_rol, estatus
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);
        $resultado = $stmt->execute([
            $num_empleado, $nombre, $apellido_paterno, $apellido_materno,
            $rfc, $curp, $fecha_nacimiento, $id_genero, $id_nacionalidad,
            $id_estado_nacimiento, $direccion, $num_celular,
            $telefono_emergencia, $mail_institucional, $mail_personal, $id_rol, $estatus
        ]);

        if ($resultado) {
            echo "success";
        }
    } catch (PDOException $e) {
        http_response_code(500);
        // Mostrar error específico si el RFC o CURP ya existen (duplicados)
        if ($e->getCode() == 23000) {
            echo "Error: El RFC, CURP o Número de Empleado ya se encuentra registrado.";
        } else {
            echo "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>