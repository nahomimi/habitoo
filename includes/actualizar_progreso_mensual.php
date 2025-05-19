<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');


if (!isset($_SESSION['usuario_id'])) exit();
$usuario_id = $_SESSION['usuario_id'];
$habito_id = $_POST['habito_id'] ?? null;
$semanas = $_POST['semana'] ?? []; // ahora puede venir un array

$conn = (new Conexion())->conectar();

if ($habito_id && !empty($semanas)) {
  $fechaHoy = date('Y-m-d');
  $anioMesActual = date('Y-m');

  // Primero eliminar registros de progreso para este hábito y usuario y mes actual
  $delete = $conn->prepare("DELETE FROM progreso_mensual WHERE habito_id = ? AND usuario_id = ? AND DATE_FORMAT(fecha, '%Y-%m') = ?");
  $delete->execute([$habito_id, $usuario_id, $anioMesActual]);

  // Insertar progreso actualizado
  $insert = $conn->prepare("INSERT INTO progreso_mensual (habito_id, semana, fecha, usuario_id) VALUES (?, ?, ?, ?)");
  foreach ($semanas as $semana) {
    $insert->execute([$habito_id, $semana, $fechaHoy, $usuario_id]);
  }

  // Obtener la meta mensual
  $habito = $conn->prepare("SELECT meta_mensual FROM habitos WHERE id = ?");
  $habito->execute([$habito_id]);
  $meta = (int)$habito->fetchColumn();

  // Contar cuántas semanas están registradas para este mes y hábito
  $completadas = $conn->prepare("SELECT COUNT(DISTINCT semana) FROM progreso_mensual WHERE habito_id = ? AND usuario_id = ? AND DATE_FORMAT(fecha, '%Y-%m') = ?");
  $completadas->execute([$habito_id, $usuario_id, $anioMesActual]);
  $totalCompletadas = (int)$completadas->fetchColumn();

  if ($totalCompletadas >= $meta) {
    // Sumar al contador y limpiar progreso mensual del mes actual
    $sumar = $conn->prepare("UPDATE habitos SET contador = contador + 1 WHERE id = ?");
    $sumar->execute([$habito_id]);

    $borrar = $conn->prepare("DELETE FROM progreso_mensual WHERE habito_id = ? AND usuario_id = ? AND DATE_FORMAT(fecha, '%Y-%m') = ?");
    $borrar->execute([$habito_id, $usuario_id, $anioMesActual]);
  }
}

header("Location: {$_SERVER['HTTP_REFERER']}");
exit();
