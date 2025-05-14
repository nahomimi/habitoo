<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Solo se inicia si no está iniciada
}

// Si no hay sesión activa, redirige a login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
    exit();
}
?>

<?php 
  require_once ($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/header.php');
?>

<?php 
  require_once ($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/menu.php');
?>

<main class="fondo-inicio-usuario flex-grow-1 py-4 "> <!-- Área de contenido principal -->
  <div class="container">
    <h1>Bienvenido a Habitoo</h1>
    <p>Tu aplicación para gestión de hábitos</p>
  </div>
  </main>

<?php 
  require_once ($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php');
?>