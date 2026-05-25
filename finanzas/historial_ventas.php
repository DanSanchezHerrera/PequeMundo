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
    // Filtros
    // ──────────────────────────────────────────
    $anio_actual  = date("Y");
    $fecha_desde  = isset($_GET["fecha_desde"]) && $_GET["fecha_desde"] != "" ? $_GET["fecha_desde"] : $anio_actual . "-01-01";
    $fecha_hasta  = isset($_GET["fecha_hasta"]) && $_GET["fecha_hasta"] != "" ? $_GET["fecha_hasta"] : date("Y-m-d");
    $estado_filtro = isset($_GET["estado"]) ? trim($_GET["estado"]) : "";
    $buscar_cliente = isset($_GET["cliente"]) ? trim($_GET["cliente"]) : "";

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_desde)) $fecha_desde = $anio_actual . "-01-01";
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_hasta)) $fecha_hasta = date("Y-m-d");

    // Paginación
    $por_pagina = 20;
    $pagina_actual = isset($_GET["pagina"]) ? max(1, intval($_GET["pagina"])) : 1;
    $offset = ($pagina_actual - 1) * $por_pagina;

    // ──────────────────────────────────────────
    // Contar total de registros para paginación
    // ──────────────────────────────────────────
    $sql_count = "SELECT COUNT(*) AS total
                  FROM pedido p
                  INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                  INNER JOIN usuario u ON p.id_usuario = u.id_usuario
                  WHERE p.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                    AND (? = '' OR pa.estado_pago = ?)
                    AND (? = '' OR CONCAT(u.nombre, ' ', u.apellido) LIKE ?)";
    $stmt_count = mysqli_prepare($conexion, $sql_count);
    $like_cliente = "%" . $buscar_cliente . "%";
    mysqli_stmt_bind_param($stmt_count, "ssssss",
        $fecha_desde, $fecha_hasta,
        $estado_filtro, $estado_filtro,
        $buscar_cliente, $like_cliente
    );
    mysqli_stmt_execute($stmt_count);
    $total_registros = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_count))["total"];
    $total_paginas = ceil($total_registros / $por_pagina);

    // ──────────────────────────────────────────
    // Consulta principal del historial
    // ──────────────────────────────────────────
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
                          u.nombre         AS cliente_nombre,
                          u.apellido        AS cliente_apellido,
                          u.email           AS cliente_email
                      FROM pedido p
                      INNER JOIN pago pa    ON p.id_pedido   = pa.id_pedido
                      INNER JOIN usuario u  ON p.id_usuario  = u.id_usuario
                      WHERE p.fecha_pedido BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                        AND (? = '' OR pa.estado_pago = ?)
                        AND (? = '' OR CONCAT(u.nombre, ' ', u.apellido) LIKE ?)
                      ORDER BY p.fecha_pedido DESC
                      LIMIT ? OFFSET ?";
    $stmt_historial = mysqli_prepare($conexion, $sql_historial);
    mysqli_stmt_bind_param($stmt_historial, "ssssssii",
        $fecha_desde, $fecha_hasta,
        $estado_filtro, $estado_filtro,
        $buscar_cliente, $like_cliente,
        $por_pagina, $offset
    );
    mysqli_stmt_execute($stmt_historial);
    $resultado_historial = mysqli_stmt_get_result($stmt_historial);
    $pedidos = [];
    while ($fila = mysqli_fetch_assoc($resultado_historial)) {
        $pedidos[] = $fila;
    }

    // ──────────────────────────────────────────
    // Detalle de un pedido específico (modal AJAX-less)
    // ──────────────────────────────────────────
    $pedido_detalle_items = [];
    $id_ver = isset($_GET["ver"]) ? intval($_GET["ver"]) : 0;
    if ($id_ver > 0) {
        $sql_detalle = "SELECT pd.cantidad, pd.precio_unitario, pd.subtotal,
                               pr.nombre, pr.imagen
                        FROM pedido_detalle pd
                        INNER JOIN producto pr ON pd.id_producto = pr.id_producto
                        WHERE pd.id_pedido = ?";
        $stmt_det = mysqli_prepare($conexion, $sql_detalle);
        mysqli_stmt_bind_param($stmt_det, "i", $id_ver);
        mysqli_stmt_execute($stmt_det);
        $resultado_det = mysqli_stmt_get_result($stmt_det);
        while ($item = mysqli_fetch_assoc($resultado_det)) {
            $pedido_detalle_items[] = $item;
        }
    }

    // Helper: color de badge según estado de pago
    function badgePago($estado) {
        switch ($estado) {
            case 'pagado':     return 'success';
            case 'pendiente':  return 'warning text-dark';
            case 'rechazado':  return 'danger';
            default:           return 'secondary';
        }
    }
    // Helper: color de badge según estado de pedido
    function badgePedido($estado) {
        switch ($estado) {
            case 'pendiente_pago':         return 'secondary';
            case 'en_produccion':          return 'info text-dark';
            case 'listo_para_despachar':   return 'primary';
            case 'entregado':              return 'success';
            default:                       return 'secondary';
        }
    }
    function labelPedido($estado) {
        $map = [
            'pendiente_pago'       => 'Pendiente pago',
            'en_produccion'        => 'En producción',
            'listo_para_despachar' => 'Listo para despachar',
            'entregado'            => 'Entregado',
        ];
        return $map[$estado] ?? ucfirst($estado);
    }

    // Construir query string sin el parámetro "ver" para los enlaces de paginación
    $qs_base = http_build_query(array_filter([
        "fecha_desde"  => $fecha_desde,
        "fecha_hasta"  => $fecha_hasta,
        "estado"       => $estado_filtro,
        "cliente"      => $buscar_cliente
    ]));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de ventas - PequeMundo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">
    <style>
        .btn-filtrar {
            background-color: #9a7c3a;
            color: #fff;
            border: none;
        }
        .btn-filtrar:hover { background-color: #b39044; color: #fff; }
        .fila-pedido { cursor: pointer; transition: background-color 0.15s; }
        .fila-pedido:hover { background-color: #fdf6e3; }
        .detalle-expandido { background-color: #faf7f2; }
        .page-link { color: #9a7c3a; }
        .page-item.active .page-link {
            background-color: #9a7c3a;
            border-color: #9a7c3a;
            color: #fff;
        }
    </style>
</head>
<body>
    <?php include '../masterpage/menu.php'; ?>

    <main class="container my-5">
        <!-- Encabezado -->
        <div class="d-flex align-items-center mb-4 gap-3">
            <i class="fa-solid fa-clock-rotate-left fa-2x" style="color:#9a7c3a;"></i>
            <div>
                <h2 class="mb-0">Historial de ventas</h2>
                <small class="text-muted">Registro completo de todos los pedidos</small>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" style="color:#9a7c3a;">Finanzas</a></li>
                <li class="breadcrumb-item active">Historial de ventas</li>
            </ol>
        </nav>

        <!-- ── Filtros ── -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="historial_ventas.php" class="row g-3 align-items-end">
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
                    <div class="col-md-2">
                        <label class="form-label">Estado pago</label>
                        <select name="estado" class="form-control">
                            <option value="">Todos</option>
                            <option value="pagado"    <?php echo $estado_filtro === "pagado"    ? "selected" : ""; ?>>Pagado</option>
                            <option value="pendiente" <?php echo $estado_filtro === "pendiente" ? "selected" : ""; ?>>Pendiente</option>
                            <option value="rechazado" <?php echo $estado_filtro === "rechazado" ? "selected" : ""; ?>>Rechazado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Buscar cliente</label>
                        <input type="text" name="cliente" class="form-control"
                               placeholder="Nombre o apellido"
                               value="<?php echo htmlspecialchars($buscar_cliente); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-filtrar w-100">
                            <i class="fa-solid fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── Info resultado ── -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <small class="text-muted">
                <?php echo $total_registros; ?> pedido<?php echo $total_registros != 1 ? "s" : ""; ?> encontrado<?php echo $total_registros != 1 ? "s" : ""; ?>
                — página <?php echo $pagina_actual; ?> de <?php echo max(1, $total_paginas); ?>
            </small>
        </div>

        <!-- ── Tabla de historial ── -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead style="background-color:#fdf3d0;">
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Entrega</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Pago</th>
                                <th class="text-center">Estado pedido</th>
                                <th class="text-center">Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($pedidos) > 0): ?>
                                <?php foreach ($pedidos as $p): ?>
                                    <?php $es_activo = $id_ver === intval($p["id_pedido"]); ?>
                                    <!-- Fila principal -->
                                    <tr class="fila-pedido <?php echo $es_activo ? 'table-warning' : ''; ?>"
                                        onclick="window.location.href='historial_ventas.php?<?php echo $qs_base; ?>&pagina=<?php echo $pagina_actual; ?>&ver=<?php echo $es_activo ? 0 : $p['id_pedido']; ?>'">
                                        <td class="fw-semibold" style="font-size:0.85rem;">
                                            <?php echo htmlspecialchars($p["codigo_pedido"]); ?>
                                        </td>
                                        <td>
                                            <?php echo date("d/m/Y H:i", strtotime($p["fecha_pedido"])); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($p["cliente_nombre"] . " " . $p["cliente_apellido"]); ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($p["cliente_email"]); ?></small>
                                        </td>
                                        <td>
                                            <?php if ($p["tipo_entrega"] === "despacho_domicilio"): ?>
                                                <i class="fa-solid fa-truck fa-sm text-muted"></i> Despacho
                                            <?php else: ?>
                                                <i class="fa-solid fa-store fa-sm text-muted"></i> Retiro
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            $<?php echo number_format($p["total_pedido"], 0, ',', '.'); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-<?php echo badgePago($p["estado_pago"]); ?>">
                                                <?php echo ucfirst($p["estado_pago"]); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-<?php echo badgePedido($p["estado_pedido"]); ?>">
                                                <?php echo labelPedido($p["estado_pedido"]); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <i class="fa-solid fa-<?php echo $es_activo ? 'chevron-up' : 'chevron-down'; ?> text-muted"></i>
                                        </td>
                                    </tr>
                                    <!-- Fila de detalle (expandible) -->
                                    <?php if ($es_activo && count($pedido_detalle_items) > 0): ?>
                                        <tr class="detalle-expandido">
                                            <td colspan="8" class="p-3">
                                                <strong class="d-block mb-2">Productos del pedido</strong>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered mb-2">
                                                        <thead>
                                                            <tr>
                                                                <th>Producto</th>
                                                                <th class="text-end">Precio unit.</th>
                                                                <th class="text-end">Cantidad</th>
                                                                <th class="text-end">Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($pedido_detalle_items as $item): ?>
                                                                <tr>
                                                                    <td>
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <img src="../<?php echo htmlspecialchars($item['imagen']); ?>"
                                                                                 width="36" height="36"
                                                                                 style="object-fit:cover;border-radius:4px;"
                                                                                 onerror="this.style.display='none'">
                                                                            <?php echo htmlspecialchars($item["nombre"]); ?>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-end">$<?php echo number_format($item["precio_unitario"], 0, ',', '.'); ?></td>
                                                                    <td class="text-end"><?php echo intval($item["cantidad"]); ?></td>
                                                                    <td class="text-end fw-semibold">$<?php echo number_format($item["subtotal"], 0, ',', '.'); ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="d-flex justify-content-end gap-4 text-sm">
                                                    <span>Subtotal productos: <strong>$<?php echo number_format($p["total_productos"], 0, ',', '.'); ?></strong></span>
                                                    <span>Despacho: <strong>$<?php echo number_format($p["costo_despacho"], 0, ',', '.'); ?></strong></span>
                                                    <span style="color:#2f7187;">Total: <strong>$<?php echo number_format($p["total_pedido"], 0, ',', '.'); ?></strong></span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No hay pedidos en el período seleccionado.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── Paginación ── -->
        <?php if ($total_paginas > 1): ?>
            <nav>
                <ul class="pagination justify-content-center flex-wrap">
                    <!-- Anterior -->
                    <li class="page-item <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link"
                           href="historial_ventas.php?<?php echo $qs_base; ?>&pagina=<?php echo $pagina_actual - 1; ?>">
                            &laquo;
                        </a>
                    </li>
                    <?php for ($i = max(1, $pagina_actual - 3); $i <= min($total_paginas, $pagina_actual + 3); $i++): ?>
                        <li class="page-item <?php echo $i === $pagina_actual ? 'active' : ''; ?>">
                            <a class="page-link"
                               href="historial_ventas.php?<?php echo $qs_base; ?>&pagina=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    <!-- Siguiente -->
                    <li class="page-item <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>">
                        <a class="page-link"
                           href="historial_ventas.php?<?php echo $qs_base; ?>&pagina=<?php echo $pagina_actual + 1; ?>">
                            &raquo;
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </main>

    <?php include '../masterpage/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
