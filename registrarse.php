<?php
// Conectar a la base de datos
$conexion = new mysqli("localhost", "user", "asd1234$$", "nevo");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar que se haya enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "pasando por aca";
    $correo = trim($_POST["Correo_electronico"]);
    $usuario = trim($_POST["usuario"]);
    $contraseña = $_POST["contraseña"];
    $confirmar = $_POST["Confirmar_contraseña"];

    // Verificar que las contraseñas coincidan
    if ($contraseña !== $confirmar) {
        echo "<script>alert('Las contraseñas no coinciden'); window.history.back();</script>";
        exit();
    }

    // Verificar si el correo o el usuario ya existen
    $sql_check = "SELECT * FROM usuarios WHERE CorreoElectronico = ? OR Usuario = ?";
    $stmt = $conexion->prepare($sql_check);
    $stmt->bind_param("ss", $correo, $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "<script>alert('El correo o el usuario ya están registrados'); window.history.back();</script>";
        exit();
    }

    // Encriptar la contraseña
    // $hash = password_hash($contraseña, PASSWORD_DEFAULT);
    $hash = $contraseña;

    // Insertar nuevo usuario
    $sql_insert = "INSERT INTO usuarios (CorreoElectronico, Usuario, Contraseña, PrimeraSesion) VALUES (?, ?, ?, CURDATE())";
    $stmt = $conexion->prepare($sql_insert);
    $stmt->bind_param("sss", $correo, $usuario, $hash);

    if ($stmt->execute()) {
        echo "<script>alert('Registro exitoso. ¡Bienvenido a Nevo!'); window.location.href='bienvenido.html';</script>";
        exit();
    } else {
        echo "Error al registrar: " . $conexion->error;
    }

    $stmt->close();
}

$conexion->close();
?>
