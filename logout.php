<?php 
session_start();

if (isset($_SESSION['usuario_id'])) {
    // Conectarse a la base de datos
    require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');
    $conexion = new Conexion();
    $conn = $conexion->conectar();

    // Guardar la última conexión
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $conn->prepare("UPDATE usuarios SET ultima_conexion = NOW() WHERE id = ?");
    $stmt->execute([$usuario_id]);
}

// Limpia todas las variables de sesión
$_SESSION = array();

// Borra la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruye la sesión
session_destroy();

// Redirige al inicio o login
header("Location: ./");
exit();
?>
