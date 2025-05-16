<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    header("Location: /habitoo/login.php?error=" . urlencode("Acceso restringido para administradores"));
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validación de campos obligatorios
    if (empty($_POST['id']) || empty($_POST['nombres']) || empty($_POST['a_paterno']) || empty($_POST['email']) || empty($_POST['rol_id']) || empty($_POST['estatus_id'])) {
        header("Location: editar.php?id=" . urlencode($_POST['id']) . "&error=" . urlencode("Por favor completa todos los campos obligatorios"));
        exit();
    }

    try {
        $db = new Conexion();
        $conn = $db->conectar();

        // Sanitización
        $id = $_POST['id'];
        $nombres = htmlspecialchars($_POST['nombres']);
        $a_paterno = htmlspecialchars($_POST['a_paterno']);
        $a_materno = htmlspecialchars($_POST['a_materno'] ?? '');
        $email = htmlspecialchars($_POST['email']);
        $telefono = htmlspecialchars($_POST['telefono'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
        $frase_motivacional = htmlspecialchars($_POST['frase_motivacional'] ?? '');
        $rol_id = $_POST['rol_id'];
        $estatus_id = $_POST['estatus_id'];

        // Consulta
        $sql = "UPDATE usuarios SET 
                    nombres = :nombres,
                    a_paterno = :a_paterno,
                    a_materno = :a_materno,
                    email = :email,
                    telefono = :telefono,
                    fecha_nacimiento = :fecha_nacimiento,
                    frase_motivacional = :frase_motivacional,
                    rol_id = :rol_id,
                    estatus_id = :estatus_id
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nombres' => $nombres,
            ':a_paterno' => $a_paterno,
            ':a_materno' => $a_materno,
            ':email' => $email,
            ':telefono' => $telefono,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':frase_motivacional' => $frase_motivacional,
            ':rol_id' => $rol_id,
            ':estatus_id' => $estatus_id,
            ':id' => $id
        ]);

        header("Location: /habitoo/home/usuarios/index.php?success=" . urlencode("Usuario actualizado correctamente"));
        exit();

    } catch (PDOException $e) {
        header("Location: editar.php?id=" . urlencode($_POST['id']) . "&error=" . urlencode("Error al actualizar el usuario"));
        exit();
    }

} else {
    header("Location: /habitoo/index.php?error=" . urlencode("Método no permitido"));
    exit();
}
?>
