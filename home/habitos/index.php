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

function obtenerHabitosPorFrecuencia($conn, $usuario_id, $frecuencia_id)
{
  $sql = "SELECT 
              h.id,
              h.nombre,
              h.descripcion,
              f.nombre AS frecuencia,
              f.id AS frecuencia_id,
              h.meta_mensual,
              h.fecha_creacion,
              h.contador
          FROM habitos h
          JOIN frecuencias f ON h.frecuencia_id = f.id
          WHERE h.usuario_id = :usuario_id
            AND f.id = :frecuencia_id
            AND h.estado = 'activo';";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':usuario_id' => $usuario_id,
    ':frecuencia_id' => $frecuencia_id
  ]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Mapear frecuencias
$frecuencias = [
  1 => 'Hábitos Diarios',
  2 => 'Hábitos Semanales',
  3 => 'Hábitos Mensuales'
];
?>

<body class="fondo-index d-flex flex-column min-vh-100">
  <main class="flex-grow-1 py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="lista_usuarios_titulo">Mis Hábitos Activos</h2>
      </div>

      <?php foreach ($frecuencias as $id => $titulo):
        $habitos = obtenerHabitosPorFrecuencia($conn, $usuario_id, $id);
      ?>
        <div class="mb-5">
          <h3 class="lista_usuarios_subtitulo text-center"><?= $titulo ?></h3>
          <div class="table-responsive">
            <table class="table-bordered shadow text-center align-middle lista_usuarios_tabla">
              <thead class="table-light">
                <tr>
                  <th>Nombre</th>
                  <th>Descripción</th>
                  <th>Frecuencia</th>
                  <th><?= $id == 1 ? 'Progreso' : ($id == 2 ? 'Días de la semana' : 'Semanas del mes') ?></th>
                  <th>Logros</th>
                  <th>Creado el</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($habitos)): ?>
                  <?php foreach ($habitos as $h): ?>
                    <tr>
                      <td><?= htmlspecialchars($h['nombre']) ?></td>
                      <td><?= htmlspecialchars($h['descripcion']) ?></td>
                      <td><?= htmlspecialchars($h['frecuencia']) ?></td>
                      <td>
                        <?php if ($id == 1): ?>
                          <!-- Progreso diario -->
                          <form action="/habitoo/includes/sumar_contador.php" method="POST">
                            <input type="hidden" name="habito_id" value="<?= $h['id'] ?>">
                            <a href="#" class="btn-progreso me-1" title="Sumar 1 al contador" onclick="event.preventDefault(); this.closest('form').submit();">
                              <i class="bi bi-clipboard-plus-fill"></i>
                            </a>
                          </form>

                        <?php elseif ($id == 2): ?>
                          <!-- Días de la semana -->
                          <form action="/habitoo/includes/actualizar_progreso_semanal.php" method="POST" class="d-flex flex-wrap justify-content-center gap-2">
                            <input type="hidden" name="habito_id" value="<?= $h['id'] ?>">
                            <?php
                            $sqlDias = "SELECT d.id, d.nombre FROM habitos_dias hd 
                                        JOIN dias d ON hd.dia_id = d.id 
                                        WHERE hd.habito_id = :habito_id";
                            $stmtDias = $conn->prepare($sqlDias);
                            $stmtDias->execute([':habito_id' => $h['id']]);
                            $dias = $stmtDias->fetchAll(PDO::FETCH_ASSOC);

                            $sqlChecks = "SELECT dia_id FROM habitos_dias_check 
                                          WHERE habito_id = :habito_id AND usuario_id = :usuario_id";
                            $stmtChecks = $conn->prepare($sqlChecks);
                            $stmtChecks->execute([
                              ':habito_id' => $h['id'],
                              ':usuario_id' => $usuario_id
                            ]);
                            $diasCheck = $stmtChecks->fetchAll(PDO::FETCH_COLUMN);

                            foreach ($dias as $dia):
                              $checked = in_array($dia['id'], $diasCheck) ? 'checked' : '';
                            ?>
                              <input type="checkbox" name="dia_id[]" value="<?= $dia['id'] ?>" id="dia<?= $h['id'] ?>_<?= $dia['id'] ?>" class="btn-dia d-none" <?= $checked ?> onchange="this.form.submit()">
                              <label for="dia<?= $h['id'] ?>_<?= $dia['id'] ?>" class="etiqueta-dia shadow-sm">
                                <?= htmlspecialchars($dia['nombre']) ?>
                              </label>
                            <?php endforeach; ?>
                          </form>

                        <?php else: ?>
                          <!-- Semanas del mes -->
                          <form action="/habitoo/includes/actualizar_progreso_mensual.php" method="POST" class="d-flex flex-wrap justify-content-center gap-2">
                            <input type="hidden" name="habito_id" value="<?= $h['id'] ?>">
                            <?php
                            $maxSemanas = !empty($h['meta_mensual']) ? (int)$h['meta_mensual'] : 4;
                            $nombresSemanas = ['1era', '2nda', '3era', '4ta'];
                            $sqlSemanas = "SELECT semana FROM progreso_mensual 
                                           WHERE habito_id = :habito_id AND usuario_id = :usuario_id";
                            $stmtSemanas = $conn->prepare($sqlSemanas);
                            $stmtSemanas->execute([
                                ':habito_id' => $h['id'],
                                ':usuario_id' => $usuario_id
                            ]);
                            $semanasGuardadas = $stmtSemanas->fetchAll(PDO::FETCH_COLUMN);

                            for ($semana = 1; $semana <= $maxSemanas; $semana++) {
                              $checked = in_array($semana, $semanasGuardadas) ? 'checked' : '';
                              ?>
                              <input type="checkbox" name="semana[]" value="<?= $semana ?>" id="semana<?= $h['id'] ?>_<?= $semana ?>" class="btn-dia d-none" <?= $checked ?> onchange="this.form.submit()">
                              <label for="semana<?= $h['id'] ?>_<?= $semana ?>" class="etiqueta-dia shadow-sm">
                                <?= $nombresSemanas[$semana - 1] ?>
                              </label>
                              <?php
                            }
                            ?>
                          </form>
                        <?php endif; ?>
                      </td>

                      <td><span class="badge bg-dark"><?= $h['contador'] ?? 0 ?></span></td>
                      <td><?= date('d/m/Y', strtotime($h['fecha_creacion'])) ?></td>
                      <td>
                        <a href="/habitoo/home/habitos/ver_habitos.php?id=<?= $h['id'] ?>" class="btn-ver me-1" title="Ver">
                          <i class="bi bi-eye-fill"></i>
                        </a>
                        <a href="/habitoo/home/habitos/editar_habitos.php?id=<?= $h['id'] ?>" class="btn-editar me-1" title="Editar">
                          <i class="bi bi-pencil-fill"></i>
                        </a>
                        <a href="/habitoo/home/habitos/eliminar_habitos.php?id=<?= $h['id'] ?>" class="btn-eliminar me-1" title="Eliminar" onclick="return confirm('¿Eliminar el hábito <?= htmlspecialchars($h['nombre']) ?>?')">
                          <i class="bi bi-trash-fill"></i>
                        </a>
                        <a href="/habitoo/includes/terminar_habito.php?habito_id=<?= $h['id'] ?>" class="btn-listo me-2" title="Terminar y ver productividad" onclick="return confirm('¿Deseas marcar este hábito como terminado?');">
  <i class="bi bi-flag-fill"></i>
</a>

                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8">No hay hábitos activos para esta frecuencia.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="text-center mt-4">
        <a href="/habitoo/home/habitos/huella.php" class="btn-login">
          Ver hábitos completados <i class="bi bi-arrow-right-circle"></i>
        </a>
      </div>
    </div>
  </main>

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>
</body>
</html>
