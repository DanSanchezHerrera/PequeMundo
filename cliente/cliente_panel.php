<?php
session_start();
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../login.php");
    exit();
}
require_once "../config/conexion.php";
$id_usuario = intval($_SESSION["id_usuario"]);
$sql_usuario = "SELECT nombre, apellido, mail, telefono, direccion, region, comuna FROM usuario WHERE id_usuario = ? LIMIT 1";
$stmt_usuario = mysqli_prepare($conexion, $sql_usuario);
mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario);
mysqli_stmt_execute($stmt_usuario);
$resultado_usuario = mysqli_stmt_get_result($stmt_usuario);
$usuario = mysqli_fetch_assoc($resultado_usuario);
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
                LIMIT 3";
$stmt_pedidos = mysqli_prepare($conexion, $sql_pedidos);
mysqli_stmt_bind_param($stmt_pedidos, "i", $id_usuario);
mysqli_stmt_execute($stmt_pedidos);
$resultado_pedidos = mysqli_stmt_get_result($stmt_pedidos);
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
    </head>
    <body>
        <?php include "../masterpage/menu.php"; ?>
        <main class="container my-5">
            <section class="bg-white rounded shadow-sm p-4 mb-4">
                <h2>Mi perfil</h2>
                <p class="text-muted mb-0">
                    Bienvenido/a, <?php echo htmlspecialchars($usuario["nombre"] . " " . $usuario["apellido"]); ?>.
                </p>
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
                        <p>
                            <strong>Teléfono:</strong><br>
                            <?php echo !empty($usuario["telefono"]) ? htmlspecialchars($usuario["telefono"]) : "No registrado"; ?>
                        </p>
                        <p>
                            <strong>Dirección:</strong><br>
                            <?php echo !empty($usuario["direccion"]) ? htmlspecialchars($usuario["direccion"]) : "No registrada"; ?>
                        </p>
                        <p>
                            <strong>Comuna:</strong><br>
                            <?php echo !empty($usuario["comuna"]) ? htmlspecialchars($usuario["comuna"]) : "No registrada"; ?>
                        </p>
                        <p>
                            <strong>Región:</strong><br>
                            <?php echo !empty($usuario["region"]) ? htmlspecialchars($usuario["region"]) : "No registrada"; ?>
                        </p>
                        <a href="editar_perfil.php" class="btn btn-finalizar-compra w-100">
                            Editar perfil
                        </a>
                    </article>
                </div>
                <div class="col-md-8 mb-4">
                    <article class="bg-white rounded shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="mb-1">Resumen de pedidos</h4>
                                <p class="text-muted mb-0">Aquí se muestran tus últimos 3 pedidos.</p>
                            </div>
                            <a href="mis_pedidos.php" class="btn btn-sm btn-seguir-comprando">
                                Ver todos
                            </a>
                        </div>
                        <?php if ($resultado_pedidos && mysqli_num_rows($resultado_pedidos) > 0) { ?>
                            <div class="row g-3">
                                <?php while ($pedido = mysqli_fetch_assoc($resultado_pedidos)) { ?>
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 d-flex justify-content-between align-items-center flex-wrap">
                                            <div>
                                                <h6 class="mb-1">
                                                    Pedido <?php echo htmlspecialchars($pedido["codigo_pedido"]); ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <?php echo date("d/m/Y H:i", strtotime($pedido["fecha_pedido"])); ?>
                                                </small>
                                                <br>
                                                <span class="badge bg-secondary mt-2">
                                                    <?php echo mostrarEstadoPedidoPanel($pedido["estado_pedido"]); ?>
                                                </span>
                                                <?php if ($pedido["estado_pago"] == "pagado") { ?>
                                                    <span class="badge bg-success mt-2">Pagado</span>
                                                <?php } elseif ($pedido["estado_pago"] == "pendiente") { ?>
                                                    <span class="badge bg-warning text-dark mt-2">Pendiente</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-danger mt-2">Rechazado</span>
                                                <?php } ?>
                                            </div>
                                            <div class="text-end mt-3 mt-md-0">
                                                <strong>$<?php echo number_format($pedido["total_pedido"], 0, ",", "."); ?></strong>
                                                <br>
                                                <a href="detalle_pedido.php?id_pedido=<?php echo intval($pedido["id_pedido"]); ?>" class="btn btn-sm btn-finalizar-compra mt-2">
                                                    Ver detalle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="text-center py-5">
                                <i class="fa-solid fa-box-open fa-3x mb-3" style="color:#9a7c3a;"></i>
                                <h5>Aún no tienes pedidos</h5>
                                <p class="text-muted">Cuando realices una compra, aparecerá aquí un resumen.</p>
                                <a href="../catalogo.php" class="btn btn-finalizar-compra">
                                    Ir al catálogo
                                </a>
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