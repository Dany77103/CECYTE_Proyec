<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header('Location: index.php'); exit; }

$con = new mysqli("localhost", "root", "", "cecyte_sc");
$query = $con->query("SELECT id_maestro, numEmpleado, nombre, apellidoPaterno, mailInstitucional FROM maestros");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Maestros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5 bg-light">
    <div class="container bg-white p-4 shadow-sm rounded">
        <h2 class="text-success border-bottom pb-3">Personal Docente Registrado</h2>
        
        <?php if(isset($_GET['msj'])) echo "<div class='alert alert-success'>¡Datos actualizados correctamente!</div>"; ?>

        <table class="table table-hover mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Num. Emp</th>
                    <th>Nombre Completo</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $query->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['numEmpleado']; ?></td>
                    <td><?php echo $row['nombre'] . " " . $row['apellidoPaterno']; ?></td>
                    <td><?php echo $row['mailInstitucional']; ?></td>
                    <td>
                        <a href="editar_maestro.php?id=<?php echo $row['id_maestro']; ?>" class="btn btn-warning btn-sm">Editar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="reportes.php" class="btn btn-secondary">Regresar al Menú</a>
    </div>
</body>
</html>