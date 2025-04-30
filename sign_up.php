<?php require_once('includes/header.php'); ?>

<body>
<main class="fondo-inicio-registro d-flex align-items-center justify-content-center vh-100">
  <div class="container position-relative">
    <div class="d-flex justify-content-center mb-5">
      <img src="assets/img/logito.png" alt="Habitoo Logo" width="250">
    </div>

    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="caja-login text-center p-4 shadow">

          <h2 class="titulo-login mb-3">Regístrate</h2>
          <p>¿Ya tienes cuenta? <a href="login.php" class="enlace-registro">Inicia Sesión</a></p>

          <form action="includes/registrar_usuario.php" method="POST">
            <input type="text" id="nombres" class="campo-login mb-3" placeholder="Nombres" required>
            
            <input type="text" id="a_paterno" class="campo-login mb-3" placeholder="Apellido Paterno" required>

            <input type="text" id="a_materno" class="campo-login mb-3" placeholder="Apellido Materno">

            <input type="email" id="email" class="campo-login mb-3" placeholder="Correo electrónico" required>

            <div class="position-relative mb-2">
              <input type="password" id="password" class="campo-login" placeholder="Contraseña" required>
              <i class="bi bi-eye-slash-fill icono-ojo" id="togglePassword"></i>
            </div>

            <div class="position-relative mb-2">
              <input type="password" id="confirm_password" class="campo-login" placeholder="Confirmar contraseña" required>
              <i class="bi bi-eye-slash-fill icono-ojo" id="togglePassword"></i>
            </div>

            <input type="submit" class="btn-login mt-4" value="Enviar">

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

togglePassword.addEventListener('click', function () {
  const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordInput.setAttribute('type', type);
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
