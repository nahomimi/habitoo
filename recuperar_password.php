<?php require_once('includes/header.php'); ?>
<body class="fondo-inicio-registro">
<main class="d-flex align-items-center justify-content-center vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="caja-login text-center p-4 shadow">
          <h2 class="titulo-login mb-3">Recuperar Contraseña</h2>
          <p>Ingresa tu correo y te enviaremos instrucciones para restablecer tu contraseña.</p>

          <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
          <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
          <?php endif; ?>

          <form action="includes/enviar_recovery.php" method="POST">
            <input type="email" name="email" class="campo-login" placeholder="Correo electrónico" required>
            <input type="submit" class="btn-login mt-3" value="Enviar correo">
          </form>
        </div>
      </div>
    </div>
  </div>
</main>
</body>
</html>
