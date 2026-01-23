<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECYTE - Acceso al Sistema</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #1e7e34;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar elegante */
        .navbar {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 10px 0;
        }

        .navbar .hora {
            color: #666;
            font-weight: 600;
            background: #eee;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        /* Contenedor Login */
        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            max-width: 1000px;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
        }

        /* Sección Izquierda (Identidad) */
        .login-brand {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px;
            flex: 1;
            min-width: 350px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .login-brand img {
            max-width: 220px;
            margin: 0 auto 20px;
            filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2));
        }

        /* Sección Derecha (Formulario) */
        .login-form-section {
            padding: 50px;
            flex: 1;
            min-width: 350px;
            background: white;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            background-color: #fdfdfd;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
            border-color: var(--primary-color);
        }

        .btn-login {
            background: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .input-group-text {
            background: white;
            border-left: none;
            cursor: pointer;
            border-radius: 0 10px 10px 0;
        }

        .pass-input {
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        /* Animación suave */
        .animate-in {
            animation: fadeInScale 0.6s ease-out forwards;
        }

        @keyframes fadeInScale {
            0% { opacity: 0; transform: scale(0.95); }
            100% { opacity: 1; transform: scale(1); }
        }

        @media (max-width: 768px) {
            .login-brand { padding: 30px; }
            .login-form-section { padding: 30px; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <img src="img/logo.png" alt="Logo" height="35">
            <div class="hora" id="horaNav">--:--:--</div>
        </div>
    </nav>

    <main class="login-wrapper">
        <div class="login-card animate-in">
            
            <div class="login-brand">
                <img src="img/mascota1.png" alt="Mascota">
                <h2 class="fw-bold">CECYTE</h2>
                <p class="opacity-75">Santa Catarina, Nuevo León</p>
                <hr class="w-25 mx-auto">
                <p class="small mt-2">Sistema Integral de Registro para Alumnos y Docentes.</p>
            </div>

            <div class="login-form-section">
                <div class="text-center mb-5">
                    <h3 class="fw-bold text-dark">¡Bienvenido!</h3>
                    <p class="text-muted">Ingresa tus credenciales para acceder</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert" style="border-radius: 10px;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div class="small">Usuario o clave incorrectos.</div>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="post">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NOMBRE DE USUARIO</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="username" class="form-control bg-light border-start-0" placeholder="Ej: admin2024" required style="border-radius: 0 10px 10px 0;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">CONTRASEÑA</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bi bi-shield-lock text-muted"></i></span>
                            <input type="password" id="password" name="password" class="form-control bg-light border-start-0 pass-input" placeholder="••••••••" required>
                            <span class="input-group-text bg-light" onclick="togglePassword()">
                                <i class="bi bi-eye text-muted" id="eye-icon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-login w-100 text-white shadow">
                        INICIAR SESIÓN <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>
                </form>

                <div class="text-center mt-5">
                    <p class="small text-muted">&copy; 2024 CECYTE Santa Catarina</p>
                </div>
            </div>

        </div>
    </main>

    <script>
        // Función para mostrar/ocultar contraseña
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Reloj digital en Navbar
        function actualizarHora() {
            const horaElemento = document.getElementById('horaNav');
            const ahora = new Date();
            horaElemento.textContent = ahora.toLocaleTimeString('es-MX', { hour12: false });
        }
        setInterval(actualizarHora, 1000);
        actualizarHora();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>