<?php
// Indicar que la respuesta será en formato JSON
header("Content-Type: application/json; charset=UTF-8");
// Permitir que otros sistemas puedan consultar esta API
header("Access-Control-Allow-Origin: *");
// Permitir solo método GET
header("Access-Control-Allow-Methods: GET");
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Validar que la petición sea GET
if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(405);
    echo json_encode([
        "estado" => "error",
        "mensaje" => "Método no permitido"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
// Capturar búsqueda opcional
$busqueda = isset($_GET["busqueda"]) ? trim($_GET["busqueda"]) : "";
// Crear consulta base para productos activos
$sql = "SELECT 
            id_producto,
            nombre,
            descripcion,
            precio,
            stock,
            imagen,
            estado
        FROM producto
        WHERE estado = 'activo'";
$parametros = array();
$tipos = "";
// Agregar filtro de búsqueda si existe
if (!empty($busqueda)) {
    $sql .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
    $busqueda_like = "%" . $busqueda . "%";
    $parametros[] = $busqueda_like;
    $parametros[] = $busqueda_like;
    $tipos .= "ss";
}
// Ordenar productos por id descendente
$sql .= " ORDER BY id_producto DESC";
// Preparar consulta segura
$stmt = mysqli_prepare($conexion, $sql);
// Validar preparación de consulta
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "estado" => "error",
        "mensaje" => "No se pudo preparar la consulta del catálogo"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
// Asociar parámetros si existen
if (!empty($parametros)) {
    mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
}
// Ejecutar consulta
if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    echo json_encode([
        "estado" => "error",
        "mensaje" => "No se pudo obtener el catálogo"
    ], JSON_UNESCAPED_UNICODE);
    exit();
}
// Obtener resultados
$resultado = mysqli_stmt_get_result($stmt);
// Crear arreglo de productos
$productos = array();
// Recorrer productos encontrados
while ($producto = mysqli_fetch_assoc($resultado)) {
    $productos[] = [
        "id_producto" => intval($producto["id_producto"]),
        "nombre" => $producto["nombre"],
        "descripcion" => $producto["descripcion"],
        "precio" => intval($producto["precio"]),
        "stock" => intval($producto["stock"]),
        "imagen" => $producto["imagen"],
        "estado" => $producto["estado"]
    ];
}
// Devolver respuesta JSON
echo json_encode([
    "estado" => "ok",
    "cantidad" => count($productos),
    "productos" => $productos
], JSON_UNESCAPED_UNICODE);
// Cerrar consulta y conexión
mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>