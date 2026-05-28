<?php
// Iniciar sesión para validar usuario conectado
session_start();
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Incluir configuración de Mercado Pago
require_once "../config/mercadopago.php";
// Verificar que exista sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../login.php");
    exit();
}
// Verificar que solo clientes puedan iniciar pago
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../index.php");
    exit();
}
// Verificar que exista access token configurado
if (!isset($access_token) || empty($access_token)) {
    echo "No se encontró el access token de Mercado Pago.";
    exit();
}
// Guardar id del usuario conectado
$id_usuario = intval($_SESSION["id_usuario"]);
// Capturar id del pedido
$id_pedido = isset($_GET["id_pedido"]) ? intval($_GET["id_pedido"]) : 0;
// Validar id del pedido
if ($id_pedido <= 0) {
    header("Location: ../checkout.php");
    exit();
}
// Buscar pedido pendiente del usuario
$sql_pedido = "SELECT
                    p.id_pedido,
                    p.codigo_pedido,
                    p.id_usuario,
                    p.total_pedido,
                    p.estado_pedido,
                    p.costo_despacho,
                    pa.id_pago,
                    pa.estado_pago,
                    u.nombre,
                    u.apellido,
                    u.mail
                FROM pedido p
                INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
                INNER JOIN usuario u ON p.id_usuario = u.id_usuario
                WHERE p.id_pedido = ?
                AND p.id_usuario = ?
                LIMIT 1";
$stmt_pedido = mysqli_prepare($conexion, $sql_pedido);
if (!$stmt_pedido) {
    echo "Error al preparar la consulta del pedido.";
    exit();
}
mysqli_stmt_bind_param($stmt_pedido, "ii", $id_pedido, $id_usuario);
mysqli_stmt_execute($stmt_pedido);
$resultado_pedido = mysqli_stmt_get_result($stmt_pedido);
// Validar existencia del pedido
if (mysqli_num_rows($resultado_pedido) != 1) {
    echo "El pedido no existe o no pertenece al usuario conectado.";
    exit();
}
$pedido = mysqli_fetch_assoc($resultado_pedido);
// Validar que el pedido esté pendiente de pago
if ($pedido["estado_pago"] != "pendiente" || $pedido["estado_pedido"] != "pendiente_pago") {
    header("Location: ../index.php");
    exit();
}
// Buscar productos del pedido
$sql_detalle = "SELECT
                    pd.id_producto,
                    pd.cantidad,
                    pd.precio_unitario,
                    pd.subtotal,
                    pr.nombre,
                    pr.descripcion,
                    pr.imagen
                FROM pedido_detalle pd
                INNER JOIN producto pr ON pd.id_producto = pr.id_producto
                WHERE pd.id_pedido = ?";
$stmt_detalle = mysqli_prepare($conexion, $sql_detalle);
if (!$stmt_detalle) {
    echo "Error al preparar el detalle del pedido.";
    exit();
}
mysqli_stmt_bind_param($stmt_detalle, "i", $id_pedido);
mysqli_stmt_execute($stmt_detalle);
$resultado_detalle = mysqli_stmt_get_result($stmt_detalle);
// Crear arreglo de productos para Mercado Pago
$items = array();
$url_base = "INSERTEURLAQUI:D";
while ($producto = mysqli_fetch_assoc($resultado_detalle)) {
    $imagen = "";
    if (!empty($producto["imagen"])) {
        $imagen = $url_base . "/" . $producto["imagen"];
    }
    $items[] = array(
        "title" => $producto["nombre"],
        "description" => $producto["descripcion"],
        "picture_url" => $imagen,
        "quantity" => intval($producto["cantidad"]),
        "currency_id" => "CLP",
        "unit_price" => intval($producto["precio_unitario"])
    );
}
// Validar que existan productos en el pedido
if (count($items) == 0) {
    echo "El pedido no tiene productos asociados.";
    exit();
}
// Agregar costo de despacho como item si corresponde
$costo_despacho = intval($pedido["costo_despacho"]);
if ($costo_despacho > 0) {
    $items[] = array(
        "title" => "Costo de despacho",
        "description" => "Despacho del pedido " . $pedido["codigo_pedido"],
        "quantity" => 1,
        "currency_id" => "CLP",
        "unit_price" => $costo_despacho
    );
}
// Crear datos de preferencia
$datos_preferencia = array(
    "items" => $items,
    "payer" => array(
        "name" => $pedido["nombre"],
        "surname" => $pedido["apellido"],
        "email" => $pedido["mail"]
    ),
    "external_reference" => $pedido["codigo_pedido"],
    "back_urls" => array(
        "success" => $url_base . "/action/confirmar_pago_action.php?id_pedido=" . $id_pedido,
        "failure" => $url_base . "/action/pago_fallido_action.php?id_pedido=" . $id_pedido,
        "pending" => $url_base . "/pago_pendiente.php?id_pedido=" . $id_pedido
    ),
    "auto_return" => "approved"
);
// Enviar preferencia a Mercado Pago
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.mercadopago.com/checkout/preferences",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($datos_preferencia),
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $access_token
    )
));
$respuesta = curl_exec($curl);
$error_curl = curl_error($curl);
curl_close($curl);
// Validar error de conexión
if ($error_curl) {
    echo "Error al conectar con Mercado Pago: " . htmlspecialchars($error_curl);
    exit();
}
// Decodificar respuesta
$respuesta_decodificada = json_decode($respuesta, true);
// Redirigir a Mercado Pago si existe init_point
if (isset($respuesta_decodificada["init_point"])) {
    $referencia_pago = isset($respuesta_decodificada["id"]) ? $respuesta_decodificada["id"] : "";
    $id_pago = intval($pedido["id_pago"]);
    $sql_pago = "UPDATE pago SET referencia_pago = ? WHERE id_pago = ?";
    $stmt_pago = mysqli_prepare($conexion, $sql_pago);
    if ($stmt_pago) {
        mysqli_stmt_bind_param($stmt_pago, "si", $referencia_pago, $id_pago);
        mysqli_stmt_execute($stmt_pago);
    }
    header("Location: " . $respuesta_decodificada["init_point"]);
    exit();
}
// Mostrar error si Mercado Pago no devuelve init_point
echo "<h3>Error al crear preferencia de pago</h3>";
echo "<pre>";
print_r($respuesta_decodificada);
echo "</pre>";
?>