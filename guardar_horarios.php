<?php
include 'conexion.php'; // Asegúrate de tener tu conexión PDO aquí

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['horario'])) {
    try {
        $con->beginTransaction();

        // 1. Opcional: Limpiar horario anterior para evitar duplicados
        // Si manejas semestres, añade: WHERE id_semestre = ...
        $con->exec("DELETE FROM horarios");

        // 2. Preparar la inserción
        $sql = "INSERT INTO horarios (dia_semana, bloque_hora, id_materia) VALUES (:dia, :bloque, :id_materia)";
        $stmt = $con->prepare($sql);

        // 3. Recorrer los datos del formulario
        foreach ($_POST['horario'] as $dia => $bloques) {
            foreach ($bloques as $bloque_hora => $id_materia) {
                // Solo guardamos si se seleccionó una materia (no está vacío)
                if (!empty($id_materia)) {
                    $stmt->execute([
                        ':dia' => $dia,
                        ':bloque' => $bloque_hora,
                        ':id_materia' => $id_materia
                    ]);
                }
            }
        }

        $con->commit();
        echo "<script>alert('Horario guardado con éxito'); window.location.href='reportes.php';</script>";

    } catch (Exception $e) {
        $con->rollBack();
        die("Error al guardar: " . $e->getMessage());
    }
}
?>