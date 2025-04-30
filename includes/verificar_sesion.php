<?php 
session_start();
require 'conexion.php'; // No debes incluir el .sql

// Asegurarnos que llegan los datos
if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buscar el usuario
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Guardamos el resultado en $usuario

    // Verificar si encontró usuario y contraseña es correcta
    if ($usuario && password_verify($password, $usuario['password'])) {
        echo "Holita, " . htmlspecialchars($usuario['nombres']); // htmlspecialchars para evitar errores raros
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre_completo'] = $usuario['nombres'] . " " . $usuario['a_paterno'] . " " . $usuario['a_materno'];
        $_SESSION['rol_id'] = $usuario['rol_id'];

        header("Location: ../home/index.php");
        exit();
    } else {
        echo "Upsi";
    }
} else {
    echo "Por favor llena todos los campos.";
}
?>
