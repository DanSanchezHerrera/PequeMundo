<?php
    // Iniciar sesión
    session_start();
    // Incluir conexión a la base de datos
    require_once "config/conexion.php";
    // Capturar búsqueda enviada por GET
    $busqueda = isset($_GET["busqueda"]) ? trim($_GET["busqueda"]) : "";
    // Capturar orden seleccionado por GET
    $orden = isset($_GET["orden"]) ? trim($_GET["orden"]) : "";
    // Verificar si el usuario conectado es cliente
    $es_cliente = isset($_SESSION["id_usuario"]) && isset($_SESSION["tipo_usuario"]) && $_SESSION["tipo_usuario"] == "cliente";
    // Guardar id del usuario conectado si corresponde
    $id_usuario = $es_cliente ? intval($_SESSION["id_usuario"]) : 0;
    // Crear consulta base según exista cliente conectado
    if ($es_cliente) {
        $sql = "SELECT 
                    p.id_producto,
                    p.nombre,
                    p.descripcion,
                    p.precio,
                    p.stock,
                    p.imagen,
                    p.estado,
                    COALESCE(cd.cantidad, 0) AS cantidad_carrito
                FROM producto p
                LEFT JOIN carrito c ON c.id_usuario = ?
                LEFT JOIN carrito_detalle cd ON cd.id_carrito = c.id_carrito AND cd.id_producto = p.id_producto
                WHERE p.estado = 'activo'";
        $parametros = array($id_usuario);
        $tipos = "i";
    } else {
        $sql = "SELECT 
                    p.id_producto,
                    p.nombre,
                    p.descripcion,
                    p.precio,
                    p.stock,
                    p.imagen,
                    p.estado,
                    0 AS cantidad_carrito
                FROM producto p
                WHERE p.estado = 'activo'";
        $parametros = array();
        $tipos = "";
    }
    // Agregar filtro de búsqueda si existe texto ingresado
    if (!empty($busqueda)) {
        $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?)";
        $busqueda_like = "%" . $busqueda . "%";
        $parametros[] = $busqueda_like;
        $parametros[] = $busqueda_like;
        $tipos .= "ss";
    }
    // Agregar orden seleccionado
    if ($orden == "precio_asc") {
        $sql .= " ORDER BY p.precio ASC";
    } elseif ($orden == "precio_desc") {
        $sql .= " ORDER BY p.precio DESC";
    } elseif ($orden == "nombre_asc") {
        $sql .= " ORDER BY p.nombre ASC";
    } else {
        $sql .= " ORDER BY p.id_producto DESC";
    }
    // Preparar consulta segura
    $stmt = mysqli_prepare($conexion, $sql);
    // Validar preparación de consulta
    if (!$stmt) {
        die("Error al preparar la consulta: " . mysqli_error($conexion));
    }
    // Asociar parámetros si existen
    if (!empty($parametros)) {
        mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
    }
    // Ejecutar consulta
    mysqli_stmt_execute($stmt);
    // Obtener resultados
    $resultado = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Catálogo - PequeMundo</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Estilos propios -->
        <link rel="stylesheet" href="css/estilos.css">
        <link rel="stylesheet" href="css/navbar.css">
        <link rel="stylesheet" href="css/footer.css">
        <link rel="stylesheet" href="css/catalogo.css">
    </head>
    <body>
        <!-- Menú -->
        <?php include "masterpage/menu.php"; ?>
        <!-- Contenido principal -->
        <main class="container my-5">
            <!-- Encabezado -->
            <section class="mb-4">
                <h1 class="titulo-catalogo">Catálogo de productos</h1>
                <p class="subtitulo-catalogo">Explora nuestra oferta de muebles infantiles disponibles.</p>
                <hr>
            </section>
            <!-- Formulario de búsqueda y orden -->
            <section class="mb-5">
                <form method="GET" action="catalogo.php" id="form-filtros">
                    <div class="row g-3 align-items-center">
                        <!-- Buscar productos -->
                        <div class="col-md-6">
                            <input type="text" name="busqueda" class="form-control" placeholder="Buscar producto por nombre o descripción" value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>
                        <!-- Ordenar productos -->
                        <div class="col-md-4">
                            <select name="orden" class="form-control filtro-auto">
                                <option value="">Ordenar por</option>
                                <option value="precio_asc" <?php if ($orden == "precio_asc") echo "selected"; ?>>Precio: menor a mayor</option>
                                <option value="precio_desc" <?php if ($orden == "precio_desc") echo "selected"; ?>>Precio: mayor a menor</option>
                                <option value="nombre_asc" <?php if ($orden == "nombre_asc") echo "selected"; ?>>Nombre A-Z</option>
                            </select>
                        </div>
                        <!-- Aplicar búsqueda -->
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-buscar-catalogo w-100">Buscar</button>
                        </div>
                    </div>
                </form>
            </section>
            <!-- Productos -->
            <section class="row">
                <?php if ($resultado && mysqli_num_rows($resultado) > 0) { ?>
                    <?php while ($producto = mysqli_fetch_assoc($resultado)) { ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <article class="producto-card h-100">
                                <!-- Imagen del producto -->
                                <div class="producto-img-contenedor">
                                    <?php if (!empty($producto["imagen"])) { ?>
                                        <img src="<?php echo htmlspecialchars($producto["imagen"]); ?>" alt="<?php echo htmlspecialchars($producto["nombre"]); ?>" class="producto-img">
                                    <?php } else { ?>
                                        <div class="producto-sin-img">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    <?php } ?>
                                </div>
                                <!-- Información del producto -->
                                <div class="producto-info">
                                    <h5><?php echo htmlspecialchars($producto["nombre"]); ?></h5>
                                    <p class="producto-descripcion"><?php echo htmlspecialchars($producto["descripcion"]); ?></p>
                                    <p class="producto-stock">Stock disponible: <?php echo intval($producto["stock"]); ?></p>
                                    <p class="producto-precio">$<?php echo number_format($producto["precio"], 0, ",", "."); ?></p>
                                    <?php if (intval($producto["stock"]) <= 0) { ?>
                                        <button type="button" class="btn btn-secondary w-100" disabled>Sin stock</button>
                                    <?php } elseif (!isset($_SESSION["id_usuario"])) { ?>
                                        <a href="login.php" class="btn btn-agregar-catalogo w-100">Iniciar sesión para comprar</a>
                                    <?php } elseif ($_SESSION["tipo_usuario"] == "cliente") { ?>
                                        <!-- Acciones reales del carrito desde catálogo -->
                                        <div class="catalogo-acciones-carrito">
                                            <!-- Restar una unidad del producto -->
                                            <form action="action/carrito_action.php" method="POST">
                                                <input type="hidden" name="id_producto" value="<?php echo intval($producto["id_producto"]); ?>">
                                                <button type="submit" name="btnRestarProductoCatalogo" class="btn-cantidad" <?php if (intval($producto["cantidad_carrito"]) <= 0) echo "disabled"; ?>>-</button>
                                            </form>
                                            <!-- Mostrar cantidad actual en carrito -->
                                            <span class="cantidad-catalogo"><?php echo intval($producto["cantidad_carrito"]); ?></span>
                                            <!-- Agregar una unidad del producto -->
                                            <form action="action/carrito_action.php" method="POST">
                                                <input type="hidden" name="id_producto" value="<?php echo intval($producto["id_producto"]); ?>">
                                                <input type="hidden" name="cantidad" value="1">
                                                <button type="submit" name="btnAgregarCarrito" class="btn-cantidad" <?php if (intval($producto["cantidad_carrito"]) >= intval($producto["stock"])) echo "disabled"; ?>>+</button>
                                            </form>
                                        </div>
                                        <a href="carrito.php" class="btn btn-agregar-catalogo w-100">Ver carrito</a>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-secondary w-100" disabled>Compra solo para clientes</button>
                                    <?php } ?>
                                </div>
                            </article>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="col-12">
                        <div class="mensaje-sin-productos text-center">
                            <p class="mb-0">No se encontraron productos disponibles.</p>
                        </div>
                    </div>
                <?php } ?>
            </section>
        </main>
        <!-- Footer -->
        <?php include "masterpage/footer.php"; ?>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const filtrosAuto = document.querySelectorAll('.filtro-auto');
            filtrosAuto.forEach(function(filtro) {
                filtro.addEventListener('change', function() {
                    document.getElementById('form-filtros').submit();
                });
            });
        </script>
    </body>
</html>
<?php
    // Cerrar consulta y conexión
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
?>