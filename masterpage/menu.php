<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
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
                <!-- Nostros solo para clientes y público -->
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
                        <a class="nav-link" href="carrito.php">Carrito</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mis_pedidos.php">Mis pedidos</a>
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
                    <span class="navbar-text me-2">
                        <?php echo $_SESSION["usuario"]; ?>
                    </span>
                    <a class="btn btn-custom2" href="action/cerrar_sesion.php">Cerrar sesión</a>
                <?php } else { ?>
                    <a class="btn btn-custom me-2" href="login.php">Iniciar sesión</a>
                    <a class="btn btn-custom" href="registro.php">Registrarse</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>