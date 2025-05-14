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
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/menu_admin.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();

ini_set('display_errors', 1);
error_reporting(E_ALL);

?>
<main class="fondo-index py-5 min-vh-100 d-flex flex-column">
  <div class="container flex-grow-1">
    <div class="text-center mb-4">
      <h2 class="lista_usuarios_titulo">Edición de Registro de Usuario</h2>
    </div>

    </div>
</main>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer_admin.php"); ?>


</body>

</html>