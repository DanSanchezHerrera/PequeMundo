<?php
// Iniciar sesión
session_start();

// Verificar que el usuario sea cliente
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../login.php");
    exit();
}

// Verificar que se haya enviado un id_pedido
if (!isset($_GET["id_pedido"]) || empty($_GET["id_pedido"])) {
    header("Location: mis_pedidos.php");
    exit();
}

// Incluir conexión a la base de datos
require_once "../config/conexion.php";

$id_usuario = intval($_SESSION["id_usuario"]);
$id_pedido = intval($_GET["id_pedido"]);

// Verificar que el pedido pertenezca al usuario logueado
$sql_verificar = "SELECT id_pedido FROM pedido WHERE id_pedido = ? AND id_usuario = ?";
$stmt_verificar = mysqli_prepare($conexion, $sql_verificar);
mysqli_stmt_bind_param($stmt_verificar, "ii", $id_pedido, $id_usuario);
mysqli_stmt_execute($stmt_verificar);
$resultado_verificar = mysqli_stmt_get_result($stmt_verificar);

if (mysqli_num_rows($resultado_verificar) == 0) {
    mysqli_stmt_close($stmt_verificar);
    mysqli_close($conexion);
    header("Location: mis_pedidos.php");
    exit();
}
mysqli_stmt_close($stmt_verificar);

// Consultar datos del pedido
$sql_pedido = "SELECT p.*, 
               (SELECT estado_pago FROM pago WHERE id_pedido = p.id_pedido LIMIT 1) AS estado_pago
               FROM pedido p 
               WHERE p.id_pedido = ? AND p.id_usuario = ?";

$stmt_pedido = mysqli_prepare($conexion, $sql_pedido);
mysqli_stmt_bind_param($stmt_pedido, "ii", $id_pedido, $id_usuario);
mysqli_stmt_execute($stmt_pedido);
$resultado_pedido = mysqli_stmt_get_result($stmt_pedido);
$pedido = mysqli_fetch_assoc($resultado_pedido);
mysqli_stmt_close($stmt_pedido);

// Consultar detalles del pedido (productos)
$sql_detalles = "SELECT pd.*, pr.nombre, pr.imagen
                 FROM pedido_detalle pd
                 INNER JOIN producto pr ON pd.id_producto = pr.id_producto
                 WHERE pd.id_pedido = ?";

$stmt_detalles = mysqli_prepare($conexion, $sql_detalles);
mysqli_stmt_bind_param($stmt_detalles, "i", $id_pedido);
mysqli_stmt_execute($stmt_detalles);
$resultado_detalles = mysqli_stmt_get_result($stmt_detalles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del pedido - PequeMundo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Estilos propios -->
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
</head>
<body>
    <!-- Menú -->
    <?php include "../masterpage/menu.php"; ?>

    <!-- Contenido principal -->
    <main class="container my-5">
        <!-- Encabezado y botón volver -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h1 class="titulo-catalogo mb-2">Detalle del pedido</h1>
                        <p class="subtitulo-catalogo">
                            Código: <strong><?php echo htmlspecialchars($pedido['codigo_pedido']); ?></strong>
                        </p>
                    </div>
                    <a href="mis_pedidos.php" class="btn btn-volver-carrito">
                        <i class="fa-solid fa-arrow-left me-2"></i> Volver a mis pedidos
                    </a>
                </div>
                <hr>
            </div>
        </div>

        <!-- Información del pedido -->
        <div class="row mb-5">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fa-solid fa-info-circle me-2"></i>Información general</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 40%;">Fecha del pedido:</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Estado del pedido:</td>
                                <td>
                                    <?php
                                    $clase_estado = '';
                                    switch ($pedido['estado_pedido']) {
                                        case 'pendiente_pago':
                                            $clase_estado = 'badge bg-warning text-dark';
                                            $texto_estado = 'Pendiente pago';
                                            break;
                                        case 'pagado':
                                            $clase_estado = 'badge bg-success';
                                            $texto_estado = 'Pagado';
                                            break;
                                        case 'preparando':
                                            $clase_estado = 'badge bg-info text-dark';
                                            $texto_estado = 'Preparando';
                                            break;
                                        case 'enviado':
                                            $clase_estado = 'badge bg-primary';
                                            $texto_estado = 'Enviado';
                                            break;
                                        case 'entregado':
                                            $clase_estado = 'badge bg-success';
                                            $texto_estado = 'Entregado';
                                            break;
                                        case 'cancelado':
                                            $clase_estado = 'badge bg-danger';
                                            $texto_estado = 'Cancelado';
                                            break;
                                        default:
                                            $clase_estado = 'badge bg-secondary';
                                            $texto_estado = ucfirst($pedido['estado_pedido']);
                                    }
                                    ?>
                                    <span class="<?php echo $clase_estado; ?> fs-6"><?php echo $texto_estado; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Estado del pago:</td>
                                <td>
                                    <?php 
                                    if ($pedido['estado_pago'] == 'pagado') {
                                        echo '<span class="badge bg-success fs-6"><i class="fa-solid fa-check-circle me-1"></i> Pagado</span>';
                                    } elseif ($pedido['estado_pago'] == 'pendiente') {
                                        echo '<span class="badge bg-warning text-dark fs-6"><i class="fa-solid fa-clock me-1"></i> Pendiente</span>';
                                    } elseif ($pedido['estado_pago'] == 'fallido') {
                                        echo '<span class="badge bg-danger fs-6"><i class="fa-solid fa-times-circle me-1"></i> Fallido</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary fs-6">' . ucfirst($pedido['estado_pago']) . '</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tipo de entrega:</td>
                                <td>
                                    <?php 
                                    if ($pedido['tipo_entrega'] == 'retiro_tienda') {
                                        echo '<i class="fa-solid fa-store me-1"></i> Retiro en tienda';
                                    } else {
                                        echo '<i class="fa-solid fa-truck me-1"></i> Despacho a domicilio';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fa-solid fa-location-dot me-2"></i>Dirección de entrega</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($pedido['tipo_entrega'] == 'despacho_domicilio'): ?>
                            <p class="mb-2">
                                <strong>Dirección:</strong><br>
                                <?php echo nl2br(htmlspecialchars($pedido['direccion_entrega'])); ?>
                            </p>
                            <p class="mb-0">
                                <strong>Región/Comuna:</strong><br>
                                <?php echo htmlspecialchars($pedido['region_entrega']); ?> / <?php echo htmlspecialchars($pedido['comuna_entrega']); ?>
                            </p>
                        <?php else: ?>
                            <p class="text-muted mb-0 text-center py-3">
                                <i class="fa-solid fa-store fa-2x mb-2 d-block"></i>
                                El pedido será retirado en nuestra tienda.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de productos -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fa-solid fa-boxes me-2"></i>Productos del pedido</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="tabla-finanzas-head">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio unitario</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $subtotal_total = 0;
                                    while ($detalle = mysqli_fetch_assoc($resultado_detalles)): 
                                        $subtotal = $detalle['subtotal'];
                                        $subtotal_total += $subtotal;
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <?php if (!empty($detalle['imagen'])): ?>
                                                            <img src="<?php echo htmlspecialchars($detalle['imagen']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($detalle['nombre']); ?>"
                                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                                        <?php else: ?>
                                                            <div style="width: 50px; height: 50px; background-color: #f2eee8; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fa-solid fa-image" style="color: #9a7c3a;"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <strong><?php echo htmlspecialchars($detalle['nombre']); ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo intval($detalle['cantidad']); ?></td>
                                            <td>$<?php echo number_format($detalle['precio_unitario'], 0, ',', '.'); ?></td>
                                            <td class="text-end fw-bold">$<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Subtotal productos:</td>
                                        <td class="text-end fw-bold">$<?php echo number_format($subtotal_total, 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Costo de despacho:</td>
                                        <td class="text-end fw-bold">$<?php echo number_format($pedido['costo_despacho'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td colspan="3" class="text-end fw-bold fs-5">Total del pedido:</td>
                                        <td class="text-end fw-bold fs-5 text-success">
                                            $<?php echo number_format($pedido['total_pedido'], 0, ',', '.'); ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón acción según estado -->
        <div class="row">
            <div class="col-12 text-center">
                <?php if ($pedido['estado_pedido'] == 'pendiente_pago'): ?>
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Este pedido aún no ha sido pagado. Para completar la compra, debes crear un nuevo pedido desde el carrito.
                    </div>
                    <a href="../carrito.php" class="btn btn-custom btn-lg">
                        <i class="fa-solid fa-cart-shopping me-2"></i> Ir al carrito
                    </a>
                <?php endif; ?>
                <a href="mis_pedidos.php" class="btn btn-secondary btn-lg ms-2">
                    <i class="fa-solid fa-list me-2"></i> Ver todos mis pedidos
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include "../masterpage/footer.php"; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Cerrar recursos
mysqli_stmt_close($stmt_detalles);
mysqli_close($conexion);
?>