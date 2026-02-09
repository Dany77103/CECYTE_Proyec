<?php
require_once 'conexion.php';

if (isset($_GET['id_alumno'])) {
    $id_numerico = $_GET['id_alumno'];
    
    try {
        $con->beginTransaction();

        // 1. Buscamos la matrícula de texto (A1, A2, etc.) en la tabla 'alumnos'
        // Usamos 'id_alumno' que es tu PK en esa tabla.
        $stmt_info = $con->prepare("SELECT matriculaAlumno FROM alumnos WHERE id_alumno = :id");
        $stmt_info->execute([':id' => $id_numerico]);
        $alumno = $stmt_info->fetch(PDO::FETCH_ASSOC);

        if ($alumno) {
            $mat = $alumno['matriculaAlumno'];

            // 2. Borrar de 'historialacademicoalumnos'
            // Tu imagen confirma que la columna es 'matriculaAlumno'
            $stmt1 = $con->prepare("DELETE FROM historialacademicoalumnos WHERE matriculaAlumno = :mat");
            $stmt1->execute([':mat' => $mat]);

            // 3. Borrar de 'qralumnos' 
            // Tu imagen confirma que aquí la columna se llama 'matricula' (a secas)
            $stmt2 = $con->prepare("DELETE FROM qralumnos WHERE matricula = :mat");
            $stmt2->execute([':mat' => $mat]);

            // 4. Borrar finalmente de 'alumnos'
            $stmt3 = $con->prepare("DELETE FROM alumnos WHERE id_alumno = :id");
            $stmt3->execute([':id' => $id_numerico]);

            $con->commit();
            $mensaje = "Éxito: Alumno con matrícula $mat eliminado correctamente.";
        } else {
            $con->rollBack();
            $mensaje = "No se encontró el alumno con ID $id_numerico.";
        }

    } catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        $mensaje = "Error de SQL: " . $e->getMessage();
    }
} else {
    $mensaje = "No se recibió el ID.";
}
$con = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Resultado de Eliminación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 30px; text-align: center; max-width: 450px; background: white; }
        .btn-return { background-color: #166534; color: white; border-radius: 8px; padding: 10px 20px; text-decoration: none; display: inline-block; transition: 0.2s; }
        .btn-return:hover { background-color: #15803d; color: white; }
    </style>
</head>
<body>
    <div class="card">
        <h4 style="color: #166534;">Proceso Finalizado</h4>
        <div class="alert <?php echo strpos($mensaje, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?> my-4">
            <?php echo $mensaje; ?>
        </div>
        <a href="reportes.php" class="btn btn-return">Regresar a Reportes</a>
    </div>
</body>
</html>