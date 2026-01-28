<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECYTE - Acceso al Sistema</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #1e7e34;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            /* FONDO DEGRADADO VERDE A BLANCO */
            background: linear-gradient(135deg, #d4edda 0%, #ffffff 100%); 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
        }

        .navbar { background: rgba(255, 255, 255, 0.9) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 10px 0; }
        .navbar .hora { color: #666; font-weight: 600; background: #eee; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; }
        
        .login-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .login-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1); max-width: 1000px; width: 100%; display: flex; flex-wrap: wrap; }
        
        .login-brand { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; padding: 40px; flex: 1; min-width: 350px; display: flex; flex-direction: column; justify-content: center; text-align: center; }
        .login-brand img { max-width: 180px; margin: 0 auto 20px; filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2)); }
        
        .login-form-section { padding: 50px; flex: 1; min-width: 350px; background: white; }
        .form-control { border-radius: 10px; padding: 12px 15px; border: 1px solid #dee2e6; background-color: #fdfdfd; }

        .btn-login { background: var(--primary-color); border: none; border-radius: 10px; padding: 12px; font-weight: 600; color: white; transition: all 0.3s ease; }
        .btn-login:hover { background: var(--secondary-color); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3); }

        /* MEDIDOR DE SEGURIDAD CON BRILLO */
        .strength-meter {
            height: 8px;
            background-color: #e9ecef;
            margin-top: 10px;
            border-radius: 4px;
            overflow: hidden;
        }

        .strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: width 0.5s ease-in-out, background-color 0.5s, box-shadow 0.5s;
        }

        .strength-text { font-size: 0.75rem; font-weight: 600; margin-top: 5px; display: block; text-align: right; }
        
        .animate-in { animation: fadeInScale 0.6s ease-out forwards; }
        @keyframes fadeInScale { 0% { opacity: 0; transform: scale(0.95); } 100% { opacity: 1; transform: scale(1); } }
        
        /* Ajuste de descripción */
        .brand-description { font-size: 0.9rem; line-height: 1.5; margin-top: 15px; text-align: justify; }
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
                <p class="mb-1 fw-bold">Santa Catarina, Nuevo León</p>
                <hr class="w-25 mx-auto">
                <div class="brand-description px-3">
                    <p>Somos una institución líder en educación técnica bivalente, comprometida con la formación de profesionales técnicos que impulsan el desarrollo tecnológico y social de nuestra comunidad.</p>
                    <p class="small opacity-75 mt-2">Este portal permite la gestión eficiente de asistencias mediante tecnología QR, optimizando el control escolar de alumnos y docentes en tiempo real.</p>
                </div>
            </div>

            <div class="login-form-section">
                <div class="text-center mb-5">
                    <h3 class="fw-bold text-dark">¡Bienvenido!</h3>
                    <p class="text-muted">Ingresa tus credenciales</p>
                </div>

                <form action="login.php" method="post">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">NOMBRE DE USUARIO</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control bg-light border-start-0" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">CONTRASEÑA</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" required onkeyup="checkStrength(this.value)">
                            <span class="input-group-text bg-light" style="cursor: pointer" onclick="togglePassword()">
                                <i class="bi bi-eye" id="eye-icon"></i>
                            </span>
                        </div>
                        
                        <div class="strength-meter">
                            <div id="strength-bar" class="strength-meter-fill"></div>
                        </div>
                        <span id="strength-text" class="strength-text text-muted">Insegura</span>
                    </div>

                    <button type="submit" class="btn btn-success btn-login w-100 shadow">
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
        function checkStrength(password) {
            let strength = 0;
            const bar = document.getElementById('strength-bar');
            const text = document.getElementById('strength-text');

            if (password.length > 5) strength += 25;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 25;
            if (password.match(/\d/)) strength += 25;
            if (password.match(/[^a-zA-Z\d]/)) strength += 25;

            bar.style.width = strength + "%";

            if (strength <= 25) {
                bar.style.backgroundColor = "#dc3545";
                bar.style.boxShadow = "0 0 10px rgba(220, 53, 69, 0.6)"; 
                text.textContent = "Muy Débil";
                text.className = "strength-text text-danger";
            } else if (strength <= 50) {
                bar.style.backgroundColor = "#ffc107";
                bar.style.boxShadow = "0 0 10px rgba(255, 193, 7, 0.6)"; 
                text.textContent = "Aceptable";
                text.className = "strength-text text-warning";
            } else if (strength <= 75) {
                bar.style.backgroundColor = "#17a2b8";
                bar.style.boxShadow = "0 0 10px rgba(23, 162, 184, 0.6)"; 
                text.textContent = "Segura";
                text.className = "strength-text text-info";
            } else {
                bar.style.backgroundColor = "#28a745";
                bar.style.boxShadow = "0 0 12px rgba(40, 167, 69, 0.8)"; 
                text.textContent = "Muy Fuerte";
                text.className = "strength-text text-success";
            }
        }

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

        function actualizarHora() {
            const horaElemento = document.getElementById('horaNav');
            const ahora = new Date();
            horaElemento.textContent = ahora.toLocaleTimeString('es-MX', { hour12: false });
        }
        setInterval(actualizarHora, 1000);
        actualizarHora();
    </script>
</body>
</html>