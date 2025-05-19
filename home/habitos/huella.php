<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['usuario_id'])) {
  header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
  exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/header.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/menu.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');

$conexion = new Conexion();
$conn = $conexion->conectar();

$usuario_id = $_SESSION['usuario_id'];

// 1) Obtener hábitos diarios y semanales completados
$sql_registros = "SELECT 
          h.id,
          h.nombre,
          h.descripcion,
          f.id AS frecuencia_id,
          f.nombre AS frecuencia,
          r.fecha,
          h.fecha_creacion AS fecha_habito,
          h.meta_mensual
        FROM registros r
        JOIN habitos h ON r.habito_id = h.id
        JOIN frecuencias f ON h.frecuencia_id = f.id
        WHERE h.usuario_id = :usuario_id
          AND r.completado = 1
          AND h.frecuencia_id IN (1, 2)";

$stmt = $conn->prepare($sql_registros);
$stmt->execute([':usuario_id' => $usuario_id]);
$habitos_completados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2) Obtener hábitos mensuales completados (contador > 0)
$sql_mensual = "SELECT 
                  h.id,
                  h.nombre,
                  h.descripcion,
                  f.id AS frecuencia_id,
                  f.nombre AS frecuencia,
                  h.fecha_creacion AS fecha_habito,
                  h.meta_mensual,
                  h.contador,
                  (SELECT MAX(fecha) FROM progreso_mensual 
                   WHERE habito_id = h.id AND usuario_id = h.usuario_id) AS ultima_fecha
                FROM habitos h
                JOIN frecuencias f ON h.frecuencia_id = f.id
                WHERE h.usuario_id = :usuario_id
                  AND h.frecuencia_id = 3
                  AND h.contador > 0";

$stmt_mensual = $conn->prepare($sql_mensual);
$stmt_mensual->execute([':usuario_id' => $usuario_id]);
$habitos_mensuales = $stmt_mensual->fetchAll(PDO::FETCH_ASSOC);

// Procesar hábitos mensuales para formato consistente
foreach ($habitos_mensuales as &$habito) {
  $habito['fecha'] = $habito['ultima_fecha'] ?: $habito['fecha_habito'];
  unset($habito['ultima_fecha']);
}

// Combinar resultados
$todos_habitos = array_merge($habitos_completados, $habitos_mensuales);

// Ordenar por fecha descendente
usort($todos_habitos, function ($a, $b) {
  return strtotime($b['fecha']) - strtotime($a['fecha']);
});
?>

<body class="fondo-index d-flex flex-column min-vh-100">
  <main class="flex-grow-1 py-5">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="lista_usuarios_titulo">Huella de hábitos completados</h2>
      </div>

      <div class="table-responsive">
        <table class="table-bordered shadow text-center align-middle lista_usuarios_tabla">
          <thead class="table-light">
            <tr>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Frecuencia</th>
              <th>Detalle del cumplimiento</th>
              <th>Fecha de finalización</th>
              <th>Registrado el</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($todos_habitos) > 0): ?>
              <?php foreach ($todos_habitos as $h): ?>
                <tr>
                  <td><?= htmlspecialchars($h['nombre']) ?></td>
                  <td><?= htmlspecialchars($h['descripcion']) ?></td>
                  <td><?= htmlspecialchars($h['frecuencia']) ?></td>

                  <td>
                    <?php
                    if ($h['frecuencia_id'] == 1) {
                      echo date('d/m/Y', strtotime($h['fecha']));
                    } elseif ($h['frecuencia_id'] == 2) {
                      $dias_stmt = $conn->prepare("SELECT d.nombre FROM habitos_dias hd JOIN dias d ON hd.dia_id = d.id WHERE hd.habito_id = ?");
                      $dias_stmt->execute([$h['id']]);
                      $dias = $dias_stmt->fetchAll(PDO::FETCH_COLUMN);
                      echo implode(', ', $dias);
                    } elseif ($h['frecuencia_id'] == 3) {
                      echo $h['meta_mensual'] . " semanas";
                    }
                    ?>
                  </td>

                  <td><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
                  <td><?= date('d/m/Y', strtotime($h['fecha_habito'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No hay hábitos completados aún.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="text-center mt-4">
        <a href="/habitoo/home/habitos/index.php" class="btn-login">
          Volver a hábitos activos <i class="bi bi-arrow-left-circle"></i>
        </a>
      </div>
    </div>
  </main>

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>