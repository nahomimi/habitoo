<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();

$usuario_id = $_SESSION['usuario_id'];
$habito_id = $_POST['habito_id'] ?? null;
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$frecuencia_id = $_POST['frecuencia_id'] ?? null;
$meta_mensual = $_POST['meta_mensual'] ?? null;
$dias = $_POST['dias'] ?? [];

// Validaciones básicas
if (!$habito_id || !$nombre || !$frecuencia_id) {
    header("Location: /habitoo/home/habitos/editar_habitos.php?id=$habito_id&error=" . urlencode("Faltan campos obligatorios."));
    exit();
}

// Verificar que el hábito pertenece al usuario
$stmtVerificar = $conn->prepare("SELECT id FROM habitos WHERE id = ? AND usuario_id = ?");
$stmtVerificar->execute([$habito_id, $usuario_id]);
if (!$stmtVerificar->fetch()) {
    header("Location: /habitoo/home/habitos/index.php?error=" . urlencode("No tienes permiso para editar este hábito."));
    exit();
}

// Validar frecuencia
$stmtFreq = $conn->prepare("SELECT id, nombre FROM frecuencias WHERE id = ?");
$stmtFreq->execute([$frecuencia_id]);
$frecuencia = $stmtFreq->fetch(PDO::FETCH_ASSOC);

if (!$frecuencia) {
    header("Location: /habitoo/home/habitos/editar_habitos.php?id=$habito_id&error=" . urlencode("Frecuencia no válida."));
    exit();
}

$nombreFrecuencia = strtolower($frecuencia['nombre']);

// Validaciones específicas por frecuencia
if ($nombreFrecuencia === 'semanal' && empty($dias)) {
    header("Location: /habitoo/home/habitos/editar_habitos.php?id=$habito_id&error=" . urlencode("Selecciona días para la frecuencia semanal."));
    exit();
}

if ($nombreFrecuencia === 'mensual' && !in_array($meta_mensual, ['1','2','3','4'])) {
    header("Location: /habitoo/home/habitos/editar_habitos.php?id=$habito_id&error=" . urlencode("Meta mensual inválida. Debe ser entre 1 y 4 semanas."));
    exit();
}

try {
    $conn->beginTransaction();

    // Actualizar hábito (reiniciando el contador a 0)
    $stmt = $conn->prepare("UPDATE habitos SET 
                            nombre = ?, 
                            descripcion = ?, 
                            frecuencia_id = ?, 
                            meta_mensual = ?, 
                            contador = 0,
                            fecha_actualizacion = NOW() 
                          WHERE id = ? AND usuario_id = ?");
    
    $meta = ($nombreFrecuencia === 'mensual') ? $meta_mensual : null;
    $stmt->execute([$nombre, $descripcion, $frecuencia_id, $meta, $habito_id, $usuario_id]);

    // Limpiar días anteriores
    $conn->prepare("DELETE FROM habitos_dias WHERE habito_id = ?")->execute([$habito_id]);

    // Insertar nuevos días (solo para frecuencia semanal)
    if ($nombreFrecuencia === 'semanal' && !empty($dias)) {
        $stmtDias = $conn->prepare("INSERT INTO habitos_dias (habito_id, dia_id) VALUES (?, ?)");
        
        foreach ($dias as $dia_id) {
            if (is_numeric($dia_id)) {
                $stmtDias->execute([$habito_id, $dia_id]);
            }
        }
    }

    $conn->commit();
    header("Location: /habitoo/home/habitos/index.php?success=" . urlencode("Hábito actualizado correctamente. El contador se ha reiniciado a 0."));
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    header("Location: /habitoo/home/habitos/editar_habitos.php?id=$habito_id&error=" . urlencode("Error al actualizar: " . $e->getMessage()));
    exit();
}