<?php
session_start();

// Si ya hay sesión activa, redirige según el rol
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['rol_id'] == 1) {
        header("Location: home/usuarios/index.php"); // Página de admin
        exit();
    } elseif ($_SESSION['rol_id'] == 2) {
        header("Location: home/index.php"); // Página de usuario normal
        exit();
    }
}
?>

<?php require_once('includes/header.php'); ?>

<body class="fondo-inicio-registro">
<main class="d-flex align-items-center justify-content-center vh-100">
  <div class="container position-relative">
    <div class="d-flex justify-content-center mb-5">
      <a href="index.php">
        <img src="assets/img/logito.png" alt="Habitoo Logo" width="250">
      </a>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="caja-login text-center p-4 shadow">

          <h2 class="titulo-login mb-3">Inicia Sesión</h2>
          <p>¿Aún no tienes cuenta? <a href="sign_up.php" class="enlace-registro">Regístrate</a></p>

            <?php 
            // Mostrar mensaje de error si existe
            if (isset($_GET['error'])) {
              echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
              echo htmlspecialchars($_GET['error']);
              echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>";
              echo "</div>";
            }
            ?>

          <form action="includes/verificar_sesion.php" method="POST">

            <div class="position-relative mb-2">  
              <input type="email" name="email" class="campo-login" placeholder="Correo electrónico" required>
              <i class="bi bi-envelope-fill icono"></i>
            </div>

            <div class="position-relative mb-2">
              <input type="password" id="password" name="password" class="campo-login" placeholder="Contraseña" required>
              <i class="bi bi-eye-slash-fill icono" id="togglePassword"></i>
            </div>

            <div class="mb-4">
              <a href="/habitoo/recuperar_password.php" class="enlace-olvide">¿Olvidó su contraseña?</a>
            </div>

            <input type="submit" class="btn-login" value="Ingresar">
          </form>

          <!-- <p class="mt-4">O inicia sesión con</p>
          <a href="#" class="login-icono"><i class="bi bi-google"></i></a> -->

        </div>
      </div>
    </div>
  </div>
</main>

<script>
// Script para mostrar/ocultar contraseña
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');

togglePassword.addEventListener('click', function () {
  const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordInput.setAttribute('type', type);
  this.classList.toggle('bi-eye-fill');
  this.classList.toggle('bi-eye-slash-fill');
});
</script>

</body>
</html>
