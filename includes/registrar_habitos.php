<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();
$conn->exec("SET NAMES utf8"); // Asegura codificación correcta

// Obtener datos del formulario
$usuario_id = $_SESSION['usuario_id'];
$nombre = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$frecuencia_id = $_POST['frecuencia_id'] ?? null;
$meta_semanal = $_POST['meta_semanal'] ?? null;
$dias = $_POST['dias'] ?? [];

if (empty($nombre) || empty($frecuencia_id)) {
    header("Location: /habitoo/habitos/crear.php?error=" . urlencode("Faltan campos obligatorios"));
    exit();
}

try {
    // Iniciar transacción
    $conn->beginTransaction();

    // Insertar hábito
    $stmt = $conn->prepare("INSERT INTO habitos (usuario_id, nombre, descripcion, frecuencia_id, meta_semanal) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $nombre, $descripcion, $frecuencia_id, $meta_semanal]);

    // Obtener ID del hábito insertado
    $habito_id = $conn->lastInsertId();

    // Insertar días seleccionados (si hay)
    if (!empty($dias)) {
        $stmt_dia = $conn->prepare("INSERT INTO habitos_dias (habito_id, dia_id) VALUES (?, ?)");
        foreach ($dias as $dia_id) {
            $stmt_dia->execute([$habito_id, $dia_id]);
        }
    }

    $conn->commit();

    // 
    header("Location: /habitoo/home/habitos/registrar_habitos.php?success=" . urlencode("Hábito registrado con éxito"));
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Error al registrar hábito: " . $e->getMessage());

    // Redirigir con error
    header("Location: /habitoo/habitos/crear.php?error=" . urlencode("Error al guardar el hábito"));
    exit();
}
?>
