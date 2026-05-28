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
// Consultar productos más vendidos solo en pagos confirmados
$sql_productos = "SELECT
                    pr.id_producto,
                    pr.nombre,
                    pr.imagen,
                    COALESCE(SUM(pd.cantidad), 0) AS unidades_vendidas,
                    COALESCE(SUM(pd.subtotal), 0) AS ingresos_generados,
                    COUNT(DISTINCT p.id_pedido) AS pedidos_asociados
                FROM pedido_detalle pd
                INNER JOIN pedido p ON pd.id_pedido = p.id_pedido
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                INNER JOIN producto pr ON pd.id_producto = pr.id_producto
                WHERE pa.estado_pago = 'pagado'
                AND p.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                GROUP BY pr.id_producto, pr.nombre, pr.imagen
                ORDER BY unidades_vendidas DESC, ingresos_generados DESC";
$stmt_productos = mysqli_prepare($conexion, $sql_productos);
mysqli_stmt_bind_param($stmt_productos, "ss", $fecha_desde, $fecha_hasta);
mysqli_stmt_execute($stmt_productos);
$resultado_productos = mysqli_stmt_get_result($stmt_productos);
// Guardar productos para tabla y gráfico
$productos = array();
$labels_productos = array();
$unidades_productos = array();
while ($producto = mysqli_fetch_assoc($resultado_productos)) {
    $productos[] = $producto;
    $labels_productos[] = $producto["nombre"];
    $unidades_productos[] = intval($producto["unidades_vendidas"]);
}
// Calcular totales del ranking
$total_unidades = 0;
$total_ingresos = 0;
foreach ($productos as $producto) {
    $total_unidades += intval($producto["unidades_vendidas"]);
    $total_ingresos += intval($producto["ingresos_generados"]);
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
                <i class="fa-solid fa-ranking-star fa-2x icono-finanzas"></i>
                <div>
                    <h2 class="mb-0">Productos más vendidos</h2>
                    <small class="text-muted">Revisar productos con mayor cantidad de unidades vendidas.</small>
                </div>
            </section>
            <!-- Navegación interna -->
            <section class="mb-4">
                <a href="reportes.php" class="btn btn-seguir-comprando btn-sm me-2">Reportes</a>
                <a href="productos_mas_vendidos.php" class="btn btn-finalizar-compra btn-sm me-2">Productos más vendidos</a>
                <a href="historial_ventas.php" class="btn btn-seguir-comprando btn-sm">Historial de ventas</a>
            </section>
            <!-- Filtro de fechas -->
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="productos_mas_vendidos.php" class="row g-3 align-items-end">
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
            <!-- Resumen -->
            <section class="row g-3 mb-4">
                <div class="col-md-6">
                    <article class="card shadow-sm border-0 stat-box h-100">
                        <div class="card-body">
                            <div class="stat-label">Unidades vendidas</div>
                            <div class="stat-numero"><?php echo intval($total_unidades); ?></div>
                        </div>
                    </article>
                </div>
                <div class="col-md-6">
                    <article class="card shadow-sm border-0 stat-box h-100">
                        <div class="card-body">
                            <div class="stat-label">Ingresos generados</div>
                            <div class="stat-numero">$<?php echo number_format($total_ingresos, 0, ",", "."); ?></div>
                        </div>
                    </article>
                </div>
            </section>
            <!-- Gráfico -->
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title">Ranking visual de unidades vendidas</h5>
                    <?php if (count($productos) > 0) { ?>
                        <canvas id="graficoProductos" height="100"></canvas>
                    <?php } else { ?>
                        <p class="text-muted mb-0">No hay productos vendidos en el período seleccionado.</p>
                    <?php } ?>
                </div>
            </section>
            <!-- Tabla ranking -->
            <section class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Detalle de productos más vendidos</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="tabla-finanzas-head">
                                <tr>
                                    <th>Ranking</th>
                                    <th>Producto</th>
                                    <th class="text-end">Unidades vendidas</th>
                                    <th class="text-end">Ingresos generados</th>
                                    <th class="text-end">Pedidos asociados</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($productos) > 0) { ?>
                                    <?php $ranking = 1; ?>
                                    <?php foreach ($productos as $producto) { ?>
                                        <?php
                                            // Definir clase visual del ranking
                                            $clase_ranking = "";
                                            if ($ranking == 1) {
                                                $clase_ranking = "top1";
                                            } elseif ($ranking == 2) {
                                                $clase_ranking = "top2";
                                            } elseif ($ranking == 3) {
                                                $clase_ranking = "top3";
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="rank-badge <?php echo $clase_ranking; ?>">
                                                    <?php echo $ranking; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if (!empty($producto["imagen"])) { ?>
                                                        <img src="../<?php echo htmlspecialchars($producto["imagen"]); ?>" alt="<?php echo htmlspecialchars($producto["nombre"]); ?>" class="img-ranking">
                                                    <?php } else { ?>
                                                        <div class="img-ranking d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-image text-muted"></i>
                                                        </div>
                                                    <?php } ?>
                                                    <span><?php echo htmlspecialchars($producto["nombre"]); ?></span>
                                                </div>
                                            </td>
                                            <td class="text-end"><?php echo intval($producto["unidades_vendidas"]); ?></td>
                                            <td class="text-end">$<?php echo number_format($producto["ingresos_generados"], 0, ",", "."); ?></td>
                                            <td class="text-end"><?php echo intval($producto["pedidos_asociados"]); ?></td>
                                        </tr>
                                        <?php $ranking++; ?>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No hay productos vendidos en el período seleccionado.</td>
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
            const labelsProductos = <?php echo json_encode($labels_productos, JSON_UNESCAPED_UNICODE); ?>;
            const unidadesProductos = <?php echo json_encode($unidades_productos); ?>;
            if (document.getElementById('graficoProductos')) {
                new Chart(document.getElementById('graficoProductos'), {
                    type: 'bar',
                    data: {
                        labels: labelsProductos,
                        datasets: [{
                            label: 'Unidades vendidas',
                            data: unidadesProductos
                        }]
                    },
                    options: {
                        responsive: true,
                        indexAxis: 'y',
                        scales: {
                            x: { beginAtZero: true }
                        }
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