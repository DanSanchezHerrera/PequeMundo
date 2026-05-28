<?php
// Iniciar sesión
session_start();
// Verificar sesión iniciada
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pago fallido - PequeMundo</title>
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
                        <img src="img/pago_fallido.png" alt="Pago fallido" class="img-fluid mb-4" style="max-width: 180px;">
                        <!-- Mensaje principal -->
                        <h2 class="mb-3">No pudimos completar el pago</h2>
                        <p class="text-muted mb-4">El pago fue rechazado o cancelado. Puedes volver al carrito e intentarlo nuevamente.</p>
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-circle-xmark"></i>
                            No se descontó stock ni se confirmó la compra.
                        </div>
                        <!-- Acciones -->
                        <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
                            <a href="carrito.php" class="btn btn-volver-carrito">Volver al carrito</a>
                            <a href="catalogo.php" class="btn btn-seguir-comprando">Seguir comprando</a>
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