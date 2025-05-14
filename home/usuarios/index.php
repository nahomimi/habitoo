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

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<body class="fondo-inicio-admin flex-grow-1 py-4">
  <main class="d-flex mb-5 ">
    <div class="container position-relative">
      <div class="d-flex justify-content-center mb-5">

      </div>

      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="caja-usuarios p-4 shadow">
            <h2 class="titulo-usuarios mb-3">Usuarios Registrados</h2>

            <?php
            // Mostrar mensaje de error si existe
            if (isset($_GET['error'])) {
              echo "<p class='alert alert-aguas'>" . htmlspecialchars($_GET['error']) . "</p>";
            }
            ?>

            <div class="table-responsive">
              <table class="table table-bordered shadow text-center align-middle tabla-usuarios">
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
                          <a href="editar.php?id=<?= $u['id'] ?>" class="btn-editar me-1" title="Editar">
                            <i class="bi bi-pencil-fill"></i>
                          </a>
                          <a href="eliminar.php?id=<?= $u['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar al usuario <?= $u['nombre_completo'] ?>?')" title="Eliminar">
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
        </div>
      </div>
    </div>
  </main>


  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer_admin.php"); ?>

</body>

</html>