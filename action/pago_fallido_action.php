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
$referencia_pago = isset($_GET["referencia_pago"]) ? trim($_GET["referencia_pago"]) : "";

if ($id_pedido <= 0) {
    header("Location: ../carrito.php");
    exit();
}

$sql_pedido = "SELECT 
                    p.id_pedido,
                    p.id_usuario,
                    pa.id_pago,
                    pa.estado_pago
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                WHERE p.id_pedido = ?
                AND p.id_usuario = ?
                LIMIT 1";

$stmt_pedido = mysqli_prepare($conexion, $sql_pedido);

if (!$stmt_pedido) {
    header("Location: ../pago_fallido.php?id_pedido=" . $id_pedido);
    exit();
}

mysqli_stmt_bind_param($stmt_pedido, "ii", $id_pedido, $id_usuario);
mysqli_stmt_execute($stmt_pedido);
$resultado_pedido = mysqli_stmt_get_result($stmt_pedido);

if (mysqli_num_rows($resultado_pedido) == 1) {
    $pedido = mysqli_fetch_assoc($resultado_pedido);

    if ($pedido["estado_pago"] == "pendiente") {
        $id_pago = intval($pedido["id_pago"]);

        $sql_pago = "UPDATE pago
                     SET estado_pago = 'rechazado',
                         referencia_pago = ?,
                         fecha_pago = NOW()
                     WHERE id_pago = ?";

        $stmt_pago = mysqli_prepare($conexion, $sql_pago);

        if ($stmt_pago) {
            mysqli_stmt_bind_param($stmt_pago, "si", $referencia_pago, $id_pago);
            mysqli_stmt_execute($stmt_pago);
        }

        $sql_pedido_update = "UPDATE pedido
                              SET estado_pedido = 'cancelado'
                              WHERE id_pedido = ?";

        $stmt_pedido_update = mysqli_prepare($conexion, $sql_pedido_update);

        if ($stmt_pedido_update) {
            mysqli_stmt_bind_param($stmt_pedido_update, "i", $id_pedido);
            mysqli_stmt_execute($stmt_pedido_update);
        }
    }
}

header("Location: ../pago_fallido.php?id_pedido=" . $id_pedido);
exit();
?>