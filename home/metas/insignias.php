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

// 1. Obtener cantidad de hábitos completados (inactivos)
$sql_completados = "SELECT COUNT(*) FROM habitos WHERE usuario_id = :usuario_id AND estado = 'inactivo'";
$stmt_completados = $conn->prepare($sql_completados);
$stmt_completados->execute([':usuario_id' => $usuario_id]);
$total_completados = $stmt_completados->fetchColumn();

// 2. Obtener todas las insignias disponibles del rango 6 a 10
$sql_insignias = "SELECT * FROM insignias WHERE id BETWEEN 6 AND 10 ORDER BY id ASC";
$stmt_insignias = $conn->prepare($sql_insignias);
$stmt_insignias->execute();
$insignias = $stmt_insignias->fetchAll(PDO::FETCH_ASSOC);

// 3. Obtener cuántas insignias de ese rango ha obtenido el usuario
$sql_obtenidas = "SELECT COUNT(*) FROM usuarios_insignias 
                  WHERE usuario_id = :usuario_id AND insignia_id BETWEEN 6 AND 10";
$stmt_obtenidas = $conn->prepare($sql_obtenidas);
$stmt_obtenidas->execute([':usuario_id' => $usuario_id]);
$obtenidas_count = $stmt_obtenidas->fetchColumn();

// 4. Contar el total de insignias disponibles (para progreso)
$total_insignias = count($insignias);

// 5. Determinar nivel actual (máximo 5)
$niveles_obtenidos = min(5, $total_completados);
?>

<body class="fondo-groowi d-flex flex-column min-vh-100">
<main class="flex-grow-1 py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="lista_usuarios_titulo">Mis Insignias</h2>
    </div>

    <!-- Progreso -->
    <div class="row justify-content-center mb-5">
      <div class="col-md-6">
        <div class="card shadow border-0 p-4 bg-white">
          <div class="text-center">
            <h4 class="fw-bold mb-3">Tu progreso</h4>
            <div class="display-4 fw-bold text-warning mb-2">
              <?= $total_completados ?>/<?= $total_insignias ?>
            </div>
            <p class="text-muted mb-4">Insignias obtenidas</p>
            <div class="progress" style="height: 20px; border-radius: 10px;">
              <div class="progress-bar progress-bar-striped bg-warning" 
                    role="progressbar" 
                 style="width: <?= ($niveles_obtenidos/5)*100 ?>%" 
                 aria-valuenow="<?= $niveles_obtenidos ?>" 
                 aria-valuemin="0" 
                 aria-valuemax="5">
              Nivel <?= $niveles_obtenidos ?> de 5
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lista de insignias -->
    <div class="row justify-content-center">
      <?php foreach ($insignias as $insignia): 
        $nivel_requerido = $insignia['id'] - 5; // Nivel 1 a 5
        $obtenida = $total_completados >= $nivel_requerido;
        $imgPath = "/habitoo/assets/img/" . $insignia['icono'];
      ?>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
          <div class="card h-100 border-<?= $obtenida ? 'warning' : 'secondary' ?>">
            <div class="card-header bg-<?= $obtenida ? 'warning' : 'light' ?> text-center py-2">
              <h5 class="mb-0 fw-bold"><?= htmlspecialchars($insignia['nombre']) ?></h5>
            </div>
            <div class="card-body text-center p-3">
              <div class="position-relative mx-auto" style="width: 100px; height: 100px;">
                <img src="<?= $imgPath ?>" 
                     alt="<?= htmlspecialchars($insignia['nombre']) ?>" 
                     class="img-fluid h-100 <?= $obtenida ? '' : 'grayscale' ?>">
                <?php if (!$obtenida): ?>
                  <div class="position-absolute top-50 start-50 translate-middle">
                    <i class="bi bi-lock-fill fs-3 text-secondary"></i>
                  </div>
                <?php endif; ?>
              </div>
              <p class="card-text mt-3 small"><?= htmlspecialchars($insignia['descripcion']) ?></p>
              <span class="badge bg-<?= $obtenida ? 'warning text-dark' : 'secondary' ?>">
                <?= $obtenida ? 'Obtenida' : 'Nivel ' . $nivel_requerido ?>
              </span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-4">
      <a href="/habitoo/home/index.php" class="btn-login">
        <i class="bi bi-arrow-left-circle"></i> Volver al inicio
      </a>
    </div>
  </div>
</main>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>
