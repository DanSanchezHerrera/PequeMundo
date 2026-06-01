@ -1,59 +0,0 @@
<?php
// API interna: devuelve el detalle de productos de un pedido en formato JSON
// Solo accesible para admin y vendedor con sesión activa
session_start();
header("Content-Type: application/json; charset=utf-8");

// Verificar sesión y rol
if (
    !isset($_SESSION["id_usuario"]) ||
    !isset($_SESSION["tipo_usuario"]) ||
    ($_SESSION["tipo_usuario"] != "admin" && $_SESSION["tipo_usuario"] != "vendedor")
) {
    echo json_encode(array("ok" => false, "error" => "Sin permiso"));
    exit();
}

// Incluir conexión
require_once "../config/conexion.php";

// Validar id_pedido
$id_pedido = isset($_GET["id_pedido"]) ? intval($_GET["id_pedido"]) : 0;
if ($id_pedido <= 0) {
    echo json_encode(array("ok" => false, "error" => "ID inválido"));
    exit();
}

// Consultar productos del pedido
$sql = "SELECT
            pd.cantidad,
            pd.precio_unitario,
            pd.subtotal,
            pr.nombre,
            pr.imagen
        FROM pedido_detalle pd
        INNER JOIN producto pr ON pd.id_producto = pr.id_producto
        WHERE pd.id_pedido = ?";
$stmt = mysqli_prepare($conexion, $sql);
if (!$stmt) {
    echo json_encode(array("ok" => false, "error" => "Error en consulta"));
    exit();
}
mysqli_stmt_bind_param($stmt, "i", $id_pedido);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$items = array();
while ($fila = mysqli_fetch_assoc($resultado)) {
    $items[] = array(
        "nombre"          => $fila["nombre"],
        "imagen"          => $fila["imagen"],
        "cantidad"        => intval($fila["cantidad"]),
        "precio_unitario" => intval($fila["precio_unitario"]),
        "subtotal"        => intval($fila["subtotal"])
    );
}

echo json_encode(array("ok" => true, "items" => $items));
mysqli_close($conexion);
?>