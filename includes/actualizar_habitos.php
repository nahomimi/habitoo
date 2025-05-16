<?php
if (session_status() == PHP_SESSION_NONE) {
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

// Validar datos recibidos por POST
if (
  !isset($_POST['id'], $_POST['nombre'], $_POST['frecuencia_id']) ||
  empty($_POST['id']) || empty(trim($_POST['nombre'])) || empty($_POST['frecuencia_id'])
) {
  header("Location: /habitoo/home/habitos.php?error=" . urlencode("Faltan datos obligatorios"));
  exit();
}

$habito_id = (int) $_POST['id'];
$nombre = trim($_POST['nombre']);
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
$frecuencia_id = (int) $_POST['frecuencia_id'];
$meta_semanal = isset($_POST['meta_semanal']) && is_numeric($_POST['meta_semanal']) ? (int) $_POST['meta_semanal'] : null;

// Verificar que el hábito pertenece al usuario
$sql_check = "SELECT id FROM habitos WHERE id = :id AND usuario_id = :usuario_id";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->execute([':id' => $habito_id, ':usuario_id' => $usuario_id]);
if ($stmt_check->rowCount() === 0) {
  header("Location: /habitoo/home/habitos.php?error=" . urlencode("No tienes permiso para editar este hábito"));
  exit();
}

// Actualizar el hábito
$sql_update = "UPDATE habitos SET
                 nombre = :nombre,
                 descripcion = :descripcion,
                 frecuencia_id = :frecuencia_id,
                 meta_semanal = :meta_semanal,
                 fecha_actualizacion = NOW()
               WHERE id = :id";

$stmt_update = $conn->prepare($sql_update);

$stmt_update->bindValue(':nombre', $nombre, PDO::PARAM_STR);
$stmt_update->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
$stmt_update->bindValue(':frecuencia_id', $frecuencia_id, PDO::PARAM_INT);
if ($meta_semanal !== null) {
  $stmt_update->bindValue(':meta_semanal', $meta_semanal, PDO::PARAM_INT);
} else {
  $stmt_update->bindValue(':meta_semanal', null, PDO::PARAM_NULL);
}
$stmt_update->bindValue(':id', $habito_id, PDO::PARAM_INT);

if ($stmt_update->execute()) {
  header("Location: /habitoo/home/habitos.php?msg=" . urlencode("Hábito actualizado correctamente"));
  exit();
} else {
  header("Location: /habitoo/home/habitos.php?error=" . urlencode("Error al actualizar el hábito"));
  exit();
}
