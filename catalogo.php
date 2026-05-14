<?php
require_once 'config/conexion.php';

/* Recibir filtros desde la URL */
$categoria = $_GET['categoria'] ?? '';
$orden = $_GET['orden'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

/* Consulta base */
$sql = "SELECT 
            id_producto,
            nombre_producto,
            descripcion_producto,
            precio_producto,
            imagen_producto,
            categoria_producto,
            fecha_creacion
        FROM productos
        WHERE 1=1";

$parametros = [];
$tipos = "";

/* Filtro por categoría */
if (!empty($categoria)) {
    $sql .= " AND categoria_producto = ?";
    $parametros[] = $categoria;
    $tipos .= "s";
}

/* Filtro por búsqueda */
if (!empty($busqueda)) {
    $sql .= " AND (nombre_producto LIKE ? OR descripcion_producto LIKE ?)";
    $busquedaLike = "%" . $busqueda . "%";
    $parametros[] = $busquedaLike;
    $parametros[] = $busquedaLike;
    $tipos .= "ss";
}

/* Ordenamiento */
if ($orden === "precio_asc") {
    $sql .= " ORDER BY precio_producto ASC";
} elseif ($orden === "precio_desc") {
    $sql .= " ORDER BY precio_producto DESC";
} elseif ($orden === "recientes") {
    $sql .= " ORDER BY fecha_creacion DESC";
} else {
    $sql .= " ORDER BY id_producto DESC";
}

/* Preparar consulta */
$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . mysqli_error($conexion));
}

/* Asociar parámetros si existen */
if (!empty($parametros)) {
    mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
}

/* Ejecutar */
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - PequeMundo</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CSS propios -->
    <link rel="stylesheet" href="css/estilos.css?v=31">
    <link rel="stylesheet" href="css/navbar.css?v=31">
    <link rel="stylesheet" href="css/catalogo.css?v=31">
</head>

<body>

<?php include 'masterpage/menu.php'; ?>

<main class="container my-5">

    <!-- Encabezado del catálogo -->
    <section class="row mb-4">
        <div class="col-12">
            <h1 class="titulo-catalogo">Catálogo de productos</h1>
            <p class="subtitulo-catalogo">
                Explora nuestra oferta de muebles para bebés y niños.
            </p>
            <hr>
        </div>
    </section>

    <!-- Filtros y búsqueda -->
    <form method="GET" id="form-filtros" class="filtros-productos mb-5">
        <section class="row g-3 align-items-center">

            <!-- Categorías -->
            <div class="col-md-3">
                <select name="categoria" class="form-select filtro-catalogo filtro-auto">
                    <option value="" <?php echo ($categoria === '') ? 'selected' : ''; ?>>
                        Todas las categorías
                    </option>

                    <option value="cunas" <?php echo ($categoria === 'cunas') ? 'selected' : ''; ?>>
                        Cunas
                    </option>

                    <option value="comodas" <?php echo ($categoria === 'comodas') ? 'selected' : ''; ?>>
                        Cómodas
                    </option>

                    <option value="mudadores" <?php echo ($categoria === 'mudadores') ? 'selected' : ''; ?>>
                        Mudadores
                    </option>

                    <option value="decoracion" <?php echo ($categoria === 'decoracion') ? 'selected' : ''; ?>>
                        Decoración
                    </option>
                </select>
            </div>

            <!-- Orden -->
            <div class="col-md-3">
                <select name="orden" class="form-select filtro-catalogo filtro-auto">
                    <option value="" <?php echo ($orden === '') ? 'selected' : ''; ?>>
                        Ordenar por
                    </option>

                    <option value="precio_asc" <?php echo ($orden === 'precio_asc') ? 'selected' : ''; ?>>
                        Precio: menor a mayor
                    </option>

                    <option value="precio_desc" <?php echo ($orden === 'precio_desc') ? 'selected' : ''; ?>>
                        Precio: mayor a menor
                    </option>

                    <option value="recientes" <?php echo ($orden === 'recientes') ? 'selected' : ''; ?>>
                        Más recientes
                    </option>
                </select>
            </div>

            <!-- Búsqueda escrita -->
            <div class="col-md-6">
                <div class="input-group">
                    <input 
                        type="text"
                        name="busqueda"
                        class="form-control buscador-catalogo"
                        placeholder="Buscar productos..."
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                    >

                    <button class="btn btn-custom" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i> Buscar
                    </button>
                </div>
            </div>

        </section>
    </form>

    <!-- Productos -->
    <section class="row">

        <?php if ($resultado && mysqli_num_rows($resultado) > 0) { ?>

            <?php while ($producto = mysqli_fetch_assoc($resultado)) { ?>

                <div class="col-md-4 col-lg-3 mb-4">
                    <article class="producto-card h-100">

                        <!-- Imagen -->
                        <div class="producto-img-contenedor">
                            <?php if (!empty($producto['imagen_producto'])) { ?>
                                <img 
                                    src="<?php echo htmlspecialchars($producto['imagen_producto']); ?>" 
                                    alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                                    class="producto-img"
                                >
                            <?php } else { ?>
                                <div class="producto-sin-img">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Información -->
                        <div class="producto-info">
                            <h5>
                                <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                            </h5>

                            <p class="producto-descripcion">
                                <?php echo htmlspecialchars($producto['descripcion_producto']); ?>
                            </p>

                            <p class="producto-categoria">
                                <?php echo htmlspecialchars($producto['categoria_producto']); ?>
                            </p>

                            <p class="producto-precio">
                                $<?php echo number_format($producto['precio_producto'], 0, ',', '.'); ?>
                            </p>

                            <button class="btn btn-custom w-100" type="button">
                                Agregar
                            </button>
                        </div>

                    </article>
                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="col-12">
                <div class="mensaje-sin-productos text-center">
                    <p class="mb-0">No se encontraron productos con esos filtros.</p>
                </div>
            </div>

        <?php } ?>

    </section>

</main>

<?php include 'masterpage/footer.php'; ?>

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
mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>