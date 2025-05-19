<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');

  $conexion = new Conexion();
  $conn = $conexion->conectar();

  $habito_id = $_POST['habito_id'];
  $usuario_id = $_POST['usuario_id'];
  $dias_completados = $_POST['dias_completados'] ?? [];

  // Guardar los días nuevos
  foreach ($dias_completados as $dia_id) {
    $stmt = $conn->prepare("INSERT IGNORE INTO habitos_dias_check (habito_id, dia_id, usuario_id) VALUES (?, ?, ?)");
    $stmt->execute([$habito_id, $dia_id, $usuario_id]);
  }

  // Verificar si ya se marcaron todos los días
  $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM habitos_dias WHERE habito_id = ?");
  $stmtTotal->execute([$habito_id]);
  $total_dias = $stmtTotal->fetchColumn();

  $stmtMarcados = $conn->prepare("SELECT COUNT(*) FROM habitos_dias_check WHERE habito_id = ? AND usuario_id = ?");
  $stmtMarcados->execute([$habito_id, $usuario_id]);
  $total_marcados = $stmtMarcados->fetchColumn();

  if ($total_dias > 0 && $total_marcados >= $total_dias) {
    // Registrar como completado
    $stmtRegistro = $conn->prepare("INSERT INTO registros (habito_id, fecha, completado) VALUES (?, CURDATE(), 1)");
    $stmtRegistro->execute([$habito_id]);
  }

  header("Location: /habitoo/home/habitos/index.php");
  exit();
}
?>
