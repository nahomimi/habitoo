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

<body class="fondo-index d-flex flex-column min-vh-100">

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/menu_admin.php"); ?>

  <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="container">

      <div class="text-center mb-4">
        <h2 class="lista_usuarios_titulo">Registrar Usuario</h2>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="caja-login text-center p-4 shadow position-relative">

            <!-- Botón de regreso -->
            <a href="/habitoo/home/usuarios/index.php" class="btn-regresar-icono" title="Volver al inicio">
            <i class="bi bi-arrow-left-circle"></i>
            </a>

            <?php 
            // Mostrar mensaje de error si existe
            if (isset($_GET['error'])) {
              echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
              echo htmlspecialchars($_GET['error']);
              echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>";
              echo "</div>";
            }
              // Mostrar mensaje de éxito si existe
            if (isset($_GET['success'])) {
              echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
              echo htmlspecialchars($_GET['success']);
              echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>";
              echo "</div>";
            }
            ?>
            

            <form action="/habitoo/includes/registrar_usuario.php" method="POST" onsubmit="return validarFormulario();">

              <div class="position-relative mb-3 mt-5">
                <input type="text" name="nombres" class="campo-login" placeholder="Nombres" required>
              </div>

              <div class="position-relative mb-3">
                <input type="text" name="a_paterno" class="campo-login" placeholder="Apellido Paterno" required>
              </div>

              <div class="position-relative mb-3">
                <input type="text" name="a_materno" class="campo-login" placeholder="Apellido Materno">
              </div>

              <div class="position-relative mb-3">
                <input type="email" name="email" class="campo-login" placeholder="Correo electrónico" required>
                <i class="bi bi-envelope-fill icono"></i>
              </div>

              <div class="position-relative mb-2">
                <input type="password" id="password" name="password" class="campo-login" placeholder="Contraseña" required>
                <i class="bi bi-eye-slash-fill icono" id="togglePassword1" onclick="togglePassword('password', this)"></i>
              </div>

              <div class="position-relative mb-2">
                <input type="password" id="confirm_password" name="confirm_password" class="campo-login" placeholder="Confirmar contraseña" required>
                <i class="bi bi-eye-slash-fill icono" id="togglePassword2" onclick="togglePassword('confirm_password', this)"></i>
              </div>


              <div class="position-relative mb-3">
                <select name="rol_id" class="campo-login" required>
                  <option value="">Selecciona un rol</option>
                  <option value="1">Administrador</option>
                  <option value="2">Usuario</option>
                </select>
                <i class="bi bi-shield-lock-fill icono"></i>
              </div>

              <input type="submit" class="btn-login mt-3" value="Registrar">
            </form>

          </div>
        </div>
      </div>

    </div>
  </main>

  <script>
function togglePassword(inputId, icon) {
  const input = document.getElementById(inputId);
  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("bi-eye-slash-fill");
    icon.classList.add("bi-eye-fill");
  } else {
    input.type = "password";
    icon.classList.remove("bi-eye-fill");
    icon.classList.add("bi-eye-slash-fill");
  }
}
</script>


  <script>
    function validarFormulario() {
      const pass = document.getElementById("password").value;
      const confirmPass = document.getElementById("confirm_password").value;

      if (pass !== confirmPass) {
        alert("Las contraseñas no coinciden.");
        return false;
      }
      return true;
    }
  </script>

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/footer_admin.php"); ?>
</body>

</html>