<?php
session_start();
include 'conexion.php'; // Incluir la conexión a la base de datos

// Obtener datos del formulario
$username = $_POST['username'];
$password = $_POST['password'];

// Validar que los campos no estén vacíos
if (empty($username) || empty($password)) {
    die("Por favor, completa todos los campos.");
}

// Consultar la base de datos para obtener el usuario
$sql = "SELECT id, username, password FROM usuarios WHERE username = :username";
$stmt = $con->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->execute();

// Verificar si el usuario existe
if ($stmt->rowCount() == 1) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar la contraseña
    if (password_verify($password, $user['password'])) {
        // Iniciar sesión
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];

        // Redirigir a la página principal
        header('Location: main.php');
        exit;
    } else {
        // Contraseña incorrecta
        die("Usuario o contraseña incorrectos. <a href='index.php'>Intentar de nuevo</a>");
    }
} else {
    // Usuario no encontrado
    die("Usuario o contraseña incorrectos. <a href='index.php'>Intentar de nuevo</a>");
}


if ($stmt->rowCount() == 1) {
    echo "Usuario encontrado.<br>";
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($user); // Mostrar detalles del usuario
} else {
    echo "Usuario no encontrado.<br>";
}


?>