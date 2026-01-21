
<?php 
require_once 'conexion.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-success text-white text-center py-3">
        <a href="index.php" class="logo"><img src="img/logo.png" alt="Logo de la Escuela" class="logo-img"></a>
        <a href="index.php" class="btn btn-light btn-sm ms-3">
            <i class="fas fa-home"></i> INICIO
        </a>
        <a href="cerrar_sesion.php" class="btn btn-warning btn-sm ms-3">
            <i class="fas fa-sign-out-alt"></i> CERRAR SESION
        </a>
    </header>
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
<footer class="bg-success  text-white text-center py-3 mt-5 mt-auto">
    <p>&copy; 2023 Sistema de Reportes. Todos los derechos reservados.</p>
</footer>

    <?php include 'modales.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>



