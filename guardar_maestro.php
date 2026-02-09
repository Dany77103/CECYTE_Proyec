<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cecyte_sc";

$conn = new mysqli($servername, $username, $password, $dbname);

// Configurar charset para evitar problemas con acentos y Ñ
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recuperar datos del formulario
$numEmpleado = $_POST['numEmpleado'];
$apellidoPaterno = $_POST['apellidoPaterno'];
$apellidoMaterno = $_POST['apellidoMaterno'];
$nombre = $_POST['nombre'];
$fechaNacimiento = $_POST['fechaNacimiento'];
$id_genero = $_POST['id_genero'];
$rfc = $_POST['rfc'];
$curp = $_POST['curp'];
$id_nacionalidad = $_POST['id_nacionalidad'];
$id_estadoNacimiento = $_POST['id_estadoNacimiento'];
$direccion = $_POST['direccion'];
$numCelular = $_POST['numCelular'];
$telefonoEmergencia = $_POST['telefonoEmergencia'];
$mailInstitucional = $_POST['mailInstitucional'];
$mailPersonal = $_POST['mailPersonal'];

// NUEVO: Recuperar el Rol seleccionado
$id_rol = $_POST['id_rol']; 

// Insertar datos en la tabla maestros (incluyendo id_rol)
$sql = "INSERT INTO maestros (
    numEmpleado, 
    apellidoPaterno, 
    apellidoMaterno, 
    nombre, 
    fechaNacimiento, 
    id_genero, 
    rfc, 
    curp, 
    id_nacionalidad, 
    id_estadoNacimiento, 
    direccion, 
    numCelular, 
    telefonoEmergencia, 
    mailInstitucional, 
    mailPersonal,
    id_rol
) VALUES (
    '$numEmpleado', 
    '$apellidoPaterno', 
    '$apellidoMaterno', 
    '$nombre', 
    '$fechaNacimiento', 
    '$id_genero', 
    '$rfc', 
    '$curp', 
    '$id_nacionalidad', 
    '$id_estadoNacimiento', 
    '$direccion', 
    '$numCelular', 
    '$telefonoEmergencia', 
    '$mailInstitucional', 
    '$mailPersonal',
    '$id_rol'
)";

if ($conn->query($sql) === TRUE) {
    echo "Colaborador registrado correctamente.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>