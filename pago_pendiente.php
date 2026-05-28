<?php
// Iniciar sesión
session_start();
// Incluir conexión a la base de datos
require_once "config/conexion.php";
// Verificar sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}
// Capturar pedido
$id_usuario = intval($_SESSION["id_usuario"]);
$id_pedido = isset($_GET["id_pedido"]) ? intval($_GET["id_pedido"]) : 0;
$codigo_pedido = "";
// Buscar código del pedido
if ($id_pedido > 0) {
    $sql_pedido = "SELECT codigo_pedido FROM pedido WHERE id_pedido = ? AND id_usuario = ? LIMIT 1";
    $stmt_pedido = mysqli_prepare($conexion, $sql_pedido);
    if ($stmt_pedido) {
        mysqli_stmt_bind_param($stmt_pedido, "ii", $id_pedido, $id_usuario);
        mysqli_stmt_execute($stmt_pedido);
        $resultado_pedido = mysqli_stmt_get_result($stmt_pedido);
        if (mysqli_num_rows($resultado_pedido) == 1) {
            $pedido = mysqli_fetch_assoc($resultado_pedido);
            $codigo_pedido = $pedido["codigo_pedido"];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pago pendiente - PequeMundo</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Estilos propios -->
        <link rel="stylesheet" href="css/estilos.css">
        <link rel="stylesheet" href="css/navbar.css">
        <link rel="stylesheet" href="css/footer.css">
    </head>
    <body>
        <!-- Menú -->
        <?php include "masterpage/menu.php"; ?>
        <!-- Contenido principal -->
        <main class="container my-5">
            <section class="row justify-content-center">
                <div class="col-lg-7">
                    <article class="bg-white rounded shadow-sm p-5 text-center">
                        <!-- Imagen principal -->
                        <img src="img/pago_pendiente.png" alt="Pago pendiente" class="img-fluid mb-4" style="max-width: 180px;">
                        <!-- Mensaje principal -->
                        <h2 class="mb-3">Pago pendiente</h2>
                        <p class="text-muted mb-4">Tu pago aún está siendo procesado. Cuando se confirme, el pedido continuará con su preparación.</p>
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-clock"></i>
                            El pedido se mantiene pendiente de pago.
                            <?php if (!empty($codigo_pedido)) { ?>
                                <br>Código de pedido: <strong><?php echo htmlspecialchars($codigo_pedido); ?></strong>
                            <?php } ?>
                        </div>
                        <!-- Acciones -->
                        <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
                            <a href="catalogo.php" class="btn btn-finalizar-compra">Volver al catálogo</a>
                            <a href="index.php" class="btn btn-seguir-comprando">Volver al inicio</a>
                        </div>
                    </article>
                </div>
            </section>
        </main>
        <!-- Footer -->
        <?php include "masterpage/footer.php"; ?>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
// Cerrar conexión
mysqli_close($conexion);
?>