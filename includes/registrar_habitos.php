<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();

$usuario_id = $_SESSION['usuario_id'];
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$frecuencia_id = $_POST['frecuencia_id'] ?? null;
$meta_mensual = $_POST['meta_mensual'] ?? null;
$dias = $_POST['dias'] ?? [];

if (empty($nombre) || empty($frecuencia_id)) {
    header("Location: /habitoo/home/habitos/registrar_habitos.php?error=" . urlencode("Todos los campos obligatorios deben estar completos."));
    exit();
}

// Obtener el nombre de la frecuencia
$stmtFreq = $conn->prepare("SELECT nombre FROM frecuencias WHERE id = ?");
$stmtFreq->execute([$frecuencia_id]);
$nombreFrecuencia = strtolower($stmtFreq->fetchColumn());

if (!$nombreFrecuencia) {
    header("Location: /habitoo/home/habitos/registrar_habitos.php?error=" . urlencode("Frecuencia no válida."));
    exit();
}

// Validar según la frecuencia
if ($nombreFrecuencia === 'semanal' && empty($dias)) {
    header("Location: /habitoo/home/habitos/registrar_habitos.php?error=" . urlencode("Selecciona al menos un día para hábitos semanales."));
    exit();
}

if ($nombreFrecuencia === 'mensual') {
    if (empty($meta_mensual) || !in_array($meta_mensual, ['1', '2', '3', '4'])) {
        header("Location: /habitoo/home/habitos/registrar_habitos.php?error=" . urlencode("Selecciona una meta mensual válida (1 a 4 semanas)."));
        exit();
    }
}

try {
    $conn->beginTransaction();

    $sql = "INSERT INTO habitos (nombre, descripcion, usuario_id, frecuencia_id, meta_mensual, fecha_creacion) 
            VALUES (:nombre, :descripcion, :usuario_id, :frecuencia_id, :meta, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion,
        ':usuario_id' => $usuario_id,
        ':frecuencia_id' => $frecuencia_id,
        ':meta' => $meta_mensual ?: null,
    ]);

    $habito_id = $conn->lastInsertId();

    if ($nombreFrecuencia === 'semanal' && is_array($dias)) {
        $stmtDia = $conn->prepare("INSERT INTO habitos_dias (habito_id, dia_id) VALUES (:habito_id, :dia_id)");
        foreach ($dias as $dia_id) {
            $stmtDia->execute([':habito_id' => $habito_id, ':dia_id' => $dia_id]);
        }
    }

    $conn->commit();

    header("Location: /habitoo/home/habitos/registrar_habitos.php?success=" . urlencode("Hábito registrado correctamente."));
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    header("Location: /habitoo/home/habitos/registrar_habitos.php?error=" . urlencode("Error al registrar el hábito: " . $e->getMessage()));
    exit();
}
