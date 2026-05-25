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
    // KPI 1: Ingresos totales (pedidos pagados)
    // ──────────────────────────────────────────
    $sql_ingresos = "SELECT COALESCE(SUM(p.total_pedido), 0) AS ingresos_totales
                     FROM pedido p
                     INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                     WHERE pa.estado_pago = 'pagado'";
    $resultado_ingresos = mysqli_query($conexion, $sql_ingresos);
    $ingresos_totales = mysqli_fetch_assoc($resultado_ingresos)["ingresos_totales"] ?? 0;

    // ──────────────────────────────────────────
    // KPI 2: Pedidos pagados este mes
    // ──────────────────────────────────────────
    $sql_pedidos_mes = "SELECT COUNT(*) AS pedidos_mes
                        FROM pedido p
                        INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                        WHERE pa.estado_pago = 'pagado'
                          AND MONTH(p.fecha_pedido) = MONTH(CURDATE())
                          AND YEAR(p.fecha_pedido) = YEAR(CURDATE())";
    $resultado_pedidos_mes = mysqli_query($conexion, $sql_pedidos_mes);
    $pedidos_mes = mysqli_fetch_assoc($resultado_pedidos_mes)["pedidos_mes"] ?? 0;

    // ──────────────────────────────────────────
    // KPI 3: Ticket promedio (ingresos / pedidos totales pagados)
    // ──────────────────────────────────────────
    $sql_ticket = "SELECT COALESCE(AVG(p.total_pedido), 0) AS ticket_promedio
                   FROM pedido p
                   INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                   WHERE pa.estado_pago = 'pagado'";
    $resultado_ticket = mysqli_query($conexion, $sql_ticket);
    $ticket_promedio = mysqli_fetch_assoc($resultado_ticket)["ticket_promedio"] ?? 0;

    // ──────────────────────────────────────────
    // KPI 4: Total de clientes con al menos un pedido pagado
    // ──────────────────────────────────────────
    $sql_clientes = "SELECT COUNT(DISTINCT p.id_usuario) AS clientes_activos
                     FROM pedido p
                     INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                     WHERE pa.estado_pago = 'pagado'";
    $resultado_clientes = mysqli_query($conexion, $sql_clientes);
    $clientes_activos = mysqli_fetch_assoc($resultado_clientes)["clientes_activos"] ?? 0;

    // ──────────────────────────────────────────
    // Gráfico: Ingresos por mes (últimos 6 meses)
    // ──────────────────────────────────────────
    $sql_grafico = "SELECT
                        DATE_FORMAT(p.fecha_pedido, '%Y-%m') AS mes,
                        DATE_FORMAT(p.fecha_pedido, '%b %Y') AS mes_label,
                        COALESCE(SUM(p.total_pedido), 0) AS total
                    FROM pedido p
                    INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                    WHERE pa.estado_pago = 'pagado'
                      AND p.fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    GROUP BY DATE_FORMAT(p.fecha_pedido, '%Y-%m'), DATE_FORMAT(p.fecha_pedido, '%b %Y')
                    ORDER BY mes ASC";
    $resultado_grafico = mysqli_query($conexion, $sql_grafico);
    $labels_grafico  = [];
    $valores_grafico = [];
    while ($fila = mysqli_fetch_assoc($resultado_grafico)) {
        $labels_grafico[]  = $fila["mes_label"];
        $valores_grafico[] = intval($fila["total"]);
    }

    // ──────────────────────────────────────────
    // Top 5 productos más vendidos (resumen rápido)
    // ──────────────────────────────────────────
    $sql_top = "SELECT pr.nombre,
                       SUM(pd.cantidad) AS unidades
                FROM pedido_detalle pd
                INNER JOIN pedido pe ON pd.id_pedido = pe.id_pedido
                INNER JOIN pago pa   ON pe.id_pedido = pa.id_pedido
                INNER JOIN producto pr ON pd.id_producto = pr.id_producto
                WHERE pa.estado_pago = 'pagado'
                GROUP BY pr.id_producto, pr.nombre
                ORDER BY unidades DESC
                LIMIT 5";
    $resultado_top = mysqli_query($conexion, $sql_top);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas - PequeMundo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Estilos propios (una carpeta arriba) -->
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <style>
        .kpi-card {
            border-left: 5px solid #9a7c3a;
            border-radius: 8px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }
        .kpi-icon {
            font-size: 2rem;
            color: #9a7c3a;
        }
        .kpi-valor {
            font-family: 'Nunito', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #2f7187;
        }
        .kpi-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .acceso-rapido {
            border: 2px solid #e8dfc8;
            border-radius: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
        }
        .acceso-rapido:hover {
            border-color: #9a7c3a;
            background-color: #fdf6e3;
            transform: translateY(-2px);
            color: inherit;
        }
        .acceso-rapido .icono {
            font-size: 2.2rem;
            color: #9a7c3a;
        }
    </style>
</head>
<body>
    <!-- Menú principal -->
    <?php include '../masterpage/menu.php'; ?>

    <main class="container my-5">
        <!-- Encabezado -->
        <div class="d-flex align-items-center mb-4 gap-3">
            <i class="fa-solid fa-chart-line fa-2x" style="color:#9a7c3a;"></i>
            <div>
                <h2 class="mb-0">Panel de Finanzas</h2>
                <small class="text-muted">Resumen general de ingresos y ventas</small>
            </div>
        </div>

        <!-- ── KPIs ── -->
        <div class="row g-4 mb-5">
            <!-- Ingresos totales -->
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="fa-solid fa-dollar-sign kpi-icon"></i>
                        <div>
                            <div class="kpi-label">Ingresos totales</div>
                            <div class="kpi-valor">$<?php echo number_format($ingresos_totales, 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pedidos este mes -->
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="fa-solid fa-box-open kpi-icon"></i>
                        <div>
                            <div class="kpi-label">Pedidos este mes</div>
                            <div class="kpi-valor"><?php echo $pedidos_mes; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Ticket promedio -->
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="fa-solid fa-receipt kpi-icon"></i>
                        <div>
                            <div class="kpi-label">Ticket promedio</div>
                            <div class="kpi-valor">$<?php echo number_format($ticket_promedio, 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Clientes activos -->
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="fa-solid fa-users kpi-icon"></i>
                        <div>
                            <div class="kpi-label">Clientes con compras</div>
                            <div class="kpi-valor"><?php echo $clientes_activos; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Accesos rápidos ── -->
        <h5 class="mb-3" style="color:#9a7c3a;">Módulos de finanzas</h5>
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <a href="reportes.php" class="acceso-rapido card shadow-sm border-0 h-100 p-4 d-flex flex-row align-items-center gap-3">
                    <div class="icono"><i class="fa-solid fa-chart-bar"></i></div>
                    <div>
                        <div class="fw-bold">Reportes de ventas</div>
                        <small class="text-muted">Ingresos por período con gráficos</small>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="productos_mas_vendidos.php" class="acceso-rapido card shadow-sm border-0 h-100 p-4 d-flex flex-row align-items-center gap-3">
                    <div class="icono"><i class="fa-solid fa-ranking-star"></i></div>
                    <div>
                        <div class="fw-bold">Productos más vendidos</div>
                        <small class="text-muted">Ranking por unidades e ingresos</small>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="historial_ventas.php" class="acceso-rapido card shadow-sm border-0 h-100 p-4 d-flex flex-row align-items-center gap-3">
                    <div class="icono"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <div>
                        <div class="fw-bold">Historial de ventas</div>
                        <small class="text-muted">Todos los pedidos pagados</small>
                    </div>
                </a>
            </div>
        </div>

        <!-- ── Gráfico ingresos últimos 6 meses + Top 5 ── -->
        <div class="row g-4">
            <!-- Gráfico -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Ingresos — últimos 6 meses</h5>
                        <canvas id="graficoIngresos" height="100"></canvas>
                    </div>
                </div>
            </div>
            <!-- Top 5 productos -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Top 5 productos</h5>
                        <?php if (mysqli_num_rows($resultado_top) > 0): ?>
                            <ol class="list-group list-group-numbered list-group-flush">
                                <?php while ($prod = mysqli_fetch_assoc($resultado_top)): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-semibold"><?php echo htmlspecialchars($prod["nombre"]); ?></div>
                                        </div>
                                        <span class="badge rounded-pill" style="background-color:#2f7187;">
                                            <?php echo intval($prod["unidades"]); ?> uds.
                                        </span>
                                    </li>
                                <?php endwhile; ?>
                            </ol>
                        <?php else: ?>
                            <p class="text-muted">No hay ventas registradas aún.</p>
                        <?php endif; ?>
                        <div class="mt-3">
                            <a href="productos_mas_vendidos.php" class="btn btn-sm btn-custom text-white w-100">
                                Ver ranking completo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../masterpage/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('graficoIngresos').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_grafico); ?>,
                datasets: [{
                    label: 'Ingresos ($)',
                    data: <?php echo json_encode($valores_grafico); ?>,
                    backgroundColor: 'rgba(154, 124, 58, 0.6)',
                    borderColor: '#9a7c3a',
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => '$' + ctx.raw.toLocaleString('es-CL')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => '$' + val.toLocaleString('es-CL')
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
