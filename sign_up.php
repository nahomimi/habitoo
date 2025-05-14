<?php
// Activa el manejo de sesiones (debería ir al inicio de tu archivo PHP)
session_start();

// // Verifica si el usuario ya está logueado, de no estarlo, redirige a la página de login
// if (!isset($_SESSION['usuario_id'])) {
//   header("Location: http://localhost/habitoo/sign_up.php");
//   exit();
// }

?>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/header.php'); ?>

<body class="fondo-inicio-registro">
<main class="d-flex align-items-center justify-content-center vh-100">
  <div class="container position-relative">
    <div class="d-flex justify-content-center mb-5">
      <a href="/habitoo/index.php">
        <img src="/habitoo/assets/img/logito.png" alt="Habitoo Logo" width="250">
      </a>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="caja-login text-center p-4 shadow">

          <h2 class="titulo-login mb-3">Regístrate</h2>
          <p>¿Ya tienes cuenta? <a href="/habitoo/login.php" class="enlace-registro">Inicia Sesión</a></p>

          <form action="/habitoo/includes/registrar_usuario.php" method="POST" onsubmit="return validarFormulario();">
            <input type="text" id="nombres" name="nombres" class="campo-login mb-3" placeholder="Nombres" required>
            
            <input type="text" id="a_paterno" name="a_paterno" class="campo-login mb-3" placeholder="Apellido Paterno" required>

            <input type="text" id="a_materno" name="a_materno" class="campo-login mb-3" placeholder="Apellido Materno">

            <div class="position-relative mb-3">
              <input type="email" id="email" name="email" class="campo-login" placeholder="Correo electrónico" required>
              <i class="bi bi-envelope-fill icono"></i>
            </div>

            <div class="position-relative mb-2">
              <input type="password" id="password" name="password" class="campo-login" placeholder="Contraseña" required>
              <i class="bi bi-eye-slash-fill icono" id="togglePassword"></i>
            </div>

            <div class="position-relative mb-2">
              <input type="password" id="confirm_password" name="confirm_password" class="campo-login" placeholder="Confirmar contraseña" required>
              <i class="bi bi-eye-slash-fill icono" id="togglePassword"></i>
            </div>

            <input type="submit" class="btn-login mt-4" value="Registrarse">

          </form>

          <p class="mt-3">O regístrate con</p>
          <a href="#" class="login-icono"><i class="bi bi-google"></i></a>

        </div>
      </div>
    </div>
  </div>
</main>

<script>
// Script para mostrar/ocultar contraseña
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirm_password');

togglePassword.addEventListener('click', function () {
  const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordInput.setAttribute('type', type);
  confirmPasswordInput.setAttribute('type', type); // Para mostrar/ocultar ambas contraseñas
  this.classList.toggle('bi-eye-fill');
  this.classList.toggle('bi-eye-slash-fill');
});
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

</body>
</html>
