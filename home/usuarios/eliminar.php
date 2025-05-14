<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Solo acceso a administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
  header("Location: /habitoo/login.php?error=" . urlencode("Acceso restringido para administradores"));
  exit();
}

// Mostrar errores (solo en desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Validar ID recibido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("ID de usuario inválido.");
}

$id = intval($_GET['id']);

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

try {
  $conexion = new Conexion();
  $conn = $conexion->conectar();

  // Verificar si el usuario existe
  $verificar = $conn->prepare("SELECT id FROM usuarios WHERE id = :id");
  $verificar->execute([':id' => $id]);

  if ($verificar->rowCount() === 0) {
    die("Usuario no encontrado.");
  }

  // Eliminar usuario
  $eliminar = $conn->prepare("DELETE FROM usuarios WHERE id = :id");
  $eliminar->execute([':id' => $id]);

  // Redirigir con mensaje de éxito
  header("Location: /habitoo/home/usuarios/index.php?exito=" . urlencode("Usuario eliminado correctamente"));
  exit;

} catch (PDOException $e) {
  die("Error al eliminar el usuario: " . $e->getMessage());
}
