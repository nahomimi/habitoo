<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');

if (!isset($_SESSION['usuario_id'])) exit();

$usuario_id = $_SESSION['usuario_id'];
$habito_id = $_POST['habito_id'] ?? null;
$dias = $_POST['dia_id'] ?? [];

$conn = (new Conexion())->conectar();

if ($habito_id && !empty($dias)) {
  // Limpiar días actuales del hábito para este usuario
  $delete = $conn->prepare("DELETE FROM habitos_dias_check WHERE habito_id = ? AND usuario_id = ?");
  $delete->execute([$habito_id, $usuario_id]);

  // Insertar los días marcados
  $insert = $conn->prepare("INSERT INTO habitos_dias_check (habito_id, dia_id, usuario_id) VALUES (?, ?, ?)");
  foreach ($dias as $dia_id) {
    $insert->execute([$habito_id, $dia_id, $usuario_id]);
  }

  // Obtener los días objetivo del hábito (de la tabla habitos_dias)
  $sqlDiasObjetivo = $conn->prepare("SELECT COUNT(*) FROM habitos_dias WHERE habito_id = ?");
  $sqlDiasObjetivo->execute([$habito_id]);
  $metaDias = (int)$sqlDiasObjetivo->fetchColumn();

  // Verificar si el usuario marcó todos los días requeridos
  $sqlDiasMarcados = $conn->prepare("SELECT COUNT(*) FROM habitos_dias_check WHERE habito_id = ? AND usuario_id = ?");
  $sqlDiasMarcados->execute([$habito_id, $usuario_id]);
  $completados = (int)$sqlDiasMarcados->fetchColumn();

  if ($metaDias > 0 && $completados >= $metaDias) {
    // Registrar hábito como completado en `registros`
    $insertRegistro = $conn->prepare("INSERT INTO registros (habito_id, completado, fecha, fecha_creacion, fecha_actualizacion) VALUES (?, 1, CURDATE(), NOW(), NOW())");
    $insertRegistro->execute([$habito_id]);

    // Aumentar contador en tabla `habitos`
    $sumar = $conn->prepare("UPDATE habitos SET contador = contador + 1 WHERE id = ?");
    $sumar->execute([$habito_id]);

    // Limpiar progreso semanal
    $borrar = $conn->prepare("DELETE FROM habitos_dias_check WHERE habito_id = ? AND usuario_id = ?");
    $borrar->execute([$habito_id, $usuario_id]);
  }
}

header("Location: {$_SERVER['HTTP_REFERER']}");
exit();
