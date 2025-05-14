<?php
require_once('conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new Conexion();
        $conn = $db->conectar();

        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            header("Location: registrar_usuario.php?error=Las contraseñas no coinciden");
            exit;
        }

        if (empty($_POST['nombres']) || empty($_POST['a_paterno']) || empty($_POST['email']) || empty($password)) {
            header("Location: registrar_usuario.php?error=Todos los campos son obligatorios");
            exit;
        }

        $nombres = htmlspecialchars($_POST['nombres']);
        $a_paterno = htmlspecialchars($_POST['a_paterno']);
        $a_materno = htmlspecialchars($_POST['a_materno']);
        $email = htmlspecialchars($_POST['email']);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $rol_id = 2;
        $estatus_id = 1;

        // Verifica si ya existe un usuario con ese email
        $verificar = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
        $verificar->execute([':email' => $email]);

        if ($verificar->rowCount() > 0) {
            header("Location: registrar_usuario.php?error=El correo ya está registrado");
            exit;
        }

        // Inserta el nuevo usuario
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

        header("Location: ../registro_exitoso.php");
        exit;

    } catch (PDOException $e) {
        die("Error PDO: " . $e->getMessage());
    }
}
?>
