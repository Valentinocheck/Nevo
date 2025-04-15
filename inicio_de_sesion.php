<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$conexion = new mysqli("localhost", "user", "asd1234$$", "nevo");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Verificar si se mandó el formulario y es para iniciar sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'iniciar_sesion') {
    $correo = trim($_POST['Correo_electronico']);
    $contraseña = $_POST['contraseña'];

    // Buscar al usuario por correo
    $sql = "SELECT Contraseña FROM usuarios WHERE CorreoElectronico = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Verificar si el usuario existe
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $contraseña_en_bd = $usuario['Contraseña'];

        // Comprobamos la contraseña (si usás password_hash, reemplazá esta línea)
        if ($contraseña === $contraseña_en_bd) {
            // Inicio exitoso
            header("Location: bienvenido.html");
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Correo no registrado'); window.history.back();</script>";
    }

    $stmt->close();
}

$conexion->close();
?>
