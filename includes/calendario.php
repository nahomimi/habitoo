<?php
date_default_timezone_set('America/Mexico_City');

// Parámetros del mes actual o del mes a mostrar
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

// Día actual (para resaltarlo)
$dia_actual = date('j');
$mes_actual = date('n');
$anio_actual = date('Y');

// Crear fecha para formatear el nombre del mes
$fecha = new DateTime("$anio-$mes-01");

// Usamos IntlDateFormatter para mostrar el mes en español
$formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
$formatter->setPattern("MMMM");
$nombre_mes = ucfirst($formatter->format($fecha));

// Número de días del mes
$dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

// Día de la semana en que empieza el mes (0 = domingo, 1 = lunes, ...)
$dia_semana_inicio = (int)date('w', strtotime("$anio-$mes-01"));

// Calcular mes anterior y siguiente
$mes_anterior = $mes - 1;
$anio_anterior = $anio;
if ($mes_anterior < 1) {
    $mes_anterior = 12;
    $anio_anterior--;
}
$mes_siguiente = $mes + 1;
$anio_siguiente = $anio;
if ($mes_siguiente > 12) {
    $mes_siguiente = 1;
    $anio_siguiente++;
}
?>

<!-- Contenedor principal -->
<div class="calendario-habitoo">

  <!-- Encabezado con flechas de navegación y nombre del mes -->
<div class="encabezado-calendario">
    <a class="flecha" href="?mes=<?=$mes_anterior?>&anio=<?=$anio_anterior?>">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h2><?= $nombre_mes . " $anio" ?></h2>
    <a class="flecha" href="?mes=<?=$mes_siguiente?>&anio=<?=$anio_siguiente?>">
        <i class="bi bi-arrow-right"></i>
    </a>
</div>

  <!-- Cabecera con los días de la semana -->
  <div class="grid-calendario">
    <?php
    $dias_semana = ['D', 'L', 'M', 'M', 'J', 'V', 'S'];
    foreach ($dias_semana as $dia) {
        echo "<div class='dia-semana'>$dia</div>";
    }

    // Imprimir celdas vacías antes del primer día del mes
    for ($i = 0; $i < $dia_semana_inicio; $i++) {
        echo "<div class='celda'></div>";
    }

    // Imprimir los días del mes
    for ($dia = 1; $dia <= $dias_mes; $dia++) {
        $clase = 'celda-dia';
        if ($dia == $dia_actual && $mes == $mes_actual && $anio == $anio_actual) {
            $clase .= ' actual';
        }
        echo "<div class='$clase'>$dia</div>";
    }
    ?>
  </div>
</div>
