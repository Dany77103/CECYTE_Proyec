<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }

require 'conexion.php'; // Asegúrate de que el nombre sea correcto

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $sql = "DELETE FROM personal_institucional WHERE id_personal = :id";
        $stmt = $con->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        header("Location: reportes.php?status=deleted");
    } catch (PDOException $e) {
        echo "Error al eliminar: " . $e->getMessage();
    }
}
?>