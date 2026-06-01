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
// Obtener filtros
$anio_actual = date("Y");
$fecha_desde = isset($_GET["fecha_desde"]) && $_GET["fecha_desde"] != "" ? $_GET["fecha_desde"] : $anio_actual . "-01-01";
$fecha_hasta = isset($_GET["fecha_hasta"]) && $_GET["fecha_hasta"] != "" ? $_GET["fecha_hasta"] : date("Y-m-d");
$estado_pedido = isset($_GET["estado_pedido"]) ? trim($_GET["estado_pedido"]) : "";
// Validar formato de fechas
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_desde)) {
    $fecha_desde = $anio_actual . "-01-01";
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_hasta)) {
    $fecha_hasta = date("Y-m-d");
}
// Crear consulta base del historial
$sql_historial = "SELECT
                    p.id_pedido,
                    p.codigo_pedido,
                    p.fecha_pedido,
                    p.total_productos,
                    p.costo_despacho,
                    p.total_pedido,
                    p.tipo_entrega,
                    p.estado_pedido,
                    pa.estado_pago,
                    pa.metodo_pago,
                    pa.referencia_pago,
                    pa.fecha_pago,
                    u.nombre,
                    u.apellido,
                    u.mail
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                INNER JOIN usuario u ON p.id_usuario = u.id_usuario
                WHERE pa.estado_pago = 'pagado'
                AND p.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)";
$parametros = array($fecha_desde, $fecha_hasta);
$tipos = "ss";
// Agregar filtro de estado de pedido si corresponde
if (!empty($estado_pedido)) {
    $sql_historial .= " AND p.estado_pedido = ?";
    $parametros[] = $estado_pedido;
    $tipos .= "s";
}
$sql_historial .= " ORDER BY p.fecha_pedido DESC";
$stmt_historial = mysqli_prepare($conexion, $sql_historial);
mysqli_stmt_bind_param($stmt_historial, $tipos, ...$parametros);
mysqli_stmt_execute($stmt_historial);
$resultado_historial = mysqli_stmt_get_result($stmt_historial);
// Guardar ventas para mostrar y calcular totales
$ventas = array();
$total_ingresos = 0;
$total_ventas = 0;
while ($venta = mysqli_fetch_assoc($resultado_historial)) {
    $ventas[] = $venta;
    $total_ingresos += intval($venta["total_pedido"]);
    $total_ventas++;
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Historial de ventas - PequeMundo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
        <link rel="stylesheet" href="../css/finanzas.css">
    </head>
    <body>
        <?php include "../masterpage/menu.php"; ?>
        <main class="container my-5">
            <section class="d-flex align-items-center mb-4 gap-3">
                <i class="fa-solid fa-clock-rotate-left fa-2x icono-finanzas"></i>
                <div>
                    <h2 class="mb-0">Historial de ventas</h2>
                    <small class="text-muted">Consultar ventas confirmadas y pagadas.</small>
                </div>
            </section>
            <section class="mb-4">
                <a href="reportes.php" class="btn btn-seguir-comprando btn-sm me-2">Reportes</a>
                <a href="productos_mas_vendidos.php" class="btn btn-seguir-comprando btn-sm me-2">Productos más vendidos</a>
                <a href="historial_ventas.php" class="btn btn-finalizar-compra btn-sm">Historial de ventas</a>
            </section>
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="historial_ventas.php" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control" value="<?php echo htmlspecialchars($fecha_desde); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" value="<?php echo htmlspecialchars($fecha_hasta); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado pedido</label>
                            <select name="estado_pedido" class="form-control">
                                <option value="">Todos</option>
                                <option value="confirmado" <?php if ($estado_pedido == "confirmado") echo "selected"; ?>>Confirmado</option>
                                <option value="preparacion" <?php if ($estado_pedido == "preparacion") echo "selected"; ?>>Preparación</option>
                                <option value="camino" <?php if ($estado_pedido == "camino") echo "selected"; ?>>Camino</option>
                                <option value="entregado" <?php if ($estado_pedido == "entregado") echo "selected"; ?>>Entregado</option>
                                <option value="cancelado" <?php if ($estado_pedido == "cancelado") echo "selected"; ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-filtrar w-100">
                                <i class="fa-solid fa-filter me-1"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </section>
            <section class="row g-3 mb-4">
                <div class="col-md-6">
                    <article class="card shadow-sm border-0 stat-box h-100">
                        <div class="card-body">
                            <div class="stat-label">Ventas confirmadas</div>
                            <div class="stat-numero"><?php echo intval($total_ventas); ?></div>
                        </div>
                    </article>
                </div>
                <div class="col-md-6">
                    <article class="card shadow-sm border-0 stat-box h-100">
                        <div class="card-body">
                            <div class="stat-label">Ingresos confirmados</div>
                            <div class="stat-numero">$<?php echo number_format($total_ingresos, 0, ",", "."); ?></div>
                        </div>
                    </article>
                </div>
            </section>
            <section class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Ventas pagadas</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="tabla-finanzas-head">
                                <tr>
                                    <th>Código</th>
                                    <th>Cliente</th>
                                    <th>Fecha pedido</th>
                                    <th>Fecha pago</th>
                                    <th>Entrega</th>
                                    <th>Estado pedido</th>
                                    <th>Estado pago</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($ventas) > 0) { ?>
                                    <?php foreach ($ventas as $venta) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($venta["codigo_pedido"]); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($venta["nombre"] . " " . $venta["apellido"]); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($venta["mail"]); ?></small>
                                            </td>
                                            <td><?php echo date("d-m-Y H:i", strtotime($venta["fecha_pedido"])); ?></td>
                                            <td>
                                                <?php if (!empty($venta["fecha_pago"])) { ?>
                                                    <?php echo date("d-m-Y H:i", strtotime($venta["fecha_pago"])); ?>
                                                <?php } else { ?>
                                                    <span class="text-muted">Sin fecha</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($venta["tipo_entrega"] == "despacho_domicilio") { ?>
                                                    Despacho
                                                <?php } else { ?>
                                                    Retiro en tienda
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="badge estado-pedido">
                                                    <?php echo htmlspecialchars($venta["estado_pedido"]); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge estado-pagado">
                                                    <?php echo htmlspecialchars($venta["estado_pago"]); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">$<?php echo number_format($venta["total_pedido"], 0, ",", "."); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No hay ventas pagadas para los filtros seleccionados.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
        <?php include "../masterpage/footer.php"; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
mysqli_close($conexion);
?>