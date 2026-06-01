<?php
// Iniciar sesión
session_start();
// Verificar acceso de cliente
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../login.php");
    exit();
}
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Guardar id del usuario conectado
$id_usuario = intval($_SESSION["id_usuario"]);
// Consultar datos del usuario
$sql_usuario = "SELECT nombre, apellido, mail, telefono, direccion, region, comuna FROM usuario WHERE id_usuario = ? LIMIT 1";
$stmt_usuario = mysqli_prepare($conexion, $sql_usuario);
mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario);
mysqli_stmt_execute($stmt_usuario);
$resultado_usuario = mysqli_stmt_get_result($stmt_usuario);
$usuario = mysqli_fetch_assoc($resultado_usuario);
// Dejar foto de perfil comentada para implementación futura
// $foto_perfil = !empty($usuario["foto_perfil"]) ? "../" . $usuario["foto_perfil"] : "../img/default-avatar.png";
// Consultar últimos pedidos del cliente
$sql_pedidos = "SELECT 
                    p.id_pedido,
                    p.codigo_pedido,
                    p.fecha_pedido,
                    p.total_pedido,
                    p.estado_pedido,
                    pa.estado_pago
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_pedido DESC
                LIMIT 4";
$stmt_pedidos = mysqli_prepare($conexion, $sql_pedidos);
mysqli_stmt_bind_param($stmt_pedidos, "i", $id_usuario);
mysqli_stmt_execute($stmt_pedidos);
$resultado_pedidos = mysqli_stmt_get_result($stmt_pedidos);
// Obtener texto visible del estado
function mostrarEstadoPedidoPanel($estado) {
    switch ($estado) {
        case "pendiente_pago":
            return "Pendiente de pago";
        case "confirmado":
            return "Confirmado";
        case "preparacion":
            return "En preparación";
        case "camino":
            return "En camino";
        case "entregado":
            return "Entregado";
        case "cancelado":
            return "Cancelado";
        default:
            return ucfirst($estado);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mi perfil - PequeMundo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
    </head>
    <body>
        <?php include "../masterpage/menu.php"; ?>
        <main class="container my-5">
            <section class="bg-white rounded shadow-sm p-4 mb-4">
                <h2>Mi perfil</h2>
                <p class="text-muted mb-0">Bienvenido/a, <?php echo htmlspecialchars($usuario["nombre"] . " " . $usuario["apellido"]); ?>.</p>
            </section>
            <section class="row">
                <div class="col-md-4 mb-4">
                    <article class="bg-white rounded shadow-sm p-4 h-100 text-center">
                        <!-- Implementar foto de perfil más adelante -->
                        <!-- <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;"> -->
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:100px;height:100px;background-color:#fce07a;color:#2f7187;font-size:40px;font-weight:bold;">
                            <?php echo strtoupper(substr($usuario["nombre"], 0, 1)); ?>
                        </div>
                        <h4><?php echo htmlspecialchars($usuario["nombre"] . " " . $usuario["apellido"]); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($usuario["mail"]); ?></p>
                        <hr>
                        <p><strong>Teléfono:</strong><br><?php echo htmlspecialchars($usuario["telefono"]); ?></p>
                        <p><strong>Dirección:</strong><br><?php echo htmlspecialchars($usuario["direccion"]); ?></p>
                        <p><strong>Comuna:</strong><br><?php echo htmlspecialchars($usuario["comuna"]); ?></p>
                        <p><strong>Región:</strong><br><?php echo htmlspecialchars($usuario["region"]); ?></p>
                        <a href="editar_perfil.php" class="btn btn-finalizar-compra w-100">Editar perfil</a>
                    </article>
                </div>
                <div class="col-md-8 mb-4">
                    <article class="bg-white rounded shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Últimos pedidos</h4>
                            <a href="mis_pedidos.php" class="btn btn-sm btn-seguir-comprando">Ver todos</a>
                        </div>
                        <?php if ($resultado_pedidos && mysqli_num_rows($resultado_pedidos) > 0) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-warning">
                                        <tr>
                                            <th>Código</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Pedido</th>
                                            <th>Pago</th>
                                            <th>Detalle</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($pedido = mysqli_fetch_assoc($resultado_pedidos)) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($pedido["codigo_pedido"]); ?></td>
                                                <td><?php echo date("d/m/Y", strtotime($pedido["fecha_pedido"])); ?></td>
                                                <td>$<?php echo number_format($pedido["total_pedido"], 0, ",", "."); ?></td>
                                                <td><?php echo mostrarEstadoPedidoPanel($pedido["estado_pedido"]); ?></td>
                                                <td><?php echo ucfirst(htmlspecialchars($pedido["estado_pago"])); ?></td>
                                                <td>
                                                    <a href="detalle_pedido.php?id_pedido=<?php echo intval($pedido["id_pedido"]); ?>" class="btn btn-sm btn-finalizar-compra">Ver</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="text-center py-5">
                                <h5>Aún no tienes pedidos</h5>
                                <p class="text-muted">Cuando realices una compra, aparecerá aquí.</p>
                                <a href="../catalogo.php" class="btn btn-finalizar-compra">Ir al catálogo</a>
                            </div>
                        <?php } ?>
                    </article>
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