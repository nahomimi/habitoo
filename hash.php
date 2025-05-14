<?php
// Genera un hash de la contraseña
$password = "1234"; // Cambia por la contraseña deseada
echo password_hash($password, PASSWORD_DEFAULT);
?>
