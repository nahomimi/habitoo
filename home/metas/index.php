<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['usuario_id'])) {
  header("Location: /habitoo/login.php?error=" . urlencode("Debe iniciar sesión"));
  exit();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/header.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/menu.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/conexion.php');

$conexion = new Conexion();
$conn = $conexion->conectar();
$usuario_id = $_SESSION['usuario_id'];

// Obtener hábitos inactivos
$sql_inactivos = "SELECT h.id, h.nombre, h.descripcion, f.nombre AS frecuencia, h.fecha_creacion, h.fecha_actualizacion, h.contador
        FROM habitos h
        JOIN frecuencias f ON h.frecuencia_id = f.id
        WHERE h.usuario_id = :usuario_id
          AND h.estado = 'inactivo'
        ORDER BY h.fecha_actualizacion DESC";

$stmt_inactivos = $conn->prepare($sql_inactivos);
$stmt_inactivos->execute([':usuario_id' => $usuario_id]);
$habitos_inactivos = $stmt_inactivos->fetchAll(PDO::FETCH_ASSOC);

// Contar hábitos completados vs. activos
$sql_total = "SELECT estado, COUNT(*) AS total FROM habitos WHERE usuario_id = :usuario_id GROUP BY estado";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->execute([':usuario_id' => $usuario_id]);

$activos = 0;
$inactivos = 0;

while ($row = $stmt_total->fetch(PDO::FETCH_ASSOC)) {
  if ($row['estado'] == 'activo') {
    $activos = (int)$row['total'];
  } elseif ($row['estado'] == 'inactivo') {
    $inactivos = (int)$row['total'];
  }
}
?>

<body class="fondo-progreso d-flex flex-column min-vh-100">
  <main class="flex-grow-1 py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="lista_usuarios_titulo">Hábitos Terminados</h2>
      </div>

      <!-- Card con gráfica y Groowi centrados -->
      <div class="row justify-content-center mb-5">
        <div class="col-md-10">
          <div class="card shadow border-0 p-4 bg-white text-center">
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-4">
              <div>
                <canvas id="graficoHabitos" style="max-width: 320px; max-height: 320px;"></canvas>
              </div>
              <div>
                <img src="/habitoo/assets/img/groowi.png" alt="Groowi" class="img-fluid" style="max-width: 220px;">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de hábitos inactivos -->
      <div class="table-responsive">
        <table class="table-bordered shadow text-center align-middle lista_usuarios_tabla">
          <thead class="table-light">
            <tr>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Frecuencia</th>
              <th>Contador</th>
              <th>Fecha de Creación</th>
              <th>Fecha de Finalización</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($habitos_inactivos)): ?>
              <?php foreach ($habitos_inactivos as $habito): ?>
                <tr>
                  <td><?= htmlspecialchars($habito['nombre']) ?></td>
                  <td><?= htmlspecialchars($habito['descripcion']) ?></td>
                  <td><?= htmlspecialchars($habito['frecuencia']) ?></td>
                  <td><span class="badge bg-dark"><?= $habito['contador'] ?></span></td>
                  <td><?= date('d/m/Y', strtotime($habito['fecha_creacion'])) ?></td>
                  <td><?= date('d/m/Y', strtotime($habito['fecha_actualizacion'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No hay hábitos terminados aún.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="text-center mt-4">
        <a href="/habitoo/home/habitos/index.php" class="btn-login">
          Volver a hábitos activos <i class="bi bi-arrow-left-circle"></i>
        </a>
      </div>
    </div>
  </main>

  <?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>

  <!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Plugin para mostrar porcentajes en el gráfico -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
  const ctx = document.getElementById('graficoHabitos').getContext('2d');
  const inactivos = <?= $inactivos ?>;
  const activos = <?= $activos ?>;
  const total = inactivos + activos;

  const graficoHabitos = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Completados', 'Activos'],
      datasets: [{
        data: [inactivos, activos],
        backgroundColor: [
          'rgba(75, 192, 192, 0.7)',
          'rgba(255, 205, 86, 0.7)'
        ],
        borderColor: [
          'rgba(75, 192, 192, 1)',
          'rgba(255, 205, 86, 1)'
        ],
        borderWidth: 2
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#333',
            font: {
              size: 14
            }
          }
        },
        datalabels: {
          color: '#000',
          font: {
            weight: 'bold',
            size: 16
          },
          formatter: (value, ctx) => {
            let percentage = total ? (value / total * 100).toFixed(1) : 0;
            return percentage + '%';
          }
        }
      }
    },
    plugins: [ChartDataLabels]
  });
</script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/habitoo/includes/footer.php'); ?>