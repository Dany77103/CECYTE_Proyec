<?php
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
    <title>CECYTE - Sistema de Reportes</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed: 85px;
            --primary-color: #064e3b;
            --secondary-color: #065f46;
            --accent-color: #10b981;
            --hover-color: #34d399;
            --bg-light: #f0fdf4;
            --text-color: #ecf0f1;
            --header-height: 70px;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        .main-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* --- SIDEBAR STYLE CORREGIDO --- */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--text-color);
            position: fixed;
            height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.15);
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }
        
        .sidebar-header {
            height: var(--header-height);
            padding: 0 15px;
            background-color: rgba(0,0,0,0.15);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            white-space: nowrap; /* Evita que el texto se doble */
        }
        
        .logo-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
            transition: opacity 0.3s;
            flex-grow: 1;
        }

        /* Ocultar elementos al colapsar para que no se amontonen */
        .sidebar.collapsed .logo-name,
        .sidebar.collapsed .link-text,
        .sidebar.collapsed .search-container {
            opacity: 0;
            pointer-events: none;
        }
        
        #btn-toggle {
            font-size: 1.6rem;
            cursor: pointer;
            color: white;
            background: rgba(255,255,255,0.15);
            padding: 6px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        #btn-toggle:hover {
            background: var(--accent-color);
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
            margin: 4px 10px;
            white-space: nowrap; /* Mantiene el texto en una sola línea */
        }
        
        .nav-link i {
            font-size: 1.4rem;
            min-width: 45px;
            display: flex;
            justify-content: center;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: var(--hover-color);
        }

        .nav-link.active {
            background: var(--accent-color) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        /* --- CONTENT STYLE --- */
        .content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }
        
        .sidebar.collapsed ~ .content-wrapper {
            margin-left: var(--sidebar-collapsed);
        }

        .main-header {
            background: white;
            height: var(--header-height);
            padding: 0 30px;
            display: flex;
            align-items: center;
            border-bottom: 2px solid var(--accent-color);
            position: sticky; top: 0; z-index: 99;
        }

        .historia-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            border-left: 5px solid var(--primary-color);
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .card-pilar {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s;
            border-bottom: 3px solid transparent;
        }
        
        .card-pilar:hover { 
            transform: translateY(-8px); 
            border-bottom: 3px solid var(--accent-color);
        }

        .text-custom-green { color: var(--primary-color) !important; }

    </style>
</head>
<body>
    <div class="main-container">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo-container d-flex align-items-center justify-content-between w-100">
                    <div class="logo-name">Sistema de Reportes<br><span style="color:var(--hover-color)">CECYTE_SC</span></div>
                    <i class='bx bx-menu' id="btn-toggle"></i>
                </div>
            </div>
            
            <div class="search-container p-3">
                <div class="search-box d-flex align-items-center" style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 5px 10px;">
                    <i class='bx bx-search me-2'></i>
                    <input type="text" class="bg-transparent border-0 text-white w-100" placeholder="Buscar..." id="sidebar-search" style="outline:none; font-size: 0.9rem;">
                </div>
            </div>

            <ul class="sidebar-menu list-unstyled">
                <li class="nav-item"><a href="main.php" class="nav-link active"><i class='bx bx-home-alt-2'></i><span class="link-text">Inicio</span></a></li>
                <li class="nav-item"><a href="registro.php" class="nav-link"><i class='bx bx-file'></i><span class="link-text">Registro</span></a></li>
                <li class="nav-item"><a href="reportes.php" class="nav-link"><i class='bx bx-pencil'></i><span class="link-text">Generar Reportes</span></a></li>
                <li class="nav-item"><a href="estadisticas.php" class="nav-link"><i class='bx bx-chart'></i><span class="link-text">Estadísticas</span></a></li>
                <li class="nav-item"><a href="qr_asistencia.php" class="nav-link"><i class='bx bx-qr-scan'></i><span class="link-text">Asistencia QR</span></a></li>
                <li class="nav-item"><a href="updo.php" class="nav-link"><i class='bx bx-folder'></i><span class="link-text">Archivos</span></a></li>
                <li class="nav-item"><a href="asignar_qr.php" class="nav-link"><i class='bx bx-user-pin'></i><span class="link-text">Asignar QR</span></a></li>
            </ul>
            
            <div class="user-section">
                <a href="logout.php" class="nav-link" style="color: #ff7675; margin-top: 20px;">
                    <i class='bx bx-log-out-circle'></i>
                    <span class="link-text">Cerrar Sesión</span>
                </a>
            </div>
        </aside>
        
        <div class="content-wrapper">
            <header class="main-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="mb-0 text-dark">Panel de Control: <strong><?php echo $_SESSION['username'] ?? 'Usuario'; ?></strong></h5>
                    <span class="badge" style="background-color: var(--secondary-color);">Plantel Santa Catarina</span>
                </div>
            </header>
            
            <main class="container-fluid py-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="historia-container">
                            <h2 class="fw-bold text-custom-green mb-3">Nuestra Institución</h2>
                            <p class="text-muted" style="line-height: 1.8;">
                                El Colegio de Estudios Científicos y Tecnológicos del Estado de Nuevo León (CECyTE NL) fue fundado el 18 de agosto de 1993. El plantel <strong>Santa Catarina</strong> se distingue por su compromiso con la vanguardia educativa, preparando a los jóvenes para enfrentar los retos tecnológicos y sociales de Nuevo León con una formación técnica de excelencia.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card h-100 card-pilar shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class='bx bx-target-lock text-custom-green fs-1 me-3'></i>
                                    <h4 class="mb-0 fw-bold">Misión</h4>
                                </div>
                                <p class="text-secondary">Formar técnicos profesionales integrales con valores y competencias que les permitan destacar en el ámbito laboral y académico, impulsando el desarrollo del estado.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 card-pilar shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class='bx bx-show-alt text-custom-green fs-1 me-3'></i>
                                    <h4 class="mb-0 fw-bold">Visión</h4>
                                </div>
                                <p class="text-secondary">Ser la institución líder en educación técnica en la región, reconocida por su innovación, calidad educativa y el éxito profesional de sus egresados.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card card-pilar shadow-sm bg-white">
                            <div class="card-body p-4 text-center">
                                <h4 class="fw-bold mb-4 text-custom-green">Valores Institucionales</h4>
                                <div class="row">
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class='bx bx-shield-quarter fs-1 mb-2 text-custom-green'></i>
                                        <p class="fw-bold">Integridad</p>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class='bx bx-heart fs-1 mb-2 text-custom-green'></i>
                                        <p class="fw-bold">Respeto</p>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class='bx bx-star fs-1 mb-2 text-custom-green'></i>
                                        <p class="fw-bold">Excelencia</p>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <i class='bx bx-universal-access fs-1 mb-2 text-custom-green'></i>
                                        <p class="fw-bold">Inclusión</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const btnToggle = document.getElementById('btn-toggle');
        const sidebar = document.getElementById('sidebar');

        // Función para cargar el estado de la sidebar
        function loadSidebarState() {
            const state = localStorage.getItem('sidebar-state');
            if (state === 'collapsed') {
                sidebar.classList.add('collapsed');
                btnToggle.classList.replace('bx-menu', 'bx-menu-alt-right');
            }
        }
        loadSidebarState();

        btnToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            
            if (isCollapsed) {
                btnToggle.classList.replace('bx-menu', 'bx-menu-alt-right');
                localStorage.setItem('sidebar-state', 'collapsed');
            } else {
                btnToggle.classList.replace('bx-menu-alt-right', 'bx-menu');
                localStorage.setItem('sidebar-state', 'expanded');
            }
        });

        // Buscador de la sidebar
        document.getElementById('sidebar-search').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.nav-item').forEach(item => {
                item.style.display = item.innerText.toLowerCase().includes(term) ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>