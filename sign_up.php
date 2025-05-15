

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
                <i class="bi bi-eye-slash-fill icono" id="togglePassword1" onclick="togglePassword('password', this)"></i>
              </div>

              <div class="position-relative mb-2">
                <input type="password" id="confirm_password" name="confirm_password" class="campo-login" placeholder="Confirmar contraseña" required>
                <i class="bi bi-eye-slash-fill icono" id="togglePassword2" onclick="togglePassword('confirm_password', this)"></i>
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

</body>
</html>
