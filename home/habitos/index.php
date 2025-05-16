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

$sql = "SELECT 
            h.id,
            h.nombre,
            h.descripcion,
            f.nombre AS frecuencia,
            h.meta_semanal,
            h.fecha_creacion
        FROM habitos h
        JOIN frecuencias f ON h.frecuencia_id = f.id
        WHERE h.usuario_id = :usuario_id";

$stmt = $conn->prepare($sql);
$stmt->execute([':usuario_id' => $usuario_id]);
$habitos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body class="fondo-index d-flex flex-column min-vh-100">
<main class="flex-grow-1 py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="lista_usuarios_titulo">Mis Hábitos</h2>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered shadow text-center align-middle lista_usuarios_tabla">
  <thead class="table-light">
    <tr>
      <th>✔   Check</th> <!-- Nueva columna al inicio -->
      <th>Nombre</th>
      <th>Descripción</th>
      <th>Frecuencia</th>
      <th>Meta semanal</th>
      <th>Creado el</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($habitos) > 0): ?>
      <?php foreach ($habitos as $h): ?>
        <tr>
          <!-- Botón para marcar como hecho -->
          <td>
            <a href="marcar_hecho.php?id=<?= $h['id'] ?>" class="btn-check" title="Marcar como hecho">
  <i class="bi bi-check-circle-fill"></i>
</a>

          </td>

          <td><?= htmlspecialchars($h['nombre']) ?></td>
          <td><?= htmlspecialchars($h['descripcion']) ?></td>
          <td><?= htmlspecialchars($h['frecuencia']) ?></td>
          <td><?= $h['meta_semanal'] ?? '-' ?></td>
          <td><?= date('d/m/Y', strtotime($h['fecha_creacion'])) ?></td>

          <td>
            <a href="ver_habito.php?id=<?= $h['id'] ?>" class="btn-ver me-1" title="Ver">
              <i class="bi bi-eye-fill"></i>
            </a>
            <a href="editar_habito.php?id=<?= $h['id'] ?>" class="btn-editar me-1" title="Editar">
              <i class="bi bi-pencil-fill"></i>
            </a>
            <a href="eliminar_habito.php?id=<?= $h['id'] ?>" class="btn-eliminar" title="Eliminar"
               onclick="return confirm('¿Eliminar el hábito <?= htmlspecialchars($h['nombre']) ?>?')">
              <i class="bi bi-trash-fill"></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="7">No has registrado hábitos aún.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

    </div>
    
  </div>
</main>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>
</body>
