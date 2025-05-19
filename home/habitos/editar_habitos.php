<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/menu.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->conectar();

$usuario_id = $_SESSION['usuario_id'];
$habito_id = $_GET['id'] ?? null;

if (!$habito_id) {
    header("Location: /habitoo/home/habitos/index.php?error=" . urlencode("No se especificó hábito."));
    exit();
}

// Verificar que el hábito pertenece al usuario
$stmt = $conn->prepare("SELECT * FROM habitos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$habito_id, $usuario_id]);
$habito = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$habito) {
    header("Location: /habitoo/home/habitos/index.php?error=" . urlencode("Hábito no encontrado o no tienes permisos."));
    exit();
}

// Obtener frecuencias y días
$frecuencias = $conn->query("SELECT id, nombre FROM frecuencias")->fetchAll(PDO::FETCH_ASSOC);
$dias = $conn->query("SELECT id, nombre FROM dias")->fetchAll(PDO::FETCH_ASSOC);

// Obtener días seleccionados para hábito semanal
$stmtDias = $conn->prepare("SELECT dia_id FROM habitos_dias WHERE habito_id = ?");
$stmtDias->execute([$habito_id]);
$dias_seleccionados = $stmtDias->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="fondo-index d-flex flex-column min-vh-100">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/menu.php'; ?>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="lista_usuarios_titulo">Editar hábito</h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="caja-login text-center p-4 shadow position-relative">

                        <!-- Botón de regreso -->
                        <a href="/habitoo/home/habitos/index.php" class="btn-regresar-icono" title="Volver al inicio">
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

                        <form action="/habitoo/includes/actualizar_habitos.php" method="POST" novalidate>
                            <input type="hidden" name="habito_id" value="<?= htmlspecialchars($habito_id) ?>">

                            <div class="position-relative mb-3 mt-5">
                                <input type="text" name="nombre" class="campo-login" placeholder="Nombre del hábito" required maxlength="100" value="<?= htmlspecialchars($habito['nombre']) ?>">
                            </div>

                            <div class="position-relative mb-3">
                                <textarea name="descripcion" class="campo-login" placeholder="Descripción del hábito" rows="3" maxlength="500"><?= htmlspecialchars($habito['descripcion']) ?></textarea>
                            </div>

                            <div class="position-relative mb-3">
                                <select name="frecuencia_id" id="frecuencia_id" class="campo-login" required>
                                    <option value="">Selecciona una frecuencia</option>
                                    <?php
                                    $orden = ['Diaria', 'Semanal', 'Mensual'];
                                    foreach ($orden as $nombreFrecuencia) {
                                        foreach ($frecuencias as $f) {
                                            if (strtolower($f['nombre']) === strtolower($nombreFrecuencia)) {
                                                $selected = ($f['id'] == $habito['frecuencia_id']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($f['id']) . '" ' . $selected . '>' . htmlspecialchars($f['nombre']) . '</option>';
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Campo Meta Mensual -->
                            <div id="campoMetaMensual" class="position-relative mb-3 <?= $habito['frecuencia_id'] == 3 ? '' : 'd-none' ?>">
                                <select name="meta_mensual" id="meta_mensual" class="campo-login">
                                    <option value="">Selecciona semanas al mes</option>
                                    <?php
                                    for ($i = 1; $i <= 4; $i++) {
                                        $selected = ($habito['meta_mensual'] == $i) ? 'selected' : '';
                                        echo "<option value=\"$i\" $selected>$i semana" . ($i > 1 ? 's' : '') . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Días de la semana -->
                            <div id="campoDias" class="position-relative mb-3 <?= $habito['frecuencia_id'] == 2 ? '' : 'd-none' ?>">
                                <label class="d-block mb-2 habitos_registro_titulo" style="font-size: 1.2rem;"><strong>Días para realizar el hábito:</strong></label>
                                <div class="caja-dias d-flex flex-wrap justify-content-center gap-2">
                                    <?php foreach ($dias as $dia): 
                                        $checked = in_array($dia['id'], $dias_seleccionados) ? 'checked' : '';
                                    ?>
                                        <input type="checkbox" name="dias[]" value="<?= htmlspecialchars($dia['id']) ?>" id="dia<?= htmlspecialchars($dia['id']) ?>" class="btn-dia d-none" <?= $checked ?>>
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

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const frecuenciaSelect = document.getElementById('frecuencia_id');
            const campoDias = document.getElementById('campoDias');
            const campoMeta = document.getElementById('campoMetaMensual');

            function actualizarCampos() {
                const seleccion = frecuenciaSelect.options[frecuenciaSelect.selectedIndex].text.toLowerCase();

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
            }

            actualizarCampos();
            frecuenciaSelect.addEventListener('change', actualizarCampos);
        });
    </script>

      <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'; ?>