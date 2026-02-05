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
    die("Error de conexión: " . $e->getMessage());
}

session_start();

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
    <title>CECYTE - Sistema de Registro</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="img/x-icon">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #064e3b;    
            --secondary-color: #065f46;  
            --accent-color: #10b981;     
            --hover-color: #34d399;      
            --bg-light: #f0fdf4;         
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            margin: 0;
        }

        .main-header {
            background: #fff;
            padding: 15px 40px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--accent-color);
        }

        .btn-back {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 10px;
            transition: var(--transition);
            background: rgba(16, 185, 129, 0.1);
        }

        .btn-back:hover {
            background: var(--primary-color);
            color: #fff;
            transform: translateX(-5px);
        }

        .registration-container { padding: 40px; }

        .stat-box {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            border-left: 4px solid var(--accent-color);
            transition: var(--transition);
        }
        
        .stat-box:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 15px rgba(0,0,0,0.05);
        }

        .stat-icon { font-size: 2.5rem; color: var(--primary-color); opacity: 0.9; }

        .registration-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .registration-card {
            background: #fff;
            border: none;
            border-radius: 20px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border-bottom: 4px solid transparent;
        }

        .registration-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 6px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .registration-card:hover { 
            transform: translateY(-12px); 
            border-bottom: 4px solid var(--accent-color);
        }

        .card-icon-wrapper {
            width: 90px; height: 90px;
            background: var(--bg-light);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 25px;
            font-size: 2.2rem;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .registration-card:hover .card-icon-wrapper {
            background: var(--primary-color); 
            color: #fff;
            transform: rotateY(180deg);
        }

        .btn-action {
            display: inline-block;
            text-decoration: none;
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 50px;
            transition: var(--transition);
        }

        .btn-action:hover {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 5px 15px rgba(6, 78, 59, 0.3);
        }

        .text-custom-green { color: var(--primary-color); }

    </style>
</head>
<body>

    <div class="content-wrapper">
        <header class="main-header d-flex justify-content-between align-items-center">
            <a href="main.php" class="btn-back">
                <i class='bx bx-left-arrow-alt' style="font-size: 1.5rem;"></i>
                <span>Volver al Menú</span>
            </a>

            <h4 class="fw-bold mb-0 d-none d-md-block text-custom-green">Gestión de Registros Académicos</h4>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-lg-block">
                    <p class="mb-0 small fw-bold text-dark"><?php echo $_SESSION['username'] ?? 'Admin'; ?></p>
                    <small class="text-muted">Administrador</small>
                </div>
                <img src="https://ui-avatars.com/api/?name=Admin&background=064e3b&color=fff" class="rounded-circle border border-2 border-success" width="40" alt="Avatar">
            </div>
        </header>

        <main class="registration-container">
            <div class="row g-4 mb-5">
                <?php
                $stats = [
                    ['Alumnos', 'alumnos', 'bx-user-voice'],
                    ['Docentes', 'maestros', 'bx-briefcase-alt-2'],
                    ['Notas', 'calificaciones', 'bx-spreadsheet'],
                    ['Grupos', 'alumnos', 'bx-group'] 
                ];
                
                foreach($stats as $s):
                    try {
                        $stmt = $con->query("SELECT COUNT(*) FROM $s[1]");
                        $count = $stmt->fetchColumn();
                    } catch (PDOException $e) { $count = 0; }
                ?>
                <div class="col-md-3">
                    <div class="stat-box">
                        <i class='bx <?php echo $s[2]; ?> stat-icon'></i>
                        <div>
                            <h3 class="fw-bold mb-0 text-custom-green"><?php echo $count; ?></h3>
                            <p class="text-muted mb-0 small text-uppercase fw-semibold"><?php echo $s[0]; ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="registration-grid">
                <div class="registration-card">
                    <div class="card-icon-wrapper"><i class="fas fa-user-graduate"></i></div>
                    <h5 class="fw-bold">Alumnos</h5>
                    <p class="text-muted small mb-4">Gestión completa de inscripción y expedientes de alumnos.</p>
                    <a href="registroalumnos.php" class="btn btn-action w-100">Abrir Módulo</a>
                </div>

                <div class="registration-card">
                    <div class="card-icon-wrapper"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h5 class="fw-bold">Personal</h5>
                    <p class="text-muted small mb-4">Administración de plantilla docente y administrativa del plantel.</p>
                    <a href="registropersonal.php" class="btn btn-action w-100">Gestionar Personal</a>
                </div>

                <div class="registration-card">
                    <div class="card-icon-wrapper"><i class="fas fa-file-invoice"></i></div>
                    <h5 class="fw-bold">Calificaciones</h5>
                    <p class="text-muted small mb-4">Registro y consulta de evaluaciones por periodo semestral.</p>
                   <a href="registrocalificaciones.php" class="btn btn-action w-100">Gestionar Personal</a>
                </div>

                <div class="registration-card">
                    <div class="card-icon-wrapper"><i class="fas fa-calendar-alt"></i></div>
                    <h5 class="fw-bold">Horarios</h5>
                    <p class="text-muted small mb-4">Asignación de carga horaria y disponibilidad de aulas.</p>
                    <a href="registrohorarios.php" class="btn btn-action w-100">Gestionar Personal</a>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>