<?php
// Iniciar sesión
session_start();
// Verificar acceso de cliente
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../login.php");
    exit();
}
// Verificar pedido recibido
if (!isset($_GET["id_pedido"]) || empty($_GET["id_pedido"])) {
    header("Location: mis_pedidos.php");
    exit();
}
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Guardar datos principales
$id_usuario = intval($_SESSION["id_usuario"]);
$id_pedido = intval($_GET["id_pedido"]);
// Consultar pedido del usuario
$sql_pedido = "SELECT 
                    p.id_pedido,
                    p.codigo_pedido,
                    p.fecha_pedido,
                    p.total_productos,
                    p.costo_despacho,
                    p.total_pedido,
                    p.tipo_entrega,
                    p.direccion_entrega,
                    p.region_entrega,
                    p.comuna_entrega,
                    p.estado_pedido,
                    pa.estado_pago,
                    pa.metodo_pago,
                    pa.referencia_pago,
                    pa.fecha_pago
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                WHERE p.id_pedido = ?
                AND p.id_usuario = ?
                LIMIT 1";
$stmt_pedido = mysqli_prepare($conexion, $sql_pedido);
if (!$stmt_pedido) {
    die("Error al preparar la consulta del pedido: " . mysqli_error($conexion));
}
mysqli_stmt_bind_param($stmt_pedido, "ii", $id_pedido, $id_usuario);
mysqli_stmt_execute($stmt_pedido);
$resultado_pedido = mysqli_stmt_get_result($stmt_pedido);
if (mysqli_num_rows($resultado_pedido) != 1) {
    mysqli_stmt_close($stmt_pedido);
    mysqli_close($conexion);
    header("Location: mis_pedidos.php");
    exit();
}
$pedido = mysqli_fetch_assoc($resultado_pedido);
// Consultar productos del pedido
$sql_detalles = "SELECT 
                    pd.id_pedido_detalle,
                    pd.id_producto,
                    pd.cantidad,
                    pd.precio_unitario,
                    pd.subtotal,
                    pr.nombre,
                    pr.imagen
                FROM pedido_detalle pd
                INNER JOIN producto pr ON pd.id_producto = pr.id_producto
                WHERE pd.id_pedido = ?";
$stmt_detalles = mysqli_prepare($conexion, $sql_detalles);
if (!$stmt_detalles) {
    die("Error al preparar el detalle del pedido: " . mysqli_error($conexion));
}
mysqli_stmt_bind_param($stmt_detalles, "i", $id_pedido);
mysqli_stmt_execute($stmt_detalles);
$resultado_detalles = mysqli_stmt_get_result($stmt_detalles);
// Obtener texto y estilo del estado del pedido
function obtenerEstadoPedidoDetalle($estado_pedido) {
    switch ($estado_pedido) {
        case "pendiente_pago":
            return array("texto" => "Pendiente de pago", "clase" => "badge bg-warning text-dark");
        case "confirmado":
            return array("texto" => "Confirmado", "clase" => "badge bg-info text-dark");
        case "preparacion":
            return array("texto" => "En preparación", "clase" => "badge bg-primary");
        case "camino":
            return array("texto" => "En camino", "clase" => "badge bg-primary");
        case "entregado":
            return array("texto" => "Entregado", "clase" => "badge bg-success");
        case "cancelado":
            return array("texto" => "Cancelado", "clase" => "badge bg-danger");
        default:
            return array("texto" => ucfirst($estado_pedido), "clase" => "badge bg-secondary");
    }
}
// Obtener texto y estilo del estado del pago
function obtenerEstadoPagoDetalle($estado_pago) {
    switch ($estado_pago) {
        case "pagado":
            return array("texto" => "Pagado", "clase" => "badge bg-success");
        case "pendiente":
            return array("texto" => "Pendiente", "clase" => "badge bg-warning text-dark");
        case "rechazado":
            return array("texto" => "Rechazado", "clase" => "badge bg-danger");
        default:
            return array("texto" => ucfirst($estado_pago), "clase" => "badge bg-secondary");
    }
}
// Preparar estados visuales
$estado_pedido = obtenerEstadoPedidoDetalle($pedido["estado_pedido"]);
$estado_pago = obtenerEstadoPagoDetalle($pedido["estado_pago"]);
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detalle del pedido - PequeMundo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
    </head>
    <body>
        <?php include "../masterpage/menu.php"; ?>
        <main class="container my-5">
            <section class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <h2>Detalle del pedido</h2>
                    <p class="mb-0">Código: <strong><?php echo htmlspecialchars($pedido["codigo_pedido"]); ?></strong></p>
                </div>
                <a href="mis_pedidos.php" class="btn btn-volver-carrito mt-3 mt-md-0">
                    <i class="fa-solid fa-arrow-left me-2"></i> Volver
                </a>
            </section>
            <section class="row mb-4">
                <div class="col-md-6 mb-3">
                    <article class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i>Información general</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Fecha pedido:</strong><br><?php echo date("d/m/Y H:i", strtotime($pedido["fecha_pedido"])); ?></p>
                            <p><strong>Estado pedido:</strong><br><span class="<?php echo $estado_pedido["clase"]; ?>"><?php echo $estado_pedido["texto"]; ?></span></p>
                            <p><strong>Estado pago:</strong><br><span class="<?php echo $estado_pago["clase"]; ?>"><?php echo $estado_pago["texto"]; ?></span></p>
                            <p><strong>Método de pago:</strong><br><?php echo htmlspecialchars($pedido["metodo_pago"]); ?></p>
                            <?php if (!empty($pedido["fecha_pago"])) { ?>
                                <p><strong>Fecha pago:</strong><br><?php echo date("d/m/Y H:i", strtotime($pedido["fecha_pago"])); ?></p>
                            <?php } ?>
                            <?php if (!empty($pedido["referencia_pago"])) { ?>
                                <p><strong>Referencia pago:</strong><br><?php echo htmlspecialchars($pedido["referencia_pago"]); ?></p>
                            <?php } ?>
                        </div>
                    </article>
                </div>
                <div class="col-md-6 mb-3">
                    <article class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fa-solid fa-location-dot me-2"></i>Entrega</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($pedido["tipo_entrega"] == "despacho_domicilio") { ?>
                                <p><strong>Tipo:</strong><br><i class="fa-solid fa-truck me-1"></i> Despacho a domicilio</p>
                                <p><strong>Dirección:</strong><br><?php echo htmlspecialchars($pedido["direccion_entrega"]); ?></p>
                                <p><strong>Región:</strong><br><?php echo htmlspecialchars($pedido["region_entrega"]); ?></p>
                                <p><strong>Comuna:</strong><br><?php echo htmlspecialchars($pedido["comuna_entrega"]); ?></p>
                            <?php } else { ?>
                                <p class="text-center text-muted py-4 mb-0">
                                    <i class="fa-solid fa-store fa-2x mb-2 d-block"></i>
                                    Retiro en tienda
                                </p>
                            <?php } ?>
                        </div>
                    </article>
                </div>
            </section>
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i>Productos del pedido</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-warning">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio unitario</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resultado_detalles && mysqli_num_rows($resultado_detalles) > 0) { ?>
                                    <?php while ($detalle = mysqli_fetch_assoc($resultado_detalles)) { ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($detalle["imagen"])) { ?>
                                                        <img src="../<?php echo htmlspecialchars($detalle["imagen"]); ?>" alt="<?php echo htmlspecialchars($detalle["nombre"]); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                                    <?php } else { ?>
                                                        <div style="width: 50px; height: 50px; background-color: #f2eee8; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fa-solid fa-image" style="color: #9a7c3a;"></i>
                                                        </div>
                                                    <?php } ?>
                                                    <strong class="ms-3"><?php echo htmlspecialchars($detalle["nombre"]); ?></strong>
                                                </div>
                                            </td>
                                            <td><?php echo intval($detalle["cantidad"]); ?></td>
                                            <td>$<?php echo number_format($detalle["precio_unitario"], 0, ",", "."); ?></td>
                                            <td class="text-end fw-bold">$<?php echo number_format($detalle["subtotal"], 0, ",", "."); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No hay productos asociados a este pedido.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Subtotal productos:</td>
                                    <td class="text-end fw-bold">$<?php echo number_format($pedido["total_productos"], 0, ",", "."); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Costo despacho:</td>
                                    <td class="text-end fw-bold">$<?php echo number_format($pedido["costo_despacho"], 0, ",", "."); ?></td>
                                </tr>
                                <tr class="table-warning">
                                    <td colspan="3" class="text-end fw-bold fs-5">Total pedido:</td>
                                    <td class="text-end fw-bold fs-5">$<?php echo number_format($pedido["total_pedido"], 0, ",", "."); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>
            <?php if ($pedido["estado_pago"] == "pendiente") { ?>
                <section class="alert alert-warning">
                    <i class="fa-solid fa-clock me-2"></i>
                    Este pedido aún está pendiente de pago.
                </section>
            <?php } ?>
        </main>
        <?php include "../masterpage/footer.php"; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
mysqli_stmt_close($stmt_pedido);
mysqli_stmt_close($stmt_detalles);
mysqli_close($conexion);
?>