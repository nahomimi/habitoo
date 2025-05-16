<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');
session_start();

// Si ya está logueado, redirigir al usuario a su página correspondiente
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol_id'] == 1) {
        header("Location: /habitoo/home/usuarios/index.php");
        exit();
    } elseif ($_SESSION['rol_id'] == 2) {
        header("Location: /habitoo/home/index.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = 'Completa todos los campos';
    }

    if (empty($errors)) {
        try {
            $conexion = new Conexion();
            $pdo = $conexion->conectar();

            $sql = "SELECT * FROM usuarios WHERE email = :email AND estatus_id = 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre_completo'] = $usuario['nombres'] . " " . $usuario['a_paterno'] . " " . $usuario['a_materno'];
                $_SESSION['rol_id'] = $usuario['rol_id'];

                // Redirigir según rol
                if ($usuario['rol_id'] == 1) {
                    header("Location: /habitoo/home/usuarios/index.php");
                } elseif ($usuario['rol_id'] == 2) {
                    header("Location: /habitoo/home/index.php");
                } else {
                    $errors[] = 'Rol no válido';
                }
                exit();
            } else {
                $errors[] = 'Credenciales incorrectas o usuario inactivo';
            }

        } catch (Exception $e) {
            $errors[] = 'Error de conexión a la base de datos';
        }
    }

    // Mostrar errores si existen
    if (!empty($errors)) {
        $mensaje = implode('\n', $errors);
        $_SESSION['error_message'] = $mensaje;
        header("Location: /habitoo/login.php");
        exit();
        }

    } else {
        $_SESSION['error_message'] = 'Método no permitido';
        header("Location: /habitoo/login.php");
        exit();
    exit();
}
?>
