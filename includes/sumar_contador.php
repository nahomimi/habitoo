<?php
require_once('../includes/conexion.php');
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_POST['habito_id'])) {
    header('Location: /habitoo/login.php');
    exit();
}

$conexion = new Conexion();
$conn = $conexion->conectar();

$habito_id = $_POST['habito_id'];

$sql = "UPDATE habitos SET contador = contador + 1 WHERE id = :habito_id AND usuario_id = :usuario_id AND estado = 'activo'";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ':habito_id' => $habito_id,
    ':usuario_id' => $_SESSION['usuario_id']
]);
require_once('../includes/conexion.php');
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_POST['habito_id'])) {
    header('Location: /habitoo/login.php');
    exit();
}

$conexion = new Conexion();
$conn = $conexion->conectar();

$usuario_id = $_SESSION['usuario_id'];
$habito_id = $_POST['habito_id'];

// Verificar si el hábito está activo y pertenece al usuario
$verificar_estado = $conn->prepare("SELECT COUNT(*) FROM habitos WHERE id = :habito_id AND usuario_id = :usuario_id AND estado = 'activo'");
$verificar_estado->execute([
    ':habito_id' => $habito_id,
    ':usuario_id' => $usuario_id
]);
$es_activo = $verificar_estado->fetchColumn();


if ($verificar_estado == 0) {

    // Registrar en la tabla de registros
    $registrar = $conn->prepare("INSERT INTO registros (habito_id, fecha, completado) VALUES (:habito_id, CURDATE(), 1)");
    $registrar->execute([':habito_id' => $habito_id]);
}

header("Location: /habitoo/home/habitos/index.php"); // O la ruta correcta a donde regreses
exit();
?>

