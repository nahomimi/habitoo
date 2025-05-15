<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Solo permite el acceso si está logueado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
  header("Location: /habitoo/login.php?error=" . urlencode("Acceso restringido para administradores"));
  exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/header_admin.php");
?>

<body class="fondo-3 d-flex flex-column min-vh-100">
  <?php
  require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/menu_admin.php");
  require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

  $conexion = new Conexion();
  $conn = $conexion->conectar();

  // Obtener ID del usuario desde GET
  $usuario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

  // Consultar la información completa del usuario
  $stmt = $conn->prepare("
  SELECT u.*, r.nombre AS rol, e.descripcion AS estatus
  FROM usuarios u
  JOIN roles r ON u.rol_id = r.id
  JOIN estatus e ON u.estatus_id = e.id
  WHERE u.id = ?
");
  $stmt->execute([$usuario_id]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$usuario) {
    echo "<p class='text-danger text-center'>Usuario no encontrado.</p>";
    require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer_admin.php");
    exit();
  }
  ?>

  <main class=" py-5 d-flex flex-column">
    <div class=" flex-grow-1">

      <div class="d-flex justify-content-center align-items-start flex-wrap gap-4">


        <!-- Growi -->
        <div class="growi-imagen">
          <img src="/habitoo/assets/img/groowi2.png" alt="Growi dando la bienvenida" class="img-fluid" ">
      </div>

      <!-- Card principal -->
      <div class=" card card-diseño-usuario">
          <!-- Botón regresar -->
          <a href="/habitoo/home/usuarios/index.php" class="btn-regresar-icono" title="Volver al inicio">
          <i class="bi bi-arrow-left-circle"></i>
          </a>
          <div class="card-body p-4 text-center mt-4">
            <h3 class="card-nombre"><?= htmlspecialchars($usuario['nombres']) . " " . htmlspecialchars($usuario['a_paterno']) . " " . htmlspecialchars($usuario['a_materno']) ?></h3>
            <p class="card-correo"><?= htmlspecialchars($usuario['email']) ?></p>

            <div class="subcard-datos mt-3 text-start">
              <ul class="list-group list-group-flush">
                <li class="list-group-item">
                  <i class="bi bi-telephone"></i> <strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono']) ?>
                </li>
                <li class="list-group-item">
                  <i class="bi bi-calendar"></i> <strong>Fecha de nacimiento:</strong> <?= htmlspecialchars($usuario['fecha_nacimiento']) ?>
                </li>
                <li class="list-group-item">
                  <i class="bi bi-chat-quote"></i> <strong>Frase motivacional:</strong> <?= htmlspecialchars($usuario['frase_motivacional']) ?>
                </li>
                <li class="list-group-item">
                  <i class="bi bi-clock"></i> <strong>Última conexión:</strong> <?= htmlspecialchars($usuario['ultima_conexion']) ?>
                </li>
                <li class="list-group-item">
                  <i class="bi bi-person-badge"></i> <strong>Rol:</strong> <?= htmlspecialchars($usuario['rol']) ?>
                </li>
                <li class="list-group-item">
                  <i class="bi bi-check-circle"></i> <strong>Estatus:</strong> <?= htmlspecialchars($usuario['estatus']) ?>
                </li>
              </ul>
            </div>

            <!-- Botón editar -->
            <div class="icono-editar mt-4">
              <a href="/habitoo/home/usuarios/editar.php?id=<?= $usuario['id'] ?>" class="btn-editar-icono" title="Editar usuario">
                <i class="bi bi-pencil-square"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>


  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer_admin.php"); ?>