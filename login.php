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

          <h2 class="titulo-login mb-3">Inicia Sesión</h2>
          <p>¿Aún no tienes cuenta? <a href="sign_up.php" class="enlace-registro">Regístrate</a></p>

          <form action="includes/verificar_sesion.php" method="POST">

            <input type="email" name="email" class="campo-login mb-3" placeholder="Correo electrónico">

            <div class="position-relative mb-2">
              <input type="password" id="password" name="password" class="campo-login" placeholder="Contraseña">
              <i class="bi bi-eye-slash-fill icono-ojo" id="togglePassword"></i>
            </div>

            <div class="mb-4">
              <a href="#" class="enlace-olvide">¿Olvidó su contraseña?</a>
            </div>

            <input type="submit" class="btn-login" value="Enviar">
          </form>

          <p class="mt-4">O inicia sesión con</p>
          <a href="#" class="login-icono"><i class="bi bi-google"></i></a>

          <?php 
            //para mostrar los errores de login
            if (isset($_GET['error'])) {
                echo "<p class='alert alert-aguas'>Credenciales erróneas </p>";
            }
        
            ?>

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
