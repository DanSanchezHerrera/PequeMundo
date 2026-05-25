<?php
// Iniciar sesión para validar usuario conectado
session_start();
// Incluir conexión a la base de datos
require_once "config/conexion.php";
// Verificar que exista sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}
// Verificar que solo clientes puedan finalizar compra
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: index.php");
    exit();
}
// Guardar id del usuario conectado
$id_usuario = intval($_SESSION["id_usuario"]);
// Buscar datos del usuario para precargar información de entrega
$sql_usuario = "SELECT nombre, apellido, telefono, direccion, region, comuna FROM usuario WHERE id_usuario = ?";
$stmt_usuario = mysqli_prepare($conexion, $sql_usuario);
if (!$stmt_usuario) {
    die("Error al preparar consulta de usuario: " . mysqli_error($conexion));
}
mysqli_stmt_bind_param($stmt_usuario, "i", $id_usuario);
mysqli_stmt_execute($stmt_usuario);
$resultado_usuario = mysqli_stmt_get_result($stmt_usuario);
$usuario = mysqli_fetch_assoc($resultado_usuario);
// Buscar productos del carrito del usuario
$sql_carrito = "SELECT 
                    cd.id_carrito_detalle,
                    cd.id_producto,
                    cd.cantidad,
                    cd.precio_unitario,
                    p.nombre,
                    p.imagen,
                    p.stock,
                    p.estado
                FROM carrito c
                INNER JOIN carrito_detalle cd ON c.id_carrito = cd.id_carrito
                INNER JOIN producto p ON cd.id_producto = p.id_producto
                WHERE c.id_usuario = ?
                ORDER BY cd.id_carrito_detalle DESC";
$stmt_carrito = mysqli_prepare($conexion, $sql_carrito);
if (!$stmt_carrito) {
    die("Error al preparar consulta del carrito: " . mysqli_error($conexion));
}
mysqli_stmt_bind_param($stmt_carrito, "i", $id_usuario);
mysqli_stmt_execute($stmt_carrito);
$resultado_carrito = mysqli_stmt_get_result($stmt_carrito);
// Inicializar total de productos
$total_productos = 0;
// Guardar productos para poder recorrerlos en HTML
$productos_carrito = array();
while ($producto = mysqli_fetch_assoc($resultado_carrito)) {
    $subtotal = intval($producto["precio_unitario"]) * intval($producto["cantidad"]);
    $producto["subtotal"] = $subtotal;
    $total_productos += $subtotal;
    $productos_carrito[] = $producto;
}
// Redirigir al carrito si no hay productos
if (count($productos_carrito) == 0) {
    header("Location: carrito.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Finalizar compra - PequeMundo</title>
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
        <!-- Menú -->
        <?php include "masterpage/menu.php"; ?>
        <!-- Contenido principal -->
        <main class="container my-5">
            <!-- Encabezado -->
            <section class="mb-4">
                <h2>Finalizar compra</h2>
                <p>Revisar los datos del pedido antes de pasar al pago simulado.</p>
                <hr>
            </section>
            <form action="action/pedido_action.php" method="POST">
                <section class="row">
                    <!-- Datos de entrega -->
                    <div class="col-lg-7 mb-4">
                        <div class="bg-white rounded shadow-sm p-4">
                            <h4>Datos de entrega</h4>
                            <hr>
                            <!-- Seleccionar tipo de entrega -->
                            <div class="mb-3">
                                <label class="form-label">Tipo de entrega</label>
                                <select name="tipo_entrega" id="tipo_entrega" class="form-control" required>
                                    <option value="">Seleccione una opción</option>
                                    <option value="retiro_tienda">Retiro en tienda</option>
                                    <option value="despacho_domicilio">Despacho a domicilio</option>
                                </select>
                            </div>
                            <!-- Dirección de entrega -->
                            <div id="datos_despacho">
                                <div class="mb-3">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="direccion_entrega" class="form-control" value="<?php echo htmlspecialchars($usuario["direccion"] ?? ""); ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Región</label>
                                        <select name="region_entrega" id="region_entrega" class="form-control">
                                            <option value="">Seleccione una región</option>
                                            <option value="Arica y Parinacota" <?php if (($usuario["region"] ?? "") == "Arica y Parinacota") echo "selected"; ?>>Arica y Parinacota</option>
                                            <option value="Tarapacá" <?php if (($usuario["region"] ?? "") == "Tarapacá") echo "selected"; ?>>Tarapacá</option>
                                            <option value="Antofagasta" <?php if (($usuario["region"] ?? "") == "Antofagasta") echo "selected"; ?>>Antofagasta</option>
                                            <option value="Atacama" <?php if (($usuario["region"] ?? "") == "Atacama") echo "selected"; ?>>Atacama</option>
                                            <option value="Coquimbo" <?php if (($usuario["region"] ?? "") == "Coquimbo") echo "selected"; ?>>Coquimbo</option>
                                            <option value="Valparaíso" <?php if (($usuario["region"] ?? "") == "Valparaíso") echo "selected"; ?>>Valparaíso</option>
                                            <option value="Región Metropolitana" <?php if (($usuario["region"] ?? "") == "Región Metropolitana") echo "selected"; ?>>Región Metropolitana</option>
                                            <option value="O'Higgins" <?php if (($usuario["region"] ?? "") == "O'Higgins") echo "selected"; ?>>O'Higgins</option>
                                            <option value="Maule" <?php if (($usuario["region"] ?? "") == "Maule") echo "selected"; ?>>Maule</option>
                                            <option value="Ñuble" <?php if (($usuario["region"] ?? "") == "Ñuble") echo "selected"; ?>>Ñuble</option>
                                            <option value="Biobío" <?php if (($usuario["region"] ?? "") == "Biobío") echo "selected"; ?>>Biobío</option>
                                            <option value="La Araucanía" <?php if (($usuario["region"] ?? "") == "La Araucanía") echo "selected"; ?>>La Araucanía</option>
                                            <option value="Los Ríos" <?php if (($usuario["region"] ?? "") == "Los Ríos") echo "selected"; ?>>Los Ríos</option>
                                            <option value="Los Lagos" <?php if (($usuario["region"] ?? "") == "Los Lagos") echo "selected"; ?>>Los Lagos</option>
                                            <option value="Aysén" <?php if (($usuario["region"] ?? "") == "Aysén") echo "selected"; ?>>Aysén</option>
                                            <option value="Magallanes" <?php if (($usuario["region"] ?? "") == "Magallanes") echo "selected"; ?>>Magallanes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Comuna</label>
                                        <input type="text" name="comuna_entrega" class="form-control" value="<?php echo htmlspecialchars($usuario["comuna"] ?? ""); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3">
                                Retiro en tienda no tiene costo adicional. El despacho tiene costo según región.
                            </div>
                        </div>
                    </div>
                    <!-- Resumen del pedido -->
                    <div class="col-lg-5 mb-4">
                        <div class="bg-white rounded shadow-sm p-4">
                            <h4>Resumen del pedido</h4>
                            <hr>
                            <?php foreach ($productos_carrito as $producto) { ?>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <strong><?php echo htmlspecialchars($producto["nombre"]); ?></strong>
                                        <p class="mb-0">Cantidad: <?php echo intval($producto["cantidad"]); ?></p>
                                    </div>
                                    <span>$<?php echo number_format($producto["subtotal"], 0, ",", "."); ?></span>
                                </div>
                            <?php } ?>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total productos:</span>
                                <strong id="total_productos_texto">$<?php echo number_format($total_productos, 0, ",", "."); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Costo despacho:</span>
                                <strong id="costo_despacho_texto">$0</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span>Total final:</span>
                                <strong id="total_pedido_texto">$<?php echo number_format($total_productos, 0, ",", "."); ?></strong>
                            </div>
                            <input type="hidden" name="total_productos" id="total_productos" value="<?php echo intval($total_productos); ?>">
                            <input type="hidden" name="costo_despacho" id="costo_despacho" value="0">
                            <input type="hidden" name="total_pedido" id="total_pedido" value="<?php echo intval($total_productos); ?>">
                            <!-- Continuar al pago -->
                            <button type="submit" name="btnCrearPedido" class="btn btn-finalizar-compra w-100 mb-3">
                                Continuar al pago
                            </button>
                            <!-- Volver al carrito -->
                            <a href="carrito.php" class="btn btn-volver-carrito w-100">
                                Volver al carrito
                            </a>
                        </div>
                    </div>
                </section>
            </form>
        </main>
        <!-- Footer -->
        <?php include "masterpage/footer.php"; ?>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const tipoEntrega = document.getElementById('tipo_entrega');
            const regionEntrega = document.getElementById('region_entrega');
            const datosDespacho = document.getElementById('datos_despacho');
            const totalProductos = parseInt(document.getElementById('total_productos').value);
            const costoDespachoInput = document.getElementById('costo_despacho');
            const totalPedidoInput = document.getElementById('total_pedido');
            const costoDespachoTexto = document.getElementById('costo_despacho_texto');
            const totalPedidoTexto = document.getElementById('total_pedido_texto');
            function formatearPrecio(valor) {
                return '$' + valor.toLocaleString('es-CL');
            }
            function actualizarTotales() {
                let costoDespacho = 0;
                if (tipoEntrega.value === 'despacho_domicilio') {
                    if (regionEntrega.value === 'Región Metropolitana') {
                        costoDespacho = 3990;
                    } else if (regionEntrega.value !== '') {
                        costoDespacho = 8990;
                    }
                }
                let totalPedido = totalProductos + costoDespacho;
                costoDespachoInput.value = costoDespacho;
                totalPedidoInput.value = totalPedido;
                costoDespachoTexto.textContent = formatearPrecio(costoDespacho);
                totalPedidoTexto.textContent = formatearPrecio(totalPedido);
            }
            function mostrarDatosDespacho() {
                if (tipoEntrega.value === 'despacho_domicilio') {
                    datosDespacho.style.display = 'block';
                } else {
                    datosDespacho.style.display = 'none';
                }
                actualizarTotales();
            }
            tipoEntrega.addEventListener('change', mostrarDatosDespacho);
            regionEntrega.addEventListener('change', actualizarTotales);
            mostrarDatosDespacho();
        </script>
    </body>
</html>
<?php
// Cerrar consultas y conexión
mysqli_stmt_close($stmt_usuario);
mysqli_stmt_close($stmt_carrito);
mysqli_close($conexion);
?>