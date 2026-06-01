<?php
// 1. Configurar las cabeceras para que sea una API real (Devuelve JSON y acepta conexiones)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
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
}
?>