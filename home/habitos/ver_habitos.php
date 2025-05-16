<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['usuario_id'])) {
  header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
  exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/header.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/menu.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();

$usuario_id = $_SESSION['usuario_id'];

// Consulta para obtener hábitos con su frecuencia y días asociados
$stmt = $conn->prepare("
  SELECT h.id, h.nombre, h.descripcion, f.nombre AS frecuencia, h.meta_semanal,
    GROUP_CONCAT(d.nombre ORDER BY d.id SEPARATOR ', ') AS dias
  FROM habitos h
  JOIN frecuencias f ON h.frecuencia_id = f.id
  LEFT JOIN habitos_dias hd ON hd.habito_id = h.id
  LEFT JOIN dias d ON hd.dia_id = d.id
  WHERE h.usuario_id = ?
  GROUP BY h.id
  ORDER BY h.fecha_creacion DESC
");
$stmt->execute([$usuario_id]);
$habitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="fondo-3 py-5  d-flex flex-column">
  <div class="flex-grow-1">
    <div class="d-flex justify-content-center align-items-start flex-wrap gap-4">
      
      <?php if (count($habitos) === 0): ?>
        <p class="text-center text-muted">No tienes hábitos registrados aún.</p>
      <?php else: ?>
        <?php foreach ($habitos as $habito): ?>

          <div class="card card-diseño-usuario flex-grow-1">
            <!-- Botón regresar -->
                <a href="/habitoo/home/habitos/index.php" class="btn-regresar-icono" title="Volver al inicio">
                    <i class="bi bi-arrow-left-circle-fill"></i>
                </a>
            <div class="card-body  text-center">
              <h3 class="card-nombre"><?= htmlspecialchars($habito['nombre']) ?></h3>
              <p class="card-correo"><?= nl2br(htmlspecialchars($habito['descripcion'])) ?></p>

              <div class="subcard-datos mt-3 text-start">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item">
                    <i class="bi bi-arrow-repeat"></i> <strong>Frecuencia:</strong> <?= htmlspecialchars($habito['frecuencia']) ?>
                  </li>
                  <li class="list-group-item">
                    <i class="bi bi-trophy"></i> <strong>Meta semanal:</strong> <?= htmlspecialchars($habito['meta_semanal']) ?>
                  </li>
                  <li class="list-group-item">
                    <i class="bi bi-calendar-check"></i> <strong>Días:</strong> <?= htmlspecialchars($habito['dias'] ?: 'No asignados') ?>
                  </li>
                </ul>
              </div>

              <div class="icono-editar mt-4">
                <a href="/habitoo/home/habitos/editar.php?id=<?= $habito['id'] ?>" class="btn-editar-icono" title="Editar hábito">
                  <i class="bi bi-pencil-square"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <div class="growi-imagen flex-shrink-0">
        <img src="/habitoo/assets/img/groowi-habitos.png" alt="Growi dando la bienvenida" class="img-fluid">
      </div>
    </div>
  </div>
</main>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer.php"); ?>
