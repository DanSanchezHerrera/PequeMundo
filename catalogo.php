<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/navbar.css">
    <title>Catalogo - PequeMundo</title>
</head>

<body>
    <?php include 'masterpage/menu.php'; ?>
    <main class="container my-5">
        <section class="row mb-5">
            <div class="col-12">
                <div class="bg-white p-5 rounded shadow-sm text-center">
                    <h2>Nuestro Catalogo</h2>
                    <p>Descubre nuestra seleccion de muebles y accesorios para los mas pequeños</p>
                </div>
            </div>
        </section>
        <section class="row mb-4">
            <div class="col-md-3 mb-3">
                <select class="form-select">
                    <option selected>Todas las categorias</option>
                    <option>Cunas</option>
                    <option>Comodas</option>
                    <option>Muebles</option>
                    <option>Accesorios</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <select class="form-select">
                    <option selected>Ordenar por</option>
                    <option>Precio: menor a mayor</option>
                    <option>Precio: mayor a menor</option>
                    <option>Mas recientes</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Buscar productos...">
                    <button class="btn btn-custom" type="button">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </section>
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="bg-white rounded shadow-sm h-100">
                    <div class="bg-light d-flex align-items-center justify-content-center rounded-top"
                        style="height: 200px; overflow: hidden;">
                        <img src="img/cuna.png" alt="Cuna" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div class="p-3">
                        <h5 class="mb-1">Producto Ejemplo</h5>
                        <p class="text-muted small mb-2">Categoria del producto</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">$00.000</span>
                            <button class="btn btn-custom btn-sm">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="bg-white rounded shadow-sm h-100">
                    <div class="bg-light d-flex align-items-center justify-content-center rounded-top"
                        style="height: 200px; overflow: hidden;">
                        <img src="img/cuna.png" alt="Cuna" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div class="p-3">
                        <h5 class="mb-1">Producto Ejemplo</h5>
                        <p class="text-muted small mb-2">Categoria del producto</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">$00.000</span>
                            <button class="btn btn-custom btn-sm">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="bg-white rounded shadow-sm h-100">
                    <div class="bg-light d-flex align-items-center justify-content-center rounded-top"
                        style="height: 200px; overflow: hidden;">
                        <img src="img/cuna.png" alt="Cuna" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div class="p-3">
                        <h5 class="mb-1">Producto Ejemplo</h5>
                        <p class="text-muted small mb-2">Categoria del producto</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">$00.000</span>
                            <button class="btn btn-custom btn-sm">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="bg-white rounded shadow-sm h-100">
                    <div class="bg-light d-flex align-items-center justify-content-center rounded-top"
                        style="height: 200px; overflow: hidden;">
                        <img src="img/cuna.png" alt="Cuna" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div class="p-3">
                        <h5 class="mb-1">Producto Ejemplo</h5>
                        <p class="text-muted small mb-2">Categoria del producto</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">$00.000</span>
                            <button class="btn btn-custom btn-sm">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav aria-label="Navegacion de paginas" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Anterior</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Siguiente</a>
                </li>
            </ul>
        </nav>
    </main>
    <?php include 'masterpage/footer.php'; ?>
</body>

</html>