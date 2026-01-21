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

// Obtener la matrícula y la imagen
$matricula = $_POST['matriculaAlumno'];
$foto = $_FILES['foto'];

// Verificar si se subió un archivo
if ($foto['error'] === UPLOAD_ERR_OK) {
    $nombreArchivo = basename($foto['name']);
    $rutaTemporal = $foto['tmp_name'];
    $rutaDestino = 'img/' . $nombreArchivo;

    // Mover el archivo subido a la carpeta de imágenes
    if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
        // Actualizar la base de datos con la ruta de la imagen
        $sql = "UPDATE alumnos SET rutaImagen = '$rutaDestino' WHERE matriculaAlumno = '$matricula'";

        if ($conn->query($sql) === TRUE) {
            echo "La imagen se ha subido y la base de datos se ha actualizado correctamente.";
        } else {
            echo "Error al actualizar la base de datos: " . $conn->error;
        }
    } else {
        echo "Error al mover el archivo subido.";
    }
} else {
    echo "Error al subir el archivo.";
}

// Cerrar la conexión
$conn->close();
?>