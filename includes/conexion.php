<?php 
$host = 'localhost';
$dbname = 'habitos';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ← aquí ya está bien
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
?>
