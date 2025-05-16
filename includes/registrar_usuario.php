<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new Conexion();
        $conn = $db->conectar();

        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validaciones
        if ($password !== $confirm_password) {
            header("Location: /habitoo/sign_up.php?error=" . urlencode("Las contraseñas no coinciden"));
            exit;
        }

        if (empty($_POST['nombres']) || empty($_POST['a_paterno']) || empty($_POST['email']) || empty($password)) {
            header("Location: /habitoo/sign_up.php?error=" . urlencode("Todos los campos obligatorios deben llenarse"));
            exit;
        }

        $nombres = htmlspecialchars($_POST['nombres']);
        $a_paterno = htmlspecialchars($_POST['a_paterno']);
        $a_materno = htmlspecialchars($_POST['a_materno']);
        $email = htmlspecialchars($_POST['email']);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $rol_id = 2;
        $estatus_id = 1;

        // Verificar si el correo ya existe
        $verificar = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
        $verificar->execute([':email' => $email]);

        if ($verificar->rowCount() > 0) {
            header("Location: /habitoo/sign_up.php?error=" . urlencode("El correo ya está registrado"));
            exit;
        }

        // Insertar usuario
        $sql = "INSERT INTO usuarios (nombres, a_paterno, a_materno, email, password, rol_id, estatus_id)
                VALUES (:nombres, :a_paterno, :a_materno, :email, :password, :rol_id, :estatus_id)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombres' => $nombres,
            ':a_paterno' => $a_paterno,
            ':a_materno' => $a_materno,
            ':email' => $email,
            ':password' => $passwordHash,
            ':rol_id' => $rol_id,
            ':estatus_id' => $estatus_id
        ]);

        // Redirigir a una página de éxito o al login
        header("Location: /habitoo/registro_exitoso.php");
        exit;

    } catch (PDOException $e) {
        header("Location: /habitoo/sign_up.php?error=" . urlencode("Error de base de datos: " . $e->getMessage()));
        exit;
    }
}
?>
