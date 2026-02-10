<?php
// 1. Configuración de conexión (Asegúrate de que estos datos sean correctos)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    // Se crea la variable $con que el error decía que faltaba
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

session_start();

// 2. Verificación de sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// 3. Consulta corregida según tus imágenes (Tabla: calificaciones)
// Usamos $con->query() porque $con ya fue definida arriba
$sql = "SELECT 
            c.id_calificacion, 
            c.matriculaAlumno, 
            a.nombre, 
            a.apellidoPaterno, 
            c.numEmpleado, 
            c.calificacion
        FROM calificaciones c
        INNER JOIN alumnos a ON c.matriculaAlumno = a.matriculaAlumno
        ORDER BY c.id_calificacion DESC";

try {
    $stmt = $con->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si la tabla 'calificaciones' está vacía o hay error en nombres de columna
    $resultados = [];
    $error_msg = "Error en la consulta: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Calificaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class='bx bx-spreadsheet text-success'></i> Reporte de Calificaciones</h2>
        <a href="reportes.php" class="btn btn-secondary">Volver</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Matrícula</th>
                        <th>Alumno</th>
                        <th>Docente (Emp#)</th>
                        <th>Calificación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($resultados)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay registros encontrados o la tabla está vacía.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($resultados as $row): ?>
                        <tr>
                            <td><?php echo $row['id_calificacion']; ?></td>
                            <td><?php echo $row['matriculaAlumno']; ?></td>
                            <td><?php echo $row['nombre'] . " " . $row['apellidoPaterno']; ?></td>
                            <td><?php echo $row['numEmpleado']; ?></td>
                            <td>
                                <span class="badge <?php echo $row['calificacion'] >= 7 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $row['calificacion']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="editar_calificacion.php?id=<?php echo $row['id_calificacion']; ?>" class="btn btn-sm btn-warning">
                                    <i class='bx bx-edit-alt'></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>