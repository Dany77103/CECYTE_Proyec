<?php
require_once 'conexion.php';

// Depuración: Verifica si el parámetro está llegando correctamente
if (isset($_GET['numEmpleado'])) {

    $id = $_GET['numEmpleado'];
    echo "Registro recibido: " . htmlspecialchars($id) . "<br>"; // Depuración
	

   // $sql = "select * FROM alumnos WHERE id_maestro = :id";
	
    try {
        // Iniciar una transacción
        $con->beginTransaction();

        // 1. Eliminar registros dependientes en datosacademicosmaestros
        $sql_delete_academicos = "DELETE FROM datosacademicosmaestros WHERE numEmpleado = :id";
        $stmt_delete_academicos = $con->prepare($sql_delete_academicos);
        $stmt_delete_academicos->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_delete_academicos->execute();

        // 2. Eliminar registros dependientes en datoslaboralesmaestros
        $sql_delete_laborales = "DELETE FROM datoslaboralesmaestros WHERE numEmpleado = :id";
        $stmt_delete_laborales = $con->prepare($sql_delete_laborales);
        $stmt_delete_laborales->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_delete_laborales->execute();

        // 3. Eliminar el registro en maestros
        $sql_delete_maestro = "DELETE FROM maestros WHERE numEmpleado = :id";
        $stmt_delete_maestro = $con->prepare($sql_delete_maestro);
        $stmt_delete_maestro->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_delete_maestro->execute();

        // Confirmar la transacción
        $con->commit();

        if ($stmt_delete_maestro->rowCount() > 0) {
            echo "Registro eliminado correctamente.";
        } else {
            echo "No se encontró el registro con el número de empleado proporcionado.";
        }
    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        $con->rollBack();
        echo "Error al eliminar el registro: " . $e->getMessage();
    }
} else {
    echo "ID no proporcionado.";
}

// Cerrar la conexión asignando null a la variable
$con = null;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminado completo</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Botón que regresa anteriormente al inicio -->
    <div class="button-container">
        <a href="reportes.php" class="btn-agregar">Regresa a la página anterior</a>
    </div>
</body>
</html>