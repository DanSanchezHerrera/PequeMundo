<?php
session_start();
require_once "../config/conexion.php";
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../login.php");
    exit();
}
if (!isset($_SESSION["tipo_usuario"]) || ($_SESSION["tipo_usuario"] != "admin" && $_SESSION["tipo_usuario"] != "vendedor")) {
    header("Location: ../index.php");
    exit();
}
function redirigirGestionPedidos($filtro_estado, $mensaje) {
    $url = "../gestion/gestionar_pedidos.php";
    if ($filtro_estado != "") {
        $url .= "?estado=" . urlencode($filtro_estado) . "&msg=" . urlencode($mensaje);
    } else {
        $url .= "?msg=" . urlencode($mensaje);
    }
    header("Location: " . $url);
    exit();
}
if (isset($_POST["btnActualizarEstadoPedido"])) {
    $id_pedido = isset($_POST["id_pedido"]) ? intval($_POST["id_pedido"]) : 0;
    $nuevo_estado = isset($_POST["nuevo_estado"]) ? trim($_POST["nuevo_estado"]) : "";
    $filtro_estado = isset($_POST["filtro_estado"]) ? trim($_POST["filtro_estado"]) : "";
    $estados_permitidos = array("confirmado", "preparacion", "camino", "entregado", "cancelado");
    if ($id_pedido <= 0 || !in_array($nuevo_estado, $estados_permitidos)) {
        redirigirGestionPedidos($filtro_estado, "error");
    }
    $sql_actual = "SELECT
                        p.estado_pedido,
                        pa.estado_pago
                    FROM pedido p
                    INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                    WHERE p.id_pedido = ?
                    LIMIT 1";
    $stmt_actual = mysqli_prepare($conexion, $sql_actual);
    if (!$stmt_actual) {
        redirigirGestionPedidos($filtro_estado, "error");
    }
    mysqli_stmt_bind_param($stmt_actual, "i", $id_pedido);
    mysqli_stmt_execute($stmt_actual);
    $resultado_actual = mysqli_stmt_get_result($stmt_actual);
    if (mysqli_num_rows($resultado_actual) != 1) {
        redirigirGestionPedidos($filtro_estado, "error");
    }
    $pedido_actual = mysqli_fetch_assoc($resultado_actual);
    if ($pedido_actual["estado_pago"] != "pagado") {
        redirigirGestionPedidos($filtro_estado, "pago");
    }
    if ($pedido_actual["estado_pedido"] == "pendiente_pago") {
        redirigirGestionPedidos($filtro_estado, "pago");
    }
    if ($pedido_actual["estado_pedido"] == "cancelado") {
        redirigirGestionPedidos($filtro_estado, "error");
    }
    $sql_update = "UPDATE pedido SET estado_pedido = ? WHERE id_pedido = ?";
    $stmt_update = mysqli_prepare($conexion, $sql_update);
    if (!$stmt_update) {
        redirigirGestionPedidos($filtro_estado, "error");
    }
    mysqli_stmt_bind_param($stmt_update, "si", $nuevo_estado, $id_pedido);
    if (mysqli_stmt_execute($stmt_update)) {
        redirigirGestionPedidos($filtro_estado, "ok");
    } else {
        redirigirGestionPedidos($filtro_estado, "error");
    }
}
header("Location: ../gestion/gestionar_pedidos.php");
exit();
?>