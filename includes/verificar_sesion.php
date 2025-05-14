<?php
require_once('conexion.php');
session_start();

// Si ya está logueado, redirigir al usuario a su página correspondiente
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol_id'] == 1) {
        header("Location: ../home/usuarios/index.php");
        exit();
    } elseif ($_SESSION['rol_id'] == 2) {
        header("Location: ../home/index.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        
        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $conexion = new Conexion();
            $pdo = $conexion->conectar();

            // Solo usuarios con estatus activo
            $sql = "SELECT * FROM usuarios WHERE email = :email AND estatus_id = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre_completo'] = $usuario['nombres'] . " " . $usuario['a_paterno'] . " " . $usuario['a_materno'];
                $_SESSION['rol_id'] = $usuario['rol_id'];

                // Redirigir a las páginas según el rol
                if ($usuario['rol_id'] == 1) {
                    header("Location: ../home/usuarios/index.php");
                } elseif ($usuario['rol_id'] == 2) {
                    header("Location: ../home/index.php");
                } else {
                    header("Location: ../login.php?error=" . urlencode("Rol no válido"));
                }
                exit();
            } else {
                header("Location: ../login.php?error=" . urlencode("Credenciales incorrectas o usuario inactivo"));
                exit();
            }

        } catch (Exception $e) {
            header("Location: ../login.php?error=" . urlencode("Error de conexión"));
            exit();
        }

    } else {
        header("Location: ../login.php?error=" . urlencode("Completa todos los campos"));
        exit();
    }

} else {
    header("Location: ../login.php?error=" . urlencode("Método no permitido"));
    exit();
}
