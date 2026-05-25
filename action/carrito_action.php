<?php
// Iniciar sesión para validar usuario conectado
session_start();
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Verificar que exista sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo "<script>
            alert('Debes iniciar sesión para usar el carrito');
            window.location.href = '../login.php';
        </script>";
    exit();
}
// Verificar que solo clientes puedan usar el carrito
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    echo "<script>
            alert('Solo los clientes pueden usar el carrito');
            window.location.href = '../catalogo.php';
        </script>";
    exit();
}
// Guardar id del usuario conectado
$id_usuario = intval($_SESSION["id_usuario"]);
// Obtener o crear carrito del usuario
function obtenerCarritoUsuario($conexion, $id_usuario) {
    // Buscar carrito existente
    $sql_carrito = "SELECT id_carrito FROM carrito WHERE id_usuario = ? LIMIT 1";
    $stmt_carrito = mysqli_prepare($conexion, $sql_carrito);
    if (!$stmt_carrito) {
        return 0;
    }
    mysqli_stmt_bind_param($stmt_carrito, "i", $id_usuario);
    mysqli_stmt_execute($stmt_carrito);
    $resultado_carrito = mysqli_stmt_get_result($stmt_carrito);
    if (mysqli_num_rows($resultado_carrito) == 1) {
        $carrito = mysqli_fetch_assoc($resultado_carrito);
        return intval($carrito["id_carrito"]);
    }
    // Crear carrito si no existe
    $sql_crear = "INSERT INTO carrito (id_usuario) VALUES (?)";
    $stmt_crear = mysqli_prepare($conexion, $sql_crear);
    if (!$stmt_crear) {
        return 0;
    }
    mysqli_stmt_bind_param($stmt_crear, "i", $id_usuario);
    if (!mysqli_stmt_execute($stmt_crear)) {
        return 0;
    }
    return intval(mysqli_insert_id($conexion));
}
// Buscar producto activo
function obtenerProductoActivo($conexion, $id_producto) {
    // Buscar producto para validar precio, stock y estado
    $sql_producto = "SELECT id_producto, precio, stock, estado FROM producto WHERE id_producto = ?";
    $stmt_producto = mysqli_prepare($conexion, $sql_producto);
    if (!$stmt_producto) {
        return null;
    }
    mysqli_stmt_bind_param($stmt_producto, "i", $id_producto);
    mysqli_stmt_execute($stmt_producto);
    $resultado_producto = mysqli_stmt_get_result($stmt_producto);
    if (mysqli_num_rows($resultado_producto) != 1) {
        return null;
    }
    $producto = mysqli_fetch_assoc($resultado_producto);
    if ($producto["estado"] != "activo") {
        return null;
    }
    return $producto;
}
// Volver a la página anterior de forma segura
function volverPaginaAnterior($ruta_respaldo) {
    // Redirigir a la página anterior si existe
    if (isset($_SERVER["HTTP_REFERER"]) && !empty($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit();
    }
    // Redirigir a ruta de respaldo si no existe página anterior
    header("Location: " . $ruta_respaldo);
    exit();
}
/* AGREGAR PRODUCTO AL CARRITO */
if (isset($_POST["btnAgregarCarrito"])) {
    // Capturar datos enviados desde catálogo
    $id_producto = intval($_POST["id_producto"]);
    $cantidad = isset($_POST["cantidad"]) ? intval($_POST["cantidad"]) : 1;
    // Validar datos recibidos
    if ($id_producto <= 0 || $cantidad <= 0) {
        volverPaginaAnterior("../catalogo.php");
    }
    // Obtener producto activo
    $producto = obtenerProductoActivo($conexion, $id_producto);
    if ($producto == null) {
        volverPaginaAnterior("../catalogo.php");
    }
    // Validar stock disponible
    if (intval($producto["stock"]) <= 0 || $cantidad > intval($producto["stock"])) {
        volverPaginaAnterior("../catalogo.php");
    }
    // Guardar precio unitario para checkout y futura integración con Mercado Pago
    $precio_unitario = intval($producto["precio"]);
    // Obtener carrito del usuario
    $id_carrito = obtenerCarritoUsuario($conexion, $id_usuario);
    if ($id_carrito <= 0) {
        volverPaginaAnterior("../catalogo.php");
    }
    // Buscar si el producto ya existe en el carrito
    $sql_detalle = "SELECT id_carrito_detalle, cantidad FROM carrito_detalle WHERE id_carrito = ? AND id_producto = ? LIMIT 1";
    $stmt_detalle = mysqli_prepare($conexion, $sql_detalle);
    if (!$stmt_detalle) {
        volverPaginaAnterior("../catalogo.php");
    }
    mysqli_stmt_bind_param($stmt_detalle, "ii", $id_carrito, $id_producto);
    mysqli_stmt_execute($stmt_detalle);
    $resultado_detalle = mysqli_stmt_get_result($stmt_detalle);
    // Aumentar cantidad si el producto ya existe
    if (mysqli_num_rows($resultado_detalle) == 1) {
        $detalle = mysqli_fetch_assoc($resultado_detalle);
        $id_carrito_detalle = intval($detalle["id_carrito_detalle"]);
        $cantidad_actual = intval($detalle["cantidad"]);
        $nueva_cantidad = $cantidad_actual + $cantidad;
        // Validar que la nueva cantidad no supere el stock
        if ($nueva_cantidad > intval($producto["stock"])) {
            volverPaginaAnterior("../catalogo.php");
        }
        $sql_actualizar = "UPDATE carrito_detalle SET cantidad = ?, precio_unitario = ? WHERE id_carrito_detalle = ?";
        $stmt_actualizar = mysqli_prepare($conexion, $sql_actualizar);
        if (!$stmt_actualizar) {
            volverPaginaAnterior("../catalogo.php");
        }
        mysqli_stmt_bind_param($stmt_actualizar, "iii", $nueva_cantidad, $precio_unitario, $id_carrito_detalle);
        mysqli_stmt_execute($stmt_actualizar);
    } else {
        // Insertar producto si todavía no existe en el carrito
        $sql_insertar = "INSERT INTO carrito_detalle (id_carrito, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
        $stmt_insertar = mysqli_prepare($conexion, $sql_insertar);
        if (!$stmt_insertar) {
            volverPaginaAnterior("../catalogo.php");
        }
        mysqli_stmt_bind_param($stmt_insertar, "iiii", $id_carrito, $id_producto, $cantidad, $precio_unitario);
        mysqli_stmt_execute($stmt_insertar);
    }
    // Volver a catálogo para actualizar contador visual
    volverPaginaAnterior("../catalogo.php");
}
/* RESTAR PRODUCTO DESDE CATÁLOGO */
if (isset($_POST["btnRestarProductoCatalogo"])) {
    // Capturar producto enviado desde catálogo
    $id_producto = intval($_POST["id_producto"]);
    if ($id_producto <= 0) {
        volverPaginaAnterior("../catalogo.php");
    }
    // Buscar carrito del usuario
    $id_carrito = obtenerCarritoUsuario($conexion, $id_usuario);
    if ($id_carrito <= 0) {
        volverPaginaAnterior("../catalogo.php");
    }
    // Buscar producto en el carrito
    $sql_detalle = "SELECT id_carrito_detalle, cantidad FROM carrito_detalle WHERE id_carrito = ? AND id_producto = ? LIMIT 1";
    $stmt_detalle = mysqli_prepare($conexion, $sql_detalle);
    if (!$stmt_detalle) {
        volverPaginaAnterior("../catalogo.php");
    }
    mysqli_stmt_bind_param($stmt_detalle, "ii", $id_carrito, $id_producto);
    mysqli_stmt_execute($stmt_detalle);
    $resultado_detalle = mysqli_stmt_get_result($stmt_detalle);
    if (mysqli_num_rows($resultado_detalle) != 1) {
        volverPaginaAnterior("../catalogo.php");
    }
    $detalle = mysqli_fetch_assoc($resultado_detalle);
    $id_carrito_detalle = intval($detalle["id_carrito_detalle"]);
    $cantidad_actual = intval($detalle["cantidad"]);
    // Disminuir cantidad o eliminar si llega a cero
    if ($cantidad_actual > 1) {
        $nueva_cantidad = $cantidad_actual - 1;
        $sql_actualizar = "UPDATE carrito_detalle SET cantidad = ? WHERE id_carrito_detalle = ?";
        $stmt_actualizar = mysqli_prepare($conexion, $sql_actualizar);
        if ($stmt_actualizar) {
            mysqli_stmt_bind_param($stmt_actualizar, "ii", $nueva_cantidad, $id_carrito_detalle);
            mysqli_stmt_execute($stmt_actualizar);
        }
    } else {
        $sql_eliminar = "DELETE FROM carrito_detalle WHERE id_carrito_detalle = ?";
        $stmt_eliminar = mysqli_prepare($conexion, $sql_eliminar);
        if ($stmt_eliminar) {
            mysqli_stmt_bind_param($stmt_eliminar, "i", $id_carrito_detalle);
            mysqli_stmt_execute($stmt_eliminar);
        }
    }
    // Volver a catálogo para actualizar contador visual
    volverPaginaAnterior("../catalogo.php");
}
/* AUMENTAR CANTIDAD DESDE CARRITO */
if (isset($_POST["btnAumentarCantidad"])) {
    // Capturar detalle del carrito
    $id_carrito_detalle = intval($_POST["id_carrito_detalle"]);
    if ($id_carrito_detalle <= 0) {
        header("Location: ../carrito.php");
        exit();
    }
    // Buscar detalle y verificar que pertenezca al usuario conectado
    $sql = "SELECT cd.id_carrito_detalle, cd.cantidad, p.stock, p.precio
            FROM carrito_detalle cd
            INNER JOIN carrito c ON cd.id_carrito = c.id_carrito
            INNER JOIN producto p ON cd.id_producto = p.id_producto
            WHERE cd.id_carrito_detalle = ? AND c.id_usuario = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        header("Location: ../carrito.php");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ii", $id_carrito_detalle, $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($resultado) != 1) {
        header("Location: ../carrito.php");
        exit();
    }
    $detalle = mysqli_fetch_assoc($resultado);
    $cantidad_actual = intval($detalle["cantidad"]);
    $stock = intval($detalle["stock"]);
    $nueva_cantidad = $cantidad_actual + 1;
    if ($nueva_cantidad > $stock) {
        header("Location: ../carrito.php");
        exit();
    }
    // Actualizar cantidad y precio unitario
    $precio_unitario = intval($detalle["precio"]);
    $sql_actualizar = "UPDATE carrito_detalle SET cantidad = ?, precio_unitario = ? WHERE id_carrito_detalle = ?";
    $stmt_actualizar = mysqli_prepare($conexion, $sql_actualizar);
    if ($stmt_actualizar) {
        mysqli_stmt_bind_param($stmt_actualizar, "iii", $nueva_cantidad, $precio_unitario, $id_carrito_detalle);
        mysqli_stmt_execute($stmt_actualizar);
    }
    header("Location: ../carrito.php");
    exit();
}
/* DISMINUIR CANTIDAD DESDE CARRITO */
if (isset($_POST["btnDisminuirCantidad"])) {
    // Capturar detalle del carrito
    $id_carrito_detalle = intval($_POST["id_carrito_detalle"]);
    if ($id_carrito_detalle <= 0) {
        header("Location: ../carrito.php");
        exit();
    }
    // Buscar detalle y verificar que pertenezca al usuario conectado
    $sql = "SELECT cd.id_carrito_detalle, cd.cantidad
            FROM carrito_detalle cd
            INNER JOIN carrito c ON cd.id_carrito = c.id_carrito
            WHERE cd.id_carrito_detalle = ? AND c.id_usuario = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        header("Location: ../carrito.php");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ii", $id_carrito_detalle, $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($resultado) != 1) {
        header("Location: ../carrito.php");
        exit();
    }
    $detalle = mysqli_fetch_assoc($resultado);
    $cantidad_actual = intval($detalle["cantidad"]);
    // Disminuir cantidad o eliminar producto si queda en cero
    if ($cantidad_actual > 1) {
        $nueva_cantidad = $cantidad_actual - 1;
        $sql_actualizar = "UPDATE carrito_detalle SET cantidad = ? WHERE id_carrito_detalle = ?";
        $stmt_actualizar = mysqli_prepare($conexion, $sql_actualizar);
        if ($stmt_actualizar) {
            mysqli_stmt_bind_param($stmt_actualizar, "ii", $nueva_cantidad, $id_carrito_detalle);
            mysqli_stmt_execute($stmt_actualizar);
        }
    } else {
        $sql_eliminar = "DELETE FROM carrito_detalle WHERE id_carrito_detalle = ?";
        $stmt_eliminar = mysqli_prepare($conexion, $sql_eliminar);
        if ($stmt_eliminar) {
            mysqli_stmt_bind_param($stmt_eliminar, "i", $id_carrito_detalle);
            mysqli_stmt_execute($stmt_eliminar);
        }
    }
    header("Location: ../carrito.php");
    exit();
}
/* ELIMINAR PRODUCTO DEL CARRITO */
if (isset($_POST["btnEliminarProducto"])) {
    // Capturar detalle del producto
    $id_carrito_detalle = intval($_POST["id_carrito_detalle"]);
    if ($id_carrito_detalle <= 0) {
        header("Location: ../carrito.php");
        exit();
    }
    // Eliminar solo si pertenece al usuario conectado
    $sql = "DELETE cd FROM carrito_detalle cd
            INNER JOIN carrito c ON cd.id_carrito = c.id_carrito
            WHERE cd.id_carrito_detalle = ? AND c.id_usuario = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $id_carrito_detalle, $id_usuario);
        mysqli_stmt_execute($stmt);
    }
    header("Location: ../carrito.php");
    exit();
}
/* VACIAR CARRITO */
if (isset($_POST["btnVaciarCarrito"])) {
    // Eliminar todos los productos del carrito del usuario conectado
    $sql = "DELETE cd FROM carrito_detalle cd
            INNER JOIN carrito c ON cd.id_carrito = c.id_carrito
            WHERE c.id_usuario = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
    }
    header("Location: ../carrito.php");
    exit();
}
// Redirigir al catálogo si no se recibe una acción válida
header("Location: ../catalogo.php");
exit();
?>