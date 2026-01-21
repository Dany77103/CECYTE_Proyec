<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECYTE - Iniciar Sesión</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <?php include 'navbarinicio.php'; ?>

    <!-- Contenido Principal -->
    <main class="container d-flex justify-content-center align-items-center flex-grow-1">
        <div class="text-icons" style="max-width: 800px;">
            <img src="img/mascota1.png" alt="Logo" class="logo-index">
            <p></p>         
        </div>
        <div class="text-icons" style="max-width: 800px;">
            <h1>Bienvenido al
                <br>Sistema CECYTE Santa Catarina.            
            </h1>
            <p class="p-index">Este sistema permite tener registro de alumnos y maestros</p> 
            <p class="p-index">Por favor, inicia sesión para continuar.</p>       
        </div>  
        
        <div class="card p-4" style="max-width: 600px;">
            <h1 class="text-center mb-4">Iniciar Sesión</h1>
            <?php
            // Mostrar mensaje de error si existe
            if (isset($_GET['error'])) {
                echo '<p class="text-danger text-center mb-3">Usuario o contraseña incorrectos.</p>';
            }
            ?>
            <form action="login.php" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label"><i class="bi bi-person"></i> Usuario:</label>
                    <input type="text" id="username" name="username" class="form-control" required placeholder="Ingresa tu usuario">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><i class="bi bi-lock"></i> Contraseña:</label>
                    <div class="mb-3">
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Ingresa tu contraseña">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
        }
    </script>
</body>
</html>