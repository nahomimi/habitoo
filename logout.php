<?php 
session_start();

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
