<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit('No autorizado'); }

$con = new mysqli("localhost", "root", "", "cecyte_sc");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_historial'];
    $calif = $_POST['nueva_calificacion'];

    $stmt = $con->prepare("UPDATE historialacademicoalumnos SET calificacion = ?, fechaModificacion = CURRENT_TIMESTAMP WHERE id_historial = ?");
    $stmt->bind_param("di", $calif, $id);

    if ($stmt->execute()) {
        header("Location: reporte_calificaciones.php?status=ok");
    } else {
        echo "Error al actualizar: " . $con->error;
    }
}
?>