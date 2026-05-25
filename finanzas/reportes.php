<?php
    // Iniciar sesión para validar acceso
    session_start();
    // Conexión a base de datos
    require_once "../config/conexion.php";
    // Solo el administrador puede acceder al módulo de finanzas
    if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "admin") {
        echo "<script>
                alert('No tienes permiso para acceder a esta página');
                window.location.href = '../index.php';
              </script>";
        exit();
    }

    // ──────────────────────────────────────────
    // Filtros de fecha (por defecto: año actual)
    // ──────────────────────────────────────────
    $anio_actual  = date("Y");
    $mes_actual   = date("m");
    $fecha_desde  = isset($_GET["fecha_desde"]) && $_GET["fecha_desde"] != "" ? $_GET["fecha_desde"] : $anio_actual . "-01-01";
    $fecha_hasta  = isset($_GET["fecha_hasta"]) && $_GET["fecha_hasta"] != "" ? $_GET["fecha_hasta"] : date("Y-m-d");

    // Validar formato básico de fechas
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_desde)) $fecha_desde = $anio_actual . "-01-01";
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_hasta)) $fecha_hasta = date("Y-m-d");

    // ──────────────────────────────────────────
    // Totales del período seleccionado
    // ──────────────────────────────────────────
    $sql_totales = "SELECT
                        COUNT(p.id_pedido)         AS total_pedidos,
                        COALESCE(SUM(p.total_pedido), 0)       AS ingresos,
                        COALESCE(SUM(p.total_productos), 0)    AS subtotal_productos,
                        COALESCE(SUM(p.costo_despacho), 0)     AS total_despacho,
                        COALESCE(AVG(p.total_pedido), 0)       AS ticket_promedio
                    FROM pedido p
                    INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                    WHERE pa.estado_pago = 'pagado'
                      AND p.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)";
    $stmt_totales = mysqli_prepare($conexion, $sql_totales);
    mysqli_stmt_bind_param($stmt_totales, "ss", $fecha_desde, $fecha_hasta);
    mysqli_stmt_execute($stmt_totales);
    $totales = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_totales));

    // ──────────────────────────────────────────
    // Ingresos agrupados por mes (para el gráfico de línea)
    // ──────────────────────────────────────────
    $sql_por_mes = "SELECT
                        DATE_FORMAT(p.fecha_pedido, '%Y-%m')  AS mes_key,
                        DATE_FORMAT(p.fecha_pedido, '%b %Y')  AS mes_label,
                        COALESCE(SUM(p.total_pedido), 0)      AS ingresos,
                        COUNT(p.id_pedido)                    AS pedidos
                    FROM pedido p
                    INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                    WHERE pa.estado_pago = 'pagado'
                      AND p.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                    GROUP BY DATE_FORMAT(p.fecha_pedido, '%Y-%m'), DATE_FORMAT(p.fecha_pedido, '%b %Y')
                    ORDER BY mes_key ASC";
    $stmt_por_mes = mysqli_prepare($conexion, $sql_por_mes);
    mysqli_stmt_bind_param($stmt_por_mes, "ss", $fecha_desde, $fecha_hasta);
    mysqli_stmt_execute($stmt_por_mes);
    $resultado_por_mes = mysqli_stmt_get_result($stmt_por_mes);

    $labels_mes     = [];
    $ingresos_mes   = [];
    $pedidos_mes_arr = [];
    $tabla_meses    = [];
    while ($fila = mysqli_fetch_assoc($resultado_por_mes)) {
        $labels_mes[]      = $fila["mes_label"];
        $ingresos_mes[]    = intval($fila["ingresos"]);
        $pedidos_mes_arr[] = intval($fila["pedidos"]);
        $tabla_meses[]     = $fila;
    }

    // ──────────────────────────────────────────
    // Ingresos por tipo de entrega (para gráfico dona)
    // ──────────────────────────────────────────
    $sql_entrega = "SELECT
                        p.tipo_entrega,
                        COUNT(*) AS cantidad,
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
    $labels_entrega  = [];
    $valores_entrega = [];
    while ($fila = mysqli_fetch_assoc($resultado_entrega)) {
        $label = $fila["tipo_entrega"] === "despacho_domicilio" ? "Despacho a domicilio" : "Retiro en tienda";
        $labels_entrega[]  = $label;
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
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <style>
        .stat-box {
            border-left: 4px solid #9a7c3a;
            border-radius: 6px;
        }
        .stat-numero {
            font-family: 'Nunito', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #2f7187;
        }
        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
        }
        .btn-filtrar {
            background-color: #9a7c3a;
            color: #fff;
            border: none;
        }
        .btn-filtrar:hover {
            background-color: #b39044;
            color: #fff;
        }
    </style>
</head>
<body>
    <?php include '../masterpage/menu.php'; ?>

    <main class="container my-5">
        <!-- Encabezado -->
        <div class="d-flex align-items-center mb-4 gap-3">
            <i class="fa-solid fa-chart-bar fa-2x" style="color:#9a7c3a;"></i>
            <div>
                <h2 class="mb-0">Reportes de ventas</h2>
                <small class="text-muted">Análisis de ingresos por período</small>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" style="color:#9a7c3a;">Finanzas</a></li>
                <li class="breadcrumb-item active">Reportes</li>
            </ol>
        </nav>

        <!-- ── Filtro de fechas ── -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="reportes.php" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control"
                               value="<?php echo htmlspecialchars($fecha_desde); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control"
                               value="<?php echo htmlspecialchars($fecha_hasta); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-filtrar w-100">
                            <i class="fa-solid fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Resumen del período ── -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 stat-box h-100">
                    <div class="card-body">
                        <div class="stat-label">Ingresos del período</div>
                        <div class="stat-numero">$<?php echo number_format($totales["ingresos"], 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 stat-box h-100">
                    <div class="card-body">
                        <div class="stat-label">Total pedidos pagados</div>
                        <div class="stat-numero"><?php echo intval($totales["total_pedidos"]); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 stat-box h-100">
                    <div class="card-body">
                        <div class="stat-label">Ticket promedio</div>
                        <div class="stat-numero">$<?php echo number_format($totales["ticket_promedio"], 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm border-0 stat-box h-100">
                    <div class="card-body">
                        <div class="stat-label">Ingresos por despacho</div>
                        <div class="stat-numero">$<?php echo number_format($totales["total_despacho"], 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Gráficos ── -->
        <div class="row g-4 mb-4">
            <!-- Gráfico de línea: ingresos por mes -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title">Ingresos por mes</h5>
                        <canvas id="graficoLinea" height="110"></canvas>
                    </div>
                </div>
            </div>
            <!-- Gráfico dona: tipo de entrega -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex flex-column align-items-center">
                        <h5 class="card-title align-self-start">Por tipo de entrega</h5>
                        <canvas id="graficoDona" style="max-height:220px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Tabla mensual detallada ── -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Detalle mensual</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead style="background-color:#fdf3d0;">
                            <tr>
                                <th>Mes</th>
                                <th class="text-end">Pedidos pagados</th>
                                <th class="text-end">Ingresos</th>
                                <th class="text-end">Ticket promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($tabla_meses) > 0): ?>
                                <?php foreach ($tabla_meses as $fila): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fila["mes_label"]); ?></td>
                                        <td class="text-end"><?php echo intval($fila["pedidos"]); ?></td>
                                        <td class="text-end">$<?php echo number_format($fila["ingresos"], 0, ',', '.'); ?></td>
                                        <td class="text-end">
                                            <?php
                                                $ticket = $fila["pedidos"] > 0
                                                    ? intval($fila["ingresos"]) / intval($fila["pedidos"])
                                                    : 0;
                                                echo '$' . number_format($ticket, 0, ',', '.');
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- Fila de totales -->
                                <tr class="fw-bold" style="background-color:#fdf3d0;">
                                    <td>Total período</td>
                                    <td class="text-end"><?php echo intval($totales["total_pedidos"]); ?></td>
                                    <td class="text-end">$<?php echo number_format($totales["ingresos"], 0, ',', '.'); ?></td>
                                    <td class="text-end">$<?php echo number_format($totales["ticket_promedio"], 0, ',', '.'); ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No hay ventas registradas en el período seleccionado.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include '../masterpage/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        // Gráfico de línea
        const ctxLinea = document.getElementById('graficoLinea').getContext('2d');
        new Chart(ctxLinea, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels_mes); ?>,
                datasets: [
                    {
                        label: 'Ingresos ($)',
                        data: <?php echo json_encode($ingresos_mes); ?>,
                        borderColor: '#9a7c3a',
                        backgroundColor: 'rgba(154,124,58,0.15)',
                        borderWidth: 2,
                        pointRadius: 5,
                        pointBackgroundColor: '#9a7c3a',
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'yIngresos'
                    },
                    {
                        label: 'Pedidos',
                        data: <?php echo json_encode($pedidos_mes_arr); ?>,
                        borderColor: '#2f7187',
                        backgroundColor: 'rgba(47,113,135,0.1)',
                        borderWidth: 2,
                        pointRadius: 5,
                        pointBackgroundColor: '#2f7187',
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'yPedidos'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.dataset.yAxisID === 'yIngresos') {
                                    return 'Ingresos: $' + ctx.raw.toLocaleString('es-CL');
                                }
                                return 'Pedidos: ' + ctx.raw;
                            }
                        }
                    }
                },
                scales: {
                    yIngresos: {
                        beginAtZero: true,
                        position: 'left',
                        ticks: { callback: val => '$' + val.toLocaleString('es-CL') }
                    },
                    yPedidos: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });

        // Gráfico dona
        const ctxDona = document.getElementById('graficoDona').getContext('2d');
        new Chart(ctxDona, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels_entrega); ?>,
                datasets: [{
                    data: <?php echo json_encode($valores_entrega); ?>,
                    backgroundColor: ['#9a7c3a', '#2f7187'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.label + ': $' + ctx.raw.toLocaleString('es-CL')
                        }
                    },
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>
