        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Historial de ventas - PequeMundo</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Estilos propios -->
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/navbar.css">
        <link rel="stylesheet" href="../css/footer.css">
        <link rel="stylesheet" href="../css/finanzas.css">
    </head>
    <body>
        <!-- Menú -->
        <?php include "../masterpage/menu.php"; ?>
        <!-- Contenido principal -->
        <main class="container my-5">
            <!-- Encabezado -->
            <section class="d-flex align-items-center mb-4 gap-3">
                <i class="fa-solid fa-clock-rotate-left fa-2x icono-finanzas"></i>
                <div>
@ -97,13 +91,11 @@ while ($venta = mysqli_fetch_assoc($resultado_historial)) {
                    <small class="text-muted">Consultar ventas confirmadas y pagadas.</small>
                </div>
            </section>
            <!-- Navegación interna -->
            <section class="mb-4">
                <a href="reportes.php" class="btn btn-seguir-comprando btn-sm me-2">Reportes</a>
                <a href="productos_mas_vendidos.php" class="btn btn-seguir-comprando btn-sm me-2">Productos más vendidos</a>
                <a href="historial_ventas.php" class="btn btn-finalizar-compra btn-sm">Historial de ventas</a>
            </section>
            <!-- Filtros -->
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="GET" action="historial_ventas.php" class="row g-3 align-items-end">
@ -119,7 +111,7 @@ while ($venta = mysqli_fetch_assoc($resultado_historial)) {
                            <label class="form-label">Estado pedido</label>
                            <select name="estado_pedido" class="form-control">
                                <option value="">Todos</option>
                                <option value="recibido" <?php if ($estado_pedido == "recibido") echo "selected"; ?>>Recibido</option>
                                <option value="confirmado" <?php if ($estado_pedido == "confirmado") echo "selected"; ?>>Confirmado</option>
                                <option value="preparacion" <?php if ($estado_pedido == "preparacion") echo "selected"; ?>>Preparación</option>
                                <option value="camino" <?php if ($estado_pedido == "camino") echo "selected"; ?>>Camino</option>
                                <option value="entregado" <?php if ($estado_pedido == "entregado") echo "selected"; ?>>Entregado</option>
@ -134,7 +126,6 @@ while ($venta = mysqli_fetch_assoc($resultado_historial)) {
                    </form>
                </div>
            </section>
            <!-- Resumen -->
            <section class="row g-3 mb-4">
                <div class="col-md-6">
                    <article class="card shadow-sm border-0 stat-box h-100">
@ -153,7 +144,6 @@ while ($venta = mysqli_fetch_assoc($resultado_historial)) {
                    </article>
                </div>
            </section>
            <!-- Tabla historial -->
            <section class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Ventas pagadas</h5>
@ -219,13 +209,10 @@ while ($venta = mysqli_fetch_assoc($resultado_historial)) {
                </div>
            </section>
        </main>
        <!-- Footer -->
        <?php include "../masterpage/footer.php"; ?>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
// Cerrar conexión
mysqli_close($conexion);
?>