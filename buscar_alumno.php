<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la matrícula del alumno desde la solicitud GET
$matriculaAlumno = isset($_GET['matricula']) ? $_GET['matricula'] : '';

if (empty($matriculaAlumno)) {
    // Si no se proporciona una matrícula, devolver un error
    echo json_encode(["error" => "No se proporcionó una matrícula"]);
    exit;
}

// Consulta para buscar el alumno por matrícula
$query = "
SELECT * 
FROM alumnos a
INNER JOIN generos g ON g.id_genero=a.id_genero
LEFT JOIN nacionalidades n ON n.id_nacionalidad=a.id_nacionalidad
LEFT JOIN estadonacimiento en ON en.id_estadoNacimiento=a.id_estadoNacimiento
LEFT JOIN discapacidades di ON di.id_discapacidad=a.id_discapacidad
WHERE a.matriculaAlumno = '$matriculaAlumno'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Si se encontró el alumno, devolver los datos en formato JSON
    $alumno = $result->fetch_assoc();
    echo json_encode($alumno);
} else {
    // Si no se encontró el alumno, devolver un mensaje de error
    echo json_encode(["error" => "No se encontró ningún alumno con la matrícula proporcionada"]);
}

$conn->close();
?>