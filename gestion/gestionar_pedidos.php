<?php
    // Iniciar sesión para validar acceso
    session_start();
    // Incluir conexión a la base de datos
    require_once "../config/conexion.php";
    // Verificar que exista sesión iniciada
    if (!isset($_SESSION["id_usuario"])) {
        header("Location: ../login.php");
        exit();
session_start();
require_once "../config/conexion.php";
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../login.php");
    exit();
}
if (!isset($_SESSION["tipo_usuario"]) || ($_SESSION["tipo_usuario"] != "admin" && $_SESSION["tipo_usuario"] != "vendedor")) {
    header("Location: ../index.php");
    exit();
}
$estados_validos = array("pendiente_pago", "confirmado", "preparacion", "camino", "entregado", "cancelado");
$filtro_estado = isset($_GET["estado"]) && in_array($_GET["estado"], $estados_validos) ? $_GET["estado"] : "";
$mensaje = "";
$tipo_mensaje = "";
if (isset($_GET["msg"])) {
    if ($_GET["msg"] == "ok") {
        $mensaje = "Estado del pedido actualizado correctamente.";
        $tipo_mensaje = "success";
    } elseif ($_GET["msg"] == "error") {
        $mensaje = "No se pudo actualizar el estado del pedido.";
        $tipo_mensaje = "danger";
    } elseif ($_GET["msg"] == "pago") {
        $mensaje = "No se puede modificar un pedido que no tiene pago confirmado.";
        $tipo_mensaje = "warning";
    }
    // Verificar que solo admin y vendedor puedan acceder
    if (!isset($_SESSION["tipo_usuario"]) || ($_SESSION["tipo_usuario"] != "admin" && $_SESSION["tipo_usuario"] != "vendedor")) {
        echo "<script>
                alert('No tienes permiso para acceder a esta página');
                window.location.href = '../index.php';
              </script>";
        exit();
    }

    // ──────────────────────────────────────────
    // Filtro por estado de pedido
    // ──────────────────────────────────────────
    $estados_validos = array("pendiente_pago", "recibido", "preparacion", "camino", "entregado", "cancelado");
    $filtro_estado = isset($_GET["estado"]) && in_array($_GET["estado"], $estados_validos) ? $_GET["estado"] : "";

    // ──────────────────────────────────────────
    // Mensajes de feedback
    // ──────────────────────────────────────────
    $mensaje = "";
    $tipo_mensaje = "";
    if (isset($_GET["msg"])) {
        if ($_GET["msg"] == "ok") {
            $mensaje = "Estado del pedido actualizado correctamente.";
            $tipo_mensaje = "success";
        } elseif ($_GET["msg"] == "error") {
            $mensaje = "No se pudo actualizar el estado del pedido.";
            $tipo_mensaje = "danger";
        }
    }

    // ──────────────────────────────────────────
    // Consulta de pedidos con filtro opcional
    // ──────────────────────────────────────────
    if ($filtro_estado != "") {
        $sql_pedidos = "SELECT
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
                            pa.fecha_pago,
                            u.nombre AS cliente_nombre,
                            u.apellido AS cliente_apellido,
                            u.mail AS cliente_mail,
                            u.telefono AS cliente_telefono
                        FROM pedido p
                        INNER JOIN pago pa   ON p.id_pedido  = pa.id_pedido
                        INNER JOIN usuario u ON p.id_usuario = u.id_usuario
                        WHERE p.estado_pedido = ?
                        ORDER BY p.fecha_pedido DESC";
        $stmt_pedidos = mysqli_prepare($conexion, $sql_pedidos);
        mysqli_stmt_bind_param($stmt_pedidos, "s", $filtro_estado);
    } else {
        $sql_pedidos = "SELECT
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
                            pa.fecha_pago,
                            u.nombre AS cliente_nombre,
                            u.apellido AS cliente_apellido,
                            u.mail AS cliente_mail,
                            u.telefono AS cliente_telefono
                        FROM pedido p
                        INNER JOIN pago pa   ON p.id_pedido  = pa.id_pedido
                        INNER JOIN usuario u ON p.id_usuario = u.id_usuario
                        ORDER BY p.fecha_pedido DESC";
        $stmt_pedidos = mysqli_prepare($conexion, $sql_pedidos);
    }
    mysqli_stmt_execute($stmt_pedidos);
    $resultado_pedidos = mysqli_stmt_get_result($stmt_pedidos);
    $pedidos = array();
    while ($pedido = mysqli_fetch_assoc($resultado_pedidos)) {
        $pedidos[] = $pedido;
    }

    // ──────────────────────────────────────────
    // Contadores por estado (para las tarjetas)
    // ──────────────────────────────────────────
    $sql_contadores = "SELECT estado_pedido, COUNT(*) AS total FROM pedido GROUP BY estado_pedido";
    $resultado_contadores = mysqli_query($conexion, $sql_contadores);
    $contadores = array(
        "pendiente_pago" => 0,
        "recibido"       => 0,
        "preparacion"    => 0,
        "camino"         => 0,
        "entregado"      => 0,
        "cancelado"      => 0
    );
}
$sql_pedidos = "SELECT
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
                    pa.fecha_pago,
                    u.nombre AS cliente_nombre,
                    u.apellido AS cliente_apellido,
                    u.mail AS cliente_mail,
                    u.telefono AS cliente_telefono
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                INNER JOIN usuario u ON p.id_usuario = u.id_usuario";
if ($filtro_estado != "") {
    $sql_pedidos .= " WHERE p.estado_pedido = ?";
}
$sql_pedidos .= " ORDER BY p.fecha_pedido DESC";
$stmt_pedidos = mysqli_prepare($conexion, $sql_pedidos);
if (!$stmt_pedidos) {
    die("Error al preparar consulta de pedidos: " . mysqli_error($conexion));
}
if ($filtro_estado != "") {
    mysqli_stmt_bind_param($stmt_pedidos, "s", $filtro_estado);
}
mysqli_stmt_execute($stmt_pedidos);
$resultado_pedidos = mysqli_stmt_get_result($stmt_pedidos);
$pedidos = array();
while ($pedido = mysqli_fetch_assoc($resultado_pedidos)) {
    $pedidos[] = $pedido;
}
$sql_contadores = "SELECT estado_pedido, COUNT(*) AS total FROM pedido GROUP BY estado_pedido";
$resultado_contadores = mysqli_query($conexion, $sql_contadores);
$contadores = array(
    "pendiente_pago" => 0,
    "confirmado" => 0,
    "preparacion" => 0,
    "camino" => 0,
    "entregado" => 0,
    "cancelado" => 0
);
if ($resultado_contadores) {
    while ($fila = mysqli_fetch_assoc($resultado_contadores)) {
        if (isset($contadores[$fila["estado_pedido"]])) {
            $contadores[$fila["estado_pedido"]] = intval($fila["total"]);
        }
    }

    // ──────────────────────────────────────────
    // Funciones helper
    // ──────────────────────────────────────────
    function siguienteEstado($estado_actual) {
        $flujo = array(
            "recibido"    => "preparacion",
            "preparacion" => "camino",
            "camino"      => "entregado"
        );
        return isset($flujo[$estado_actual]) ? $flujo[$estado_actual] : "";
    }
    function labelEstado($estado) {
        $labels = array(
            "pendiente_pago" => "Pendiente pago",
            "recibido"       => "Recibido",
            "preparacion"    => "En preparación",
            "camino"         => "En camino",
            "entregado"      => "Entregado",
            "cancelado"      => "Cancelado"
        );
        return isset($labels[$estado]) ? $labels[$estado] : ucfirst($estado);
    }
    function badgeEstado($estado) {
        $clases = array(
            "pendiente_pago" => "secondary",
            "recibido"       => "warning text-dark",
            "preparacion"    => "info text-dark",
            "camino"         => "primary",
            "entregado"      => "success",
            "cancelado"      => "danger"
        );
        return isset($clases[$estado]) ? $clases[$estado] : "secondary";
    }
    function iconoSiguienteEstado($siguiente) {
        $iconos = array(
            "preparacion" => "fa-wrench",
            "camino"      => "fa-truck",
            "entregado"   => "fa-circle-check"
        );
        return isset($iconos[$siguiente]) ? $iconos[$siguiente] : "fa-arrow-right";
}
function labelEstado($estado) {
    $labels = array(
        "pendiente_pago" => "Pendiente pago",
        "confirmado" => "Confirmado",
        "preparacion" => "En preparación",
        "camino" => "En camino",
        "entregado" => "Entregado",
        "cancelado" => "Cancelado"
    );
    return isset($labels[$estado]) ? $labels[$estado] : ucfirst($estado);
}
function badgeEstado($estado) {
    $clases = array(
        "pendiente_pago" => "secondary",
        "confirmado" => "warning text-dark",
        "preparacion" => "info text-dark",
        "camino" => "primary",
        "entregado" => "success",
        "cancelado" => "danger"
    );
    return isset($clases[$estado]) ? $clases[$estado] : "secondary";
}
function obtenerDetallePedido($conexion, $id_pedido) {
    $sql_detalle = "SELECT
                        pd.cantidad,
                        pd.precio_unitario,
                        pd.subtotal,
                        pr.nombre,
                        pr.imagen
                    FROM pedido_detalle pd
                    INNER JOIN producto pr ON pd.id_producto = pr.id_producto
                    WHERE pd.id_pedido = ?";
    $stmt_detalle = mysqli_prepare($conexion, $sql_detalle);
    $detalles = array();
    if ($stmt_detalle) {
        mysqli_stmt_bind_param($stmt_detalle, "i", $id_pedido);
        mysqli_stmt_execute($stmt_detalle);
        $resultado_detalle = mysqli_stmt_get_result($stmt_detalle);
        while ($detalle = mysqli_fetch_assoc($resultado_detalle)) {
            $detalles[] = $detalle;
        }
    }
    return $detalles;
}
?>
<!DOCTYPE html>
<html lang="es">
@ -166,131 +131,72 @@
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestionar pedidos - PequeMundo</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Estilos propios -->
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
        <style>
            .contador-card {
                border-radius: 10px;
                border-left: 5px solid #9a7c3a;
                cursor: pointer;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
                text-decoration: none;
                color: inherit;
            }
            .contador-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(0,0,0,0.12) !important;
                color: inherit;
            }
            .contador-card.activo {
                border-left-color: #2f7187;
                background-color: #eaf4f8;
            }
            .contador-numero {
                font-family: 'Nunito', sans-serif;
                font-size: 1.6rem;
                font-weight: 700;
                color: #2f7187;
            }
            .contador-label {
                font-size: 0.78rem;
                text-transform: uppercase;
                color: #6c757d;
                letter-spacing: 0.4px;
            }
            .fila-pedido { cursor: pointer; transition: background-color 0.15s; }
            .fila-pedido:hover { background-color: #fdf6e3; }
            .fila-detalle { background-color: #faf7f2; }
            .btn-avanzar {
                background-color: #9a7c3a;
                color: #fff;
                border: none;
                font-family: var(--fuente-texto);
                font-weight: 600;
            }
            .btn-avanzar:hover { background-color: #b39044; color: #fff; }
        </style>
    </head>
    <body>
        <!-- Menú -->
        <?php include "../masterpage/menu.php"; ?>

        <main class="container my-5">
            <!-- Encabezado -->
            <section class="d-flex align-items-center mb-4 gap-3">
                <i class="fa-solid fa-boxes-stacked fa-2x" style="color:#9a7c3a;"></i>
                <div>
                    <h2 class="mb-0">Gestionar pedidos</h2>
                    <small class="text-muted">Seguimiento y actualización del estado de los pedidos.</small>
                </div>
            <section class="mb-4">
                <h2>Gestionar pedidos</h2>
                <p>Revisar pedidos, pagos y actualizar el estado de preparación o despacho.</p>
                <hr>
            </section>

            <!-- Alerta de feedback -->
            <?php if ($mensaje != "") { ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <!-- Tarjetas contadoras por estado -->
            <section class="row g-3 mb-4">
                <?php
                $estados_cards = array(
                    "recibido"       => array("Recibidos",       "fa-inbox"),
                    "preparacion"    => array("En preparación",  "fa-wrench"),
                    "camino"         => array("En camino",       "fa-truck"),
                    "entregado"      => array("Entregados",      "fa-circle-check"),
                    "pendiente_pago" => array("Pend. pago",      "fa-clock"),
                    "cancelado"      => array("Cancelados",      "fa-ban")
                );
                foreach ($estados_cards as $estado => $info) {
                    $activo = $filtro_estado == $estado ? "activo" : "";
                    $link = "gestionar_pedidos.php" . ($filtro_estado == $estado ? "" : "?estado=" . $estado);
                    $estados_cards = array(
                        "confirmado" => array("Confirmados", "fa-inbox"),
                        "preparacion" => array("En preparación", "fa-wrench"),
                        "camino" => array("En camino", "fa-truck"),
                        "entregado" => array("Entregados", "fa-circle-check"),
                        "pendiente_pago" => array("Pend. pago", "fa-clock"),
                        "cancelado" => array("Cancelados", "fa-ban")
                    );
                ?>
                <?php foreach ($estados_cards as $estado => $info) { ?>
                    <?php
                        $activo = $filtro_estado == $estado ? "border border-2 border-primary" : "";
                        $link = $filtro_estado == $estado ? "gestionar_pedidos.php" : "gestionar_pedidos.php?estado=" . $estado;
                    ?>
                    <div class="col-6 col-md-4 col-xl-2">
                        <a href="<?php echo $link; ?>" class="card shadow-sm border-0 contador-card <?php echo $activo; ?> h-100 p-3 d-flex flex-row align-items-center gap-2">
                            <i class="fa-solid <?php echo $info[1]; ?> fa-lg" style="color:#9a7c3a;"></i>
                            <div>
                                <div class="contador-label"><?php echo $info[0]; ?></div>
                                <div class="contador-numero"><?php echo $contadores[$estado]; ?></div>
                        <a href="<?php echo $link; ?>" class="card shadow-sm text-decoration-none text-dark h-100 <?php echo $activo; ?>">
                            <div class="card-body text-center">
                                <i class="fa-solid <?php echo $info[1]; ?> mb-2" style="color:#9a7c3a;"></i>
                                <div class="small text-muted"><?php echo $info[0]; ?></div>
                                <div class="fw-bold fs-4" style="color:#2f7187;"><?php echo $contadores[$estado]; ?></div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </section>

            <!-- Barra de búsqueda e info -->
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body py-3">
                <div class="card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <input type="text" id="buscador" class="form-control"
                                   placeholder="Buscar por código, cliente o correo...">
                            <input type="text" id="buscador" class="form-control" placeholder="Buscar por código, cliente o correo">
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">
                                <?php if ($filtro_estado != "") { ?>
                                    Filtrando por: <strong><?php echo labelEstado($filtro_estado); ?></strong>
                                    &mdash; <a href="gestionar_pedidos.php" style="color:#9a7c3a;">Ver todos</a>
                                    Filtrando por: <strong><?php echo labelEstado($filtro_estado); ?></strong> — <a href="gestionar_pedidos.php">Ver todos</a>
                                <?php } else { ?>
                                    Mostrando todos los pedidos
                                <?php } ?>
                                &mdash; <strong><?php echo count($pedidos); ?></strong>
                                resultado<?php echo count($pedidos) != 1 ? "s" : ""; ?>
                                — <strong><?php echo count($pedidos); ?></strong> resultado<?php echo count($pedidos) != 1 ? "s" : ""; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tabla de pedidos -->
            <section class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
@ -303,131 +209,148 @@
                                    <th>Entrega</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Pago</th>
                                    <th class="text-center">Estado pedido</th>
                                    <th class="text-center">Acción</th>
                                    <th class="text-center">Estado actual</th>
                                    <th class="text-center">Cambiar estado</th>
                                    <th class="text-center">Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($pedidos) > 0) { ?>
                                    <?php foreach ($pedidos as $p) { ?>
                                    <?php foreach ($pedidos as $pedido) { ?>
                                        <?php
                                            $siguiente = siguienteEstado($p["estado_pedido"]);
                                            $id = intval($p["id_pedido"]);
                                            $id_pedido = intval($pedido["id_pedido"]);
                                            $detalles = obtenerDetallePedido($conexion, $id_pedido);
                                        ?>
                                        <!-- Fila principal -->
                                        <tr class="fila-pedido" id="fila-<?php echo $id; ?>">
                                            <td class="fw-semibold" style="font-size:0.83rem;">
                                                <?php echo htmlspecialchars($p["codigo_pedido"]); ?>
                                            </td>
                                        <tr class="fila-pedido" id="fila-<?php echo $id_pedido; ?>">
                                            <td class="fw-semibold"><?php echo htmlspecialchars($pedido["codigo_pedido"]); ?></td>
                                            <td>
                                                <?php echo date("d/m/Y", strtotime($p["fecha_pedido"])); ?>
                                                <br><small class="text-muted"><?php echo date("H:i", strtotime($p["fecha_pedido"])); ?></small>
                                                <?php echo date("d/m/Y", strtotime($pedido["fecha_pedido"])); ?>
                                                <br>
                                                <small class="text-muted"><?php echo date("H:i", strtotime($pedido["fecha_pedido"])); ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($p["cliente_nombre"] . " " . $p["cliente_apellido"]); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($p["cliente_mail"]); ?></small>
                                                <?php if (!empty($p["cliente_telefono"])) { ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($p["cliente_telefono"]); ?></small>
                                                <strong><?php echo htmlspecialchars($pedido["cliente_nombre"] . " " . $pedido["cliente_apellido"]); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($pedido["cliente_mail"]); ?></small>
                                                <?php if (!empty($pedido["cliente_telefono"])) { ?>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($pedido["cliente_telefono"]); ?></small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($p["tipo_entrega"] == "despacho_domicilio") { ?>
                                                    <i class="fa-solid fa-truck fa-sm text-muted me-1"></i> Despacho
                                                    <?php if (!empty($p["region_entrega"])) { ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($p["region_entrega"]); ?></small>
                                                <?php if ($pedido["tipo_entrega"] == "despacho_domicilio") { ?>
                                                    <i class="fa-solid fa-truck text-muted me-1"></i> Despacho
                                                    <?php if (!empty($pedido["region_entrega"])) { ?>
                                                        <br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($pedido["region_entrega"]); ?></small>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <i class="fa-solid fa-store fa-sm text-muted me-1"></i> Retiro en tienda
                                                    <i class="fa-solid fa-store text-muted me-1"></i> Retiro en tienda
                                                <?php } ?>
                                            </td>
                                            <td class="text-end fw-semibold">
                                                $<?php echo number_format($p["total_pedido"], 0, ",", "."); ?>
                                            </td>
                                            <td class="text-end fw-semibold">$<?php echo number_format($pedido["total_pedido"], 0, ",", "."); ?></td>
                                            <td class="text-center">
                                                <?php if ($p["estado_pago"] == "pagado") { ?>
                                                <?php if ($pedido["estado_pago"] == "pagado") { ?>
                                                    <span class="badge bg-success">Pagado</span>
                                                    <?php if (!empty($p["fecha_pago"])) { ?>
                                                        <br><small class="text-muted"><?php echo date("d/m/Y", strtotime($p["fecha_pago"])); ?></small>
                                                    <?php if (!empty($pedido["fecha_pago"])) { ?>
                                                        <br>
                                                        <small class="text-muted"><?php echo date("d/m/Y", strtotime($pedido["fecha_pago"])); ?></small>
                                                    <?php } ?>
                                                <?php } elseif ($p["estado_pago"] == "pendiente") { ?>
                                                <?php } elseif ($pedido["estado_pago"] == "pendiente") { ?>
                                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-danger"><?php echo ucfirst($p["estado_pago"]); ?></span>
                                                    <span class="badge bg-danger"><?php echo htmlspecialchars(ucfirst($pedido["estado_pago"])); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-<?php echo badgeEstado($p["estado_pedido"]); ?>">
                                                    <?php echo labelEstado($p["estado_pedido"]); ?>
                                                <span class="badge bg-<?php echo badgeEstado($pedido["estado_pedido"]); ?>">
                                                    <?php echo labelEstado($pedido["estado_pedido"]); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($siguiente != "") { ?>
                                                    <form action="../action/gestionar_pedido_action.php" method="POST"
                                                          onsubmit="return confirm('¿Confirmar cambio a «<?php echo labelEstado($siguiente); ?>»?');">
                                                        <input type="hidden" name="id_pedido" value="<?php echo $id; ?>">
                                                        <input type="hidden" name="nuevo_estado" value="<?php echo $siguiente; ?>">
                                                <?php if ($pedido["estado_pago"] == "pagado" && $pedido["estado_pedido"] != "cancelado") { ?>
                                                    <form action="../action/gestionar_pedido_action.php" method="POST" class="d-flex gap-2 justify-content-center" onsubmit="return confirm('¿Confirmar cambio de estado del pedido?');">
                                                        <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
                                                        <input type="hidden" name="filtro_estado" value="<?php echo htmlspecialchars($filtro_estado); ?>">
                                                        <button type="submit" name="btnAvanzarEstado" class="btn btn-sm btn-avanzar">
                                                            <i class="fa-solid <?php echo iconoSiguienteEstado($siguiente); ?> me-1"></i>
                                                            <?php echo labelEstado($siguiente); ?>
                                                        <select name="nuevo_estado" class="form-control form-control-sm" required>
                                                            <option value="confirmado" <?php if ($pedido["estado_pedido"] == "confirmado") echo "selected"; ?>>Confirmado</option>
                                                            <option value="preparacion" <?php if ($pedido["estado_pedido"] == "preparacion") echo "selected"; ?>>En preparación</option>
                                                            <option value="camino" <?php if ($pedido["estado_pedido"] == "camino") echo "selected"; ?>>En camino</option>
                                                            <option value="entregado" <?php if ($pedido["estado_pedido"] == "entregado") echo "selected"; ?>>Entregado</option>
                                                            <option value="cancelado" <?php if ($pedido["estado_pedido"] == "cancelado") echo "selected"; ?>>Cancelado</option>
                                                        </select>
                                                        <button type="submit" name="btnActualizarEstadoPedido" class="btn btn-sm btn-finalizar-compra">
                                                            Actualizar
                                                        </button>
                                                    </form>
                                                <?php } elseif ($p["estado_pedido"] == "entregado") { ?>
                                                    <span class="text-success fw-semibold">
                                                        <i class="fa-solid fa-circle-check"></i> Completado
                                                    </span>
                                                <?php } elseif ($p["estado_pedido"] == "pendiente_pago") { ?>
                                                    <span class="text-muted">
                                                        <i class="fa-solid fa-clock"></i> Esperando pago
                                                    </span>
                                                <?php } elseif ($p["estado_pedido"] == "cancelado") { ?>
                                                    <span class="text-danger">
                                                        <i class="fa-solid fa-ban"></i> Cancelado
                                                    </span>
                                                <?php } elseif ($pedido["estado_pago"] != "pagado" && $pedido["estado_pedido"] != "cancelado") { ?>
                                                    <span class="text-muted"><i class="fa-solid fa-clock"></i> Esperando pago</span>
                                                <?php } elseif ($pedido["estado_pedido"] == "cancelado") { ?>
                                                    <span class="text-danger"><i class="fa-solid fa-ban"></i> Cancelado</span>
                                                <?php } else { ?>
                                                    <span class="text-muted">Sin acción</span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-secondary"
                                                        type="button"
                                                        onclick="toggleDetalle(<?php echo $id; ?>)"
                                                        id="btn-detalle-<?php echo $id; ?>">
                                                    <i class="fa-solid fa-chevron-down" id="icono-<?php echo $id; ?>"></i>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleDetalle(<?php echo $id_pedido; ?>)" id="btn-detalle-<?php echo $id_pedido; ?>">
                                                    <i class="fa-solid fa-chevron-down" id="icono-<?php echo $id_pedido; ?>"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Fila de detalle expandible -->
                                        <tr class="fila-detalle" id="detalle-<?php echo $id; ?>" style="display:none;">
                                        <tr class="fila-detalle" id="detalle-<?php echo $id_pedido; ?>" style="display:none;">
                                            <td colspan="9" class="p-0">
                                                <div class="p-3">
                                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                                        <div>
                                                <div class="p-3 bg-light">
                                                    <div class="row mb-3">
                                                        <div class="col-md-7">
                                                            <strong>Dirección de entrega</strong><br>
                                                            <?php if ($p["tipo_entrega"] == "despacho_domicilio") { ?>
                                                                <?php echo htmlspecialchars($p["direccion_entrega"] ?? "—"); ?>,
                                                                <?php echo htmlspecialchars($p["comuna_entrega"] ?? ""); ?>,
                                                                <?php echo htmlspecialchars($p["region_entrega"] ?? ""); ?>
                                                            <?php if ($pedido["tipo_entrega"] == "despacho_domicilio") { ?>
                                                                <?php echo htmlspecialchars($pedido["direccion_entrega"] ?? ""); ?>,
                                                                <?php echo htmlspecialchars($pedido["comuna_entrega"] ?? ""); ?>,
                                                                <?php echo htmlspecialchars($pedido["region_entrega"] ?? ""); ?>
                                                            <?php } else { ?>
                                                                <span class="text-muted">Retiro en tienda física</span>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="text-end">
                                                            <small class="text-muted d-block">
                                                                Subtotal: <strong>$<?php echo number_format($p["total_productos"], 0, ",", "."); ?></strong>
                                                            </small>
                                                            <small class="text-muted d-block">
                                                                Despacho: <strong>$<?php echo number_format($p["costo_despacho"], 0, ",", "."); ?></strong>
                                                            </small>
                                                            <strong style="color:#2f7187;">
                                                                Total: $<?php echo number_format($p["total_pedido"], 0, ",", "."); ?>
                                                            </strong>
                                                        <div class="col-md-5 text-md-end">
                                                            <small class="text-muted d-block">Subtotal: <strong>$<?php echo number_format($pedido["total_productos"], 0, ",", "."); ?></strong></small>
                                                            <small class="text-muted d-block">Despacho: <strong>$<?php echo number_format($pedido["costo_despacho"], 0, ",", "."); ?></strong></small>
                                                            <strong style="color:#2f7187;">Total: $<?php echo number_format($pedido["total_pedido"], 0, ",", "."); ?></strong>
                                                        </div>
                                                    </div>
                                                    <!-- Productos cargados por JS -->
                                                    <div id="productos-<?php echo $id; ?>">
                                                        <p class="text-muted mb-0">
                                                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Cargando productos...
                                                        </p>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 bg-white">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Producto</th>
                                                                    <th class="text-end">Precio unitario</th>
                                                                    <th class="text-end">Cantidad</th>
                                                                    <th class="text-end">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (count($detalles) > 0) { ?>
                                                                    <?php foreach ($detalles as $detalle) { ?>
                                                                        <tr>
                                                                            <td>
                                                                                <div class="d-flex align-items-center gap-2">
                                                                                    <?php if (!empty($detalle["imagen"])) { ?>
                                                                                        <img src="../<?php echo htmlspecialchars($detalle["imagen"]); ?>" width="38" height="38" style="object-fit:cover;border-radius:5px;">
                                                                                    <?php } ?>
                                                                                    <span><?php echo htmlspecialchars($detalle["nombre"]); ?></span>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-end">$<?php echo number_format($detalle["precio_unitario"], 0, ",", "."); ?></td>
                                                                            <td class="text-end"><?php echo intval($detalle["cantidad"]); ?></td>
                                                                            <td class="text-end fw-semibold">$<?php echo number_format($detalle["subtotal"], 0, ",", "."); ?></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <tr>
                                                                        <td colspan="4" class="text-center text-muted">Sin productos registrados.</td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
@ -435,13 +358,7 @@
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <?php if ($filtro_estado != "") { ?>
                                                No hay pedidos con estado «<?php echo labelEstado($filtro_estado); ?>».
                                            <?php } else { ?>
                                                No hay pedidos registrados.
                                            <?php } ?>
                                        </td>
                                        <td colspan="9" class="text-center text-muted py-4">No hay pedidos registrados.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
@ -450,14 +367,9 @@
                </div>
            </section>
        </main>

        <!-- Footer -->
        <?php include "../masterpage/footer.php"; ?>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Buscador en tiempo real
            document.getElementById("buscador").addEventListener("input", function () {
                var texto = this.value.toLowerCase();
                var filas = document.querySelectorAll("#tablaPedidos tbody tr.fila-pedido");
@ -469,60 +381,28 @@
                        fila.style.display = "";
                    } else {
                        fila.style.display = "none";
                        if (filaDetalle) filaDetalle.style.display = "none";
                        if (filaDetalle) {
                            filaDetalle.style.display = "none";
                        }
                    }
                });
            });

            // Detalle expandible con carga de productos
            var detallesCargados = {};

            function toggleDetalle(id) {
                var filaDetalle = document.getElementById("detalle-" + id);
                var icono       = document.getElementById("icono-" + id);
                var contenedor  = document.getElementById("productos-" + id);
                var visible     = filaDetalle.style.display !== "none";

                if (visible) {
                var icono = document.getElementById("icono-" + id);
                if (filaDetalle.style.display === "none") {
                    filaDetalle.style.display = "";
                    icono.classList.remove("fa-chevron-down");
                    icono.classList.add("fa-chevron-up");
                } else {
                    filaDetalle.style.display = "none";
                    icono.classList.replace("fa-chevron-up", "fa-chevron-down");
                    return;
                    icono.classList.remove("fa-chevron-up");
                    icono.classList.add("fa-chevron-down");
                }

                filaDetalle.style.display = "";
                icono.classList.replace("fa-chevron-down", "fa-chevron-up");

                if (detallesCargados[id]) return;

                fetch("../api/detalle_pedido_api.php?id_pedido=" + id)
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        detallesCargados[id] = true;
                        if (!data.ok || data.items.length === 0) {
                            contenedor.innerHTML = "<p class='text-muted'>Sin productos registrados.</p>";
                            return;
                        }
                        var html = "<div class='table-responsive'><table class='table table-sm table-bordered mb-0'>";
                        html += "<thead class='table-light'><tr><th>Producto</th><th class='text-end'>Precio unit.</th><th class='text-end'>Cantidad</th><th class='text-end'>Subtotal</th></tr></thead><tbody>";
                        data.items.forEach(function (item) {
                            html += "<tr><td><div class='d-flex align-items-center gap-2'>";
                            if (item.imagen) {
                                html += "<img src='../" + item.imagen + "' width='38' height='38' style='object-fit:cover;border-radius:5px;' onerror='this.style.display=\"none\"'>";
                            }
                            html += "<span>" + item.nombre + "</span></div></td>";
                            html += "<td class='text-end'>$" + parseInt(item.precio_unitario).toLocaleString("es-CL") + "</td>";
                            html += "<td class='text-end'>" + item.cantidad + "</td>";
                            html += "<td class='text-end fw-semibold'>$" + parseInt(item.subtotal).toLocaleString("es-CL") + "</td>";
                            html += "</tr>";
                        });
                        html += "</tbody></table></div>";
                        contenedor.innerHTML = html;
                    })
                    .catch(function () {
                        contenedor.innerHTML = "<p class='text-danger'>Error al cargar los productos.</p>";
                    });
            }
        </script>
    </body>
</html>
<?php mysqli_close($conexion); ?>
<?php
mysqli_close($conexion);
?>