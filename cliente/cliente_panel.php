<?php
// Iniciar sesión
session_start();

// 1. Incluye tu archivo de conexión a la base de datos
// Cambia esta ruta por la correcta en tu proyecto
include 'conexion.php'; 

// 2. Verificar si el usuario inició sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php"); // Ajusta la ruta a tu login
// Verificar acceso de cliente
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../login.php");
    exit();
}

$id_del_usuario = $_SESSION['usuario_id'];

// 3. Obtener los datos del usuario actual
$query_usuario = "SELECT nombre, foto FROM usuarios WHERE id = '$id_del_usuario'";
$resultado_usuario = mysqli_query($conexion, $query_usuario);
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

// Si el usuario no tiene foto en la BD, le ponemos una por defecto
$foto_perfil = !empty($usuario['foto']) ? $usuario['foto'] : 'img/default-avatar.png';

// 4. (Opcional) Aquí iría la consulta real para obtener los pedidos.
// Por ahora, para que no se te rompa el diseño si no tienes la tabla pedidos creada,
// dejo el arreglo simulado. Cuando tengas la tabla, lo cambias por un mysqli_query() con un WHILE.
$pedidos = [
    [
        'id' => '657', 'fecha' => '05 Marzo 2026, 08:28 PM', 'estado' => 'En preparación',
        'total_items' => 1, 'total_precio' => '129.990',
        'items' => [
            ['nombre' => 'Cuna Nube Sueño', 'descripcion' => 'Cuna de diseño moderno', 'precio' => '129.990', 'cantidad' => 1, 'imagen' => 'img/cuna.jpg']
        ]
    ]
];
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
    <title>PequeMundo - Mi Perfil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #fcfbf9; color: #333; }

        .dashboard-wrapper { display: flex; max-width: 1200px; margin: 40px auto; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; min-height: 600px; }
        .sidebar { width: 280px; background-color: #fce07a; padding: 40px 30px; display: flex; flex-direction: column; align-items: center; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 3px solid white; background-color: white;}
        .sidebar h2 { font-size: 20px; color: #2c7a7b; margin-bottom: 30px; text-align: center; }
        .sidebar-menu { width: 100%; list-style: none; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu a { text-decoration: none; color: #2c7a7b; font-weight: 600; font-size: 15px; display: block; }
        .sidebar-menu a:hover { opacity: 0.7; }
        .sidebar-menu a.activo { border-bottom: 2px solid #2c7a7b; padding-bottom: 2px; display: inline-block; }
        .divider { width: 100%; height: 1px; background-color: #d1b860; margin: 20px 0; }

        .main-content { flex: 1; padding: 40px; background-color: #fcfbf9; }
        .orders-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .order-card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .order-header { display: flex; justify-content: space-between; border-bottom: 1px solid #edf2f7; padding-bottom: 10px; margin-bottom: 15px; }
        .order-header h3 { font-size: 16px; color: #2d3748; }
        .order-header p { font-size: 12px; color: #718096; }
        .order-item { display: flex; align-items: center; margin-bottom: 15px; }
        .order-item img { width: 50px; height: 50px; margin-right: 15px; border-radius: 4px; object-fit: cover; background:#eee;}
        .order-item-details h4 { font-size: 14px; color: #2b6cb0; margin-bottom: 2px; }
        .order-item-details p { font-size: 11px; color: #a0aec0; margin-bottom: 4px; }
        .order-item-price { font-size: 13px; font-weight: bold; color: #4a5568; }
        .order-item-price span { color: #718096; font-weight: normal; margin-left: 10px; }
        .order-footer { border-top: 1px solid #edf2f7; padding-top: 15px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #718096; }
        .status { color: #319795; font-weight: bold; }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Perfil" class="profile-img">
            <h2>¡Hola <?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?>!</h2>
            
            <ul class="sidebar-menu">
                <li><a href="cliente_panel.php" class="activo">Pedidos</a></li>
                <li><a href="#">Seguimiento de pedidos</a></li>
                <li><a href="#">Favoritos</a></li>
                <li><a href="editar_perfil.php">Editar perfil</a></li>
            </ul>
            <div class="divider"></div>
            <ul class="sidebar-menu">
                <li><a href="login.php?logout=true">Cerrar sesión</a></li> 
            </ul>
        </aside>

        <main class="main-content">
            <div class="orders-grid">
                
                <?php foreach($pedidos as $pedido): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo htmlspecialchars($pedido['id']); ?></h3>
                            <p><?php echo htmlspecialchars($pedido['fecha']); ?></p>
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
                        <div class="icon">🌍</div>
                    </div>
                    
                    <?php foreach($pedido['items'] as $item): ?>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                        <div class="order-item-details">
                            <h4><?php echo htmlspecialchars($item['nombre']); ?></h4>
                            <p><?php echo htmlspecialchars($item['descripcion']); ?></p>
                            <div class="order-item-price">
                                $<?php echo htmlspecialchars($item['precio']); ?> 
                                <span>Cantidad: <?php echo htmlspecialchars($item['cantidad']); ?></span>
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
                    </div>
                    <?php endforeach; ?>

                    <div class="order-footer">
                        <span>X<?php echo htmlspecialchars($pedido['total_items']); ?> Items</span>
                        <span>Total más envío: $<?php echo htmlspecialchars($pedido['total_precio']); ?></span>
                        <span class="status">Estado: <?php echo htmlspecialchars($pedido['estado']); ?></span>
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
                <?php endforeach; ?>
                
            </div>
            </section>
        </main>
    </div>

</body>
</html>
        <?php include "../masterpage/footer.php"; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
mysqli_close($conexion);
?>