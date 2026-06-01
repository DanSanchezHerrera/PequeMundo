<?php
// Iniciar sesión para validar acceso
session_start();
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Verificar que exista sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../login.php");
    exit();
}
// Verificar que solo admin y vendedor puedan acceder
if (!isset($_SESSION["tipo_usuario"]) || ($_SESSION["tipo_usuario"] != "admin" && $_SESSION["tipo_usuario"] != "vendedor")) {
    header("Location: ../index.php");
    exit();
}

// ──────────────────────────────────────────
// Avanzar estado del pedido
// ──────────────────────────────────────────
if (isset($_POST["btnAvanzarEstado"])) {
    $id_pedido     = isset($_POST["id_pedido"])     ? intval($_POST["id_pedido"])              : 0;
    $nuevo_estado  = isset($_POST["nuevo_estado"])  ? trim($_POST["nuevo_estado"])             : "";
    $filtro_estado = isset($_POST["filtro_estado"]) ? trim($_POST["filtro_estado"])            : "";

    // Construir URL de retorno manteniendo el filtro activo
    $redireccion_ok    = "../gestion/gestionar_pedidos.php" . ($filtro_estado != "" ? "?estado=" . urlencode($filtro_estado) . "&msg=ok" : "?msg=ok");
    $redireccion_error = "../gestion/gestionar_pedidos.php" . ($filtro_estado != "" ? "?estado=" . urlencode($filtro_estado) . "&msg=error" : "?msg=error");

    // Validar id y estado recibidos
    if ($id_pedido <= 0 || $nuevo_estado == "") {
        header("Location: " . $redireccion_error);
        exit();
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

    // Solo se permiten estas transiciones válidas
    $transiciones_permitidas = array(
        "recibido"    => "preparacion",
        "preparacion" => "camino",
        "camino"      => "entregado"
    );

    // Buscar el estado actual del pedido en la BD (no confiar en el POST)
    $sql_actual = "SELECT estado_pedido FROM pedido WHERE id_pedido = ? LIMIT 1";
    $sql_actual = "SELECT
                        p.estado_pedido,
                        pa.estado_pago
                    FROM pedido p
                    INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                    WHERE p.id_pedido = ?
                    LIMIT 1";
    $stmt_actual = mysqli_prepare($conexion, $sql_actual);
    if (!$stmt_actual) {
        header("Location: " . $redireccion_error);
        exit();
        redirigirGestionPedidos($filtro_estado, "error");
    }
    mysqli_stmt_bind_param($stmt_actual, "i", $id_pedido);
    mysqli_stmt_execute($stmt_actual);
    $resultado_actual = mysqli_stmt_get_result($stmt_actual);

    if (mysqli_num_rows($resultado_actual) != 1) {
        header("Location: " . $redireccion_error);
        exit();
        redirigirGestionPedidos($filtro_estado, "error");
    }
    $pedido_actual = mysqli_fetch_assoc($resultado_actual);
    $estado_actual = $pedido_actual["estado_pedido"];

    // Validar que la transición sea la correcta para ese estado
    if (!isset($transiciones_permitidas[$estado_actual]) || $transiciones_permitidas[$estado_actual] != $nuevo_estado) {
        header("Location: " . $redireccion_error);
        exit();
    if ($pedido_actual["estado_pago"] != "pagado") {
        redirigirGestionPedidos($filtro_estado, "pago");
    }
    if ($pedido_actual["estado_pedido"] == "pendiente_pago") {
        redirigirGestionPedidos($filtro_estado, "pago");
    }
    if ($pedido_actual["estado_pedido"] == "cancelado") {
        redirigirGestionPedidos($filtro_estado, "error");
    }

    // Actualizar el estado del pedido
    $sql_update = "UPDATE pedido SET estado_pedido = ? WHERE id_pedido = ?";
    $stmt_update = mysqli_prepare($conexion, $sql_update);
    if (!$stmt_update) {
        header("Location: " . $redireccion_error);
        exit();
        redirigirGestionPedidos($filtro_estado, "error");
    }
    mysqli_stmt_bind_param($stmt_update, "si", $nuevo_estado, $id_pedido);

    if (mysqli_stmt_execute($stmt_update)) {
        header("Location: " . $redireccion_ok);
        redirigirGestionPedidos($filtro_estado, "ok");
    } else {
        header("Location: " . $redireccion_error);
        redirigirGestionPedidos($filtro_estado, "error");
    }
    exit();
}

// Si no llega una acción válida, redirigir
header("Location: ../gestion/gestionar_pedidos.php");
exit();
?>
?>