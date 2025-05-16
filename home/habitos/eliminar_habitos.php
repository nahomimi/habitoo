<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Solo acceso a usuarios autenticados (puedes ajustar si quieres solo admin)
if (!isset($_SESSION['usuario_id'])) {
  header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
  exit();
}

// Mostrar errores (solo en desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validar ID recibido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("ID de hábito inválido.");
}

$id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

try {
  $conexion = new Conexion();
  $conn = $conexion->conectar();

  // Verificar si el hábito existe y pertenece al usuario actual
  $verificar = $conn->prepare("SELECT id FROM habitos WHERE id = :id AND usuario_id = :usuario_id");
  $verificar->execute([':id' => $id, ':usuario_id' => $usuario_id]);

  if ($verificar->rowCount() === 0) {
    die("Hábito no encontrado o no tienes permiso para eliminarlo.");
  }

  // Eliminar hábito
  $eliminar = $conn->prepare("DELETE FROM habitos WHERE id = :id AND usuario_id = :usuario_id");
  $eliminar->execute([':id' => $id, ':usuario_id' => $usuario_id]);

  // Redirigir con mensaje de éxito
  header("Location: /habitoo/home/habitos/index.php?exito=" . urlencode("Hábito eliminado correctamente"));
  exit;

} catch (PDOException $e) {
  die("Error al eliminar el hábito: " . $e->getMessage());
}
?>
