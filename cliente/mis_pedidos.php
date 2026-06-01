<?php
// Iniciar sesión
session_start();

// Verificar que el usuario sea cliente
if (!isset($_SESSION["id_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../login.php");
    exit();
}

// Incluir conexión a la base de datos
require_once "../config/conexion.php";

$id_usuario = intval($_SESSION["id_usuario"]);

// Consultar pedidos del usuario
$sql_pedidos = "SELECT id_pedido, codigo_pedido, fecha_pedido, total_pedido, estado_pedido, tipo_entrega
                FROM pedido 
                WHERE id_usuario = ? 
                ORDER BY fecha_pedido DESC";

$sql_pedidos = "SELECT 
                    p.id_pedido,
                    p.codigo_pedido,
                    p.fecha_pedido,
                    p.total_pedido,
                    p.estado_pedido,
                    p.tipo_entrega,
                    pa.estado_pago
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_pedido DESC";
$stmt_pedidos = mysqli_prepare($conexion, $sql_pedidos);
if (!$stmt_pedidos) {
    die("Error al preparar la consulta de pedidos: " . mysqli_error($conexion));
}
mysqli_stmt_bind_param($stmt_pedidos, "i", $id_usuario);
mysqli_stmt_execute($stmt_pedidos);
$resultado_pedidos = mysqli_stmt_get_result($stmt_pedidos);
function obtenerEstadoPedido($estado_pedido) {
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
function obtenerEstadoPago($estado_pago) {
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis pedidos - PequeMundo</title>
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
        <div class="row">
            <div class="col-12">
                <h1 class="titulo-catalogo mb-2">Mis pedidos</h1>
                <p class="subtitulo-catalogo mb-4">Revisa el estado y detalles de tus compras</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <?php if (mysqli_num_rows($resultado_pedidos) > 0): ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mis pedidos - PequeMundo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
    </head>
    <body>
        <?php include "../masterpage/menu.php"; ?>
        <main class="container my-5">
            <section class="mb-4">
                <h2>Mis pedidos</h2>
                <p>Revisa el estado y detalle de tus compras.</p>
                <hr>
            </section>
            <section class="bg-white rounded shadow-sm p-4">
                <?php if ($resultado_pedidos && mysqli_num_rows($resultado_pedidos) > 0) { ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="tabla-finanzas-head">
                        <table class="table table-bordered align-middle">
                            <thead class="table-warning">
                                <tr>
                                    <th>Código pedido</th>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Tipo entrega</th>
                                    <th>Estado pedido</th>
                                    <th>Estado pago</th>
                                    <th>Entrega</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($pedido = mysqli_fetch_assoc($resultado_pedidos)): ?>
                                <?php while ($pedido = mysqli_fetch_assoc($resultado_pedidos)) { ?>
                                    <?php
                                    // Definir clase de estado según el estado del pedido
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

                                    // Formatear total
                                    $total_formateado = '$' . number_format($pedido['total_pedido'], 0, ',', '.');
                                        $estado_pedido = obtenerEstadoPedido($pedido["estado_pedido"]);
                                        $estado_pago = obtenerEstadoPago($pedido["estado_pago"]);
                                    ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($pedido["codigo_pedido"]); ?></strong></td>
                                        <td><?php echo date("d/m/Y H:i", strtotime($pedido["fecha_pedido"])); ?></td>
                                        <td><strong>$<?php echo number_format($pedido["total_pedido"], 0, ",", "."); ?></strong></td>
                                        <td><span class="<?php echo $estado_pedido["clase"]; ?>"><?php echo $estado_pedido["texto"]; ?></span></td>
                                        <td><span class="<?php echo $estado_pago["clase"]; ?>"><?php echo $estado_pago["texto"]; ?></span></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($pedido['codigo_pedido']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success"><?php echo $total_formateado; ?></span>
                                        </td>
                                        <td>
                                            <span class="<?php echo $clase_estado; ?>"><?php echo $texto_estado; ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                            $tipo = $pedido['tipo_entrega'];
                                            if ($tipo == 'retiro_tienda') {
                                                echo '<i class="fa-solid fa-store me-1"></i> Retiro en tienda';
                                            } else {
                                                echo '<i class="fa-solid fa-truck me-1"></i> Despacho a domicilio';
                                            }
                                            ?>
                                            <?php if ($pedido["tipo_entrega"] == "retiro_tienda") { ?>
                                                <i class="fa-solid fa-store me-1"></i> Retiro en tienda
                                            <?php } else { ?>
                                                <i class="fa-solid fa-truck me-1"></i> Despacho a domicilio
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="detalle_pedido.php?id_pedido=<?php echo $pedido['id_pedido']; ?>" 
                                               class="btn btn-sm btn-custom">
                                            <a href="detalle_pedido.php?id_pedido=<?php echo intval($pedido["id_pedido"]); ?>" class="btn btn-sm btn-custom">
                                                <i class="fa-solid fa-eye me-1"></i> Ver detalle
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                <?php } else { ?>
                    <div class="text-center py-5">
                        <div class="mensaje-sin-productos">
                            <i class="fa-solid fa-box-open fa-3x mb-3" style="color: #9a7c3a;"></i>
                            <h4 class="mb-2">Aún no tienes pedidos</h4>
                            <p class="text-muted mb-4">¡Explora nuestro catálogo y realiza tu primera compra!</p>
                            <a href="../catalogo.php" class="btn btn-custom">
                                <i class="fa-solid fa-store me-1"></i> Ir al catálogo
                            </a>
                        </div>
                        <i class="fa-solid fa-box-open fa-3x mb-3" style="color: #9a7c3a;"></i>
                        <h4 class="mb-2">Aún no tienes pedidos</h4>
                        <p class="text-muted mb-4">Explora nuestro catálogo y realiza tu primera compra.</p>
                        <a href="../catalogo.php" class="btn btn-custom">
                            <i class="fa-solid fa-store me-1"></i> Ir al catálogo
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include "../masterpage/footer.php"; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
                <?php } ?>
            </section>
        </main>
        <?php include "../masterpage/footer.php"; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

<?php
// Cerrar recursos
mysqli_stmt_close($stmt_pedidos);
mysqli_close($conexion);
?>