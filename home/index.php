<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start(); // Solo se inicia si no está iniciada
}

if (!isset($_SESSION['usuario_id'])) {
  header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
  exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/header.php');
?>

<body class="fondo-welcome">
  <?php

  require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/menu.php');
  require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

  $conexion = new Conexion();
  $conn = $conexion->conectar();

  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  // Obtener nombre completo y frase motivacional
  $usuario_id = $_SESSION['usuario_id'];
  $stmt = $conn->prepare("SELECT CONCAT(nombres, ' ', a_paterno, ' ', a_materno) AS nombre_completo, frase_motivacional FROM usuarios WHERE id = ?");
  $stmt->execute([$usuario_id]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  ?>

  <main>
    <div class="container">
      <div class="row flex-column align-items-center">

        <!-- Presentación personalizada -->
        <div class="col-11 text-center mt-4 mb-3">
          <div class="bienvenida-card p-4  sombra-suave">
            <h2 class="bienvenida-nombre mb-3">
              <strong>
                ¡Hola, <?= htmlspecialchars($usuario['nombre_completo']) ?>! 🌟<br>
                Recuerda:
              </strong> Cada hábito te acerca a tu mejor versión. ✨
            </h2>

          </div>
        </div>

        <!-- Contenedor general en fila -->
        <div class="row d-flex justify-content-center align-items-start flex-wrap gap-4 mt-4 mb-5">

          <!-- Calendario + frase motivacional -->
          <div class="col-auto calendario-col text-center">
            <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/calendario.php"); ?>

            <!-- Frase debajo del calendario (siempre en su lugar) -->
            <div class="frase-calendario  p-3 sombra-suave ">
              <p class="mb-0 fst-italic">"<?php echo htmlspecialchars($usuario['frase_motivacional']); ?>"</p>
            </div>
          </div>


          <!-- Imagen de Growi -->
          <div class="col-auto imagen-growi-col text-center">
            <img src="/habitoo/assets/img/groowi-index.png" alt="Growi" class="groowi-index">
          </div>

          <!-- Botones -->
            <div class="col-auto botones-col d-flex flex-column align-items-center gap-3">
            <a href="<?= '/habitoo/home/habitos/registrar_habitos.php' ?>" class="boton-habito boton-agregar boton-cuadrado">
              <i class="bi bi-plus-circle agregar-icon"></i> Agregar hábito
            </a>
            <a href="<?= '/habitoo/home/habitos/index.php' ?>"  class="boton-habito boton-ver boton-cuadrado">
              <i class="bi bi-eye ver-icon"></i> Ver mis hábitos
            </a>
            <a href="<?= '/habitoo/home/habitos/registrar.php' ?>"  class="boton-habito boton-huella boton-cuadrado mb-2">
              <i class="bi bi-clock-history huella-icon"></i> Huella de mis Metas
            </a>
          </div>

        </div>


      </div>
    </div>
  </main>

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>