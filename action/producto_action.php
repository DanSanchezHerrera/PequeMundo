<?php
session_start();

require_once "../config/conexion.php";

/* Validar acceso - admin/vendedor */
if (!isset($_SESSION["tipo_usuario"]) || ($_SESSION["tipo_usuario"] != "admin" && $_SESSION["tipo_usuario"] != "vendedor")) {
    echo "<script>
            alert('No tienes permiso para realizar esta acción');
            window.location.href = '../index.php';
        </script>";
    exit();
}

/* Función para subir imagen */
function subirImagenProducto($input_name) {
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]["error"] != 0) {
        return null;
    }

    $nombre_imagen = $_FILES[$input_name]["name"];
    $tmp_imagen = $_FILES[$input_name]["tmp_name"];

    $extension = strtolower(pathinfo($nombre_imagen, PATHINFO_EXTENSION));
    $extensiones_permitidas = array("jpg", "jpeg", "png", "webp");

    if (!in_array($extension, $extensiones_permitidas)) {
        echo "<script>
                alert('Solo se permiten imágenes JPG, JPEG, PNG o WEBP');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    $carpeta_destino = "../img/productos/";

    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0777, true);
    }

    $nombre_unico = time() . "_" . rand(1000, 9999) . "." . $extension;

    $ruta_guardar = $carpeta_destino . $nombre_unico;
    $ruta_bd = "img/productos/" . $nombre_unico;

    if (move_uploaded_file($tmp_imagen, $ruta_guardar)) {
        return $ruta_bd;
    } else {
        echo "<script>
                alert('No se pudo subir la imagen del producto');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }
}

/*CRUD*/

/* CREAR */
if (isset($_POST["btnRegistrarProducto"])) {

    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = intval($_POST["precio"]);
    $stock = intval($_POST["stock"]);
    $estado = trim($_POST["estado"]);

    if (empty($nombre) || empty($descripcion) || $precio < 0 || $stock < 0 || empty($estado)) {
        echo "<script>
                alert('Debe completar correctamente todos los campos');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    if ($estado != "activo" && $estado != "inactivo") {
        echo "<script>
                alert('Estado de producto no válido');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    $ruta_imagen = subirImagenProducto("imagen");

    if ($ruta_imagen == null) {
        echo "<script>
                alert('Debe seleccionar una imagen');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    $sql = "INSERT INTO producto (
                nombre,
                descripcion,
                precio,
                stock,
                imagen,
                estado
            ) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexion, $sql);

    if (!$stmt) {
        echo "<script>
                alert('Error al preparar el registro del producto');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssiiss",
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $ruta_imagen,
        $estado
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Producto registrado correctamente');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    } else {
        echo "<script>
                alert('Error al registrar el producto');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }
}


/* ACTUALIZAR */
if (isset($_POST["btnActualizarProducto"])) {

    $id_producto = intval($_POST["id_producto"]);
    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = intval($_POST["precio"]);
    $stock = intval($_POST["stock"]);
    $estado = trim($_POST["estado"]);
    $imagen_actual = trim($_POST["imagen_actual"]);

    if ($id_producto <= 0 || empty($nombre) || empty($descripcion) || $precio < 0 || $stock < 0 || empty($estado)) {
        echo "<script>
                alert('Debe completar correctamente todos los campos');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    if ($estado != "activo" && $estado != "inactivo") {
        echo "<script>
                alert('Estado de producto no válido');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    /* Mantener imagen actual si no se sube una nueva */
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        $ruta_imagen = subirImagenProducto("imagen");
    } else {
        $ruta_imagen = $imagen_actual;
    }

    $sql = "UPDATE producto
            SET nombre = ?,
                descripcion = ?,
                precio = ?,
                stock = ?,
                imagen = ?,
                estado = ?
            WHERE id_producto = ?";

    $stmt = mysqli_prepare($conexion, $sql);

    if (!$stmt) {
        echo "<script>
                alert('Error al preparar la actualización del producto');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssiissi",
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $ruta_imagen,
        $estado,
        $id_producto
    );

    /* Verificar actualización */
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Producto actualizado correctamente');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    } else {
        echo "<script>
                alert('Error al actualizar el producto');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }
}


/* ACTIVAR / DESACTIVAR PRODUCTO */
if (isset($_POST["btnCambiarEstadoProducto"])) {

    $id_producto = intval($_POST["id_producto"]);
    $estado_actual = trim($_POST["estado_actual"]);

    if ($id_producto <= 0) {
        echo "<script>
                alert('Producto no válido');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    if ($estado_actual == "activo") {
        $nuevo_estado = "inactivo";
    } else {
        $nuevo_estado = "activo";
    }

    $sql = "UPDATE producto
            SET estado = ?
            WHERE id_producto = ?";

    $stmt = mysqli_prepare($conexion, $sql);

    if (!$stmt) {
        echo "<script>
                alert('Error al preparar el cambio de estado');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }

    mysqli_stmt_bind_param($stmt, "si", $nuevo_estado, $id_producto);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Estado del producto actualizado correctamente');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    } else {
        echo "<script>
                alert('Error al cambiar el estado del producto');
                window.location.href = '../gestion/gestionar_productos.php';
            </script>";
        exit();
    }
}


/* Si alguien entra al action directamente */
header("Location: ../gestion/gestionar_productos.php");
exit();
?>