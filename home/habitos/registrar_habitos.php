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

// Obtener frecuencias y días
$frecuencias = $conn->query("SELECT id, nombre FROM frecuencias")->fetchAll(PDO::FETCH_ASSOC);
$dias = $conn->query("SELECT id, nombre FROM dias")->fetchAll(PDO::FETCH_ASSOC);
?>

<body class="fondo-2 d-flex flex-column min-vh-100">
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
                            <i class="bi bi-arrow-left-circle-fill"></i>
                        </a>

                        <?php
                        if (!empty($_GET['error'])) {
                            echo '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">'
                                . htmlspecialchars($_GET['error']) .
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>';
                        }

                        if (!empty($_GET['success'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">'
                                . htmlspecialchars($_GET['success']) .
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>';
                        }
                        ?>

                        <form action="/habitoo/includes/registrar_habitos.php" method="POST" novalidate>

                            <div class="position-relative mb-3 mt-5">
                                <input type="text" name="nombre" class="campo-login" placeholder="Nombre del hábito" required maxlength="100">
                            </div>

                            <div class="position-relative mb-3">
                                <textarea name="descripcion" class="campo-login" placeholder="Descripción del hábito" rows="3" maxlength="500"></textarea>
                            </div>

                            <div class="position-relative mb-3">
                                <select name="frecuencia_id" id="frecuencia_id" class="campo-login" required>
                                    <option value="">Selecciona una frecuencia</option>
                                    <?php
                                    $orden = ['Diaria', 'Semanal', 'Mensual'];
                                    foreach ($orden as $nombre) {
                                        foreach ($frecuencias as $f) {
                                            if (strtolower($f['nombre']) === strtolower($nombre)) {
                                                echo '<option value="' . htmlspecialchars($f['id']) . '">' . htmlspecialchars($f['nombre']) . '</option>';
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>


                            <!-- Campo Meta Mensual -->
                            <div id="campoMetaMensual" class="position-relative mb-3 d-none">
                                <select name="meta_mensual" id="meta_mensual" class="campo-login">
                                    <option value="">Selecciona semanas al mes</option>
                                    <option value="1">1 semana</option>
                                    <option value="2">2 semanas</option>
                                    <option value="3">3 semanas</option>
                                    <option value="4">4 semanas</option>
                                </select>
                            </div>

                            <!-- Días de la semana -->
                            <div id="campoDias" class="position-relative mb-3 d-none">
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
                                <i class="bi bi-check-circle"></i> Guardar hábito
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const frecuenciaSelect = document.getElementById('frecuencia_id');
            const campoDias = document.getElementById('campoDias');
            const campoMeta = document.getElementById('campoMetaMensual');
            const checkboxesDias = document.querySelectorAll('input[name="dias[]"]');
            const metaMensualSelect = document.getElementById('meta_mensual');

            frecuenciaSelect.addEventListener('change', function() {
                const seleccion = this.options[this.selectedIndex].text.toLowerCase();

                // Limpiar campos ocultos
                checkboxesDias.forEach(cb => cb.checked = false);
                metaMensualSelect.value = "";

                if (seleccion === 'diaria') {
                    campoDias.classList.add('d-none');
                    campoMeta.classList.add('d-none');
                } else if (seleccion === 'semanal') {
                    campoDias.classList.remove('d-none');
                    campoMeta.classList.add('d-none');
                } else if (seleccion === 'mensual') {
                    campoDias.classList.add('d-none');
                    campoMeta.classList.remove('d-none');
                } else {
                    campoDias.classList.add('d-none');
                    campoMeta.classList.add('d-none');
                }
            });
        });
    </script>


</body>