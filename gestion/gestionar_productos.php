<?php
    // Iniciar sesión para validar el tipo de usuario
    session_start();
    // Conexión BD
    require_once "../config/conexion.php";
    // Validar solo admin y vendedor pueden ingresar
    if (!isset($_SESSION["tipo_usuario"]) || ($_SESSION["tipo_usuario"] != "admin" && $_SESSION["tipo_usuario"] != "vendedor")) {
        echo "<script>
                alert('No tienes permiso para acceder a esta página');
                window.location.href = '../index.php';
            </script>";
        exit();
    }
    // Variable editar producto
    $producto_editar = null;
    // Cargar datos del formulario
    if (isset($_GET["editar"])) {
        $id_producto = $_GET["editar"];
        $sql_editar = "SELECT * FROM producto WHERE id_producto = ?";
        $stmt_editar = mysqli_prepare($conexion, $sql_editar);
        mysqli_stmt_bind_param($stmt_editar, "i", $id_producto);
        mysqli_stmt_execute($stmt_editar);
        $resultado_editar = mysqli_stmt_get_result($stmt_editar);
        // Guardar datos si se encuentra el producto
        if (mysqli_num_rows($resultado_editar) == 1) {
            $producto_editar = mysqli_fetch_assoc($resultado_editar);
        }
    }
    // Listar productos desde el más reciente
    $sql_productos = "SELECT * FROM producto ORDER BY id_producto DESC";
    $resultado_productos = mysqli_query($conexion, $sql_productos);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestionar productos - PequeMundo</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Estilos propios -->
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
    </head>
    <body>
        <!-- Menu -->
        <?php include '../masterpage/menu.php'; ?>
        <!-- MAIN -->
        <main class="container my-5">
            <h2 class="mb-4">Gestionar productos</h2>
            <div class="row">
                <!-- Columna del formulario para agregar o editar productos -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <!-- Título dinámico -->
                            <?php if ($producto_editar) { ?>
                                <h4 class="mb-3">Editar producto</h4>
                            <?php } else { ?>
                                <h4 class="mb-3">Agregar producto</h4>
                            <?php } ?>
                            <!-- Formulario productos -->
                            <form action="../action/producto_action.php" method="POST" enctype="multipart/form-data">
                                <!-- Campos ocultos para editar -->
                                <?php if ($producto_editar) { ?>
                                    <input type="hidden" name="id_producto" value="<?php echo $producto_editar['id_producto']; ?>">
                                    <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($producto_editar['imagen']); ?>">
                                <?php } ?>
                                <!-- Nombre del producto -->
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input 
                                        type="text" 
                                        name="nombre" 
                                        class="form-control" 
                                        value="<?php echo $producto_editar ? $producto_editar['nombre'] : ''; ?>"
                                        required
                                    >
                                </div>
                                <!-- Descripción del producto -->
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea 
                                        name="descripcion" 
                                        class="form-control" 
                                        rows="3"
                                        required
                                    ><?php echo $producto_editar ? $producto_editar['descripcion'] : ''; ?></textarea>
                                </div>
                                <!-- Precio del producto -->
                                <div class="mb-3">
                                    <label class="form-label">Precio</label>
                                    <input 
                                        type="number" 
                                        name="precio" 
                                        class="form-control" 
                                        min="0"
                                        value="<?php echo $producto_editar ? $producto_editar['precio'] : ''; ?>"
                                        required
                                    >
                                </div>
                                <!-- Stock disponible -->
                                <div class="mb-3">
                                    <label class="form-label">Stock</label>
                                    <input 
                                        type="number" 
                                        name="stock" 
                                        class="form-control" 
                                        min="0"
                                        value="<?php echo $producto_editar ? $producto_editar['stock'] : ''; ?>"
                                        required
                                    >
                                </div>
                                <!-- Imagen del producto -->
                                <div class="mb-3">
                                    <label class="form-label">Imagen</label>
                                    <input 
                                        type="file" 
                                        name="imagen" 
                                        class="form-control" 
                                        accept="image/*"
                                        <?php echo $producto_editar ? '' : 'required'; ?>
                                    >
                                    <!-- Aviso mostrado solo cuando se edita un producto -->
                                    <?php if ($producto_editar) { ?>
                                        <small class="text-muted">
                                            Si no seleccionas una nueva imagen, se mantiene la actual.
                                        </small>
                                    <?php } ?>
                                </div>
                                <!-- Estado del producto -->
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-control" required>
                                        <option value="activo" <?php echo ($producto_editar && $producto_editar['estado'] == 'activo') ? 'selected' : ''; ?>>
                                            Activo
                                        </option>
                                        <option value="inactivo" <?php echo ($producto_editar && $producto_editar['estado'] == 'inactivo') ? 'selected' : ''; ?>>
                                            Inactivo
                                        </option>
                                    </select>
                                </div>
                                <!-- Botones del formulario -->
                                <div class="text-center">
                                    <?php if ($producto_editar) { ?>
                                        <button type="submit" name="btnActualizarProducto" class="btn btn-custom text-white">
                                            Actualizar producto
                                        </button>
                                        <a href="gestionar_productos.php" class="btn btn-secondary">
                                            Cancelar
                                        </a>
                                    <?php } else { ?>
                                        <button type="submit" name="btnRegistrarProducto" class="btn btn-custom text-white">
                                            Registrar producto
                                        </button>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Columna de listados de productos -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="mb-3">Listado de productos</h4>
                            <!-- Responsive :) -->
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>Imagen</th>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Stock</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Si existen productos, se muestran en la tabla -->
                                        <?php if (mysqli_num_rows($resultado_productos) > 0) { ?>
                                            <?php while ($producto = mysqli_fetch_assoc($resultado_productos)) { ?>
                                                <tr>
                                                    <!-- Imagen del producto -->
                                                    <td>
                                                        <img 
                                                            src="../<?php echo $producto['imagen']; ?>" 
                                                            alt="<?php echo $producto['nombre']; ?>" 
                                                            width="70"
                                                        >
                                                    </td>
                                                    <!-- Nombre y descripción -->
                                                    <td>
                                                        <strong><?php echo $producto['nombre']; ?></strong>
                                                        <br>
                                                        <small><?php echo $producto['descripcion']; ?></small>
                                                    </td>
                                                    <!-- Precio formateado -->
                                                    <td>
                                                        $<?php echo number_format($producto['precio'], 0, ',', '.'); ?>
                                                    </td>
                                                    <!-- Stock -->
                                                    <td>
                                                        <?php echo $producto['stock']; ?>
                                                    </td>
                                                    <!-- Estado visual del producto -->
                                                    <td>
                                                        <?php if ($producto['estado'] == 'activo') { ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php } else { ?>
                                                            <span class="badge bg-secondary">Inactivo</span>
                                                        <?php } ?>
                                                    </td>
                                                    <!-- Botón editar -->
                                                    <td>
                                                        <a 
                                                            href="gestionar_productos.php?editar=<?php echo $producto['id_producto']; ?>" 
                                                            class="btn btn-sm btn-warning mb-1"
                                                        >
                                                            Editar
                                                        </a>
                                                        <!-- Formulario para activar o desactivar producto -->
                                                        <form action="../action/producto_action.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                                            <input type="hidden" name="estado_actual" value="<?php echo $producto['estado']; ?>">
                                                            <button type="submit" name="btnCambiarEstadoProducto" class="btn btn-sm btn-secondary mb-1">
                                                                <?php echo ($producto['estado'] == 'activo') ? 'Desactivar' : 'Activar'; ?>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <!-- Mensaje "no hay productos" -->
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    No hay productos registrados.
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- Footer -->
        <?php include '../masterpage/footer.php'; ?>
    </body>
</html>