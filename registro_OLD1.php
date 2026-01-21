<?php
// Conexión a la base de datos usando PDO
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
</head>
<body class="d-flex flex-column min-vh-100">

     <!-- Navbar -->
    <?php include 'navbar.php'; ?>
	
    <main class="container mt-5 flex-grow-1">
        <h1 class="text-center mb-4">Sistema de Registro</h1>
        <div class="d-flex flex-wrap justify-content-center">
            <?php
            // Datos de los botones
            $buttons = [
                [
                    'color' => 'primary',
                    'icon' => 'fas fa-user-graduate',
                    'text' => 'Alta de Alumnos',
                    'modal_target' => '#modalAlumnos'
                ],
                [
                    'color' => 'success',
                    'icon' => 'fas fa-chalkboard-teacher',
                    'text' => 'Alta de Maestros o Admvos.',
                    'modal_target' => '#modalMaestros'
                ],
                // Agrega más botones aquí
				[
                    'color' => 'info',
                    'icon' => 'fas fa-briefcase',
                    'text' => 'Datos Laborales Maestros o Admvos.',
                    'modal_target' => '#modalDatosLaborales'
                ],
				
				[
                    'color' => 'warning',
                    'icon' => 'fas fa-graduation-cap',
                    'text' => 'Datos Academicos Maestros o Admvos.',
                    'modal_target' => '#modalDatosAcademicos'
                ],
				
				[
                    'color' => 'danger',
                    'icon' => 'fas fa-history',
                    'text' => 'Historial Academico Alumnos',
                    'modal_target' => '#modalHistorialAcademico'
                ],
				
				
				[
                    'color' => 'secondary',
                    'icon' => 'fas fa-check-circle',
                    'text' => 'Calificaciones Alumnos',
                    'modal_target' => '#modalCalificaciones'
                ],
				
				[
                    'color' => 'dark',
                    'icon' => 'fas fa-calendar-alt',
                    'text' => 'Horarios Maestros',
                    'modal_target' => '#modalHorarios'
                ],
				
				[
                    'color' => 'light',
                    'icon' => 'fas fa-camera',
                    'text' => ' Fotos y Perfil de Alumnos',
                    'modal_target' => '#modalSubirFoto'
                ]
				
            ];

            // Generar botones dinámicamente
            foreach ($buttons as $button) {
                echo '
                <button type="button" class="btn btn-' . $button['color'] . ' btn-custom m-2" data-bs-toggle="modal" data-bs-target="' . $button['modal_target'] . '">
                    <i class="' . $button['icon'] . '"></i> ' . $button['text'] . '
                </button>';
            }
            ?>
        </div>
		
		
		<!-- Modal único -->
<div class="modal fade" id="modalGenerico" tabindex="-1" aria-labelledby="modalGenericoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGenericoLabel">Título del Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Contenido dinámico -->				
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
		
		
		
    </main>

<script>
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function (e) {
        const inputs = this.querySelectorAll('input, select, textarea');
        let isValid = true;

        inputs.forEach(input => {
            if (input.required && input.value.trim() === '') {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Por favor, completa todos los campos requeridos.');
        }
    });
});



document.querySelectorAll('.btn-custom').forEach(button => {
    button.addEventListener('click', function () {
        const title = this.querySelector('i').nextSibling.textContent.trim();
        document.querySelector('#modalGenericoLabel').textContent = title;
        // Aquí puedes cargar contenido dinámico mediante AJAX
    });
});


</script>
 <!-- Footer -->
  <footer class="bg-success  text-white text-center py-3 mt-5 mt-auto">
  <div class="footer-container">
	<div class="footer-info">
     <p > CECyTE SANTA CATARINA N.L.</p>
     <p class="footer-year">&copy <?php echo date("Y"); ?> Sistema de Reportes. Todos los derechos reservados.</p>
	</div>
</footer>


    <?php include 'modales.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

