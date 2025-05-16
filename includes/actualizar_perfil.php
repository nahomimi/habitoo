<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: /habitoo/login.php?error=" . urlencode("Acceso restringido a usuarios"));
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validación de campos obligatorios
    if (empty($_POST['nombres']) || empty($_POST['a_paterno']) || empty($_POST['email'])) {
        header("Location: /habitoo/home/perfil.php?error=" . urlencode("Por favor completa todos los campos obligatorios"));
        exit();
    }

    try {
        $db = new Conexion();
        $conn = $db->conectar();

        // Sanitización
        $id = $_SESSION['usuario_id'];
        $nombres = htmlspecialchars($_POST['nombres']);
        $a_paterno = htmlspecialchars($_POST['a_paterno']);
        $a_materno = htmlspecialchars($_POST['a_materno'] ?? '');
        $email = htmlspecialchars($_POST['email']);
        $telefono = htmlspecialchars($_POST['telefono'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
        $frase_motivacional = htmlspecialchars($_POST['frase_motivacional'] ?? '');

        $avatar_url = null;

        // Manejo de imagen
        if (!empty($_FILES['avatar']['name'])) {
            $archivo = $_FILES['avatar'];
            $nombreArchivo = basename($archivo['name']);
            $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png'];

            if (in_array($extension, $permitidos) && $archivo['size'] <= 2 * 1024 * 1024) {
                $nombreUnico = uniqid('perfil_', true) . '.' . $extension;
                $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT'] . "/habitoo/assets/uploads/" . $nombreUnico;

                if (move_uploaded_file($archivo['tmp_name'], $rutaAbsoluta)) {
                    $avatar_url = "assets/uploads/" . $nombreUnico;
                }
            } else {
                header("Location: /habitoo/home/perfil.php?error=" . urlencode("La imagen debe ser JPG o PNG y pesar máximo 2MB"));
                exit();
            }
        }

        // SQL base
        $sql = "UPDATE usuarios SET 
                    nombres = :nombres,
                    a_paterno = :a_paterno,
                    a_materno = :a_materno,
                    email = :email,
                    telefono = :telefono,
                    fecha_nacimiento = :fecha_nacimiento,
                    frase_motivacional = :frase_motivacional";

        if ($avatar_url !== null) {
            $sql .= ", avatar_url = :avatar_url";
        }

        $sql .= " WHERE id = :id";

        $stmt = $conn->prepare($sql);

        $params = [
            ':nombres' => $nombres,
            ':a_paterno' => $a_paterno,
            ':a_materno' => $a_materno,
            ':email' => $email,
            ':telefono' => $telefono,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':frase_motivacional' => $frase_motivacional,
            ':id' => $id
        ];

        if ($avatar_url !== null) {
            $params[':avatar_url'] = $avatar_url;
        }

        $stmt->execute($params);

        header("Location: /habitoo/home/perfil.php?success=" . urlencode("Perfil actualizado correctamente"));
        exit();

    } catch (PDOException $e) {
        header("Location: /habitoo/home/perfil.php?error=" . urlencode("Error al actualizar perfil"));
        exit();
    }

} else {
    header("Location: /habitoo/home/perfil.php?error=" . urlencode("Método no permitido"));
    exit();
}
