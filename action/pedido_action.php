<?php
// Iniciar sesión para validar usuario conectado
session_start();
// Incluir conexión a la base de datos
require_once "../config/conexion.php";
// Verificar que exista sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../login.php");
    exit();
}
// Verificar que solo clientes puedan crear pedidos
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "cliente") {
    header("Location: ../index.php");
    exit();
}
// Guardar id del usuario conectado
$id_usuario = intval($_SESSION["id_usuario"]);
// Generar código de pedido
function generarCodigoPedido() {
    return "PM" . date("YmdHis") . rand(100, 999);
}
// Redirigir al checkout con mensaje simple
function volverCheckout() {
    header("Location: ../checkout.php");
    exit();
}
// Crear pedido pendiente de pago
if (isset($_POST["btnCrearPedido"])) {
    // Capturar datos enviados desde checkout
    $tipo_entrega = trim($_POST["tipo_entrega"]);
    $direccion_entrega = isset($_POST["direccion_entrega"]) ? trim($_POST["direccion_entrega"]) : "";
    $region_entrega = isset($_POST["region_entrega"]) ? trim($_POST["region_entrega"]) : "";
    $comuna_entrega = isset($_POST["comuna_entrega"]) ? trim($_POST["comuna_entrega"]) : "";
    // Validar tipo de entrega
    if ($tipo_entrega != "retiro_tienda" && $tipo_entrega != "despacho_domicilio") {
        volverCheckout();
    }
    // Validar datos si corresponde despacho
    if ($tipo_entrega == "despacho_domicilio") {
        if (empty($direccion_entrega) || empty($region_entrega) || empty($comuna_entrega)) {
            volverCheckout();
        }
    }
    // Calcular costo de despacho en servidor para no confiar en el formulario
    $costo_despacho = 0;
    if ($tipo_entrega == "despacho_domicilio") {
        if ($region_entrega == "Región Metropolitana") {
            $costo_despacho = 3990;
        } else {
            $costo_despacho = 8990;
        }
    }
    // Buscar productos actuales del carrito
    $sql_carrito = "SELECT 
                        c.id_carrito,
                        cd.id_carrito_detalle,
                        cd.id_producto,
                        cd.cantidad,
                        cd.precio_unitario,
                        p.stock,
                        p.estado
                    FROM carrito c
                    INNER JOIN carrito_detalle cd ON c.id_carrito = cd.id_carrito
                    INNER JOIN producto p ON cd.id_producto = p.id_producto
                    WHERE c.id_usuario = ?";
    $stmt_carrito = mysqli_prepare($conexion, $sql_carrito);
    if (!$stmt_carrito) {
        volverCheckout();
    }
    mysqli_stmt_bind_param($stmt_carrito, "i", $id_usuario);
    mysqli_stmt_execute($stmt_carrito);
    $resultado_carrito = mysqli_stmt_get_result($stmt_carrito);
    // Guardar productos en arreglo
    $productos = array();
    $total_productos = 0;
    $id_carrito = 0;
    while ($producto = mysqli_fetch_assoc($resultado_carrito)) {
        $cantidad = intval($producto["cantidad"]);
        $precio_unitario = intval($producto["precio_unitario"]);
        $subtotal = $cantidad * $precio_unitario;
        // Validar producto activo y stock suficiente
        if ($producto["estado"] != "activo" || $cantidad <= 0 || $cantidad > intval($producto["stock"])) {
            volverCheckout();
        }
        $producto["subtotal"] = $subtotal;
        $productos[] = $producto;
        $total_productos += $subtotal;
        $id_carrito = intval($producto["id_carrito"]);
    }
    // Validar que el carrito no esté vacío
    if (count($productos) == 0 || $id_carrito <= 0) {
        header("Location: ../carrito.php");
        exit();
    }
    // Calcular total final
    $total_pedido = $total_productos + $costo_despacho;
    // Generar código único de pedido
    $codigo_pedido = generarCodigoPedido();
    // Iniciar transacción para guardar pedido, detalle y pago juntos
    mysqli_begin_transaction($conexion);
    try {
        // Insertar pedido pendiente de pago
        $sql_pedido = "INSERT INTO pedido (
                            codigo_pedido,
                            id_usuario,
                            total_productos,
                            costo_despacho,
                            total_pedido,
                            tipo_entrega,
                            direccion_entrega,
                            region_entrega,
                            comuna_entrega,
                            estado_pedido
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente_pago')";
        $stmt_pedido = mysqli_prepare($conexion, $sql_pedido);
        if (!$stmt_pedido) {
            throw new Exception("Error al preparar pedido");
        }
        mysqli_stmt_bind_param(
            $stmt_pedido,
            "siiiissss",
            $codigo_pedido,
            $id_usuario,
            $total_productos,
            $costo_despacho,
            $total_pedido,
            $tipo_entrega,
            $direccion_entrega,
            $region_entrega,
            $comuna_entrega
        );
        if (!mysqli_stmt_execute($stmt_pedido)) {
            throw new Exception("Error al crear pedido");
        }
        // Obtener id del pedido creado
        $id_pedido = mysqli_insert_id($conexion);
        // Insertar detalle de pedido
        $sql_detalle = "INSERT INTO pedido_detalle (
                            id_pedido,
                            id_producto,
                            cantidad,
                            precio_unitario,
                            subtotal
                        ) VALUES (?, ?, ?, ?, ?)";
        $stmt_detalle = mysqli_prepare($conexion, $sql_detalle);
        if (!$stmt_detalle) {
            throw new Exception("Error al preparar detalle");
        }
        foreach ($productos as $producto) {
            $id_producto = intval($producto["id_producto"]);
            $cantidad = intval($producto["cantidad"]);
            $precio_unitario = intval($producto["precio_unitario"]);
            $subtotal = intval($producto["subtotal"]);
            mysqli_stmt_bind_param(
                $stmt_detalle,
                "iiiii",
                $id_pedido,
                $id_producto,
                $cantidad,
                $precio_unitario,
                $subtotal
            );
            if (!mysqli_stmt_execute($stmt_detalle)) {
                throw new Exception("Error al guardar detalle");
            }
        }
        // Crear pago pendiente para simulación de Mercado Pago
        $sql_pago = "INSERT INTO pago (
                        id_pedido,
                        metodo_pago,
                        estado_pago,
                        monto,
                        referencia_pago
                    ) VALUES (?, 'mercado_pago_simulado', 'pendiente', ?, NULL)";
        $stmt_pago = mysqli_prepare($conexion, $sql_pago);
        if (!$stmt_pago) {
            throw new Exception("Error al preparar pago");
        }
        mysqli_stmt_bind_param($stmt_pago, "ii", $id_pedido, $total_pedido);
        if (!mysqli_stmt_execute($stmt_pago)) {
            throw new Exception("Error al crear pago");
        }
        // Confirmar transacción
        mysqli_commit($conexion);
        // Redirigir a creación de pago con Mercado Pago
        header("Location: ../action/pago_action.php?id_pedido=" . $id_pedido);
        exit();
    } catch (Exception $e) {
        // Revertir cambios si ocurre un error
        mysqli_rollback($conexion);
        header("Location: ../checkout.php");
        exit();
    }
}
// Redirigir si no se recibe acción válida
header("Location: ../checkout.php");
exit();
?>