<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inicio - PequeMundo</title>
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
        <!-- NAVBAR -->
        <?php include 'masterpage/menu.php'; ?>
        <!--Main-->
        <main class="container my-5">
            <!-- Primera fila: imagen + descripción -->
            <section class="row align-items-center mb-5">
                <div class="col-md-4 text-center">
                    <img src="img/pequeMundo_icono2.png" alt="Logo PequeMundo" class="imagen-home">
                </div>
                <div class="col-md-8">
                    <div class="bg-white p-5 rounded shadow-sm text-center">
                        <h2>Los mejores muebles para tu bebé.</h2>
                        <p>Conoce nuestras opciones y descuentos. Retira en tienda o pide despacho a domicilio.</p>
                        <a class="btn btn-custom" href="catalogo.php">Catálogo</a>
                    </div>
                </div>
            </section>
            <!-- Segunda fila: imagen principal -->
            <section class="row mb-5">
                <div class="col-12">
                    <img src="img/cuna.png" alt="Cuna para bebé" class="img-fluid rounded">
                </div>
            </section>
            <!-- Tercera fila: sobre nosotros -->
            <section class="row mb-5 p-3 bg-white rounded shadow-sm">
                <div class="col-12">
                    <h2>Sobre Nosotros</h2>
                    <p>
                        PequeMundo es un emprendimiento chileno dedicado al diseño y venta de muebles infantiles,
                        enfocado en crear espacios seguros y acogedores para los más pequeños. Nos especializamos
                        en productos funcionales y de calidad que acompañan el crecimiento de cada niño.
                    </p>
                    <div class="text-end">
                        <a class="btn btn-custom" href="nosotros.php">Conocer Más</a>
                    </div>
                </div>
            </section>
        </main>
        <!-- FOOTER -->
        <?php include 'masterpage/footer.php'; ?>
    </body>
</html>