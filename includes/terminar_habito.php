<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['usuario_id'])) {
  header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
  exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');

$conexion = new Conexion();
$conn = $conexion->conectar();

$usuario_id = $_SESSION['usuario_id'];
$habito_id = $_GET['habito_id'] ?? null;

if (!$habito_id) {
  header("Location: /habitoo/home/habitos/index.php?error=Hábito no especificado");
  exit();
}

// Verifica si el hábito pertenece al usuario
$sqlCheck = "SELECT id FROM habitos WHERE id = :habito_id AND usuario_id = :usuario_id";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->execute([
  ':habito_id' => $habito_id,
  ':usuario_id' => $usuario_id
]);

if (!$stmtCheck->fetch()) {
  header("Location: /habitoo/home/habitos/index.php?error=Hábito no encontrado o no autorizado");
  exit();
}

// Cambiar el estado a 'inactivo'
$sql = "UPDATE habitos SET estado = 'inactivo' WHERE id = :habito_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':habito_id' => $habito_id]);

// Redirigir a metas/index.php
header("Location: /habitoo/home/metas/index.php?mensaje=Hábito terminado");
exit();
