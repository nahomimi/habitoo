<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Solo permite el acceso si está logueado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
  header("Location: /habitoo/login.php?error=" . urlencode("Acceso restringido para administradores"));
  exit();
}

?>
<body class="fondo-5 d-flex flex-column min-vh-100">
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/header_admin.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/menu_admin.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* --------- Buscador en pausa ----------
$busqueda = $_GET['busqueda'] ?? '';

if ($busqueda !== '') {
    $sql = "... WHERE condiciones LIKE :busqueda ...";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':busqueda' => "%$busqueda%"]);
} else {
*/
$sql = "SELECT 
            u.id,
            CONCAT(u.nombres, ' ', u.a_paterno, ' ', u.a_materno) AS nombre_completo,
            u.email,
            r.nombre AS rol,
            e.descripcion AS estatus
        FROM usuarios u
        JOIN roles r ON u.rol_id = r.id
        JOIN estatus e ON u.estatus_id = e.id";

$stmt = $conn->query($sql);
// }

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class=" py-5 d-flex flex-column">
  <div class="container flex-grow-1">
    <div class="text-center mb-4">
      <h2 class="lista_usuarios_titulo">Usuarios Registrados</h2>
    </div>

    <!-- Buscador en pausa
    <form method="GET" class="mb-4 d-flex justify-content-center">
      <input type="text" name="busqueda" class="campo-login me-2" placeholder="Buscar por nombre o correo" value="<?= htmlspecialchars($busqueda) ?>">
      <button type="submit" class="btn-login">Buscar</button>
    </form>
    -->

    <div class="table-responsive">
      <table class=" table-bordered shadow text-center align-middle lista_usuarios_tabla">
        
        <?php if (isset($_GET['exito'])): ?>
          <div class="alert alert-success text-center lista_usuarios_alerta">
            <?= htmlspecialchars($_GET['exito']) ?>
          </div>
        <?php endif; ?>



        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Nombre completo</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estatus</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>

          <?php if (count($usuarios) > 0): ?>
            <?php foreach ($usuarios as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= $u['nombre_completo'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['rol'] ?></td>
                <td><?= $u['estatus'] ?></td>
                <td>
                  <!-- Icono para ver -->
                  <a href="ver.php?id=<?= $u['id'] ?>" class="btn-ver" title="Ver">
                    <i class="bi bi-eye-fill"></i>
                  </a>

                  <!-- Icono para editar -->
                  <a href="editar.php?id=<?= $u['id'] ?>" class="btn-editar me-1" title="Editar">
                    <i class="bi bi-pencil-fill"></i>
                  </a>

                  <!-- Icono para eliminar -->
                  <a href="eliminar.php?id=<?= $u['id'] ?>" class="btn-eliminar"
                    onclick="return confirm('¿Eliminar al usuario <?= $u['nombre_completo'] ?>?')" title="Eliminar">
                    <i class="bi bi-trash-fill"></i>
                  </a>
                </td>

              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">No se encontraron usuarios.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
  // Esperar 4 segundos y ocultar la alerta con efecto suave
  setTimeout(function () {
    const alerta = document.getElementById("alerta-exito");
    if (alerta) {
      alerta.style.transition = "opacity 0.5s ease";
      alerta.style.opacity = 0;
      
      // Después de la transición, ocultar completamente
      setTimeout(() => alerta.style.display = "none", 500);
    }
  }, 4000);
</script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer_admin.php"); ?>