<?php
// Conexion.php - Conexión a la base de datos
$conexion = new mysqli("localhost", "user", "asd1234$$", "nevo");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Verificamos la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Función para registrar un nuevo usuario
function registrar_usuario($conexion, $usuario, $contraseña) {
    // Ciframos la contraseña
    $contraseña_cifrada = password_hash($contraseña, PASSWORD_DEFAULT);

    // Insertamos el nuevo usuario en la base de datos
    $sql = $conexion->prepare("INSERT INTO usuarios (usuario, contraseña) VALUES (?, ?)");
    $sql->bind_param("ss", $usuario, $contraseña_cifrada);
    if ($sql->execute()) {
        echo "Usuario registrado correctamente.";
    } else {
        echo "Error al registrar el usuario: " . $conexion->error;
    }
    $sql->close();
}

// Función para iniciar sesión
function iniciar_sesion($conexion, $usuario, $contraseña) {
    // Consulta preparada para obtener la contraseña cifrada
    $sql = $conexion->prepare("SELECT contraseña FROM usuarios WHERE usuario = ?");
    $sql->bind_param("s", $usuario);
    $sql->execute();
    $resultado = $sql->get_result();
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        // Verificamos la contraseña ingresada
        if ($contraseña == $fila['contraseña']) {
            echo "Inicio de sesión exitoso. ¡Bienvenido, $usuario!";
            header("Location: bienvenido.html");
            // Puedes redirigir con header("Location: pagina_principal.php");
        } else {
            echo "Nombre de usuario o contraseña incorrectos.";
        }
    } else {
        echo $contraseña;
        echo "Nombre de usuario o contraseña incorrectos.";
    }

    $sql->close();
}

// Verificamos si estamos en el formulario de registro o login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
        $usuario = $_POST['usuario'];
        $contraseña = $_POST['contraseña'];

        if ($accion === 'registrar') {
            registrar_usuario($conexion, $usuario, $contraseña);
        } elseif ($accion === 'iniciar_sesion') {
            iniciar_sesion($conexion, $usuario, $contraseña);
        }
    }
}

$conexion->close();
?>
