<?php
// Iniciar sesión para validar acceso
session_start();
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Validar acceso exclusivo para finanzas
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "finanzas") {
    echo "<script>
            alert('No tienes permiso para acceder a esta página');
            window.location.href = '../index.php';
          </script>";
    exit();
}
// Obtener fechas del filtro
$anio_actual = date("Y");
$fecha_desde = isset($_GET["fecha_desde"]) && $_GET["fecha_desde"] != "" ? $_GET["fecha_desde"] : $anio_actual . "-01-01";
$fecha_hasta = isset($_GET["fecha_hasta"]) && $_GET["fecha_hasta"] != "" ? $_GET["fecha_hasta"] : date("Y-m-d");
// Validar formato de fechas
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_desde)) {
    $fecha_desde = $anio_actual . "-01-01";
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_hasta)) {
    $fecha_hasta = date("Y-m-d");
}
// Consultar resumen del período solo con pagos confirmados
$sql_totales = "SELECT
                    COUNT(p.id_pedido) AS total_pedidos,
                    COALESCE(SUM(p.total_pedido), 0) AS ingresos,
                    COALESCE(SUM(p.total_productos), 0) AS subtotal_productos,
                    COALESCE(SUM(p.costo_despacho), 0) AS total_despacho,
                    COALESCE(AVG(p.total_pedido), 0) AS ticket_promedio
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                WHERE pa.estado_pago = 'pagado'
                AND p.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)";
$stmt_totales = mysqli_prepare($conexion, $sql_totales);
mysqli_stmt_bind_param($stmt_totales, "ss", $fecha_desde, $fecha_hasta);
mysqli_stmt_execute($stmt_totales);
$totales = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_totales));
// Consultar ingresos por mes
$sql_por_mes = "SELECT
                    DATE_FORMAT(p.fecha_pedido, '%Y-%m') AS mes_key,
                    DATE_FORMAT(p.fecha_pedido, '%m/%Y') AS mes_label,
                    COALESCE(SUM(p.total_pedido), 0) AS ingresos,
                    COUNT(p.id_pedido) AS pedidos
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                WHERE pa.estado_pago = 'pagado'
                AND p.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                GROUP BY DATE_FORMAT(p.fecha_pedido, '%Y-%m'), DATE_FORMAT(p.fecha_pedido, '%m/%Y')
                ORDER BY mes_key ASC";
$stmt_por_mes = mysqli_prepare($conexion, $sql_por_mes);
mysqli_stmt_bind_param($stmt_por_mes, "ss", $fecha_desde, $fecha_hasta);
mysqli_stmt_execute($stmt_por_mes);
$resultado_por_mes = mysqli_stmt_get_result($stmt_por_mes);
// Preparar datos del gráfico por mes
$labels_mes = array();
$ingresos_mes = array();
$pedidos_mes = array();
$tabla_meses = array();
while ($fila = mysqli_fetch_assoc($resultado_por_mes)) {
    $labels_mes[] = $fila["mes_label"];
    $ingresos_mes[] = intval($fila["ingresos"]);
    $pedidos_mes[] = intval($fila["pedidos"]);
    $tabla_meses[] = $fila;
}
// Consultar ingresos por tipo de entrega
$sql_entrega = "SELECT
                    p.tipo_entrega,
                    COUNT(p.id_pedido) AS cantidad,
                    COALESCE(SUM(p.total_pedido), 0) AS ingresos
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                WHERE pa.estado_pago = 'pagado'
                AND p.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                GROUP BY p.tipo_entrega";
$stmt_entrega = mysqli_prepare($conexion, $sql_entrega);
mysqli_stmt_bind_param($stmt_entrega, "ss", $fecha_desde, $fecha_hasta);
mysqli_stmt_execute($stmt_entrega);
$resultado_entrega = mysqli_stmt_get_result($stmt_entrega);
// Preparar datos del gráfico por entrega
$labels_entrega = array();
$valores_entrega = array();
while ($fila = mysqli_fetch_assoc($resultado_entrega)) {
    $label_entrega = $fila["tipo_entrega"] == "despacho_domicilio" ? "Despacho a domicilio" : "Retiro en tienda";
    $labels_entrega[] = $label_entrega;
    $valores_entrega[] = intval($fila["ingresos"]);
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reportes de ventas - PequeMundo</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Estilos propios -->
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
        <link rel="stylesheet" href="../css/finanzas.css">
    </head>
    <body>
        <!-- Menú -->
        <?php include "../masterpage/menu.php"; ?>
        <!-- Contenido principal -->
        <main class="container my-5">
            <!-- Encabezado -->
            <section class="d-flex align-items-center mb-4 gap-3">
                <i class="fa-solid fa-chart-bar fa-2x icono-finanzas"></i>
                <div>
                    <h2 class="mb-0">Reportes de ventas</h2>
                    <small class="text-muted">Analizar ingresos confirmados por período.</small>
                </div>
            </section>
            <!-- Navegación interna -->
            <section class="mb-4">
                <a href="reportes.php" class="btn btn-finalizar-compra btn-sm me-2">Reportes</a>
                <a href="productos_mas_vendidos.php" class="btn btn-seguir-comprando btn-sm me-2">Productos más vendidos</a>
                <a href="historial_ventas.php" class="btn btn-seguir-comprando btn-sm">Historial de ventas</a>
            </section>
            <!-- Filtro de fechas -->
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="reportes.php" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control" value="<?php echo htmlspecialchars($fecha_desde); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" value="<?php echo htmlspecialchars($fecha_hasta); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-filtrar w-100">
                                <i class="fa-solid fa-filter me-1"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </section>
            <!-- Resumen del período -->
            <section class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <article class="card shadow-sm border-0 stat-box h-100">
                        <div class="card-body">
                            <div class="stat-label">Ingresos del período</div>
                            <div class="stat-numero">$<?php echo number_format($totales["ingresos"], 0, ",", "."); ?></div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <article class="card shadow-sm border-0 stat-box h-100">
                        <div class="card-body">
                            <div class="stat-label">Pedidos pagados</div>
                            <div class="stat-numero"><?php echo intval($totales["total_pedidos"]); ?></div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <article class="card shadow-sm border-0 stat-box h-100">
                        <div class="card-body">
                            <div class="stat-label">Ticket promedio</div>
                            <div class="stat-numero">$<?php echo number_format($totales["ticket_promedio"], 0, ",", "."); ?></div>
                        </div>
                    </article>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <article class="card shadow-sm border-0 stat-box h-100">
                        <div class="card-body">
                            <div class="stat-label">Ingresos por despacho</div>
                            <div class="stat-numero">$<?php echo number_format($totales["total_despacho"], 0, ",", "."); ?></div>
                        </div>
                    </article>
                </div>
            </section>
            <!-- Gráficos -->
            <section class="row g-4 mb-4">
                <div class="col-lg-8">
                    <article class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Ingresos por mes</h5>
                            <?php if (count($labels_mes) > 0) { ?>
                                <canvas id="graficoLinea" height="110"></canvas>
                            <?php } else { ?>
                                <p class="text-muted mb-0">No hay ventas pagadas para graficar en este período.</p>
                            <?php } ?>
                        </div>
                    </article>
                </div>
                <div class="col-lg-4">
                    <article class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title">Ingresos por tipo de entrega</h5>
                            <?php if (count($labels_entrega) > 0) { ?>
                                <canvas id="graficoEntrega" height="180"></canvas>
                            <?php } else { ?>
                                <p class="text-muted mb-0">No hay datos de entrega para este período.</p>
                            <?php } ?>
                        </div>
                    </article>
                </div>
            </section>
            <!-- Tabla mensual -->
            <section class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Detalle mensual</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="tabla-finanzas-head">
                                <tr>
                                    <th>Mes</th>
                                    <th class="text-end">Pedidos pagados</th>
                                    <th class="text-end">Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($tabla_meses) > 0) { ?>
                                    <?php foreach ($tabla_meses as $fila) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($fila["mes_label"]); ?></td>
                                            <td class="text-end"><?php echo intval($fila["pedidos"]); ?></td>
                                            <td class="text-end">$<?php echo number_format($fila["ingresos"], 0, ",", "."); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay ventas pagadas en el período seleccionado.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
        <!-- Footer -->
        <?php include "../masterpage/footer.php"; ?>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const labelsMes = <?php echo json_encode($labels_mes, JSON_UNESCAPED_UNICODE); ?>;
            const ingresosMes = <?php echo json_encode($ingresos_mes); ?>;
            const pedidosMes = <?php echo json_encode($pedidos_mes); ?>;
            const labelsEntrega = <?php echo json_encode($labels_entrega, JSON_UNESCAPED_UNICODE); ?>;
            const valoresEntrega = <?php echo json_encode($valores_entrega); ?>;
            if (document.getElementById('graficoLinea')) {
                new Chart(document.getElementById('graficoLinea'), {
                    type: 'line',
                    data: {
                        labels: labelsMes,
                        datasets: [{
                            label: 'Ingresos',
                            data: ingresosMes,
                            tension: 0.3
                        }, {
                            label: 'Pedidos',
                            data: pedidosMes,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true },
                            y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
                        }
                    }
                });
            }
            if (document.getElementById('graficoEntrega')) {
                new Chart(document.getElementById('graficoEntrega'), {
                    type: 'doughnut',
                    data: {
                        labels: labelsEntrega,
                        datasets: [{
                            data: valoresEntrega
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });
            }
        </script>
    </body>
</html>
<?php
// Cerrar conexión
mysqli_close($conexion);
?>