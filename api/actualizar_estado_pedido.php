<?php
// 1. Configurar las cabeceras para que sea una API real (Devuelve JSON y acepta conexiones)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// 2. Incluir tu archivo de conexión a la base de datos
// OJO: Ajusta esta ruta dependiendo de dónde esté tu conexion.php
include '../config/conexion.php';

// 3. Definimos la API KEY secreta. 
// Esta es la "contraseña" que debe ingresar el repartidor en el formulario.
$API_KEY_SECRETA = "RayoPack2026"; 

// 4. Verificamos que los datos vengan por el método POST (como lo enviamos desde JavaScript)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recibir los datos (Si no vienen, asignamos un string vacío)
    $api_key = isset($_POST['api_key']) ? $_POST['api_key'] : '';
    $codigo_pedido = isset($_POST['codigo_pedido']) ? $_POST['codigo_pedido'] : '';
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

    // 5. Seguridad: Validar que la API Key sea correcta
    if ($api_key !== $API_KEY_SECRETA) {
        echo json_encode(['success' => false, 'mensaje' => 'API Key incorrecta. Acceso denegado.']);
        exit(); // Detenemos la ejecución aquí
    }

    // 6. Validar que no falten datos importantes
    if (empty($codigo_pedido) || empty($estado)) {
        echo json_encode(['success' => false, 'mensaje' => 'Faltan datos (código de pedido o estado).']);
        exit();
    }

    // 7. Sanitizar los datos para evitar Inyección SQL (Seguridad en la Base de Datos)
    $codigo_pedido_limpio = mysqli_real_escape_string($conexion, $codigo_pedido);
    $estado_limpio = mysqli_real_escape_string($conexion, $estado);

    // 8. Hacer el UPDATE en la base de datos
    // ASUMO que tu tabla se llama 'pedidos' y el identificador es 'id'. Ajusta los nombres si son distintos.
    $query = "UPDATE pedidos SET estado = '$estado_limpio' WHERE id = '$codigo_pedido_limpio'";

    if (mysqli_query($conexion, $query)) {
        // mysqli_affected_rows comprueba si realmente se cambió alguna fila (por si inventaron un número de pedido que no existe)
        if (mysqli_affected_rows($conexion) > 0) {
            echo json_encode(['success' => true, 'mensaje' => 'Estado actualizado correctamente en la tienda.']);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'El código de pedido no existe en la base de datos.']);
        }
    } else {
        // Error de SQL (por ejemplo, si la tabla no existe)
        echo json_encode(['success' => false, 'mensaje' => 'Error en la base de datos: ' . mysqli_error($conexion)]);
    }

} else {
    // Si alguien intenta entrar a la URL por el navegador web (que es método GET), le mostramos este error
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido. Solo se acepta POST.']);
require_once "../config/conexion.php";
$api_key_correcta = "pequemundo_transporte_2026";
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(["estado" => "error", "mensaje" => "Método no permitido"], JSON_UNESCAPED_UNICODE);
    exit();
}
$api_key = isset($_POST["api_key"]) ? trim($_POST["api_key"]) : "";
$codigo_pedido = isset($_POST["codigo_pedido"]) ? trim($_POST["codigo_pedido"]) : "";
$nuevo_estado = isset($_POST["estado_pedido"]) ? trim($_POST["estado_pedido"]) : "";
$estados_permitidos = array("camino", "entregado");
if ($api_key != $api_key_correcta) {
    http_response_code(401);
    echo json_encode(["estado" => "error", "mensaje" => "API key no válida"], JSON_UNESCAPED_UNICODE);
    exit();
}
if ($codigo_pedido == "" || $nuevo_estado == "") {
    http_response_code(400);
    echo json_encode(["estado" => "error", "mensaje" => "Debe enviar código de pedido y estado"], JSON_UNESCAPED_UNICODE);
    exit();
}
if (!in_array($nuevo_estado, $estados_permitidos)) {
    http_response_code(400);
    echo json_encode(["estado" => "error", "mensaje" => "Estado no permitido para transporte"], JSON_UNESCAPED_UNICODE);
    exit();
}
$sql = "SELECT 
            p.id_pedido,
            p.codigo_pedido,
            p.estado_pedido,
            pa.estado_pago
        FROM pedido p
        INNER JOIN pago pa ON p.id_pedido = pa.id_pedido
        WHERE p.codigo_pedido = ?
        LIMIT 1";
$stmt = mysqli_prepare($conexion, $sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["estado" => "error", "mensaje" => "Error al preparar consulta"], JSON_UNESCAPED_UNICODE);
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $codigo_pedido);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($resultado) != 1) {
    http_response_code(404);
    echo json_encode(["estado" => "error", "mensaje" => "Pedido no encontrado"], JSON_UNESCAPED_UNICODE);
    exit();
}
$pedido = mysqli_fetch_assoc($resultado);
if ($pedido["estado_pago"] != "pagado") {
    http_response_code(400);
    echo json_encode(["estado" => "error", "mensaje" => "El pedido no tiene pago confirmado"], JSON_UNESCAPED_UNICODE);
    exit();
}
if ($pedido["estado_pedido"] == "pendiente_pago" || $pedido["estado_pedido"] == "cancelado") {
    http_response_code(400);
    echo json_encode(["estado" => "error", "mensaje" => "El pedido no puede ser actualizado por transporte"], JSON_UNESCAPED_UNICODE);
    exit();
}
if ($nuevo_estado == "camino" && !in_array($pedido["estado_pedido"], array("confirmado", "preparacion", "camino"))) {
    http_response_code(400);
    echo json_encode(["estado" => "error", "mensaje" => "No se puede pasar este pedido a camino"], JSON_UNESCAPED_UNICODE);
    exit();
}
if ($nuevo_estado == "entregado" && !in_array($pedido["estado_pedido"], array("camino", "entregado"))) {
    http_response_code(400);
    echo json_encode(["estado" => "error", "mensaje" => "Solo un pedido en camino puede marcarse como entregado"], JSON_UNESCAPED_UNICODE);
    exit();
}
$sql_update = "UPDATE pedido SET estado_pedido = ? WHERE id_pedido = ?";
$stmt_update = mysqli_prepare($conexion, $sql_update);
if (!$stmt_update) {
    http_response_code(500);
    echo json_encode(["estado" => "error", "mensaje" => "Error al preparar actualización"], JSON_UNESCAPED_UNICODE);
    exit();
}
$id_pedido = intval($pedido["id_pedido"]);
mysqli_stmt_bind_param($stmt_update, "si", $nuevo_estado, $id_pedido);
if (mysqli_stmt_execute($stmt_update)) {
    echo json_encode([
        "estado" => "ok",
        "mensaje" => "Estado del pedido actualizado correctamente",
        "codigo_pedido" => $codigo_pedido,
        "estado_anterior" => $pedido["estado_pedido"],
        "estado_nuevo" => $nuevo_estado
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
http_response_code(500);
echo json_encode(["estado" => "error", "mensaje" => "No se pudo actualizar el pedido"], JSON_UNESCAPED_UNICODE);
?>