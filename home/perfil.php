<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 2) {
    header("Location: /habitoo/login.php?error=" . urlencode("Acceso restringido a usuarios"));
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/header.php");
?>

<body class="fondo-4 d-flex flex-column min-vh-100">
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/menu.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: /habitoo/login.php?error=Usuario no encontrado");
    exit();
}
?>

<main class="py-5 container flex-grow-1">

  <div class="text-center mb-4">
    <h2 class="lista_usuarios_titulo">Mi Perfil</h2>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="caja-login text-center p-4 shadow position-relative">

        <!-- Botón regresar -->
        <a href="/habitoo/home/inicio.php" class="btn-regresar-icono" title="Inicio">
          <i class="bi bi-arrow-left-circle"></i>
        </a>

        <!-- Imagen de perfil -->
        <?php if (!empty($usuario['avatar_url']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/habitoo/" . $usuario['avatar_url'])) : ?>
          <img src="/habitoo/<?= htmlspecialchars($usuario['avatar_url']) ?>" alt="Avatar" class="mb-3 rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
        <?php else : ?>
          <img src="/habitoo/assets/default_avatar.png" alt="Avatar" class="mb-3 rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
        <?php endif; ?>

        <?php 
            // Mostrar mensaje de error si existe
            if (isset($_GET['error'])) {
              echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
              echo htmlspecialchars($_GET['error']);
              echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>";
              echo "</div>";
            }
            ?>

        <form action="/habitoo/includes/actualizar_perfil.php" method="POST" enctype="multipart/form-data">

          <div class="position-relative mb-3">
            <input type="text" name="nombres" class="campo-login" placeholder="Nombres" required value="<?= htmlspecialchars($usuario['nombres']) ?>">
          </div>

          <div class="position-relative mb-3">
            <input type="text" name="a_paterno" class="campo-login" placeholder="Apellido Paterno" required value="<?= htmlspecialchars($usuario['a_paterno']) ?>">
          </div>

          <div class="position-relative mb-3">
            <input type="text" name="a_materno" class="campo-login" placeholder="Apellido Materno" value="<?= htmlspecialchars($usuario['a_materno']) ?>">
          </div>

          <div class="position-relative mb-3">
            <input type="email" name="email" class="campo-login" placeholder="Correo" required value="<?= htmlspecialchars($usuario['email']) ?>">
            <i class="bi bi-envelope-fill icono"></i>
          </div>

          <div class="position-relative mb-3">
            <input type="text" name="telefono" class="campo-login" placeholder="Teléfono" value="<?= htmlspecialchars($usuario['telefono']) ?>">
            <i class="bi bi-telephone-fill icono"></i>
          </div>

          <div class="position-relative mb-3">
            <input type="date" name="fecha_nacimiento" class="campo-login" value="<?= htmlspecialchars($usuario['fecha_nacimiento']) ?>">
            <i class="bi bi-calendar-date-fill icono" style="background-color: white;"></i>
          </div>

          <div class="position-relative mb-3">
            <input type="text" name="frase_motivacional" class="campo-login" placeholder="Frase motivacional" value="<?= htmlspecialchars($usuario['frase_motivacional']) ?>">
            <i class="bi bi-chat-heart icono"></i>
          </div>

          <div class="position-relative mb-3">
            <label for="avatar" class="d-block mb-1">Actualizar imagen (PNG o JPG - máx. 2MB)</label>
            <input type="file" name="avatar" accept=".jpg,.jpeg,.png" class="form-control">
          </div>

          <input type="submit" class="btn-login mt-3" value="Actualizar perfil">
        </form>

      </div>
    </div>
  </div>
</main>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>
