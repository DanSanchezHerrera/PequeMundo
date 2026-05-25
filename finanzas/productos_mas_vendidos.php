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
    $anio_actual = date("Y");
    $fecha_desde = isset($_GET["fecha_desde"]) && $_GET["fecha_desde"] != "" ? $_GET["fecha_desde"] : $anio_actual . "-01-01";
    $fecha_hasta = isset($_GET["fecha_hasta"]) && $_GET["fecha_hasta"] != "" ? $_GET["fecha_hasta"] : date("Y-m-d");
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_desde)) $fecha_desde = $anio_actual . "-01-01";
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_hasta)) $fecha_hasta = date("Y-m-d");

    // Criterio de orden
    $orden = isset($_GET["orden"]) && $_GET["orden"] === "ingresos" ? "ingresos" : "unidades";

    // ──────────────────────────────────────────
    // Ranking por unidades vendidas e ingresos generados
    // ──────────────────────────────────────────
    $orden_sql = $orden === "ingresos" ? "ingresos DESC" : "unidades DESC";
    $sql_ranking = "SELECT
                        pr.id_producto,
                        pr.nombre,
                        pr.imagen,
                        pr.precio,
                        pr.estado,
                        SUM(pd.cantidad)            AS unidades,
                        SUM(pd.subtotal)            AS ingresos,
                        COUNT(DISTINCT pe.id_pedido) AS veces_pedido
                    FROM pedido_detalle pd
                    INNER JOIN pedido pe  ON pd.id_pedido   = pe.id_pedido
                    INNER JOIN pago pa    ON pe.id_pedido   = pa.id_pedido
                    INNER JOIN producto pr ON pd.id_producto = pr.id_producto
                    WHERE pa.estado_pago = 'pagado'
                      AND pe.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                    GROUP BY pr.id_producto, pr.nombre, pr.imagen, pr.precio, pr.estado
                    ORDER BY $orden_sql
                    LIMIT 20";
    $stmt_ranking = mysqli_prepare($conexion, $sql_ranking);
    mysqli_stmt_bind_param($stmt_ranking, "ss", $fecha_desde, $fecha_hasta);
    mysqli_stmt_execute($stmt_ranking);
    $resultado_ranking = mysqli_stmt_get_result($stmt_ranking);
    $productos_ranking = [];
    while ($fila = mysqli_fetch_assoc($resultado_ranking)) {
        $productos_ranking[] = $fila;
    }

    // ──────────────────────────────────────────
    // Datos para el gráfico horizontal (top 10)
    // ──────────────────────────────────────────
    $labels_chart  = [];
    $valores_chart = [];
    $limite = min(10, count($productos_ranking));
    for ($i = 0; $i < $limite; $i++) {
        $labels_chart[]  = $productos_ranking[$i]["nombre"];
        $valores_chart[] = $orden === "ingresos"
            ? intval($productos_ranking[$i]["ingresos"])
            : intval($productos_ranking[$i]["unidades"]);
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos más vendidos - PequeMundo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <style>
        .medalla-1 { color: #ffd700; }
        .medalla-2 { color: #c0c0c0; }
        .medalla-3 { color: #cd7f32; }
        .btn-filtrar {
            background-color: #9a7c3a;
            color: #fff;
            border: none;
        }
        .btn-filtrar:hover { background-color: #b39044; color: #fff; }
        .btn-orden {
            border-color: #9a7c3a;
            color: #9a7c3a;
        }
        .btn-orden.active, .btn-orden:hover {
            background-color: #9a7c3a;
            color: #fff;
        }
        .rank-badge {
            min-width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            background-color: #f0e8d4;
            color: #9a7c3a;
        }
        .rank-badge.top1 { background-color: #ffd700; color: #5a4000; }
        .rank-badge.top2 { background-color: #c0c0c0; color: #333; }
        .rank-badge.top3 { background-color: #cd7f32; color: #fff; }
    </style>
</head>
<body>
    <?php include '../masterpage/menu.php'; ?>

    <main class="container my-5">
        <!-- Encabezado -->
        <div class="d-flex align-items-center mb-4 gap-3">
            <i class="fa-solid fa-ranking-star fa-2x" style="color:#9a7c3a;"></i>
            <div>
                <h2 class="mb-0">Productos más vendidos</h2>
                <small class="text-muted">Ranking por unidades e ingresos generados</small>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" style="color:#9a7c3a;">Finanzas</a></li>
                <li class="breadcrumb-item active">Productos más vendidos</li>
            </ol>
        </nav>

        <!-- ── Filtros ── -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="productos_mas_vendidos.php" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control"
                               value="<?php echo htmlspecialchars($fecha_desde); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control"
                               value="<?php echo htmlspecialchars($fecha_hasta); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ordenar por</label>
                        <select name="orden" class="form-control">
                            <option value="unidades" <?php echo $orden === "unidades" ? "selected" : ""; ?>>Unidades vendidas</option>
                            <option value="ingresos" <?php echo $orden === "ingresos" ? "selected" : ""; ?>>Ingresos generados</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-filtrar w-100">
                            <i class="fa-solid fa-filter me-1"></i> Aplicar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Gráfico de barras horizontal (top 10) ── -->
        <?php if (count($productos_ranking) > 0): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title">Top 10 — <?php echo $orden === "ingresos" ? "por ingresos" : "por unidades vendidas"; ?></h5>
                <canvas id="graficoRanking" height="<?php echo min(count($productos_ranking), 10) * 28 + 30; ?>"></canvas>
            </div>
        </div>

        <!-- ── Tabla de ranking ── -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Listado completo (top 20)</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead style="background-color:#fdf3d0;">
                            <tr>
                                <th class="text-center" style="width:60px;">#</th>
                                <th>Producto</th>
                                <th class="text-end">Unidades vendidas</th>
                                <th class="text-end">Ingresos generados</th>
                                <th class="text-end">Pedidos distintos</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_ranking as $pos => $prod): ?>
                                <?php $num = $pos + 1; ?>
                                <tr>
                                    <td class="text-center">
                                        <div class="rank-badge mx-auto
                                            <?php if ($num === 1) echo 'top1'; elseif ($num === 2) echo 'top2'; elseif ($num === 3) echo 'top3'; ?>
                                        ">
                                            <?php echo $num; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="../<?php echo htmlspecialchars($prod['imagen']); ?>"
                                                 alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                                                 width="48" height="48"
                                                 style="object-fit:cover; border-radius:6px;"
                                                 onerror="this.style.display='none'">
                                            <div>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($prod["nombre"]); ?></div>
                                                <small class="text-muted">Precio: $<?php echo number_format($prod["precio"], 0, ',', '.'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-semibold"><?php echo number_format($prod["unidades"], 0, ',', '.'); ?></td>
                                    <td class="text-end fw-semibold" style="color:#2f7187;">
                                        $<?php echo number_format($prod["ingresos"], 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-end"><?php echo intval($prod["veces_pedido"]); ?></td>
                                    <td class="text-center">
                                        <?php if ($prod["estado"] === "activo"): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-info">
                No hay ventas registradas en el período seleccionado.
            </div>
        <?php endif; ?>
    </main>

    <?php include '../masterpage/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <?php if (count($productos_ranking) > 0): ?>
    <script>
        const ctxRanking = document.getElementById('graficoRanking').getContext('2d');
        new Chart(ctxRanking, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_reverse($labels_chart)); ?>,
                datasets: [{
                    label: '<?php echo $orden === "ingresos" ? "Ingresos ($)" : "Unidades vendidas"; ?>',
                    data: <?php echo json_encode(array_reverse($valores_chart)); ?>,
                    backgroundColor: 'rgba(154,124,58,0.7)',
                    borderColor: '#9a7c3a',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => <?php echo $orden === "ingresos"
                                ? "'$' + ctx.raw.toLocaleString('es-CL')"
                                : "ctx.raw + ' uds.'"; ?>
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => <?php echo $orden === "ingresos"
                                ? "'$' + val.toLocaleString('es-CL')"
                                : "val"; ?>
                        }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
