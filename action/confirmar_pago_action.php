<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../index.php");
    exit();
}

$id_usuario = intval($_SESSION["id_usuario"]);
$id_pedido = isset($_GET["id_pedido"]) ? intval($_GET["id_pedido"]) : 0;

$estado_mp = isset($_GET["status"]) ? strtolower(trim($_GET["status"])) : "";
$estado_collection = isset($_GET["collection_status"]) ? strtolower(trim($_GET["collection_status"])) : "";
$payment_id = isset($_GET["payment_id"]) ? $_GET["payment_id"] : "";
$collection_id = isset($_GET["collection_id"]) ? $_GET["collection_id"] : "";
$referencia_pago = !empty($payment_id) ? $payment_id : $collection_id;

if ($id_pedido <= 0) {
    header("Location: ../carrito.php");
    exit();
}

$estado_retorno = !empty($estado_mp) ? $estado_mp : $estado_collection;

if (in_array($estado_retorno, array("pending", "in_process"))) {
    header("Location: ../pago_pendiente.php?id_pedido=" . $id_pedido);
    exit();
}

if (in_array($estado_retorno, array("rejected", "cancelled", "cancelled_by_user", "refunded"))) {
    header("Location: ../action/pago_fallido_action.php?id_pedido=" . $id_pedido . "&referencia_pago=" . urlencode($referencia_pago));
    exit();
}

if (!empty($estado_retorno) && $estado_retorno != "approved") {
    header("Location: ../action/pago_fallido_action.php?id_pedido=" . $id_pedido . "&referencia_pago=" . urlencode($referencia_pago));
    exit();
}

$sql_pedido = "SELECT 
                    p.id_pedido,
                    p.id_usuario,
                    p.codigo_pedido,
                    p.estado_pedido,
                    pa.id_pago,
                    pa.estado_pago
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                WHERE p.id_pedido = ?
                AND p.id_usuario = ?
                LIMIT 1";

$stmt_pedido = mysqli_prepare($conexion, $sql_pedido);

if (!$stmt_pedido) {
    header("Location: ../pago_pendiente.php?id_pedido=" . $id_pedido);
    exit();
}

mysqli_stmt_bind_param($stmt_pedido, "ii", $id_pedido, $id_usuario);
mysqli_stmt_execute($stmt_pedido);
$resultado_pedido = mysqli_stmt_get_result($stmt_pedido);

if (mysqli_num_rows($resultado_pedido) != 1) {
    header("Location: ../carrito.php");
    exit();
}

$pedido = mysqli_fetch_assoc($resultado_pedido);

if ($pedido["estado_pago"] == "pagado") {
    header("Location: ../pago_exitoso.php?id_pedido=" . $id_pedido);
    exit();
}

if ($pedido["estado_pago"] == "pendiente") {
    mysqli_begin_transaction($conexion);

    try {
        $sql_detalle = "SELECT id_producto, cantidad FROM pedido_detalle WHERE id_pedido = ?";
        $stmt_detalle = mysqli_prepare($conexion, $sql_detalle);

        if (!$stmt_detalle) {
            throw new Exception("Error al preparar detalle");
        }

        mysqli_stmt_bind_param($stmt_detalle, "i", $id_pedido);
        mysqli_stmt_execute($stmt_detalle);
        $resultado_detalle = mysqli_stmt_get_result($stmt_detalle);

        $productos_pedido = array();

        while ($detalle = mysqli_fetch_assoc($resultado_detalle)) {
            $id_producto = intval($detalle["id_producto"]);
            $cantidad = intval($detalle["cantidad"]);

            $sql_stock = "SELECT stock FROM producto WHERE id_producto = ?";
            $stmt_stock = mysqli_prepare($conexion, $sql_stock);

            if (!$stmt_stock) {
                throw new Exception("Error al preparar stock");
            }

            mysqli_stmt_bind_param($stmt_stock, "i", $id_producto);
            mysqli_stmt_execute($stmt_stock);
            $resultado_stock = mysqli_stmt_get_result($stmt_stock);

            if (mysqli_num_rows($resultado_stock) != 1) {
                throw new Exception("Producto no encontrado");
            }

            $producto_stock = mysqli_fetch_assoc($resultado_stock);

            if (intval($producto_stock["stock"]) < $cantidad) {
                throw new Exception("Stock insuficiente");
            }

            $productos_pedido[] = array(
                "id_producto" => $id_producto,
                "cantidad" => $cantidad
            );
        }

        foreach ($productos_pedido as $producto) {
            $sql_descuento = "UPDATE producto SET stock = stock - ? WHERE id_producto = ?";
            $stmt_descuento = mysqli_prepare($conexion, $sql_descuento);

            if (!$stmt_descuento) {
                throw new Exception("Error al preparar descuento");
            }

            mysqli_stmt_bind_param($stmt_descuento, "ii", $producto["cantidad"], $producto["id_producto"]);

            if (!mysqli_stmt_execute($stmt_descuento)) {
                throw new Exception("Error al descontar stock");
            }
        }

        $sql_pago = "UPDATE pago
                     SET estado_pago = 'pagado',
                         referencia_pago = ?,
                         fecha_pago = NOW()
                     WHERE id_pago = ?";
        $stmt_pago = mysqli_prepare($conexion, $sql_pago);

        if (!$stmt_pago) {
            throw new Exception("Error al preparar pago");
        }

        $id_pago = intval($pedido["id_pago"]);
        mysqli_stmt_bind_param($stmt_pago, "si", $referencia_pago, $id_pago);

        if (!mysqli_stmt_execute($stmt_pago)) {
            throw new Exception("Error al actualizar pago");
        }

        $sql_actualizar_pedido = "UPDATE pedido SET estado_pedido = 'confirmado' WHERE id_pedido = ?";
        $stmt_actualizar_pedido = mysqli_prepare($conexion, $sql_actualizar_pedido);

        if (!$stmt_actualizar_pedido) {
            throw new Exception("Error al preparar pedido");
        }

        mysqli_stmt_bind_param($stmt_actualizar_pedido, "i", $id_pedido);

        if (!mysqli_stmt_execute($stmt_actualizar_pedido)) {
            throw new Exception("Error al actualizar pedido");
        }

        $sql_vaciar = "DELETE cd
                       FROM carrito_detalle cd
                       INNER JOIN carrito c ON cd.id_carrito = c.id_carrito
                       WHERE c.id_usuario = ?";
        $stmt_vaciar = mysqli_prepare($conexion, $sql_vaciar);

        if (!$stmt_vaciar) {
            throw new Exception("Error al preparar vaciado");
        }

        mysqli_stmt_bind_param($stmt_vaciar, "i", $id_usuario);

        if (!mysqli_stmt_execute($stmt_vaciar)) {
            throw new Exception("Error al vaciar carrito");
        }

        mysqli_commit($conexion);

        header("Location: ../pago_exitoso.php?id_pedido=" . $id_pedido);
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        header("Location: ../pago_pendiente.php?id_pedido=" . $id_pedido);
        exit();
    }
}

header("Location: ../pago_pendiente.php?id_pedido=" . $id_pedido);
exit();
?>