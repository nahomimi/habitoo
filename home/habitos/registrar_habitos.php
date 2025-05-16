<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
    exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/header.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/habitoo/includes/conexion.php");

$conexion = new Conexion();
$conn = $conexion->conectar();


// Obtener frecuencias y días para el formulario
$frecuencias = $conn->query("SELECT id, nombre FROM frecuencias")->fetchAll(PDO::FETCH_ASSOC);
$dias = $conn->query("SELECT id, nombre FROM dias")->fetchAll(PDO::FETCH_ASSOC);
?>

<body class="fondo-index d-flex flex-column min-vh-100">
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/menu.php'); ?>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="lista_usuarios_titulo">Crear nuevo hábito</h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="caja-login text-center p-4 shadow position-relative">

                        <!-- Botón de regreso -->
                        <a href="/habitoo/home/index.php" class="btn-regresar-icono" title="Volver al inicio">
                            <i class="bi bi-arrow-left-circle"></i>
                        </a>

                        <?php
// Mostrar mensaje de error si existe
if (!empty($_GET['error'])) {
    $mensaje = htmlspecialchars($_GET['error']);
    echo <<<HTML
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        $mensaje
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
HTML;
}

// Mostrar mensaje de éxito si existe
if (!empty($_GET['success'])) {
    $mensaje = htmlspecialchars($_GET['success']);
    echo <<<HTML
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        $mensaje
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
HTML;
}
?>


                        <form action="/habitoo/includes/registrar_habitos.php" method="POST" novalidate>

                            <div class="position-relative mb-3 mt-5">
                                <input
                                    type="text"
                                    name="nombre"
                                    class="campo-login"
                                    placeholder="Nombre del hábito"
                                    required
                                    maxlength="100">
                            </div>

                            <div class="position-relative mb-3">
                                <textarea
                                    name="descripcion"
                                    class="campo-login"
                                    placeholder="Descripción del hábito"
                                    rows="3"
                                    maxlength="500"></textarea>
                            </div>

                            <div class="position-relative mb-3">
                                <select name="frecuencia_id" class="campo-login" required>
                                    <option value="">Selecciona una frecuencia</option>
                                    <?php foreach ($frecuencias as $f): ?>
                                        <option value="<?= htmlspecialchars($f['id']) ?>"><?= htmlspecialchars($f['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="position-relative mb-3">
                                <input
                                    type="number"
                                    name="meta_semanal"
                                    class="campo-login"
                                    placeholder="Meta semanal (opcional)"
                                    min="1"
                                    max="7">
                            </div>

                            <div class="position-relative mb-3">
                                <label class="d-block mb-2 habitos_registro_titulo" style="font-size: 1.2rem;"><strong>Días para realizar el hábito:</strong></label>
                                <div class="caja-dias d-flex flex-wrap justify-content-center gap-2">
                                    <?php foreach ($dias as $dia): ?>
                                        <input type="checkbox" name="dias[]" value="<?= htmlspecialchars($dia['id']) ?>" id="dia<?= htmlspecialchars($dia['id']) ?>" class="btn-dia d-none">
                                        <label for="dia<?= htmlspecialchars($dia['id']) ?>" class="etiqueta-dia shadow-sm">
                                            <?= htmlspecialchars($dia['nombre']) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>


                            <button type="submit" class="btn-login mt-3">
                                <i class="bi bi-check-circle "></i> Guardar hábito
                            </button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>
</body>