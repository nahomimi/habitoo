<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de hábito inválido.");
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/header.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/menu.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();

$usuario_id = $_SESSION['usuario_id'];
$habito_id = intval($_GET['id']);

// Obtener hábito
$stmt = $conn->prepare("
    SELECT h.id, h.nombre, h.descripcion, f.nombre AS frecuencia, f.id AS frecuencia_id, h.meta_mensual,
           h.contador,
           GROUP_CONCAT(d.nombre ORDER BY d.id SEPARATOR ', ') AS dias
    FROM habitos h
    JOIN frecuencias f ON h.frecuencia_id = f.id
    LEFT JOIN habitos_dias hd ON hd.habito_id = h.id
    LEFT JOIN dias d ON hd.dia_id = d.id
    WHERE h.usuario_id = ? AND h.id = ?
    GROUP BY h.id
");

$stmt->execute([$usuario_id, $habito_id]);
$habito = $stmt->fetch(PDO::FETCH_ASSOC);

$progreso_semanal_total = $progreso_mensual = 0;

if ($habito) {
    $frecuencia_id = (int)$habito['frecuencia_id'];

    if ($frecuencia_id === 2) {
        // Semanal
        $stmt = $conn->prepare("SELECT dia_id FROM habitos_dias WHERE habito_id = ?");
        $stmt->execute([$habito_id]);
        $diasObjetivo = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($diasObjetivo) > 0) {
            $stmt = $conn->prepare("SELECT COUNT(DISTINCT dia_id) FROM habitos_dias_check 
                                 WHERE habito_id = ? AND usuario_id = ? AND dia_id IN (" . implode(',', $diasObjetivo) . ")");
            $stmt->execute([$habito_id, $usuario_id]);
            $marcados = $stmt->fetchColumn();
            $progreso_semanal_total = intval(($marcados / count($diasObjetivo)) * 100);
        }
    }

    if ($frecuencia_id === 3) {
        // Mensual
        $meta = (int)$habito['meta_mensual'];
        if ($meta > 0) {
            $mes = date('Y-m');
            $stmt = $conn->prepare("SELECT COUNT(DISTINCT semana) FROM progreso_mensual 
                                 WHERE habito_id = ? AND usuario_id = ? 
                                 AND DATE_FORMAT(fecha, '%Y-%m') = ?");
            $stmt->execute([$habito_id, $usuario_id, $mes]);
            $completadas = $stmt->fetchColumn();
            $progreso_mensual = min(intval(($completadas / $meta) * 100), 100);
        }
    }
}
?>

<main class="fondo-3 py-5 min-vh-100 d-flex flex-column">
  <div class="flex-grow-1">
    <div class="d-flex flex-column flex-lg-row justify-content-center align-items-center gap-5">

      <?php if (!$habito): ?>
        <p class="text-center text-muted">Hábito no encontrado.</p>
      <?php else: ?>
        <div class="card card-diseño-usuario flex-grow-1">
          <!-- Botón regresar -->
          <a href="/habitoo/home/habitos/index.php" class="btn-regresar-icono" title="Volver al inicio">
            <i class="bi bi-arrow-left-circle-fill"></i>
          </a>

          <div class="card-body text-center">
            <h3 class="card-nombre"><?= htmlspecialchars($habito['nombre']) ?></h3>
            <p class="card-correo"><?= nl2br(htmlspecialchars($habito['descripcion'])) ?></p>

            <div class="subcard-datos mt-3 text-start">
              <ul class="list-group list-group-flush">
                <li class="list-group-item">
                  <i class="bi bi-arrow-repeat"></i> <strong>Frecuencia:</strong> <?= htmlspecialchars($habito['frecuencia']) ?>
                </li>

                <?php if ($frecuencia_id === 1): ?>
                  <li class="list-group-item">
                    <i class="bi bi-star-fill"></i> <strong>Días cumplidos:</strong> <?= $habito['contador'] ?? 0 ?>
                  </li>

                <?php elseif ($frecuencia_id === 2): ?>
                  <li class="list-group-item">
                    <i class="bi bi-calendar-week"></i> <strong>Días asignados:</strong> <?= htmlspecialchars($habito['dias'] ?: 'No asignados') ?>
                  </li>
                  <li class="list-group-item">
                    <i class="bi bi-calendar-check"></i> <strong>Progreso esta semana: </strong> <?= $progreso_semanal_total ?>%
                  </li>
                  <div class="progress mt-2" style="height: 25px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $progreso_semanal_total ?>%;" aria-valuenow="<?= $progreso_semanal_total ?>" aria-valuemin="0" aria-valuemax="100">
                      <?= $progreso_semanal_total ?>%
                    </div>
                  </div>

                <?php elseif ($frecuencia_id === 3): ?>
                  <li class="list-group-item">
                    <i class="bi bi-calendar"></i> <strong>Meta mensual:</strong> <?= $habito['meta_mensual'] ?> veces por mes
                  </li>
                  <li class="list-group-item">
                    <i class="bi bi-graph-up"></i> <strong>Progreso mensual:</strong> <?= $progreso_mensual ?>%
                  </li>
                  <div class="progress mt-2" style="height: 25px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $progreso_mensual ?>%;" aria-valuenow="<?= $progreso_mensual ?>" aria-valuemin="0" aria-valuemax="100">
                      <?= $progreso_mensual ?>%
                    </div>
                  </div>
                <?php endif; ?>
              </ul>
            </div>
          </div>

          <div class="icono-editar mt-4">
            <a href="/habitoo/home/habitos/editar_habitos.php?id=<?= $habito['id'] ?>" class="btn-editar-icono" title="Editar hábito">
              <i class="bi bi-pencil-square"></i>
            </a>
          </div>
        </div>
      <?php endif; ?>

      <!-- Imagen Growi -->
      <div class="growi-imagen flex-shrink-0">
        <img src="/habitoo/assets/img/groowi-habitos.png" alt="Growi dando la bienvenida" class="img-fluid">
      </div>
    </div>
  </div>
</main>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer.php"); ?>