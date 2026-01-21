<?php
require_once 'conexion.php'; // Incluye la conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Archivo CSS externo -->
	<h1 class="text-center mb-4 display-4">  ' '  </h1>
</head>
<body>
    <?php include 'header.php'; ?>



<main class="container mt-5">
    
		
	<h1 class="text-center mb-4 display-4">Sistema de Reportes</h1>
		
		
	<div class="row">
			<!-- Tarjeta para Reporte de Alumnos -->
				<div class="col-12 col-md-6 col-lg-4 mb-4">
					<div class="card h-100 text-center">
						<div class="card-body">
							<h5 class="card-title"><i class="fas fa-users"></i> Reporte de Alumnos</h5>
							<p class="card-text">Genera un reporte detallado de los alumnos.</p>
							<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalReporteAlumnos">
								Ver Reporte
							</button>
						</div>
					</div>
				</div>			
			</div>

	
			
			<!-- Tarjeta para Reporte de Maestros -->
			<div class="row">
			   <div class="col-12 col-md-6 col-lg-4 mb-4">
					<div class="card h-100 text-center">
						<div class="card-body">
							<h5 class="card-title"><i class="fas fa-chalkboard-teacher"></i> Reporte de Maestros</h5>
							<p class="card-text">Genera un reporte detallado de los Maestros.</p>
							<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalReporteMaestros">
								Ver Reporte
							</button>
						</div>
					</div>
				</div>
			</div>
					
			
			
			
			<!-- Tarjeta para Reporte de Calificaciones -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-check-circle"></i> Reporte de Calificaciones</h5>
                        <p class="card-text">Genera un reporte detallado de las calificaciones.</p>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalReporteCalificaciones">
                            Ver Reporte
                        </button>
                    </div>
                </div>
            </div>
			
			
			
			
			
			<!-- Tarjeta para Reporte Academico de Maestros -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-briefcase"></i> Reporte Academico de Maestros</h5>
                        <p class="card-text">Genera un reporte detallado academico de maestros.</p>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalReporteDatosAcademicosMaestros">
                            Ver Reporte
                        </button>
                    </div>
                </div>
            </div>
			
			
			
			
			
			<!-- Tarjeta para Reporte de Horarios -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-calendar-alt"></i> Reporte de Horarios</h5>
                        <p class="card-text">Genera un reporte detallado de los horarios.</p>
                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalReporteHorarios">
                            Ver Reporte
                        </button>
                    </div>
                </div>
            </div>
			
			
			
			
			
			
			<!-- Tarjeta para Reporte de Asistencias -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-clipboard-list"></i> Reporte de Asistencias</h5>
                        <p class="card-text">Genera un reporte detallado de Asistencias.</p>
                        <button type="button" class="btn btn-light " data-bs-toggle="modal" data-bs-target="#modalReporteAsistencias">
                            Ver Reporte
                        </button>
                    </div>
                </div>
            </div>
			
			
			
			
			
			
			<!-- Tarjeta para Reporte de Materias -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-book"></i> Reporte de Materias</h5>
                        <p class="card-text">Genera un reporte detallado de Materias.</p>
                        <button type="button" class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#modalReporteMaterias">
                            Ver Reporte
                        </button>
                    </div>
                </div>
            </div>
			
		
		
			<!-- Tarjeta para Reporte de Fotos Alumnos -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-camera"></i> Reporte de Fotos Alumnos</h5>
                        <p class="card-text">Genera un reporte detallado de Fotos de Alumnos.</p>
                        <button type="button" class="btn btn-success " data-bs-toggle="modal" data-bs-target="#modalReporteFotoAlumno">
                            Ver Reporte
                        </button>
                    </div>
                </div>
            </div>
			
		
		
		
		
		
		
    </div>
</main>






 <script>
document.querySelector('form').addEventListener('submit', function (e) {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput.value.trim() === '') {
        e.preventDefault();
        const errorMessage = document.createElement('div');
        errorMessage.className = 'alert alert-danger';
        errorMessage.textContent = 'Por favor, ingresa un término de búsqueda.';
        searchInput.parentNode.insertBefore(errorMessage, searchInput.nextSibling);
    }
});
 </script>

<footer class="bg-success  text-white text-center py-3 mt-5">
    <p>&copy; 2023 Sistema de Reportes. Todos los derechos reservados.</p>
</footer>

    <?php include 'modales_reportes.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>


