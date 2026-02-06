<?php
// Conexi�n a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexi�n fallida: " . $conn->connect_error);
}

// Recuperar datos del formulario
$matriculaAlumno = $_POST['matriculaAlumno'];
$apellidoPaterno = $_POST['apellidoPaterno'];
$apellidoMaterno = $_POST['apellidoMaterno'];
$nombre = $_POST['nombre'];
$fechaNacimiento = $_POST['fechaNacimiento'];
$id_genero = $_POST['id_genero'];
$rfc = $_POST['rfc'];
$id_nacionalidad = $_POST['id_nacionalidad'];
$id_estadoNacimiento = $_POST['id_estadoNacimiento'];
$direccion = $_POST['direccion'];
$numCelular = $_POST['numCelular'];
$telefonoEmergencia = $_POST['telefonoEmergencia'];
$mailInstitucional = $_POST['mailInstitucional'];
$mailPersonal = $_POST['mailPersonal'];
$id_discapacidad = $_POST['id_discapacidad'];
//<!-- $rutaImagen = $_POST['rutaImagen'];-->

// Insertar datos en la tabla alumnos
$sql = "INSERT INTO alumnos (matriculaAlumno, apellidoPaterno, apellidoMaterno, nombre, fechaNacimiento, id_genero, rfc, id_nacionalidad, id_estadoNacimiento, direccion, numCelular, telefonoEmergencia, mailInstitucional, mailPersonal, id_discapacidad)
VALUES ('$matriculaAlumno', '$apellidoPaterno', '$apellidoMaterno', '$nombre', '$fechaNacimiento', '$id_genero', '$rfc', '$id_nacionalidad', '$id_estadoNacimiento', '$direccion', '$numCelular', '$telefonoEmergencia', '$mailInstitucional', '$mailPersonal', '$id_discapacidad')";

if ($conn->query($sql) === TRUE) {
    echo "Alumno registrado correctamente.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>