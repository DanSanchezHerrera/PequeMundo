<?php
    // Iniciar sesión solo si todavía no existe una sesión activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Guardar cantidad de productos en el carrito
    $cantidad_carrito = 0;
    // Contar productos del carrito solo si el usuario es cliente
    if (isset($_SESSION["id_usuario"]) && isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "cliente") {
        require_once "config/conexion.php";
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
?>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="img/pequeMundo_logo.png" alt="Logo PequeMundo" width="140" height="56" class="d-inline-block align-text-top">
        </a>
        <!-- Botón responsive -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPequeMundo" aria-controls="menuPequeMundo" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPequeMundo">
            <!-- Menú público -->
            <ul class="navbar-nav ms-auto me-4 mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="catalogo.php">Catálogo</a>
                </li>
                <!-- Mostrar Nosotros solo para público y clientes -->
                <?php if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] == "cliente") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="nosotros.php">Nosotros</a>
                    </li>
                <?php } ?>
                <!-- Opciones para cliente -->
                <?php if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "cliente") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cliente_panel.php">Mi perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mis_pedidos.php">Mis pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link carrito-link" href="carrito.php" aria-label="Carrito de compras">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="carrito-badge"><?php echo $cantidad_carrito; ?></span>
                        </a>
                    </li>
                <?php } ?>
                <!-- Opciones para vendedor -->
                <?php if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "vendedor") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionar_productos.php">Gestionar Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionar_pedidos.php">Gestionar Pedidos</a>
                    </li>
                <?php } ?>
                <!-- Opciones para administrador -->
                <?php if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "admin") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionar_productos.php">Gestionar Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionar_pedidos.php">Gestionar Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionar_usuarios.php">Gestionar Usuarios</a>
                    </li>
                <?php } ?>
                <!-- Opciones para finanzas -->
                <?php if (isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "finanzas") { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes.php">Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ranking.php">Ranking Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="historial.php">Historial de ventas</a>
                    </li>
                <?php } ?>
            </ul>
            <!-- Zona derecha: sesión -->
            <div class="d-flex align-items-center gap-2">
                <?php if (isset($_SESSION["usuario"])) { ?>
                    <span class="navbar-text me-2"><?php echo $_SESSION["usuario"]; ?></span>
                    <a class="btn btn-custom2" href="action/cerrar_sesion.php">Cerrar sesión</a>
                <?php } else { ?>
                    <a class="btn btn-custom me-2" href="login.php">Iniciar sesión</a>
                    <a class="btn btn-custom" href="registro.php">Registrarse</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>