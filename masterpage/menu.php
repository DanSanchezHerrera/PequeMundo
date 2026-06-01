<?php
// Iniciar sesión solo si todavía no existe una sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Calcular ruta base según la carpeta actual
$carpeta_actual = basename(dirname($_SERVER["SCRIPT_NAME"]));
$ruta_base = "";
if ($carpeta_actual == "cliente" || $carpeta_actual == "admin" || $carpeta_actual == "gestion" || $carpeta_actual == "finanzas") {
    $ruta_base = "../";
}
// Guardar cantidad de productos en el carrito
$cantidad_carrito = 0;
// Contar productos del carrito solo si el usuario es cliente
if (isset($_SESSION["id_usuario"]) && isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "cliente") {
    require_once __DIR__ . "/../config/conexion.php";
    $id_usuario_menu = intval($_SESSION["id_usuario"]);
    $sql_carrito_menu = "SELECT SUM(cd.cantidad) AS total_carrito
                         FROM carrito c
                         INNER JOIN carrito_detalle cd ON c.id_carrito = cd.id_carrito
                         WHERE c.id_usuario = ?";
    $stmt_carrito_menu = mysqli_prepare($conexion, $sql_carrito_menu);
    if ($stmt_carrito_menu) {
        mysqli_stmt_bind_param($stmt_carrito_menu, "i", $id_usuario_menu);
        mysqli_stmt_execute($stmt_carrito_menu);
        $resultado_carrito_menu = mysqli_stmt_get_result($stmt_carrito_menu);
        $datos_carrito_menu = mysqli_fetch_assoc($resultado_carrito_menu);
        if ($datos_carrito_menu && $datos_carrito_menu["total_carrito"] != null) {
            $cantidad_carrito = intval($datos_carrito_menu["total_carrito"]);
        }
    }
}
// Obtener nombre visible de sesión
$nombre_sesion = "";
if (isset($_SESSION["usuario"])) {
    $nombre_sesion = $_SESSION["usuario"];
} elseif (isset($_SESSION["nombre"])) {
    $nombre_sesion = $_SESSION["nombre"];
}
?>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="<?php echo $ruta_base; ?>index.php">
            <img src="<?php echo $ruta_base; ?>img/pequeMundo_logo.png" alt="Logo PequeMundo" width="140" height="56" class="d-inline-block align-text-top">
        </a>
        <!-- Botón responsive -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPequeMundo" aria-controls="menuPequeMundo" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPequeMundo">
            <!-- Menú principal -->
            <ul class="navbar-nav ms-auto me-4 mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $ruta_base; ?>index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $ruta_base; ?>catalogo.php">Catálogo</a>
                </li>
                <!-- Mostrar Nosotros solo para público y clientes -->
                <?php if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] == "cliente") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>nosotros.php">Nosotros</a>
                    </li>
                <?php } ?>
                <!-- Opciones para cliente -->
                <?php if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "cliente") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>cliente/cliente_panel.php">Mi perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>cliente/mis_pedidos.php">Mis pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link carrito-link" href="<?php echo $ruta_base; ?>carrito.php" aria-label="Carrito de compras">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="carrito-badge"><?php echo $cantidad_carrito; ?></span>
                        </a>
                    </li>
                <?php } ?>
                <!-- Opciones para vendedor -->
                <?php if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "vendedor") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>gestion/gestionar_productos.php">Gestionar Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>gestion/gestionar_pedidos.php">Gestionar Pedidos</a>
                    </li>
                <?php } ?>
                <!-- Opciones para administrador -->
                <?php if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "admin") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>gestion/gestionar_productos.php">Gestionar Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>gestion/gestionar_pedidos.php">Gestionar Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>admin/gestionar_usuarios.php">Gestionar Usuarios</a>
                    </li>
                <?php } ?>
                <!-- Opciones para finanzas -->
                <?php if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "finanzas") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>finanzas/reportes.php">Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>finanzas/productos_mas_vendidos.php">Productos más vendidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $ruta_base; ?>finanzas/historial_ventas.php">Historial de ventas</a>
                    </li>
                <?php } ?>
            </ul>
            <!-- Zona derecha: sesión -->
            <div class="d-flex align-items-center gap-2">
                <?php if (isset($_SESSION["id_usuario"])) { ?>
                    <?php if ($nombre_sesion != "") { ?>
                        <span class="navbar-text me-2"><?php echo htmlspecialchars($nombre_sesion); ?></span>
                    <?php } ?>
                    <a class="btn btn-custom2" href="<?php echo $ruta_base; ?>action/cerrar_sesion.php">Cerrar sesión</a>
                <?php } else { ?>
                    <a class="btn btn-custom me-2" href="<?php echo $ruta_base; ?>login.php">Iniciar sesión</a>
                    <a class="btn btn-custom" href="<?php echo $ruta_base; ?>registro.php">Registrarse</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>