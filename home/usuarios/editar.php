<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
  header("Location: /habitoo/login.php?error=" . urlencode("Acceso restringido para administradores"));
  exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/header_admin.php");


?>
<body class="fondo-2 d-flex flex-column min-vh-100">
<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/menu_admin.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();

$id = $_GET['id'] ?? null;
if (!$id) {
  header("Location: /habitoo/home/usuarios/index.php?error=Usuario no especificado");
  exit();
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
  header("Location: /habitoo/home/usuarios/index.php?error=Usuario no encontrado");
  exit();
}
?>

<main class="py-5 min-vh-100 d-flex flex-column">
  <div class="container flex-grow-1">
  
    <div class="text-center mb-4">
      <h2 class="lista_usuarios_titulo">Edición de Registro de Usuario</h2>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="caja-login text-center p-4 shadow position-relative">
          
          <!-- Botón de regreso -->
          <a href="/habitoo/home/usuarios/index.php" class="btn-regresar-icono" title="Volver al inicio">
          <i class="bi bi-arrow-left-circle"></i>
          </a>
        
          <form action="/habitoo/includes/actualizar.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">

            <div class="position-relative mb-3 mt-5">
              <input type="text" name="nombres" class="campo-login" placeholder="Nombres" required value="<?= htmlspecialchars($usuario['nombres']) ?>">              
            </div>

            <div class="position-relative mb-3">
              <input type="text" name="a_paterno" class="campo-login" placeholder="Apellido Paterno" required value="<?= htmlspecialchars($usuario['a_paterno']) ?>">
            </div>

            <div class="position-relative mb-3">
              <input type="text" name="a_materno" class="campo-login" placeholder="Apellido Materno" value="<?= htmlspecialchars($usuario['a_materno']) ?>">
            
            </div>

            <div class="position-relative mb-3">
              <input type="email" name="email" class="campo-login" placeholder="Correo electrónico" required value="<?= htmlspecialchars($usuario['email']) ?>">
              <i class="bi bi-envelope-fill icono"></i>
            </div>

            <div class="position-relative mb-3">
              <input type="text" name="telefono" class="campo-login" placeholder="Teléfono" value="<?= htmlspecialchars($usuario['telefono']) ?>">
              <i class="bi bi-telephone-fill icono"></i>
            </div>

            <div class="position-relative mb-3">
              <input type="date" name="fecha_nacimiento" class="campo-login" placeholder="Fecha de nacimiento" value="<?= htmlspecialchars($usuario['fecha_nacimiento']) ?>">
              <i class="bi bi-calendar-date-fill icono" style="background-color: white;"></i>
            </div>

            <div class="position-relative mb-3">
              <input type="text" name="frase_motivacional" class="campo-login" placeholder="Frase motivacional" value="<?= htmlspecialchars($usuario['frase_motivacional']) ?>">
              <i class="bi bi-chat-heart icono"></i>
            </div>

            <div class="position-relative mb-3">
              <select name="rol_id" class="campo-login" required>
                <option value="">Selecciona un rol</option>
                <option value="1" <?= $usuario['rol_id'] == 1 ? 'selected' : '' ?>>Administrador</option>
                <option value="2" <?= $usuario['rol_id'] == 2 ? 'selected' : '' ?>>Usuario</option>
              </select>
              <i class="bi bi-shield-lock-fill icono"></i>
            </div>

            <div class="position-relative mb-3">
              <select name="estatus_id" class="campo-login" required>
                <option value="">Selecciona estatus</option>
                <option value="1" <?= $usuario['estatus_id'] == 1 ? 'selected' : '' ?>>Activo</option>
                <option value="2" <?= $usuario['estatus_id'] == 2 ? 'selected' : '' ?>>Inactivo</option>
              </select>
              <i class="bi bi-toggle-on icono"></i>
            </div>

            <input type="submit" class="btn-login mt-3" value="Guardar Cambios">
          </form>

        </div>
      </div>
    </div>

  </div>
</main>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer_admin.php"); ?>
</body>
</html>
