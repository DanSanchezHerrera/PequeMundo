<?php
// Iniciar sesión para validar al usuario conectado
session_start();
// Incluir conexión a la base de datos
require_once "config/conexion.php";
// Verificar que haya una sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}
// Verificar que solo clientes puedan ver el carrito
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: index.php");
    exit();
}
// Guardar id del usuario conectado
$id_usuario = intval($_SESSION["id_usuario"]);
// Buscar productos agregados al carrito del usuario
$sql = "SELECT 
            cd.id_carrito_detalle,
            cd.id_carrito,
            cd.id_producto,
            cd.cantidad,
            cd.precio_unitario,
            p.nombre,
            p.descripcion,
            p.imagen,
            p.stock,
            p.estado
        FROM carrito c
        INNER JOIN carrito_detalle cd ON c.id_carrito = cd.id_carrito
        INNER JOIN producto p ON cd.id_producto = p.id_producto
        WHERE c.id_usuario = ?
        ORDER BY cd.id_carrito_detalle DESC";
// Preparar consulta segura
$stmt = mysqli_prepare($conexion, $sql);
if (!$stmt) {
    die("Error al preparar la consulta del carrito: " . mysqli_error($conexion));
}
// Asociar usuario conectado a la consulta
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
// Ejecutar consulta
mysqli_stmt_execute($stmt);
// Obtener resultados
$resultado = mysqli_stmt_get_result($stmt);
// Inicializar total del carrito
$total_carrito = 0;
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Carrito - PequeMundo</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Estilos propios -->
        <link rel="stylesheet" href="css/estilos.css">
        <link rel="stylesheet" href="css/navbar.css">
        <link rel="stylesheet" href="css/footer.css">
    </head>
    <body>
        <!-- NAVBAR -->
        <?php include "masterpage/menu.php"; ?>
        <!-- CONTENIDO PRINCIPAL -->
        <main class="container my-5">
            <!-- Título de la página -->
            <section class="mb-4">
                <h2>Mi carrito</h2>
                <p>Revisa los productos que agregaste antes de finalizar tu compra.</p>
                <hr>
            </section>
            <?php if ($resultado && mysqli_num_rows($resultado) > 0) { ?>
                <!-- Contenedor del carrito -->
                <section class="row">
                    <!-- Listado de productos -->
                    <div class="col-lg-8">
                        <?php while ($producto = mysqli_fetch_assoc($resultado)) { ?>
                            <?php
                                // Calcular subtotal del producto
                                $subtotal = intval($producto["precio_unitario"]) * intval($producto["cantidad"]);
                                // Sumar subtotal al total del carrito
                                $total_carrito += $subtotal;
                            ?>
                            <!-- Producto del carrito -->
                            <article class="bg-white rounded shadow-sm p-3 mb-3">
                                <div class="row align-items-center">
                                    <!-- Imagen del producto -->
                                    <div class="col-md-3 text-center mb-3 mb-md-0">
                                        <img 
                                            src="<?php echo htmlspecialchars($producto["imagen"]); ?>" 
                                            alt="<?php echo htmlspecialchars($producto["nombre"]); ?>" 
                                            class="img-fluid rounded"
                                            style="max-height: 130px; object-fit: cover;"
                                        >
                                    </div>
                                    <!-- Información del producto -->
                                    <div class="col-md-5">
                                        <h5><?php echo htmlspecialchars($producto["nombre"]); ?></h5>
                                        <p class="mb-1">
                                            Precio unitario:
                                            $<?php echo number_format($producto["precio_unitario"], 0, ",", "."); ?>
                                        </p>
                                        <p class="mb-1">
                                            Stock disponible:
                                            <?php echo intval($producto["stock"]); ?>
                                        </p>
                                        <p class="fw-bold mb-0">
                                            Subtotal:
                                            $<?php echo number_format($subtotal, 0, ",", "."); ?>
                                        </p>
                                    </div>
                                    <!-- Acciones del producto -->
                                    <div class="col-md-4 text-center">
                                        <!-- Cambiar cantidad -->
                                        <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                                            <!-- Disminuir cantidad -->
                                            <form action="action/carrito_action.php" method="POST">
                                                <input type="hidden" name="id_carrito_detalle" value="<?php echo intval($producto["id_carrito_detalle"]); ?>">
                                                <button type="submit" name="btnDisminuirCantidad" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fa-solid fa-minus"></i>
                                                </button>
                                            </form>
                                            <!-- Mostrar cantidad actual -->
                                            <span class="fw-bold">
                                                <?php echo intval($producto["cantidad"]); ?>
                                            </span>
                                            <!-- Aumentar cantidad -->
                                            <form action="action/carrito_action.php" method="POST">
                                                <input type="hidden" name="id_carrito_detalle" value="<?php echo intval($producto["id_carrito_detalle"]); ?>">
                                                <button type="submit" name="btnAumentarCantidad" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fa-solid fa-plus"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <!-- Eliminar producto -->
                                        <form action="action/carrito_action.php" method="POST">
                                            <input type="hidden" name="id_carrito_detalle" value="<?php echo intval($producto["id_carrito_detalle"]); ?>">
                                            <button type="submit" name="btnEliminarProducto" class="btn btn-danger btn-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                    <!-- Resumen del carrito -->
                    <div class="col-lg-4">
                        <aside class="bg-white rounded shadow-sm p-4">
                            <h4>Resumen</h4>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Total:</span>
                                <strong>
                                    $<?php echo number_format($total_carrito, 0, ",", "."); ?>
                                </strong>
                            </div>
                            <!-- Volver al catálogo -->
                            <a href="catalogo.php" class="btn btn-seguir-comprando w-100 mb-3">
                                Seguir comprando
                            </a>
                            <!-- Ir al checkout -->
                            <a href="checkout.php" class="btn btn-finalizar-compra w-100 mb-3">
                                Finalizar compra
                            </a>
                            <!-- Vaciar carrito -->
                            <form action="action/carrito_action.php" method="POST">
                                <button type="submit" name="btnVaciarCarrito" class="btn btn-outline-danger w-100">
                                    Vaciar carrito
                                </button>
                            </form>
                        </aside>
                    </div>
                </section>
            <?php } else { ?>
                <!-- Mensaje de carrito vacío -->
                <section class="bg-white rounded shadow-sm p-5 text-center">
                    <h4>Tu carrito está vacío</h4>
                    <p class="mb-4">Aún no has agregado productos al carrito.</p>
                    <a href="catalogo.php" class="btn btn-custom" style="color: white;">
                        Ver catálogo
                    </a>
                </section>
            <?php } ?>
        </main>
        <!-- FOOTER -->
        <?php include "masterpage/footer.php"; ?>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
// Cerrar consulta y conexión
mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>