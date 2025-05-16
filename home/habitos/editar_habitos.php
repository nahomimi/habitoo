<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['usuario_id'])) {
  header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
  exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/header.php");
?>

<body class="fondo-2 d-flex flex-column min-vh-100">

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/menu.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();

$usuario_id = $_SESSION['usuario_id'];
$habito_id = $_GET['id'] ?? null;

if (!$habito_id) {
  header("Location: /habitoo/home/habitos/index.php?error=" . urlencode("Hábito no especificado"));
  exit();
}

// Obtener datos del hábito solo si pertenece al usuario
$stmt = $conn->prepare("
  SELECT h.*, f.id AS frecuencia_id, f.nombre AS frecuencia_nombre
  FROM habitos h
  JOIN frecuencias f ON h.frecuencia_id = f.id
  WHERE h.id = ? AND h.usuario_id = ?
");
$stmt->execute([$habito_id, $usuario_id]);
$habito = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$habito) {
  header("Location: /habitoo/home/habitos/index.php?error=" . urlencode("Hábito no encontrado"));
  exit();
}

// Obtener días asociados al hábito
$stmtDias = $conn->prepare("SELECT dia_id FROM habitos_dias WHERE habito_id = ?");
$stmtDias->execute([$habito_id]);
$diasSeleccionados = $stmtDias->fetchAll(PDO::FETCH_COLUMN);

// Obtener todos los días para mostrar en checkboxes
$stmtTodosDias = $conn->query("SELECT id, nombre FROM dias ORDER BY id");
$dias = $stmtTodosDias->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las frecuencias para el select
$stmtFrecuencias = $conn->query("SELECT id, nombre FROM frecuencias ORDER BY id");
$frecuencias = $stmtFrecuencias->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="py-5 min-vh-100 d-flex flex-column">
  <div class="container flex-grow-1">
    
    <div class="text-center mb-4">
      <h2 class="lista_usuarios_titulo">Edición de Hábito</h2>
    </div>
    
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="caja-login text-center p-4 shadow position-relative">

          <!-- Botón de regreso -->
          <a href="/habitoo/home/habitos/index.php" class="btn-regresar-icono" title="Volver a hábitos">
            <i class="bi bi-arrow-left-circle"></i>
          </a>

          <?php 
          if (isset($_GET['error'])) {
            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
            echo htmlspecialchars($_GET['error']);
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>";
            echo "</div>";
          }
          if (isset($_GET['success'])) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
            echo htmlspecialchars($_GET['success']);
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>";
            echo "</div>";
          }
          ?>

          <form action="/habitoo/includes/actualizar_habito.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($habito['id']) ?>">

            <div class="position-relative mb-3 mt-5">
              <input type="text" name="nombre" class="campo-login" placeholder="Nombre del hábito" required value="<?= htmlspecialchars($habito['nombre']) ?>">
            </div>

            <div class="position-relative mb-3">
              <textarea name="descripcion" class="campo-login" placeholder="Descripción" rows="3"><?= htmlspecialchars($habito['descripcion']) ?></textarea>
            </div>

            <div class="position-relative mb-3">
              <select name="frecuencia_id" class="campo-login" required>
                <option value="">Selecciona una frecuencia</option>
                <?php foreach ($frecuencias as $frecuencia): ?>
                  <option value="<?= $frecuencia['id'] ?>" <?= $frecuencia['id'] == $habito['frecuencia_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($frecuencia['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <i class="bi bi-arrow-repeat icono"></i>
            </div>

            <div class="position-relative mb-3">
              <input type="number" min="0" name="meta_semanal" class="campo-login" placeholder="Meta semanal" required value="<?= htmlspecialchars($habito['meta_semanal']) ?>">
              <i class="bi bi-trophy icono"></i>
            </div>

            <div class="text-start mb-3">
              <label class="mb-1"><strong>Días para cumplir hábito:</strong></label>
              <div class="d-flex flex-wrap gap-2">
                <?php foreach ($dias as $dia): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias[]" value="<?= $dia['id'] ?>" id="dia-<?= $dia['id'] ?>"
                      <?= in_array($dia['id'], $diasSeleccionados) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="dia-<?= $dia['id'] ?>">
                      <?= htmlspecialchars($dia['nombre']) ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <input type="submit" class="btn-login mt-3" value="Guardar Cambios">
          </form>

        </div>
      </div>
    </div>

  </div>
</main>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer.php"); ?>
</body>
</html>
