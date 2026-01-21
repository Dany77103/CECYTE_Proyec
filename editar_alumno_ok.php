<?php
// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

// Verificar si se ha enviado un ID válido
if (!isset($_GET['matriculaAlumno']) || !is_numeric($_GET['matriculaAlumno'])) {
    header("Location: index.php"); // Redirigir si no hay un ID válido
    exit();
}

$matriculaAlumno = intval($_GET['matriculaAlumno']); // Sanitizar el ID

// Obtener los datos del registro a editar
$sql = "SELECT * 
        FROM alumnos a
        INNER JOIN generos g ON g.id_genero = a.id_genero
        LEFT JOIN nacionalidades n ON n.id_nacionalidad = a.id_nacionalidad
        LEFT JOIN estadonacimiento en ON en.id_estadoNacimiento = a.id_estadoNacimiento
        LEFT JOIN discapacidades di ON di.id_discapacidad = a.id_discapacidad
        WHERE a.matriculaAlumno = :matriculaAlumno";

try {
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':matriculaAlumno', $matriculaAlumno, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "No se encontró el registro.";
        exit();
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Sanitizar y validar los datos del formulario
    $apellidoPaterno = htmlspecialchars($_POST['apellidoPaterno'], ENT_QUOTES, 'UTF-8');
    $apellidoMaterno = htmlspecialchars($_POST['apellidoMaterno'], ENT_QUOTES, 'UTF-8');
    $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
    $fechaNacimiento = htmlspecialchars($_POST['fechaNacimiento'], ENT_QUOTES, 'UTF-8');
    $id_genero = intval($_POST['id_genero']);
    $rfc = htmlspecialchars($_POST['rfc'], ENT_QUOTES, 'UTF-8');
    $id_nacionalidad = intval($_POST['id_nacionalidad']);
    $id_estadoNacimiento = intval($_POST['id_estadoNacimiento']);
    $direccion = htmlspecialchars($_POST['direccion'], ENT_QUOTES, 'UTF-8');
    $numCelular = htmlspecialchars($_POST['numCelular'], ENT_QUOTES, 'UTF-8');
    $telefonoEmergencia = htmlspecialchars($_POST['telefonoEmergencia'], ENT_QUOTES, 'UTF-8');
    $mailInstitucional = htmlspecialchars($_POST['mailInstitucional'], ENT_QUOTES, 'UTF-8');
    $mailPersonal = htmlspecialchars($_POST['mailPersonal'], ENT_QUOTES, 'UTF-8');
    $id_discapacidad = intval($_POST['id_discapacidad']);

    // Actualizar el registro en la base de datos
    $sql = "UPDATE alumnos 
            SET apellidoPaterno = :apellidoPaterno, 
                apellidoMaterno = :apellidoMaterno, 
                nombre = :nombre, 
                fechaNacimiento = :fechaNacimiento, 
                id_genero = :id_genero, 
                rfc = :rfc, 
                id_nacionalidad = :id_nacionalidad, 
                id_estadoNacimiento = :id_estadoNacimiento, 
                direccion = :direccion, 
                numCelular = :numCelular, 
                telefonoEmergencia = :telefonoEmergencia, 
                mailInstitucional = :mailInstitucional, 
                mailPersonal = :mailPersonal, 
                id_discapacidad = :id_discapacidad 
            WHERE matriculaAlumno = :matriculaAlumno";

    try {
        $stmt = $con->prepare($sql);
        $stmt->bindValue(':matriculaAlumno', $matriculaAlumno, PDO::PARAM_INT);
        $stmt->bindValue(':apellidoPaterno', $apellidoPaterno, PDO::PARAM_STR);
        $stmt->bindValue(':apellidoMaterno', $apellidoMaterno, PDO::PARAM_STR);
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindValue(':fechaNacimiento', $fechaNacimiento, PDO::PARAM_STR);
        $stmt->bindValue(':id_genero', $id_genero, PDO::PARAM_INT);
        $stmt->bindValue(':rfc', $rfc, PDO::PARAM_STR);
        $stmt->bindValue(':id_nacionalidad', $id_nacionalidad, PDO::PARAM_INT);
        $stmt->bindValue(':id_estadoNacimiento', $id_estadoNacimiento, PDO::PARAM_INT);
        $stmt->bindValue(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindValue(':numCelular', $numCelular, PDO::PARAM_STR);
        $stmt->bindValue(':telefonoEmergencia', $telefonoEmergencia, PDO::PARAM_STR);
        $stmt->bindValue(':mailInstitucional', $mailInstitucional, PDO::PARAM_STR);
        $stmt->bindValue(':mailPersonal', $mailPersonal, PDO::PARAM_STR);
        $stmt->bindValue(':id_discapacidad', $id_discapacidad, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "Registro actualizado correctamente.";
            header("Refresh: 2; URL=index.php"); // Redirigir después de 2 segundos
            exit();
        } else {
            echo "Error al actualizar el registro.";
        }
    } catch (PDOException $e) {
        die("Error en la actualización: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Alumnos</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Formulario para editar el alumno -->
    <div class="contai">
        <h1>Editar Alumnos</h1>
        <form method="POST" action="">
            <input type="hidden" name="matriculaAlumno" value="<?php echo $row['matriculaAlumno']; ?>">

            <!-- Campos del formulario -->
            <!-- Asegúrate de que todos los campos estén correctamente vinculados -->
            <div class="form-columns">
                <!-- Columna 1 -->
                <div class="col">
                    <div class="input-group">
                        <input type="text" name="apellidoPaterno" value="<?php echo htmlspecialchars($row['apellidoPaterno'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        <span>Apellido Paterno:</span>
                        <i></i>
                    </div>
                    <!-- Repite para los demás campos -->
                </div>
                <!-- Columna 2 -->
                <div class="col">
                    <!-- Campos adicionales -->
                </div>
            </div>
            <br><br>
            <button class="but-box"><span class="box">Editar Alumno</span></button>
        </form>
    </div>
    <br><br>
</body>
</html>